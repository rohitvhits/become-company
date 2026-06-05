<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\GroupWiseUserNotification;
use App\Model\GroupWiseServiceNotification;
use App\User;

class GroupNotificationMaster extends Model
{
    use SoftDeletes;
    protected $table = 'group_notification_master';
    protected $guarded = ['id'];


    public function services(){
        return $this->hasMany(GroupWiseServiceNotification::class, 'group_id'); 
    }

    public function users(){
        return $this->hasMany(GroupWiseUserNotification::class,"group_id");
    }

    public function userData(){
        return $this->belongsTo(User::class,"created_by","id");
    }
}
