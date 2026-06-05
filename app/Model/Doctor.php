<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Doctor extends Model
{
	use Notifiable; 

	protected $table = 'doctor_master';
	protected $fillable = ['id','full_name','email', 'gender', 'phone', 'remarks','created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'deleted_flag','license','address','city','state','zipcode','place_of_examination','date_of_examination','signature_upload','stamp_upload','specialty','registry_number','npi_number','is_active','is_signature_stamp_active'];

	
}
