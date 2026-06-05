<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadApi extends Model
{
    public $timestamps = false;
    protected $table = 'lead_api';
    protected $guarded = ['id'];
    
}
