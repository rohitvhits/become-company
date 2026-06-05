<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

class HubLogs extends Model
{
    use SoftDeletes;
    protected $table = "hub_logs";
    protected $guarded = ["id"];

    public function user(){
        return $this->belongsTo(User::class,'created_by','id');
    }
   
}
