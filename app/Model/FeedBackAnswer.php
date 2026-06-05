<?php

namespace App\Model;
use App\User;
use App\Master;
use App\Model\Patient;
use App\Model\PatientServiceRequest;

use Illuminate\Database\Eloquent\Model;

class FeedBackAnswer extends Model
{
    protected $table = "client_review_feedback_answer";
    protected $guarded = ["id"];


    public function createdUser(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function patientDetail(){
        return $this->hasOne(Patient::class,'id','patient_id');
    }

    public function serviceRequestDetail(){
        return $this->hasOne(PatientServiceRequest::class,'id','service_id');
    }
}
