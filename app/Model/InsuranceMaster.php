<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceMaster extends Model
{
    public $timestamps = false;
    protected $table = 'insurance_masters';
    protected $guarded = ["id"];
    protected $fillable = ['id', 'insurance_name','del_flag','created_date', 'updated_at', 'deleted_date', 'created_by', 'updated_by', 'deleted_by'];
}