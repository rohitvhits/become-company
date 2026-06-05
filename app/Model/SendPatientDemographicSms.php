<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SendPatientDemographicSms extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'send_patient_demographic_sms';
    protected $guarded = ['id'];

}