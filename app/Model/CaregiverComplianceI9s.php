<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaregiverComplianceI9s extends Model
{
    use SoftDeletes;
    public $timestamps =false;
    protected $table = "caregiver_compliance_I9s";
    protected $guarded = ["id"];

   
}
