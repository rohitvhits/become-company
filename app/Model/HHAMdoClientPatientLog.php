<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HHAMdoClientPatientLog extends Model
{
    public $timestamps = false;
    protected $table = 'hha_mdo_client_patient_log';
    protected $guarded = ["id"];

}