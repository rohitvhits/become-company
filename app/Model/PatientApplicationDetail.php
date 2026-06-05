<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientApplicationDetail extends Model
{
    public $timestamps=false;
    public $table="patient_application_detail";
    public $fillable=['id',"patient_id",'bank_name','income_amount','account_no','how_often','income_type','balance','marital_status','name_of_owner','name_of_income',"del_flag","created_date",'created_by',"updated_date",'updated_by',"deleted_date",'deleted_by'];

}
