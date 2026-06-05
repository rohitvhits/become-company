<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class HHACaregiverSubject extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'hha_caregiver_subject';
	protected $fillable = ['id','caregiver_id','subject_id','subject_name','del_flag', 'created_date','created_by'];

}
