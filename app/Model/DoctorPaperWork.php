<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class DoctorPaperWork extends Model
{
    use Notifiable;
    public $timestamps = false;
    protected $table = 'doctor_paper_work';
    protected $fillable = ['id','name','portal_id','gender','dob','doctor_name','phone','fax','agency','rep','notes_rep','medical_report','progress_notes','date','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by','fax_date','record_id','emc_user_id'];
	
	
}
