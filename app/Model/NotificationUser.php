<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class NotificationUser extends Model
{
    use SoftDeletes;
    protected $table = 'notification_user';
    protected $guarded = ['id'];


    public function users() 
	{
		return $this->belongsTo(User::class,'created_by','id');
	}
}
