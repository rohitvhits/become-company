<?php

namespace App\Services;

use App\Model\HHAAuditLog;

class HhaAuditLogService
{
    public function getListing($filters = [])
    {
        $query = HHAAuditLog::with(['patient', 'createdByUser']);

        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }

        if (!empty($filters['hha_patient_id'])) {
            $query->where('hha_patient_id', $filters['hha_patient_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->whereBetween('created_at', [
                $filters['from_date'] . ' 00:00:00',
                $filters['to_date'] . ' 23:59:59'
            ]);
        }

        return $query->orderBy('id', 'desc')->paginate(50);
    }

    public function getById($id)
    {
        return HHAAuditLog::with(['patient', 'createdByUser'])->find($id);
    }

    public function save($data)
    {
        $auth = auth()->user();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $insert = new HHAAuditLog($data);
        $insert->save();
        return $insert->id;
    }
}
