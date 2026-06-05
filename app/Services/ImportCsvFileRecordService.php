<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\ImportCsvFileRecord;
use Illuminate\Support\Facades\Log;

class ImportCsvFileRecordService
{
    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";

        $insert = new ImportCsvFileRecord($data);
        $insert->save();
        return $insert->id;
    }

    /**
     * Bulk insert records with optimized performance
     * Removes double chunking - chunks are now handled by the Job
     * @param array $records Array of records to insert
     * @return int Number of records inserted
     */
    public function bulkInsert(array $records)
    {
        if (empty($records)) {
            return 0;
        }

        $timestamp = date('Y-m-d H:i:s');

        // Add common fields to all records
        foreach ($records as &$record) {
            // Only add created_date if not already set
            if (!isset($record['created_date'])) {
                $record['created_date'] = $timestamp;
            }
            if (!isset($record['del_flag'])) {
                $record['del_flag'] = "N";
            }
        }
        unset($record); // Break reference

        try {
            // Single insert without re-chunking (chunking handled by Job)
            // For very large batches, we still chunk to avoid query size limits
            $maxChunkSize = 500; // MySQL has limits on query size

            if (count($records) <= $maxChunkSize) {
                // Small batch - insert directly
                DB::table('import_csv_file_record')->insert($records);
                return count($records);
            } else {
                // Large batch - chunk to avoid query size limits
                $chunks = array_chunk($records, $maxChunkSize);
                $totalInserted = 0;

                foreach ($chunks as $chunk) {
                    DB::table('import_csv_file_record')->insert($chunk);
                    $totalInserted += count($chunk);
                }

                return $totalInserted;
            }
        } catch (\Exception $e) {
            Log::error("Bulk insert failed: " . $e->getMessage(), [
                'record_count' => count($records),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Check for existing records to avoid duplicates
     * @param array $records Array of records to check
     * @param int $agencyId Agency ID
     * @param int $import_file_id Import file ID
     * @return array Array of duplicate records information
     */
    public function checkDuplicates(array $records, $agencyId, $import_file_id)
    {
        if (empty($records)) {
            return [];
        }

        $duplicates = [];

        // Extract unique identifiers for bulk checking
        $identifiers = [];
        foreach ($records as $record) {
            if (!empty($record['mobile']) && !empty($record['dob'])) {
                $identifiers[] = [
                    'mobile' => $record['mobile'],
                    'dob' => $record['dob'],
                    'first_name' => $record['first_name'],
                    'last_name' => $record['last_name']
                ];
            }
        }

        if (empty($identifiers)) {
            return [];
        }

        // Bulk query to find existing records
        $existing = ImportCsvFileRecord::where('agency_id', $agencyId)
            ->where('del_flag', 'N')
            ->where('import_file_id', $import_file_id)
            ->where(function($query) use ($identifiers) {
                foreach ($identifiers as $identifier) {
                    $query->orWhere(function($q) use ($identifier) {
                        $q->where('mobile', $identifier['mobile'])
                          ->where('dob', $identifier['dob'])
                          ->where('first_name', $identifier['first_name'])
                          ->where('last_name', $identifier['last_name']);
                    });
                }
            })
            ->select('mobile', 'dob', 'first_name', 'last_name')
            ->get();

        // Create a lookup map of existing records
        foreach ($existing as $exist) {
            $key = $exist->mobile . '|' . $exist->dob . '|' . $exist->first_name . '|' . $exist->last_name;
            $duplicates[$key] = true;
        }

        return $duplicates;
    }

    public function update($data, $where)
    {
        // Check if user is authenticated
        if (auth()->check()) {
            $auth = auth()->user();
            $data['updated_date'] = date('Y-m-d H:i:s');
            $data['updated_by'] = $auth['id'];
        } else {
            // For queue jobs where auth might not be available
            $data['updated_date'] = date('Y-m-d H:i:s');
            if (!isset($data['updated_by'])) {
                $data['updated_by'] = 482; // System user
            }
        }

        return ImportCsvFileRecord::where($where)->update($data);
    }

    public function getList($search, $id)
    {
        $perPage = $search['per_page'] ?? 50;
        $searchTerm = $search['search'] ?? "";
        $status = $search['status'] ?? null;

        $query = ImportCsvFileRecord::where('import_file_id', $id)
            ->where('del_flag', 'N');

        // Search functionality
        if (!empty($searchTerm)) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('mobile', 'like', "%{$searchTerm}%")
                  ->orWhere('patient_code', 'like', "%{$searchTerm}%")
                  ->orWhere('type', 'like', "%{$searchTerm}%")
                  ->orWhere('status', 'like', "%{$searchTerm}%");
            });
        }

        // Status filter (fixed - was using undefined $status variable)
        if (isset($status) && !empty($status)) {
            if ($status == 'Completed') {
                $query->where('sync_status', '=', "Y");
            } else if ($status == 'Pending') {
                $query->where('sync_status', '=', "N");
            } else if ($status == 'Failed') {
                $query->where('sync_status', '=', "F");
            }
        }

        // Order by latest first
        $query->orderBy('id', 'desc');

        // Get paginated results
        return $query->paginate($perPage);
    }

    /**
     * Get import statistics
     * @param int $import_file_id
     * @return array
     */
    public function getImportStats($import_file_id)
    {
        $stats = DB::table('import_csv_file_record')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN sync_status = "Y" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN sync_status = "N" THEN 1 ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN sync_status = "F" THEN 1 ELSE 0 END) as failed')
            )
            ->where('import_file_id', $import_file_id)
            ->where('del_flag', 'N')
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'completed' => $stats->completed ?? 0,
            'pending' => $stats->pending ?? 0,
            'failed' => $stats->failed ?? 0
        ];
    }

    /**
     * Delete records by import file ID
     * @param int $import_file_id
     * @return int Number of records deleted
     */
    public function deleteByImportFileId($import_file_id)
    {
        $auth = auth()->user();

        return ImportCsvFileRecord::where('import_file_id', $import_file_id)
            ->update([
                'del_flag' => 'Y',
                'updated_date' => date('Y-m-d H:i:s'),
                'updated_by' => $auth['id']
            ]);
    }

    public function getDetailsById($id){
        return ImportCsvFileRecord::with(['userDetail:id,first_name,last_name'])->where('id', $id)
            ->where('del_flag', 'N')
            ->first();
    }

    public function getFetchAllRecordByImportId($importId){
        return ImportCsvFileRecord::where('sync_status', 'N')
                ->where('import_file_id', $importId)
                ->where('del_flag', 'N')
                ->get();
    }

    public function syncStatusData($id){
        return ImportCsvFileRecord::where('import_file_id', $id)
            ->select('sync_status', DB::raw('COUNT(*) as count'))
            ->groupBy('sync_status')
            ->pluck('count', 'sync_status');
    }
}
