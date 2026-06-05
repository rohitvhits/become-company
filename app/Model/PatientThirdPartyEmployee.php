<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class PatientThirdPartyEmployee extends Model
{
    public $timestamps = false;

    protected $table = 'patient_third_party_employees';

    protected $guarded = ["id"];
}
