<?php

namespace App\Services;

use App\Model\Patient;
use App\Model\SendTaskHealthDocument;
use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;
use Carbon\Carbon;
class SendTaskHealthDocumentService
{
    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        if(isset($auth['id'])){
            $data['created_by'] = $auth['id'];
        }
        $data['del_flag'] = "N";

        $insert = new SendTaskHealthDocument($data);
        $insert->save();
        return $insert->id;
    }

    public function update($data, $where)
    {
        return SendTaskHealthDocument::where($where)->update($data);
    }

    public function getAll($where = [])
    {
        $query = SendTaskHealthDocument::query();

        if (!empty($where)) {
            $query->where($where);
        }

        return $query->get();
    }

    public function getById($id)
    {
        return SendTaskHealthDocument::find($id);
    }
}