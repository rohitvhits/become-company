<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\SendTaskHealthDocumentLog;

class SendTaskHealthDocumentLogService{
    protected const DATE_FORMAT_YMD='Y-m-d H:i:s';
    public function save($data){
        $auth = auth()->user();
        $data['created_date'] = date(self::DATE_FORMAT_YMD);
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";
        
        $insert = new SendTaskHealthDocumentLog($data);
        $insert->save();
        
        return $insert->id;
    }

    public function update($data, $where){
        $auth = auth()->user();
        $data['updated_date'] = date(self::DATE_FORMAT_YMD);
        $data['updated_by'] = $auth['id'];
        
        return SendTaskHealthDocumentLog::where($where)->update($data);
    }
}
