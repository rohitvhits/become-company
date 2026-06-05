<?php

namespace App\Services;

use App\Model\DocusignRecordResponsesLog;

class DocusignRecordResponsesLogService
{
    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');

        if (isset($auth->id)) {
            $data['created_by'] = $auth->id;
        }

        $insert = new DocusignRecordResponsesLog($data);
        $insert->save();

        return $insert->id;
    }
}