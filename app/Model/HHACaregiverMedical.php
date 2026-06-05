<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class HHACaregiverMedical extends Model
{
	use Notifiable;
	public $timestamps =false;
	protected $table = 'hha_caregivers_medical';
	protected $fillable = ['id','visit_id','caregiver_id','medical_id','medical_name','due_date','status','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by'];
}
