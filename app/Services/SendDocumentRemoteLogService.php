<?php
namespace App\Services;

use App\Model\SendDocumentRemoteLog;

class SendDocumentRemoteLogService{

    public function save($data)
    {
        $auth = auth()->user();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";

        $insert = new SendDocumentRemoteLog($data);
        $insert->save();
        return $insert->id;
    }
}