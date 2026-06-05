<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class DoctorPaperWorkDetail extends Model
{
    use Notifiable;
    public $timestamps = false;
    protected $table = 'doctor_paper_work_detail';
    protected $fillable = ['id','paper_work_id','notes','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by','fax_date'];
	
	
}
