<?php

namespace App\Helpers;

use App\Services\LogsService;


class LogsHelper
{
    public function __construct()
    {
    }

    public static function handleLogs($logs){
        $ipaddress = Utility::getIP();
		$insertLog = [
			'type' => $logs['type'],
			'link' => $logs['link'],
			'module' => $logs['module'],
			'object_id' => $logs['object_id'],
			'message' => $logs['message'],
			'old_response' => isset($logs['old_response']) && !empty($logs['old_response']) ? serialize($logs['old_response']) : NULL,
			'new_response' => isset($logs['new_response']) && !empty($logs['new_response']) ? serialize($logs['new_response']) : NULL,
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
    }
}