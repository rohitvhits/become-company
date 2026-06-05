<?php

namespace App\Services;

use App\Model\SendVisitingDocumentThirdParty;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class SendVisitingDocumentThirdPartyService
{
    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        if(isset($auth['id'])){
            $data['created_by'] = $auth['id'];
        }
        $data['del_flag'] = "N";

        $insert = new SendVisitingDocumentThirdParty($data);
        $insert->save();
        return $insert->id;
    }
}