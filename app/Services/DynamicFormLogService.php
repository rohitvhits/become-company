<?php

namespace App\Services;

use App\Model\DynamicFormLog;
use App\Helpers\Utility;
class DynamicFormLogService
{
	public static function storeFormLog($fieldsArr)
	{
        $auth =auth()->user();
        $id = null;
        if($auth){
            $id = $auth->id;
        }else{
            if(isset($fieldsArr['created_by'])){
                $id = $fieldsArr['created_by'];
            }
        }
		$insertData  = array(
			    'type' => $fieldsArr['type'],
			    'link' => $fieldsArr['link'],
                'module' => $fieldsArr['module'],
                'module_id' => isset($fieldsArr['module_id'])? $fieldsArr['module_id'] :0,
                'old_response' => isset($fieldsArr['old_response'])?$fieldsArr['old_response']:null,
                'new_response' => isset($fieldsArr['new_response'])?$fieldsArr['new_response']:null,
                'ip' =>Utility::getIP(),
                'is_status' => isset($fieldsArr['is_status'])?$fieldsArr['is_status']:null,
                'created_by' =>$id,
                'created_at' => date('Y-m-d H:i:s'),
                'message'=>isset($fieldsArr['message'])?$fieldsArr['message']:null,
                'esign_old_response'=>isset($fieldsArr['esign_old_response'])?$fieldsArr['esign_old_response']:null,
                'esign_new_response'=>isset($fieldsArr['esign_new_response'])?$fieldsArr['esign_new_response']:null,
		    );
        $insert = new DynamicFormLog($insertData);
        $insert->save();
		$insertId = $insert->id;
		return $insertId;
	}
    public static function getLogData($document_id,$module){
        return DynamicFormLog::where('module_id', $document_id)->where('module',$module)->get();
    }

    public static function getLogDataWithEsign($document_id,$module){
        return DynamicFormLog::with(['userDetails:id,first_name,last_name'])->where('module_id', $document_id)->where('module',$module)->get();
    }

    public static function getDetailsById($id,$module){
        return DynamicFormLog::with(['userDetails:id,first_name,last_name'])->where('id', $id)->where('module',$module)->first();
    }
}