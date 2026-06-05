<?php

namespace App\Services;

use App\Model\EmmacareReferalTable;

class EmmacareReferalTableService
{

    public function getList($search,$paginate=""){
        $query =  EmmacareReferalTable::select('record_id','first_name','middle_name','last_name','dob','note','gender','primaryLanguage','externalId','phones','insurance','referral_uid','created_at','created_by','return_response')->with(['userDetaials:id,first_name,last_name'])->where('del_flag','N');
        if(isset($search['record_id']) && $search['record_id'] !=""){
            $query->where('record_id',$search['record_id']);
        }

        if(isset($search['full_name']) && $search['full_name'] !=""){
            $query->whereRaw('CONCAT(first_name,last_name) LIKE "%'.str_replace(" ",'',$search['full_name']).'%"');
        }

        if(isset($search['dob']) && $search['dob'] !=""){
            $explode = explode('-',$search['dob']);
            if(isset($explode[1])){
                $query->whereDate('dob','>=',date('Y-m-d',strtotime($explode[0])))->whereDate('dob','<=',date('Y-m-d',strtotime($explode[1])));
            }else{
                $query->whereDate('dob',date('Y-m-d',strtotime($explode[0])));
            }
        }

        if(isset($search['gender']) && $search['gender'] !=""){
            $query->where('gender',$search['gender']);
        }

        if(isset($search['language']) && $search['language'] !=""){
            $query->where('primaryLanguage',$search['language']);
        }

        if(isset($search['mobile']) && $search['mobile'] !=""){
            $query->where('phones',$search['mobile']);
        }

        if(isset($search['insurance']) && $search['insurance'] !=""){
            $query->where('insurance',$search['insurance']);
        }

        if(isset($search['referral_uid']) && $search['referral_uid'] !=""){
            $query->where('referral_uid',$search['referral_uid']);
        }

        if(isset($search['created_date']) && $search['created_date'] !=""){
            $explode = explode('-',$search['created_date']);
            if(isset($explode[1])){
                $query->whereDate('created_at','>=',date('Y-m-d',strtotime($explode[0])))->whereDate('created_at','<=',date('Y-m-d',strtotime($explode[1])));
            }else{
                $query->whereDate('created_at',date('Y-m-d',strtotime($explode[0])));
            }
        }

        if(isset($search['created_by']) && $search['created_by'] !=""){
            $query->where('created_by',$search['created_by']);
        }

        $query->orderBy('id','desc');
        if($paginate !=""){
            return $query->get();
        }else{
            return $query->paginate(50);
        }
       
    }
}
