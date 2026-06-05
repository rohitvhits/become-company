<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Agency;
class HhaAppointment extends Model
{
    public $timestamps = false;
    protected $table = 'hha_appointment';
    protected $guarded = ["id"];

    public function agencyDetails(){
        return $this->belongsTo(Agency::class,"agency_id","id");
    }

    public function hhaCaregivers(){
        return $this->belongsTo(HHACaregivers::class,"caregiver_id","caregiver_id")->where('hha_delete_flag','N');
    }

    public function hhaOffices(){
        return $this->belongsTo(HHAOffice::class,"office_id","office_id")->where('del_flag','N');
    }
    public static  function insertData($data)
    {
        $insertData = $data;
        $inserId = new HhaAppointment($insertData);
        $inserId->save();
        return $inserId->id;
    }

    public static  function updateData($data, $where)
    {
        return HhaAppointment::where($where)->update($data);
    }
    
    public static  function getDataByMedicalID($medicalId)
    {
        return HhaAppointment::where('medical_id', $medicalId)->where('del_flag', 'N')->first();
    }

    public static function getDetailsByAgencyIdAndId($agency_fk,$id){
        return HHAAppointment::where('agency_id', $agency_fk)->where('id', $id)->first();
    }
}