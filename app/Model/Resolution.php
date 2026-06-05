<?php

namespace App\Model;

use App\Model\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resolution extends Model
{
    use SoftDeletes;
    protected $table = 'resolution_log';
    protected $guarded = ['id'];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }
}
