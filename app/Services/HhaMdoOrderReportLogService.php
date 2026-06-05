<?php

namespace App\Services;

use App\Model\HhaMdoOrderReportLog;

use Carbon\Carbon;
use App\Helpers\Utility;

class HhaMdoOrderReportLogService
{

    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $insert = new HhaMdoOrderReportLog($data);
        $insert->save();
        return $insert->id;
    }

    public function getAllData($search,$paginate=""){
        $query = HhaMdoOrderReportLog::select('hha_send_mdo_log.id','hha_send_mdo_log.attachment','hha_send_mdo_log.hha_document_id','hha_send_mdo_log.created_date','pt.full_name','ag.agency_name','users.first_name as uFirstName','users.last_name as uLastName')
        ->leftjoin('patient_master as pt',function($join){
            $join->on('pt.id','=','hha_send_mdo_log.patient_id');
        })
        ->leftjoin('agency as ag',function($join){
            $join->on('ag.id','=','hha_send_mdo_log.agency_id');
        })->leftjoin('users',function($join){
            $join->on('users.id','=','hha_send_mdo_log.created_by');
        })->where('pt.deleted_flag','N')->where('ag.delete_flag','N');

        if(isset($search['agency_id']) && $search['agency_id'] !=""){
            $query->whereIn('hha_send_mdo_log.agency_id',$search['agency_id']);
        }
        if(isset($search['patient_name']) && $search['patient_name'] !=""){
            $query->where('pt.full_name',$search['patient_name']);
        }
        if(isset($search['created_date']) && $search['created_date'] !=""){
            $explode = explode('-',$search['created_date']);
            $startDate = $explode[0].' 00:00:00';
            $endDate = $explode[1].' 00:00:00';
            $query->where('hha_send_mdo_log.created_date','>=',Utility::convertYMDTime($startDate))->where('hha_send_mdo_log.created_date','<=',Utility::convertYMDTime($endDate));
        }

        $query->orderBy('hha_send_mdo_log.created_date','desc');
        if($paginate !=""){
            return $query->get();
        }else{
            return $query->paginate(50);
        }
    }

    public function getDetailsById($id){
        return HhaMdoOrderReportLog::where('id',$id)->where('del_flag','N')->first();
    }
}