<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientBankDetail extends Model
{
    public $timestamps=false;
    public $table="patient_bank_detail";
    public $fillable=['id',"patient_id",'bank_name','account_no','balance','name_of_owner',"del_flag","created_date",'created_by',"updated_date",'updated_by',"deleted_date",'deleted_by'];

}
 