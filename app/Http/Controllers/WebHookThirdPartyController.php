<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller as BaseController;
use App\Services\ThirdPartyPatientMasterService;
use Illuminate\Support\Facades\Validator;
use App\Services\PatientService;
use App\Helpers\ThirdPartyWebHookHelper;
use App\Model\ThirdPartyWebhookLog;
class WebHookThirdPartyController extends BaseController{

    protected $thirdPartyPatientMaster,$patientService = "";
    public function __construct(ThirdPartyPatientMasterService $thirdPartyPatientMaster,PatientService $patientService)
    { 
        $this->thirdPartyPatientMaster = $thirdPartyPatientMaster;
        $this->patientService = $patientService;
    }

    public function index(Request $request){
        $data = [
            "first_name" => "Test",
            "last_name" => "TEST",
            "patient_code" => "dsds",
            "type" => "Patient",
            "dob" => "2025-05-07",
            "gender" => "male",
            "phone" => "2222222222"
        ];
        return view('third_party_patient.webhook',compact('data'));
    }

    function sendWebHook(Request $request){
       $query = $this->thirdPartyPatientMaster->getPatientDetailsWithoutAgencyId($request->appointment_id);
       if(isset($query->id)){
        if($query->agency_id ==191){
            $getPatientDetails = $this->patientService->getDetailByIdEncrypt(sha1($query->patient_id));
      
            $data=['first_name'=>$query->first_name,'last_name'=>$query->last_name,'patient_code'=>$query->patient_code,'type'=>$query->type,'dob'=>$query->dob,'gender'=>$query->gender,'phone'=>$query->phone];
            
            $response = ThirdPartyWebHookHelper::sendWebHook($data);
           $json = json_decode($response,true);
            if(isset($json['status']) && $json['status'] =='success'){
                $createdBy = null;
                if(isset($auth->id)){
                    $createdBy = $auth->id;
                }
                $saveResponse = [
                    'third_party_id'=>$request->appointment_id,
                    'send_response'=>serialize($data),
                    'return_response'=>serialize(array($json['data'])),
                    'created_date'=>date('Y-m-d H:i:s'),
                    'created_by'=>$createdBy
                ];

                $saveData = new ThirdPartyWebhookLog($saveResponse);
                $saveData->save();
                return response()->json(['status'=>true,'error_msg'=>$json['message'],'data'=>$json['data']]);
            }else{
             return response()->json(['status'=>false,'error_msg'=>'Appointment does not linked with patient'],500);
            }
        }else{
            return response()->json(['status'=>false,'error_msg'=>'Appointment not found'],500);
        }
       }else{
        return response()->json(['status'=>false,'error_msg'=>'Appointment not found'],500);
       }
       
    }
    
}