<?php

namespace App\Services;

use App\Model\DateWiseAgencyAccessLog;

class DateWiseAgencyAccessLogService
{
    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";
        
        $insert = new DateWiseAgencyAccessLog($data);
        $insert->save();
        return $insert->id;
    }
}