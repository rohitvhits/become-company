<?php

namespace App\Services;

use App\Model\NotificationType;

class NotificationTypeService
{
    public static function getAllNotificationTypeData(){
        $query = NotificationType::where('delete_flag','N')->pluck('name')->toArray();
        return $query;
    }
}
