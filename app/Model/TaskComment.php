<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    public $timestamps = false;
    protected $table = 'task_comment';
    protected $guarded = ["id"];
    
    public function userDetails()
	{
		return $this->belongsTo(User::class,'created_by','id');
	}
}
