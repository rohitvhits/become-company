<?php
namespace App\Services;

use App\Model\AgencyPocDocumentType;

class AgencyPocDocumentTypeService
{

    public function save($data)
    {
        $auth = auth()->user();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";
        $insert = new AgencyPocDocumentType($data);
        return $insert->save();
    }
    public function saveOrUpdate($data)
    {
        $auth = auth()->user();
        $now = date('Y-m-d H:i:s');
        $record = AgencyPocDocumentType::firstOrNew([
            'agency_id'   => $data['agency_id'],
            'document_id' => $data['document_id'],
            'del_flag' => 'N'
        ]);
        if (!$record->exists) {
            $record->created_at = $now;
            $record->created_by = $auth['id'];
             $record->del_flag      = 'N';
        }else{
            $record->updated_at    = $now;
            $record->updated_by    = $auth['id'];
        }
        $record->document_name = $data['document_name'];
        $record->status        = $data['status'];
        return $record->save();
    }

    public function getDocumentbyAgencyId($agencyId){
        return AgencyPocDocumentType::select('document_id','document_name')->where('del_flag','N')->where('agency_id',$agencyId)->orderBy('document_name','asc')->get();
    }

    public function getDocumentNameById($documentId,$agencyId){
        $record = AgencyPocDocumentType::where('document_id', $documentId)->where('agency_id', $agencyId)->where('del_flag', 'N')->first();
        return $record ? $record->document_name : null;
    }
}