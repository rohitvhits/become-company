<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\EsignImportDetail;

use App\Helpers\Common;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Services\TemplateService;
use App\Services\DocumentSignerService;
use App\Services\UserService;
use App\Services\PatientService;
use App\Services\SmsService;
use App\Services\DocumentSendService;
use App\Services\LogsService;
use App\Services\DynamicFormLogService;
use App\Services\EsignImportDetailService;
use App\Services\EsignImportLogService;
use App\Helpers\EsignHelper;

class SendBulkEsignSMS extends Command
{
    protected $signature = 'esign:send-bulk-sms {--limit=500 : Number of records to process per execution}';

    protected $description = 'Send bulk eSign SMS for pending import detail records';
    protected $templateService;
    protected $documentSignerService;
    protected $userService;
    protected $patientService;
    protected $smsService;
    protected $documentSendService;
    protected $esignImportDetailService;
    protected $esignImportLogService;

    protected const SMS_ESIGN_LINK = 'esign/nye/';
    protected const MODULE_TYPE = "Patient Appointment";
    protected const LOG_ESIGN_SMS_LINK = "/esign/esign-import-confirm";

    public function __construct(
        TemplateService $templateService,
        DocumentSignerService $documentSignerService,
        UserService $userService,
        PatientService $patientService,
        SmsService $smsService,
        DocumentSendService $documentSendService,
        DynamicFormLogService $dynamicFormLogService,
        EsignImportDetailService $esignImportDetailService,
        EsignImportLogService $esignImportLogService
    ) {
        parent::__construct();
        $this->templateService = $templateService;
        $this->documentSignerService = $documentSignerService;
        $this->userService = $userService;
        $this->patientService = $patientService;
        $this->smsService = $smsService;
        $this->documentSendService = $documentSendService;
        $this->dynamicFormLogService = $dynamicFormLogService;
        $this->esignImportDetailService = $esignImportDetailService;
        $this->esignImportLogService = $esignImportLogService;
    }

    public function handle()
    {
        $limit = 500;

        $getProcessingImport = $this->esingLogProcessing();
        if (!isset($getProcessingImport->id)) {
            return 0;
        }

        $successCount = 0;
        $failedCount = 0;

        do {
            $records = $this->esignImportDetailService->fetchDetails($limit, $getProcessingImport->id);

            if ($records->isEmpty()) {
                break;
            }

            foreach ($records as $record) {

                try {
                    if (!$record->mobile) {
                        $this->updateFailedRecord($record, 'failed', 'No mobile number');
                        $failedCount++;
                        continue;
                    }

                    // Save document_sent_report, generate eSign link, send SMS, update record
                    if ($record->import_status == 'Success') {
                        $this->insertDocumentSentReport($record);
                    }
                    // Reload record to check updated status
                    $record->refresh();

                    if ($record->status === 'success') {
                        $successCount++;
                    } else {
                        $failedCount++;
                    }

                } catch (\Exception $e) {
                    Log::error("SendBulkEsignSMS: Failed for record ID {$record->id}, Patient: {$record->patient_id}. Error: " . $e->getMessage());

                    $this->updateFailedRecord($record, 'failed', $e->getMessage());
                    $failedCount++;
                }
            }

            // Update parent import log counts after each batch
            $this->updateImportLogCounts($getProcessingImport->id);

        } while ($records->count() >= $limit);

        return 0;
    }

    private function updateRecord(
        EsignImportDetail $record,
        string $status,
        ?string $smsId,
        ?string $smsStatus,
        ?string $smsDate,
        $smsMessage = ""
    ) {
        $record->update([
            'status' => $status,
            'sms_id' => $smsId,
            'sms_status' => $smsStatus,
            'sms_date' => $smsDate,
            'message' => $smsMessage
        ]);
    }

    private function insertDocumentSentReport(EsignImportDetail $record)
    {
        try {
            $context = $this->getContextData($record);

            $portalId = [];
            $smsResponse = [];

            foreach ($context['signers'] as $index => $signer) {

                $response = $this->processSigner(
                    $signer,
                    $index,
                    $context,
                    $record
                );

                if (!empty($response['portalId'])) {
                    $portalId[] = $response['portalId'];
                }

                if (!empty($response['smsResponse'])) {
                    $smsResponse = $response['smsResponse'];
                }
            }

            $this->saveLog($context);

            $this->processSMSLogs($context, $smsResponse, $portalId);

        } catch (\Exception $e) {

            throw $e;
        }
    }

    private function updateImportLogCounts($importId)
    {
     
        try {
            $successCount = $this->esignImportDetailService->getStatusWiseRecordCount($importId, 'success');

            $failedCount = $this->esignImportDetailService->getStatusWiseRecordCount($importId, 'failed');

            $pendingCount = $this->esignImportDetailService->getStatusWiseRecordCount($importId);

            $updateData = [
                'success_count' => count($successCount),
                'failed_count' => count($failedCount),
            ];

            // If no more pending records, mark import as Completed
            if (count($pendingCount) === 0) {
                $updateData['status'] = 'Completed';
            }

            $updateData['updated_by'] = env('CRONJOB_USER_ID');
            $this->esignImportLogService->update($updateData,['id'=>$importId]);

        } catch (\Exception $e) {
            Log::error("SendBulkEsignSMS: Failed to update import log ID {$importId}. Error: " . $e->getMessage());
        }
       
    }

    private function getContextData($record)
    {
        $importLog = $this->esignImportLogService->getDetailById($record->import_id);
        return [
            'record' => $record,
            'importLog' => $this->esignImportLogService->getDetailById($record->import_id),
            'sourceFile' => $this->templateService->getDetailsById($importLog->template_id),
            'signers' => $this->documentSignerService->getDocumentSignerMasterListById($importLog->template_id),
            'patient' => $this->patientService->getPatientDetailsByIdWhitoutAgency($record->patient_id),
            'createdUser' => $this->userService->getDetailsById($importLog->created_by),
            'groupId' => uniqid(),
        ];
    }

    private function buildSignerReportData($signer, $index, $context)
    {
        $name = strtolower($signer->name);
        $isFirst = ($index === 0);

        $common = $this->getCommonData($context, $isFirst);

        switch ($name) {

            case 'caregiver':
                return array_merge($common, [
                    'caregiver_code' => $context['patient']->patient_code ?? null,
                    'receipt_name' => $this->getPatientName($context),
                    'sent_on' => 'caregiver',
                ]);

            case 'officestaff':
                $user = $this->userService->getDetailsById($signer->user_id);

                return array_merge($common, [
                    'caregiver_code' => $signer->user_id,
                    'receipt_name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                    'sent_on' => 'OfficeStaff',
                ]);

            case 'patient':
            case 'other':
            case 'stampuser':
            case 'formfill':
            case 'sign':
            case 'stamp':
            case 'sign&stamp':
                return array_merge($common, [
                    'caregiver_code' => $context['patient']->patient_code ?? null,
                    'receipt_name' => $this->getPatientName($context),
                    'sent_on' => $this->mapSignerType($name),
                ]);

            default:
                return [];
        }
    }

    private function getCommonData($context, $isFirst)
    {
        $user = $context['createdUser'];
        $importLog = $context['importLog'];
        $sourceFile = $context['sourceFile'];

        return [
            'sender_name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
            'status' => $isFirst ? 'Pending' : '',
            'sender_id' => $importLog->created_by,
            'templete_id' => $importLog->template_id,
            'sourceFile' => $isFirst ? ($sourceFile->upload_document ?? '') : '',
            'main_intakeId' => $context['record']->patient_id,
            'groupId' => $context['groupId'],
            'template_response' => $sourceFile->response ?? null,
            'created_date' => now(),
            'created_by' => env('CRONJOB_USER_ID'),
        ];
    }

    private function saveDocumentSentReport($data)
    {
        return $this->documentSendService->saveesign($data);
    }

    private function getPatientName($context)
    {
        $patient = $context['patient'];

        if (!$patient) {
            return 'N/A';
        }

        return trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));
    }

    private function mapSignerType($name)
    {
        return EsignHelper::populateSignerType($name);
    }

    private function sendSMSStatus($caregiverDocId, $record, $context)
    {
        $link = URL::to(self::SMS_ESIGN_LINK) . '/' . $caregiverDocId . '?id=' . $context['groupId'];

        $patientName = $this->getPatientName($context);

        $smsMessage = "Dear {$patientName},\nPlease complete esign from below link.\n{$link}";

        // Send SMS
        $smsId = null;
        $smsStatus = null;
        $smsDate = null;
        $status = 'failed';

        try {
            $response = $this->smsService->bulkEsignSmsDynamic(
                $record->patient_id,
                $record->mobile,
                $smsMessage
            );

            if ($response) {

                $smsId = $response['smsId'] ?? null;
                $smsStatus = $response['status'] ?? 'unknown';
                $smsDate = $response['date_updated'];
                $status = 'success';

            } else {
                $smsStatus = 'No response from SMS API';
            }

        } catch (\Exception $e) {
            Log::error("SendBulkEsignSMS: SMS send failed for record ID {$record->id}. Error: " . $e->getMessage());

            $smsStatus = 'SMS send failed for record ID';
        }

        // Update document_sent_report with SMS details
        $response = [
            'sms' => $record->mobile,
            'send_sms_mobile_no' => $record->mobile,
            'bulk_send_sms_text' => $smsMessage,
            'sms_id' => $smsId ?? '',
            'sms_status' => $smsStatus ?? '',
        ];

        $this->documentSendService->updateEsign($response, ['id' => $caregiverDocId]);

        // Update esign_import_details with SMS result
        $this->updateRecord($record, $status, $smsId, $smsStatus, $smsDate, $smsMessage);

        return $response;
    }

    private function saveLog($context)
    {

        $documentRes = $this->documentSendService->getAllDetailsByGroupId($context['groupId']);

        $logDetails = [];
        $groupId = "";

        if (count($documentRes) > 0) {

            foreach ($documentRes->toArray() as $val) {

                $val['template_name'] = $val['template_details']['template_name'];
                $val['added_by_name'] = $val['user_details']['first_name'] . ' ' . $val['user_details']['last_name'];

                unset($val['template_details'], $val['user_details']);

                $logDetails[] = $val;
                $groupId = $val['groupId'];
            }
        }

        $message = $context['createdUser']->first_name . ' ' . $context['createdUser']->last_name . ' has create a new esign template';

        $insertLog = [
            'type' => 'Bulk Added',
            'link' => url(self::LOG_ESIGN_SMS_LINK),
            'module' => 'Esign Section',
            'module_id' => $groupId,
            'new_response' => serialize($logDetails),
            'old_response' => '',
            'is_status' => 'Added',
            'ip_address' => $context['importLog']->ip_address,
            'message' => $message,
            'created_by' => env('CRONJOB_USER_ID'),
        ];

        $this->dynamicFormLogService->storeFormLog($insertLog);

        $insertLog = [
            'type' => 'Add Bulk Esign Template',
            'link' => url(self::LOG_ESIGN_SMS_LINK),
            'module' => self::MODULE_TYPE,
            'object_id' => $context['record']->patient_id,
            'message' => $message,
            'new_response' => serialize([
                'id' => $context['record']->patient_id,
                'patient_code' => $context['patient']->patient_code,
                'receipt_name' => $context['patient']->first_name . ' ' . $context['patient']->last_name,
                'template_id' => $context['importLog']->template_id
            ]),
            'ip' => $context['importLog']->ip_address,
            'created_by' =>  env('CRONJOB_USER_ID'),
        ];

        LogsService::save($insertLog);
    }

    private function saveSMSLog($context, $smsResponse, $portalId)
    {

        $messages = $context['createdUser']->first_name . ' ' . $context['createdUser']->last_name . ' has sent Esign message';

        $insertLog = [
            'type' => 'Send Bulk Esign SMS',
            'link' => url(self::LOG_ESIGN_SMS_LINK),
            'module' => self::MODULE_TYPE,
            'object_id' => $portalId,
            'message' => $messages,
            'new_response' => serialize($smsResponse),
            'ip' => $context['importLog']->ip_address,
            'created_by' => $context['importLog']->created_by,
        ];

        LogsService::save($insertLog);

        $insertLog = [
            'type' => 'Send Bulk Esign SMS',
            'link' => url(self::LOG_ESIGN_SMS_LINK),
            'module' => 'Esign Section',
            'module_id' => $context['groupId'],
            'new_response' => serialize($smsResponse),
            'message' => $messages,
            'is_status' => 'Send SMS - Email',
            'created_by' => $context['importLog']->created_by,
        ];

        $this->dynamicFormLogService->storeFormLog($insertLog);
    }

    private function updateFailedRecord(
        EsignImportDetail $record,
        string $status,
        $smsMessage = ""
    ) {
        $record->update([
            'status' => $status,
            'error_message' => $smsMessage
        ]);
    }

    private function processSigner($signer, $index, $context, $record)
    {
        $data = $this->buildSignerReportData($signer, $index, $context);

        if (empty($data)) {
            return [];
        }

        $savedId = $this->saveDocumentSentReport($data);

        if (!$this->isCaregiverSigner($signer) || !$savedId) {
            return [];
        }

        return $this->handleCaregiverSigner(
            $savedId,
            $record,
            $context
        );
    }

    private function isCaregiverSigner($signer)
    {
        return strtolower($signer->name) == 'caregiver';
    }

    private function handleCaregiverSigner($caregiverDocId, $record, $context)
    {
        if (!$caregiverDocId || !$record->mobile) {
            return [];
        }

        $smsResponse = $this->sendSMSStatus(
            $caregiverDocId,
            $record,
            $context
        );

        $portalId = null;

        if (!in_array($record->patint_id, [])) {
            $portalId = $record->patient_id;
        }

        return [
            'portalId' => $portalId,
            'smsResponse' => $smsResponse,
        ];
    }

    private function processSMSLogs($context, $smsResponse, $portalId)
    {
        if (empty($portalId[0])) {
            return;
        }

        foreach ($portalId as $pid) {
            $this->saveSMSLog($context, $smsResponse, $pid);
        }
    }

    private function esingLogProcessing(){
        return $this->esignImportLogService->fetchProcessingDetails();
    }
}
