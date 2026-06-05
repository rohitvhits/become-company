<?php

namespace App\Services;
use App\Model\TokenwiseApiCall;

class TokenwiseApiCallService
{
    public function getDetailsById($id){
        return TokenwiseApiCall::where('id',$id)->where('del_flag','N')->first();
    }

    public function getAllList($id){
        return TokenwiseApiCall::where('id',$id)->where('del_flag','N')->orderBy('id','desc')->paginate(50);
    }
}