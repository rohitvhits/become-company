<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logs extends Model
{
    use SoftDeletes;
    protected $table = "logs";
    protected $guarded = ["id"];

    public function user(){
        return $this->belongsTo(User::class,'created_by','id');
    }
   
    public function userWithTrash(){
        return $this->belongsTo(User::class,'created_by','id')->withTrashed();
    }
}
