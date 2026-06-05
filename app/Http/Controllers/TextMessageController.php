<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Validator;

use App\Services\TextMessageService;
use App\Helpers\Common;
use App\Services\PatientService;
use App\User;
use DB;
use App\Model\TextMessages;
use App\Services\LogsService;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use URL;
use App\Services\AppointmentPortalMergeLogsService;
use App\Helpers\MergeUtilityHelper;
class TextMessageController extends BaseController
{

    protected   $textMessageService,$patientService="";
    protected $appointmentMergeLogsService;
    public function __construct(TextMessageService $textMessageService,PatientService $patientService,AppointmentPortalMergeLogsService $appointmentMergeLogsService)
    {
      

        $this->middleware('auth');
        $this->textMessageService=$textMessageService;
        $this->patientService=$patientService;
        $this->appointmentMergeLogsService = $appointmentMergeLogsService;
    }

   public function getMessageList(Request $request){
        $getAppointmentDetails =$this->patientService->getDetailById($request->case_id);
        $ids = [$request->case_id];
        if(isset($getAppointmentDetails->id) && $getAppointmentDetails->id !=""){
            if($getAppointmentDetails->merge_appointment_id !=""){
                //$ids = [$request->case_id,$getAppointmentDetails->merge_appointment_id];
                $ids = $this->convertMergePatientArray($getAppointmentDetails->merge_appointment_id,$request->case_id);
            }
        }
        $query= $this->textMessageService->getMessageListWithMultipleIds($ids);
        if(count($query) >0){
            foreach($query as $val){
                if($val->message_file !=""){
                   
                    $extension = pathinfo($val->message_file, PATHINFO_EXTENSION);
                    $val->file_extension =$extension;
                    if(env('FILE_UPLOAD_PERMISSION') !="development"){
                        $expiry = Carbon::now()->addMinutes(10);
                        $path = 'twillio/' . $val->message_file;
                    
                        $val->message_file = Storage::disk('s3')->temporaryUrl($path, $expiry);
                    }else{
                        $val->message_file = URL::to('/twillio').'/'.$val->message_file;
                    }
                    
                }
            }
        }
    
    return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => $query], 200);
   }

   public function smsTextMessage(Request $request){
        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'case_id' => 'required',
            'message' => 'required'
        ], [
            'case_id.required' => 'Patient is required',
            'message.required' => 'Message is required',
        ]);

        // At least one phone number must be provided
        if (empty($request->mobile) && empty($request->phone)) {
            return response()->json(['error_msg' => 'Please select a phone number to send the message.', 'status' => 0, 'data' => array()], 422);
        }
        if ($validator->fails()) {

            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 500);
        } else {
            $data = array(
				'mobile' => $request->mobile,
				'phone' => $request->phone,
				'case_id' => $request->case_id,
                'message' => $request->message,
                'message_type' => 'text',
			);
            $save = $this->textMessageService->save($data);
			if ($save) {
                $getDetails=    $this->textMessageService->getDetailsId($save);
                $getPatientDetails = $this->patientService->getDetailById($request->case_id);
                $mobileArray = [];

                if($request->phone !=""){
                    $mobileArray[]=str_replace(['(', ')', '-', ' '], '',$getPatientDetails->phone);
                }
                if($request->mobile !=""){
                    if(isset($getPatientDetails->mobile)){
                        $mobileArray[]=str_replace(['(', ')', '-', ' '], '',$getPatientDetails->mobile);
                    }
                    
                }
                
                $sms="";
                if(count($mobileArray) >0){
                    foreach($mobileArray as $mb){
                        $sms=Common::sendTwillioSms($mb,$request->message);
                    
                    }
                }

                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Send Text Messages',
                    'link' => url('/patient/text-message-notes'),
                    'module' => 'Patient Appointment',
                    'object_id' => $request->case_id,
                    'message' => $auth->first_name . ' ' . $auth->last_name . ' has send Text message',
                     'new_response' => serialize($data),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
                
                return response()->json(['error_msg' => '', 'status' => 1, 'data' => $getDetails], 200);
            }else{
                return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array(), 'status' => 0], 500);
            }


        }
   }

   public function getDeletedMessageList(Request $request){
    
        $ids = [$request->case_id];
        
        $query= $this->textMessageService->getMessageListWithMultipleIds($ids);
        return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => $query], 200);
    }

    private function convertMergePatientArray($mergeIds,$currentId){
		$mergeData = ['merge_id'=>$mergeIds,'currentId'=>$currentId,'del_flag'=>"N"];
		return MergeUtilityHelper::convertData($mergeData);
	}
}
