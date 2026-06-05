<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
class GroupWiseUserNotification extends Model
{
    use SoftDeletes;
    protected $table = 'group_wise_user_notification';
    protected $guarded = ['id'];

    
    public function userDeatils(){
        return $this->belongsTo(User::class,"user_id","id");
    }
}
