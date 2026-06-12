<?php

namespace App\Services;

use App\Model\Patient;
use App\Model\SendRNPadDocument;
use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;
use Carbon\Carbon;
class SendRNPadDocumentService
{
    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'] ?? env('API_USER_ID');
        $data['del_flag'] = "N";

        $insert = new SendRNPadDocument($data);
        $insert->save();
        return $insert->id;
    }

    public function update($data, $where)
    {
        return SendRNPadDocument::where($where)->update($data);
    }

    public function getAll($where = [])
    {
        $query = SendRNPadDocument::query();

        if (!empty($where)) {
            $query->where($where);
        }

        return $query->get();
    }

    public function getById($id)
    {
        return SendRNPadDocument::find($id);
    }

    public function getAllData($search,$documentIds=[],$paginate="")
    {
        $query = SendRNPadDocument::leftJoin('patient_master',function($join){
            $join->on('patient_master.id','=','rnpad_document.patient_id');
        })
        ->leftJoin('document_patient',function($join){
            $join->on('document_patient.id','=','rnpad_document.document_id');
        })->leftJoin('users',function($join){
            $join->on('users.id','=','rnpad_document.created_by');
        })->leftJoin('agency',function($join){
            $join->on('agency.id','=','patient_master.agency_id');
        })->leftJoin('patient_service_requests',function($join){
            $join->on('patient_service_requests.id','=','rnpad_document.request_service_id');
        })->where('rnpad_document.del_flag','N')->where('agency.delete_flag','N')->where('patient_master.deleted_flag','N')->where('document_patient.deleted_flag','N');
        // Filters
        if (isset($search['patient_name']) && $search['patient_name'] !="") {
            $query->where('patient_master.full_name', 'LIKE', '%' . $search['patient_name'] . '%');
        }

        if (isset($search['document_name']) && $search['document_name'] !="") {
            $query->where('document_patient.document_name', 'LIKE', '%' . $search['document_name'] . '%');
        }

      
        if (isset($search['created_date']) && $search['created_date'] !="") {
            $explode = explode('-',$search['created_date']);
            $startDate = Carbon::parse($explode[0])->format('Y-m-d');
            $endDate = Carbon::parse($explode[1])->format('Y-m-d');
            $query->where('rnpad_document.created_date','>=',$startDate.' 00:00:00')->where('rnpad_document.created_date','<=',$endDate.' 23:59:59');
        }

        if(!empty($documentIds[0])){
            $query->whereIn('rnpad_document.document_id', $documentIds);
        }

        if (!empty($search['status'][0])) {

            $query->whereIn('patient_service_requests.status', $search['status'] );
        }

        if (!empty($search['agency_id'][0])) {
            
            $query->whereIn('patient_master.agency_id', $search['agency_id'] );
        }
        // Select columns (important: prefix with alias)
        $query->select([
            'rnpad_document.id',
            'rnpad_document.send_third_party_document_date',
            'rnpad_document.document_id',
            'document_patient.document_name',
            'rnpad_document.request_service_id',
            'rnpad_document.is_checked',
            'rnpad_document.created_date',
            'rnpad_document.patient_id',
            'rnpad_document.created_by',
            "patient_master.full_name",
            "patient_master.agency_id",
            "agency.agency_name",
            'users.first_name as createdUserFirstName',
            'users.last_name as createdUserLastName',
            'rnpad_document.send_third_party_document_date',
            'patient_service_requests.status as patientServiceStatus',
            'document_patient.attachment'
        ]);

        // Order by created date
        $query->orderBy('rnpad_document.created_date', 'DESC');

        // Paginate
        if($paginate !=""){
            return $query->get();
        }
       return $query->paginate(15);
    }
}