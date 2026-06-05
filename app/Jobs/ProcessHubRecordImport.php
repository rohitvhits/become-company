<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\HubRecordService;
use App\Services\HubRecordAgencyService;
use App\Services\HubRecordImportLogService;
use App\Services\HubLogsService;
use App\Services\HubCompanyService;
use App\Model\HubRecordAgency;
use App\Helpers\Common;
use App\Helpers\Utility;
use App\Mail\HubImportComplete;
use Illuminate\Support\Facades\Mail;

class ProcessHubRecordImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 7200;
    public $tries = 1;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        
        $hubRecordService      = new HubRecordService();
        $hubRecordAgencyService = new HubRecordAgencyService();
        $logService            = new HubRecordImportLogService();
        $hubCompanyService     = new HubCompanyService();

        $logId     = $this->data['log_id'];
        $path      = $this->data['path'];
        $addRemove = $this->data['add_remove'];
        $agencyId  = $this->data['agency_id'];
        $filetype  = $this->data['filetype'] ?? null;
        $uniqueFields = $this->data['unique_fields'] ?? [];
        $authId    = $this->data['auth_id'];
        $authName  = $this->data['auth_name'];
        $authEmail = $this->data['auth_email'] ?? null;
        $fileName  = $this->data['file_name'] ?? 'import.csv';
        $totalRows = $this->data['total_rows'] ?? 0;
        $colIndex  = $this->data['col_index'];

        $imported        = 0;
        $updated         = 0;
        $skipped         = 0;
        $deactivated     = 0;
        $dublicateRecords = 0;
        $errors          = [];
        $importedRecords = [];

        try {
            $isS3 = !str_starts_with($path, '/') && !preg_match('/^[A-Za-z]:[\\\\\/]/', $path);

            Log::info('ProcessHubRecordImport: path=' . $path . ' isS3=' . ($isS3 ? 'true' : 'false'));

            if ($isS3) {
                if (!Storage::disk('s3')->exists($path)) {
                    $logService->failImport($logId, "S3 file not found: {$path}");
                    return;
                }
                $lines = explode("\n", str_replace("\r", '', Storage::disk('s3')->get($path)));
                array_shift($lines); // skip header row
            } else {
                $handle = fopen($path, 'r');
                if ($handle === false) {
                    $logService->failImport($logId, 'Unable to open file for processing.');
                    return;
                }
                fgetcsv($handle); // skip header row
            }

            $chunkSize = 500;
            $batch     = [];
            $rowNum    = 0;

            $flushChunk = function () use (
                &$batch, &$rowNum, $colIndex, $addRemove, $agencyId, $filetype,
                $uniqueFields, $authId, $authName, $hubRecordService,
                $hubRecordAgencyService, $hubCompanyService,
                &$imported, &$updated, &$skipped, &$deactivated,
                &$dublicateRecords, &$errors, &$importedRecords, $logService, $logId
            ) {
                foreach ($batch as [$i, $row]) {
                    $record = [];
                    foreach ($colIndex as $field => $idx) {
                        $record[$field] = isset($row[$idx]) ? trim($row[$idx]) : null;
                    }

                    $currentAgencyId = $agencyId;

                    // Resolve agency for master_file
                    if ($filetype === 'master_file' && !empty($record['company_name'])) {
                        $agencyDetails = $hubCompanyService->getAllAgencyPluck($record['company_name']);
                        $firstKey = array_key_first($agencyDetails->toArray());
                        if ($firstKey) {
                            $currentAgencyId = $firstKey;
                        } else {
                            $errors[] = "For Row $i: Company name '{$record['company_name']}' not found.";
                            $skipped++;
                            continue;
                        }
                    }

                    $record = $this->parseDob($record);

                    if (!empty($record['mobile'])) {
                        $record['mobile'] = Common::normalizePhoneNumberdate($record['mobile']);
                    }
                    if (!empty($record['phone'])) {
                        $record['phone'] = Common::normalizePhoneNumberdate($record['phone']);
                    }
                    $record['ssn'] = str_replace('-', '', $record['ssn'] ?? '');

                    if ($addRemove === 'remove') {
                        $existingRecords = $this->findByUniqueFields(
                            $hubRecordService, $record, $currentAgencyId, $uniqueFields, 'get'
                        ) ?: collect();

                        foreach ($existingRecords as $existingR) {
                            $existingRecord = HubRecordAgency::where('hub_record_id', $existingR->id)
                                ->where('agency_id', $currentAgencyId)->first();
                            if (isset($existingRecord->id)) {
                                HubRecordAgency::where('id', $existingRecord->id)->update([
                                    'status'           => 'deactivated',
                                    'deactivated_date' => date('Y-m-d H:i:s'),
                                    'deactivated_by'   => $authId,
                                ]);
                                HubLogsService::save([
                                    'type'      => 'Hub record agency deactivated',
                                    'link'      => url('/hub-record/'),
                                    'module'    => 'Hub Record',
                                    'object_id' => $existingR->id,
                                    'message'   => "$authName has deactivate Hub Record",
                                    'ip'        => $this->data['ip'],
                                ]);
                                $deactivated++;
                            }
                        }
                        continue;
                    }

                    // add / add_remove — validate required fields
                    $missingFields = [];
                    if (empty($record['first_name'])) $missingFields[] = 'First name';
                    if (empty($record['last_name']))  $missingFields[] = 'Last name';
                    if (empty($record['dob']))        $missingFields[] = 'Date Of Birth';
                    if (empty($record['gender']))     $missingFields[] = 'Gender';
                    if ($filetype === 'master_file' && empty($record['company_name'])) $missingFields[] = 'Company name';

                    if (!empty($missingFields)) {
                        $errors[] = "For Row $i: The following required fields are empty: [" . implode(', ', $missingFields) . "]. Please upload a valid file.";
                        $skipped++;
                        continue;
                    }

                    $agencyData = [];
                    $record['import_flag'] = 1;

                    $existing = $this->findByUniqueFields(
                        $hubRecordService, $record, $currentAgencyId, $uniqueFields, 'first'
                    );

                    if ($existing) {
                        $hubRecordService->update(
                            ['updated_by' => $authId],
                            ['id' => $existing->id]
                        );
                        $checkagencyData = $hubRecordAgencyService->getAgencyData($existing->id, $currentAgencyId);
                        $agencyPayload = [
                            'hub_record_id'   => $existing->id,
                            'agency_id'       => $currentAgencyId,
                            'status'          => 'active',
                            'hire_date'       => !empty($record['hire_date']) ? date('Y-m-d H:i:s', strtotime($record['hire_date'])) : null,
                            'work_contact'    => $record['work_contact'] ?? '',
                            'work_email'      => $record['work_email'] ?? '',
                            'employee_code'   => $record['employee_code'] ?? '',
                            'member_id'       => $record['member_id'] ?? '',
                            'last_worked_date' => !empty($record['last_worked_date']) ? date('Y-m-d H:i:s', strtotime($record['last_worked_date'])) : null,
                        ];
                        if (empty($checkagencyData)) {
                            $agencyPayload['created_by'] = $authId;
                            $hubRecordAgencyService->save($agencyPayload);
                        } else {
                            $agencyPayload['updated_by'] = $authId;
                            $hubRecordAgencyService->update($agencyPayload, [
                                'hub_record_id' => $existing->id,
                                'agency_id'     => $currentAgencyId,
                            ]);
                        }
                        $agencyData = $agencyPayload;
                        $updated++;
                        HubLogsService::save([
                            'type'      => 'Hub record activated',
                            'link'      => url('/hub-record/'),
                            'module'    => 'Hub Record',
                            'object_id' => $existing->id,
                            'message'   => "$authName has activate Hub Record",
                            'ip'        => $this->data['ip'],
                        ]);
                    } else {
                        $record['full_name']   = $record['first_name'] . ' ' . $record['last_name'];
                        $record['created_by']  = $authId;
                        $checkSSN = $hubRecordService->checkDuplicateSSN($record);
                        if ($checkSSN) {
                            if (!empty($record['ssn'])) {
                                $dublicateRecords++;
                            }
                            $errors[] = "For Row $i: SSN is duplicate.";
                        }
                        $recordInsId = $hubRecordService->save($record);
                        $agencyData = [
                            'hub_record_id'   => $recordInsId,
                            'agency_id'       => $currentAgencyId,
                            'status'          => 'active',
                            'hire_date'       => !empty($record['hire_date']) ? date('Y-m-d H:i:s', strtotime($record['hire_date'])) : null,
                            'work_contact'    => $record['work_contact'] ?? '',
                            'work_email'      => $record['work_email'] ?? '',
                            'employee_code'   => $record['employee_code'] ?? '',
                            'member_id'       => $record['member_id'] ?? '',
                            'last_worked_date' => !empty($record['last_worked_date']) ? date('Y-m-d H:i:s', strtotime($record['last_worked_date'])) : null,
                        ];
                        $hubRecordAgencyService->save($agencyData);
                        $imported++;
                        HubLogsService::save([
                            'type'      => 'Hub record created',
                            'link'      => url('/hub-record/'),
                            'module'    => 'Hub Record',
                            'object_id' => $recordInsId,
                            'message'   => "$authName has created Hub Record",
                            'ip'        => $this->data['ip'],
                        ]);
                    }

                    $importedRecords[] = $agencyData;
                }

                $logService->updateImportProgress($logId, [
                    'successful_records' => $imported,
                    'failed_records'     => $skipped,
                    'updated_records'    => $updated,
                ]);

                $batch = [];
            };

            $rowIterator = $isS3
                ? $lines
                : (function () use ($handle) {
                    while (($row = fgetcsv($handle)) !== false) yield $row;
                })();

            foreach ($rowIterator as $row) {
                if ($isS3) {
                    if (trim($row) === '') continue;
                    $row = str_getcsv($row);
                }
                $rowNum++;

                if (count(array_filter($row)) === 0) {
                    $skipped++;
                    continue;
                }

                $batch[] = [$rowNum, $row];

                if (count($batch) >= $chunkSize) {
                    $flushChunk();
                }
            }

            if (!empty($batch)) {
                $flushChunk();
            }

            if (!$isS3) {
                fclose($handle);
            }

            // For add_remove: deactivate records not in this import
            if ($addRemove === 'add_remove' && !empty($importedRecords)) {
                $importedMap    = [];
                foreach ($importedRecords as $r) {
                    $importedMap[$r['hub_record_id'] . '-' . $r['agency_id']] = true;
                }
                $existingRecords = $hubRecordAgencyService->getAllRecord($agencyId);
                foreach ($existingRecords as $existingRecord) {
                    $key = $existingRecord->hub_record_id . '-' . $existingRecord->agency_id;
                    if (!isset($importedMap[$key])) {
                        $existingRecord->status           = 'deactivated';
                        $existingRecord->deactivated_date = date('Y-m-d H:i:s');
                        $existingRecord->deactivated_by   = $authId;
                        $existingRecord->save();
                        $deactivated++;
                        HubLogsService::save([
                            'type'      => 'Hub record agency deactivated',
                            'link'      => url('/hub-record/'),
                            'module'    => 'Hub Record',
                            'object_id' => $existingRecord->hub_record_id,
                            'message'   => "$authName has deactivate Hub Record",
                            'ip'        => $this->data['ip'],
                        ]);
                    }
                }
            }

            // Clean up the uploaded file
            if ($isS3) {
                Storage::disk('s3')->delete($path);
            } elseif (file_exists($path) && str_contains($path, storage_path('app/temp'))) {
                @unlink($path);
            }

            $errorJson = !empty($errors) ? json_encode(array_slice($errors, 0, 500)) : null;

            $logService->completeImport($logId, [
                'successful_records'   => $imported,
                'failed_records'       => $skipped,
                'updated_records'      => $updated,
                'deactivate_records'   => $deactivated,
                'duplicate_ssn_records' => $dublicateRecords,
            ], $errorJson);

            if ($authEmail) {
                Mail::to($authEmail)->send(new HubImportComplete(
                    $authName,
                    $fileName,
                    $totalRows,
                    $imported,
                    $skipped,
                    $updated,
                    $deactivated,
                    'completed',
                    $errorJson,
                    now()->format('M d, Y h:i A')
                ));
            }

            HubLogsService::save([
                'type'    => 'Hub Record data imported',
                'link'    => url('/hub-record/'),
                'module'  => 'Hub Record Import',
                'message' => "Import completed. $imported imported, $updated updated, $skipped skipped, $deactivated deactivated.",
                'ip'      => $this->data['ip'],
            ]);

        } catch (\Exception $e) {
            Log::error('ProcessHubRecordImport failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $logService->failImport($logId, $e->getMessage());

            if (!empty($authEmail)) {
                try {
                    Mail::to($authEmail)->send(new HubImportComplete(
                        $authName,
                        $fileName,
                        $totalRows,
                        $imported,
                        $skipped,
                        $updated,
                        $deactivated,
                        'failed',
                        $e->getMessage(),
                        now()->format('M d, Y h:i A')
                    ));
                } catch (\Exception $mailEx) {
                    Log::error('Failed to send hub import failure email: ' . $mailEx->getMessage());
                }
            }

            throw $e;
        }
    }

    public function failed(\Throwable $e)
    {
        Log::error('ProcessHubRecordImport job failed permanently: ' . $e->getMessage());
        $logService = new HubRecordImportLogService();
        $logService->failImport($this->data['log_id'], $e->getMessage());

        $authEmail = $this->data['auth_email'] ?? null;
        $authName  = $this->data['auth_name'] ?? 'User';
        $fileName  = $this->data['file_name'] ?? 'import.csv';
        $totalRows = $this->data['total_rows'] ?? 0;

        if ($authEmail) {
            try {
                Mail::to($authEmail)->send(new HubImportComplete(
                    $authName,
                    $fileName,
                    $totalRows,
                    0, 0, 0, 0,
                    'failed',
                    $e->getMessage(),
                    now()->format('M d, Y h:i A')
                ));
            } catch (\Exception $mailEx) {
                Log::error('Failed to send hub import failure email (failed hook): ' . $mailEx->getMessage());
            }
        }
    }

    private function parseDob(array $record): array
    {
        if (empty($record['dob'])) {
            $record['dob'] = null;
            return $record;
        }

        try {
            $dateStr = $record['dob'];
            $parts   = explode('-', $dateStr);
            $dateFlag = 0;

            if (isset($parts[2])) {
                $exPort = $parts[2];
            } else {
                $parts    = explode('/', $dateStr);
                $dateFlag = 1;
                $exPort   = $parts[2] ?? null;
            }

            if (empty($exPort)) {
                $record['dob'] = null;
                return $record;
            }

            $yearPart = trim($exPort);
            $date     = $dateStr;

            if (strlen($yearPart) === 2) {
                $date = $dateFlag === 0
                    ? Carbon::createFromFormat('m-d-y', $dateStr)
                    : Carbon::createFromFormat('m/d/y', $dateStr);
                if ($date->year > (int)date('Y') + 10) {
                    $date = $date->subCentury();
                }
            }

            $parsed = date('Y-m-d', strtotime((string)$date));
            if ($parsed === '1969-12-31') {
                $parsed = Utility::parseFlexibleDate($date);
            }
            $record['dob'] = $parsed;
        } catch (\Exception $e) {
            $record['dob'] = null;
        }

        return $record;
    }

    private function findByUniqueFields($hubRecordService, array $record, $agencyId, array $uniqueFields, string $mode)
    {
        $hubRecordQuery = \App\Model\HubRecord::select(
            'hub_record.*',
            'hub_record_agency.agency_id',
            'hub_record_agency.hire_date',
            'hub_record_agency.work_contact',
            'hub_record_agency.work_email',
            'hub_record_agency.employee_code',
            'hub_record_agency.member_id',
            'hub_record_agency.id as hub_record_agency_id'
        )->where('deleted_flag', 'N')
         ->join('hub_record_agency', 'hub_record.id', '=', 'hub_record_agency.hub_record_id');

        $hubConditions    = [];
        $agencyConditions = [];

        foreach ($uniqueFields as $field) {
            if (empty($record[$field])) continue;

            if (in_array($field, ['member_id', 'employee_code', 'work_email', 'work_contact'])) {
                $agencyConditions[] = [$field, '=', $record[$field]];
            } else {
                if ($field === 'email')  $hubConditions[] = ['email', '=', $record[$field]];
                if ($field === 'ssn')    $hubConditions[] = ['ssn', '=', $record[$field]];
                if ($field === 'dob')    $hubConditions[] = ['dob', '=', $record['dob']];
                if ($field === 'phone' || $field === 'mobile') {
                    $hubConditions[] = [$field, '=', Common::normalizePhoneNumberdate($record[$field])];
                }
                if ($field === 'first_name') $hubConditions[] = ['first_name', '=', $record['first_name']];
                if ($field === 'last_name')  $hubConditions[] = ['last_name', '=', $record['last_name']];
                if ($field === 'gender')     $hubConditions[] = ['gender', '=', $record['gender']];
                else {
                    if (!in_array($field, ['member_id', 'employee_code', 'work_email', 'work_contact'])) {
                        $hubConditions[] = [$field, '=', $record[$field]];
                    }
                }
            }
        }

        if (!empty($hubConditions)) {
            $hubRecordQuery->where(function ($q) use ($hubConditions) {
                foreach ($hubConditions as $condition) {
                    $q->where($condition[0], $condition[1], $condition[2]);
                }
            });
        }

        if (!empty($agencyConditions)) {
            $hubRecordQuery->where(function ($q) use ($agencyConditions) {
                foreach ($agencyConditions as $condition) {
                    $q->where('hub_record_agency.' . $condition[0], $condition[1], $condition[2]);
                }
            });
        }

        if ($mode === 'first') {
            return $hubRecordQuery->first();
        }

        return $hubRecordQuery->where('hub_record_agency.agency_id', $agencyId)
            ->where('hub_record_agency.status', 'active')
            ->get();
    }
}
