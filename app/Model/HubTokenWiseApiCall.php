<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class HubTokenWiseApiCall extends Model
{
    public $timestamps = false;
    protected $table = 'hub_token_wise_api_call';
    protected $fillable = ['id','token_id','api_call','del_flag','created_date','created_by'];


}
