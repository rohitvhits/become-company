<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationEmail extends Model
{
    use SoftDeletes;
    protected $table = "notification_email";
    protected $guarded = ["id"];


    public static function NotificationList(){
        return NotificationEmail::where('del_flag','N')
        ->orderBy('id','desc')
        ->paginate(10);
    }

    public static function NotificationDataById($id){
        return NotificationEmail::find($id);
    }
}
