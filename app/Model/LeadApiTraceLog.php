<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\GenerateAgencyToken;
class LeadApiTraceLog extends Model
{

    public $timestamps = false;
    protected $table = 'lead_api_trace_log';
    protected $guarded = ['id'];
  
}
