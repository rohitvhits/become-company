<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class VnsEsignFormData extends Model
{
	use Notifiable;
	public $timestamps = false;
	protected $table = 'vns_esign_form_data';
	protected $fillable = ['id', 'patient_id', 'template_id', 'req_data', 'created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by','pdf','main_template_id'];
}
