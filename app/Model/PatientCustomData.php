<?php

namespace App\Model;

use App\Agency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientCustomData extends Model
{
    use SoftDeletes;
    protected $table = 'patient_custom_data_submit';
    protected $guarded = ['id'];

    public function agency()
    {
        return $this->hasOne(Agency::class, 'id', 'agency_id');
    }

    public function fields()
    {
        return $this->hasOne(FieldMaster::class, 'id', 'field_id');
    }

}
