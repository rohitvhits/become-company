<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientRentDetail extends Model
{
    public $timestamps=false;
    public $table="patient_rent_detail";
    public $fillable=['id',"patient_id",'amount',"del_flag","created_date",'created_by',"updated_date",'updated_by',"deleted_date",'deleted_by'];

}
 