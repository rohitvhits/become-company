<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Helpers\Utility;

use Illuminate\Support\Facades\Validator;
use URL;

use App\Services\LogsService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class LogController extends BaseController
{

    public function index(Request $request){

        $data['menu'] = "User Log";
        $data['user'] = $user = auth()->user();
        $data['id'] =request('id');
       
        return view("Logs/all_log", $data);

    }
    public function allLogList(Request $request)
    {

        $user = auth()->user();
        
        $data['user'] = auth()->user();
        $data['patientId'] = $patientId = request('patientId');
        $final = [];
        
        if($patientId !=""){
            $final = LogsService::getDataPatient($patientId,'');
            foreach($final as $val){
                
                $val->old_response = ($val->old_response !="")?unserialize($val->old_response):[]; 
                $val->new_response = ($val->new_response !="")?unserialize($val->new_response):[]; 
            }
        }
        
        

      return response()->json(['status'=>1,'data'=>$final],200);
    }
}