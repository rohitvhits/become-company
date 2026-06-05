<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Model\TelehealthLocationScheduleEvent;
use App\Model\PatientTelehealthSchedule;

class Appointment extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'appointment';
    protected $guarded = ['id'];
    public static function boot()
    {
        parent::boot();
        Appointment::creating(function ($model) {
            $model->created_by = Auth::id();
        });
        Appointment::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    public static function getPastAppointmentList($patientId)
    {
       return  Appointment::with(['patient','location:id,location_name','doctor','getCreatedBy','getUpdateBy'])->where('patient_id',$patientId)->where('appointment_date','<',date('Y-m-d H:i:s',strtotime(now())))->get();
    }

    public function location(){
        return $this->belongsTo(LocationMaster::class,'location_id','id');
    }
    public function doctor(){
        return $this->belongsTo(Doctor::class,'doctor_id','id');
    }
    public function getCreatedBy(){
        return $this->belongsTo(User::class,'created_by','id')->withTrashed();
    }

    public function getUpdateBy(){
        return $this->belongsTo(User::class,'updated_by','id')->whereNotNull('updated_by');
    }

    public function patient(){
        return $this->belongsTo(Patient::class,'patient_id','id');
    }

    public static function getPastAppointmentListNew($patientId)
    {
       return  Appointment::with(['patient','location:id,location_name,address1','doctor','getCreatedBy','getUpdateBy'])->where('patient_id',$patientId)->get();
    }

    public static function getPastAppointmentListNewAll($patientId)
    {
       return  Appointment::with(['patient','location:id,location_name,address1','doctor','getCreatedBy','getUpdateBy'])->whereIn('patient_id',$patientId)->get();
    }
    
    public function appointmentScheduleSlot(){
        return $this->belongsTo(TelehealthLocationScheduleEvent::class,'telehealth_time_slot','id');
    }

    public function appointmentScheduleLanguage(){
        return $this->belongsTo(TelehealthLocationScheduleEvent::class,'telehealth_language','id');
    }

    public function appointmentPatientScheduleSlot(){
        return $this->belongsTo(PatientTelehealthSchedule::class,'telehealth_time_slot','id');
    }

    public function appointmentScheduleNurse(){
        return $this->belongsTo(User::class,'telehealth_nurse','id');
    }
}
