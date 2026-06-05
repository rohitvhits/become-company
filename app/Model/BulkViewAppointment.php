<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BulkViewAppointment extends Model
{
    public $timestamps =false;
    protected $table = "bulk_view_appointment";
    protected $guarded = ["id"];
  
}
