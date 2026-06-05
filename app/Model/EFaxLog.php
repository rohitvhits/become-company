<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EFaxLog extends Model
{
    public $timestamps = false;
    protected $table = 'e_fax_log';
    protected $fillable = ['id','document_id','patient_id','fax_no','del_flag','created_date','created_by','updated_date','updated_by','deleted_date','deleted_by','send_response','return_response'];
}
