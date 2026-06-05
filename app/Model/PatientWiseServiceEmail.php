<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class PatientWiseServiceEmail extends Model
{
    protected $table = 'patient_wise_service_email';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function userDetails()
    {
        return $this->hasOne(User::class, "id", "created_by");
    }
}
