<?php

namespace App\Services;

use App\Model\EsignImportDetail;

class EsignImportDetailService
{
    protected const COMMON_YMD = 'Y-m-d H:i:s';

    public function save($data)
    {
        $auth = auth()->user();

        $data['created_at'] = date(self::COMMON_YMD);
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";

        $insert = new EsignImportDetail($data);

        return $insert->save();
    }

    public function getListByImportId($searchResponse,$importId){
        $query = EsignImportDetail::where('import_id', $importId)
            ->where('del_flag', 'N')
            ->orderBy('id', 'asc');

        if ($searchResponse['search']) {
            $search = $searchResponse['search'];
            $query->where(function ($q) use ($search) {
                $q->where('patient_id', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('sms_status', 'like', "%{$search}%")
                  ->orWhere('import_status', 'like', "%{$search}%");
            });
        }

        if ($searchResponse['status_filter'] && $searchResponse['status_filter'] !== 'all') {
            if ($searchResponse['status_filter'] === 'pending') {
                $query->whereNull('status');
            } else {
                $query->where('status', $searchResponse['status_filter']);
            }
        }

        if (!empty($searchResponse['sms_date_from']) && !empty($searchResponse['sms_date_to'])) {
            $query->whereBetween('sms_date', [$searchResponse['sms_date_from'], $searchResponse['sms_date_to'] . ' 23:59:59']);
        }

        $perPage = $searchResponse['per_page'] ?? 50;
        return $query->paginate($perPage);
    }

    public function getDetailsById($id)
    {
        return EsignImportDetail::where('id', $id)->where('del_flag', 'N')->first();
    }

    /***Use for Queue */
    public function getListByImportIDAndPatientId($importId,$portalIds){
        return EsignImportDetail::where('import_id', $importId)
                ->whereIn('patient_id', $portalIds)
                ->where('del_flag', 'N')
                ->pluck('patient_id')
                ->toArray();
    }

    public function combineSMSStatus(){
        return EsignImportDetail::where('del_flag','N')->whereNotNull('sms_status')->groupBy('sms_status')->orderBy('sms_status')->pluck('sms_status')->toArray();
    }

    public function fetchDetails($limit,$importId){
        return EsignImportDetail::whereNull('status')->where('import_id',$importId)
            ->where('del_flag', 'N')
            ->limit($limit)
            ->get();
    }

    public function getStatusWiseRecordCount($importId,$type=""){
        $query = EsignImportDetail::where('import_id', $importId)
            ->where('del_flag', 'N');
            if($type !=""){
                $query->where('status', $type);
            }else{
                $query->whereNull('status');
            }
            return $query->get();
    }

    public function bulkInsertSave($save){
        return EsignImportDetail::insert($save);
    }

    public function fetchCronSMSDetails($reportDate){
        
        $start = $reportDate->copy()->startOfDay();
        $end = $reportDate->copy()->endOfDay();

        return EsignImportDetail::with(['patientDetail:id,first_name,last_name'])->where('import_status', 'Success')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('id', 'asc')
            ->get();
    }
}
