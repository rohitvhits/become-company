<?php

namespace App\Helpers;

use URL;
use App\Model\PatientApplicationDetail;

class PatientApplicationDetailHelper
{
    public static function save($data)
    {
        $userId = Auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        if ($userId) {
            $data['created_by'] = $userId['id'];
        }
        $insert = new PatientApplicationDetail($data);
        $insert->save();
        $insertId = $insert->id;
        return $insertId;
    }
    public static function update($data, $where)
    {
        $userId = Auth()->user();

        $update = PatientApplicationDetail::where($where)->update($data);
        return $update;
    }
    public static function SoftDelete($data, $where)
    {
        $userId = Auth()->user();
        $data['deleted_date'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $userId['id'];
        $update = PatientApplicationDetail::where($where)->update($data);
        return $update;
    }


    public static function getDetailsByPatientId($patient_id){
        $query = PatientApplicationDetail::where('del_flag','N')->where('patient_id',$patient_id)->orderBy('id','desc')->get();
        return $query;
    }
    public static function getDetailsById($patient_id){
        $query = PatientApplicationDetail::where('del_flag','N')->where('id',$patient_id)->first();
        return $query;
    }
}