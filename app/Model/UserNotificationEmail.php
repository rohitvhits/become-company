<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserNotificationEmail extends Model
{
    protected $table = "user_notification_email";
    protected $guarded = ["id"];

    public static function notificationEmailByUserId($userId){
        return UserNotificationEmail::where('user_id',$userId)->where('delete_flag','N')->first();
    }

    public static function SoftDelete($data,$where){
        $auth = auth()->user();
        $data['deleted_at'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = UserNotificationEmail::where($where)->update($data);
        return $update;
    }

    public static function getDetailsByUserId($userId){
        return UserNotificationEmail::where('user_id',$userId)->where('delete_flag','N')->first();
    }

    
}
