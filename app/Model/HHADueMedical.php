<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Agency;
class HHADueMedical extends Model
{
    public $timestamps = false;
    protected $table = 'hha_due_medical';
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
   
}