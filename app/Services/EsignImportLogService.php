<?php

namespace App\Services;

use App\Model\EsignImportLog;

class EsignImportLogService
{
    protected const COMMON_YMD = 'Y-m-d H:i:s';

    public function save($data)
    {
        $auth = auth()->user();

        $data['created_at'] = date(self::COMMON_YMD);
        if(isset($auth->id)){
            $data['created_by'] = $auth['id'];
        }
        
        $data['del_flag'] = "N";

        return EsignImportLog::create($data);
    }

    public function update($data, $where)
    {
        $auth = auth()->user();

        $data['updated_at'] = date(self::COMMON_YMD);
        if(isset($auth->id)){
            $data['updated_by'] = $auth['id'];
        }
    
        return EsignImportLog::where($where)->update($data);
    }

    /*****if not need so i will removed */
    public function getDetailsById($id)
    {
        return EsignImportLog::findOrFail($id);
    }

    public function softDelete($data, $where)
    {
        $auth = auth()->user();

        $data['deleted_at'] = date(self::COMMON_YMD);
        $data['deleted_by'] = $auth['id'];

        return EsignImportLog::where($where)->update($data);
    }

    public function getList($searchResponse)
    {
        $query = EsignImportLog::orderBy('created_at', 'desc')->where('del_flag','N');
  
        if ($searchResponse['search']) {
            $search = $searchResponse['search'];

            $query->where(function ($q) use ($search) {
                $q->where('template_name', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $perPage = $searchResponse['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    /*****Use for Cronjob and queue */
    public function getDetailById($id){
        return EsignImportLog::find($id);
    }

    public function fetchProcessingDetails(){
        return EsignImportLog::where('del_flag','N')->where('status','Processing')->orderBy('id','asc')->first();
    }
}