<?php

namespace App\Services;

use App\Model\PatientThirdPartyEmployee;

class PatientThirdPartyEmployeeService
{
    protected const YMD_DATE_FORMAT = 'Y-m-d H:i:s';

    public function save($data)
    {
        $auth = auth()->user();

        $data['created_date'] = date(self::YMD_DATE_FORMAT);
        $data['created_by'] = $auth->id;
        $data['del_flag'] = "N";

        $insert = new PatientThirdPartyEmployee($data);

        return $insert->save();
    }

    public function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date(self::YMD_DATE_FORMAT);
        $data['updated_by'] = $auth->id;

        return PatientThirdPartyEmployee::where($where)->update($data);
    }

    public function getDetailsByPatientAndType($patientId, $type, $agencyId)
    {
        return PatientThirdPartyEmployee::where('patient_id', $patientId)
            ->where('type', $type)
            ->where('agency_id', $agencyId)
            ->first();
    }

    public function getDetailsByPatientId($patientId)
    {
        return PatientThirdPartyEmployee::select('id','type','patient_id','third_party_id','third_party_code','third_party_first_name','third_party_last_name')->where('patient_id', $patientId)
            ->get();
    }
}
