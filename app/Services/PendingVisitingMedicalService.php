<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Model\PendingVisitingMedical;
use App\Helpers\Utility;

class PendingVisitingMedicalService
{
    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];

        $insert = new PendingVisitingMedical($data);
        $insert->save();
        return $insert->id;
    }

    public function update($data,$where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];

        return PendingVisitingMedical::where($where)->update($data);
    }

    public function checkExistingRecordOrNot($agencyId,$employee_code){
        return PendingVisitingMedical::where('agency_id',$agencyId)->where('employee_code',$employee_code)->first();
    }

    public function getPendingMedicalList($agencyId, $medicalDueDate = "", $paginate = false)
    {
        $query = PendingVisitingMedical::where('del_flag', 'N')
            ->where('agency_id', $agencyId)
            ->orderBy('created_date', 'desc');

        if ($medicalDueDate != "") {
            $explode = explode('-',$medicalDueDate);
            $query->whereDate('medical_due_date','>=',Utility::convertYMD($explode[0]))->whereDate('medical_due_date','<=',Utility::convertYMD($explode[1]));
        }
   
        return $paginate ? $query->get(): $query->paginate(50);
    }
}