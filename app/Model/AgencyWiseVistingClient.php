<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AgencyWiseVistingClient extends Model
{
    protected $table = "agency_wise_visiting_client";
    public $timestamps = false;
    protected $guarded = ["id"];
}
