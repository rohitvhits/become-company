<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Master;

class PatientServiceRequestLog extends Model
{
    use SoftDeletes;

    protected $table = 'patient_service_requests_log';

    protected $fillable = [
        'patient_id',
        'service_request_id',
        'type',
        'message',
        'old_response',
        'new_response',
        'created_by',
        'created_date'
    ];

    protected $dates = ['deleted_at'];

      public function patient(){
        return $this->hasOne(Patient::class,'id','patient_id');
    }
    public function services(){
        return $this->hasMany(Master::class,'id','service_request_id');
    }


}
