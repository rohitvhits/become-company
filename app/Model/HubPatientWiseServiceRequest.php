<?php

namespace App\Model;

use App\User;
use App\Master;
use Illuminate\Database\Eloquent\Model;

class HubPatientWiseServiceRequest extends Model
{
    protected $table = 'hub_patient_wise_service_requested';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function userDetails()
    {
        return $this->hasOne(User::class, "id", "created_by");
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function services()
    {
        return $this->hasMany(Master::class, 'id', 'service_id');
    }

    public function requestService()
    {
        return $this->hasOne(Master::class, 'id', 'service_id');
    }
}
