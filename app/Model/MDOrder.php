<?php

namespace App\Model;
use App\User;
use Illuminate\Database\Eloquent\Model;


class MDOrder extends Model
{
	public $timestamps = false;
	protected $table = 'md_order';
	protected $fillable = ['id','patient_id','start_date','end_date','document_id', 'del_flag', 'created_date', 'created_by','updated_date','updated_by', 'deleted_date', 'deleted_by'];

	public function users() 
	{
		return $this->belongsTo(User::class,'created_by','id');
	}

    public function patientDetails() 
	{
		return $this->belongsTo(Patient::class,'patient_id','id')->where('deleted_flag','N');
	}

    public function documentDetails() 
	{
		return $this->belongsTo(DocumentPatient::class,'document_id','id');
	}
}
 