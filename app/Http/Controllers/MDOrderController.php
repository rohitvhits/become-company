<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Services\MDOrderService;
use Illuminate\Http\Request;
use App\Services\DocumentPatientService;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Utility;
use App\Services\LogsService;
use App\Services\PatientService;
use App\Helpers\ThirdPartyWebHookHelper;
use App\Model\ThirdPartyWebhookLog;
use App\Services\SendEmailNotificationSerivce;
class MDOrderController extends BaseController
{
    protected $mdOrderService,$documentPatientService,$PatientService,$sendEmailNotificationSerivce="";
    public function __construct(MDOrderService $mdOrderService,DocumentPatientService $documentPatientService,PatientService $PatientService,SendEmailNotificationSerivce $sendEmailNotificationSerivce)
    {
        $this->middleware('auth');
        $this->mdOrderService = $mdOrderService;
        $this->documentPatientService = $documentPatientService;
        $this->PatientService = $PatientService;
        $this->sendEmailNotificationSerivce = $sendEmailNotificationSerivce;
    }

    public function patientMDOrderList(Request $request){
       $data['query'] = $this->mdOrderService->patientMDOrderList($request->all());
       
       return view('patient._partial.md_orders.patient_md_order_ajax_list',$data);
    }

    public function mdOrderDocumentList(Request $request){
        $response = $this->documentPatientService->getDocumentListByPatientId($request->id);
        return response()->json(['status' => true, 'error_msg' => '', 'data' => $response]);
    }

    public  function saveMdOrder(Request $request){
        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
			'mq_order_start_date' => 'required',
			'mq_order_end_date' => 'required',
		]);
		if ($validator->fails()) {
	
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
            if(isset($request->patient_mq_order_document_id) && !empty($request->patient_mq_order_document_id)){
                $doc_id = $request->patient_mq_order_document_id;
            }else{
                $doc_id = $request->mq_order_document_id;
            }
            $data = [
                'patient_id'=>$request->patient_id,
                'start_date'=>Utility::convertYMD($request->mq_order_start_date),
                'end_date'=>Utility::convertYMD($request->mq_order_end_date),
                'document_id'=>$doc_id,
            ];
            $save = $this->mdOrderService->save($data);
            if($save){
                $ipaddress = Utility::getIP();
                $insertLog = [
					'type' => 'Add MD Order',
					'link' => url('/save-patient-mq-order'),
					'module' => 'Patient Appointment',
					'object_id' => $request->patient_id,
					'message' => 'MD Order created',
                    'ip' => $ipaddress,
					'new_response' => serialize($request->all()),
				];
				LogsService::save($insertLog);	

                $getExistingRecord = $this->PatientService->getDetailByIdNew($request->patient_id);
                $newDocument  = $this->documentPatientService->getDetailsById($doc_id);
				if($getExistingRecord->platform_type =='dssdsd' || $getExistingRecord->platform_type =='ITPTEK'){
					if($getExistingRecord->link_third_party !=""){
						// $data=['id'=>$getExistingRecord->link_third_party,'first_name'=>$getExistingRecord->first_name,'last_name'=>$getExistingRecord->last_name,'document_id'=>$doc_id,'document_name'=>$newDocument->document_name,'start_date'=>Utility::convertYMD($request->mq_order_start_date),'end_date'=>Utility::convertYMD($request->mq_order_end_date)];
                        $data=['id'=>$getExistingRecord->link_third_party,'first_name'=>$getExistingRecord->first_name,'middle_name'=>$getExistingRecord->middle_name,'last_name'=>$getExistingRecord->last_name,'type'=>$getExistingRecord->type,'dob'=>$getExistingRecord->dob,'fu_date'=>$getExistingRecord->fu_date,'due_date'=>$getExistingRecord->due_date,'phone'=>$getExistingRecord->phone,'mobile'=>$getExistingRecord->mobile,'gender'=>$getExistingRecord->gender,'message'=>$getExistingRecord->remarks,'service_id'=>$getExistingRecord->service_id,'patient_code'=>$getExistingRecord->patient_code,'diciplin'=>$getExistingRecord->diciplin,'language'=>$getExistingRecord->language,'address1'=>$getExistingRecord->address1,'address2'=>$getExistingRecord->address2,'state'=>$getExistingRecord->state,'city'=>$getExistingRecord->city,'zipcode'=>$getExistingRecord->zip_code,'country'=>$getExistingRecord->county,'platform_type'=>$getExistingRecord->platform_type,'partner_agency'=>$getExistingRecord->partner_agency,'document_id'=>$doc_id,'document_name'=>$newDocument->document_name,'start_date'=>Utility::convertYMD($request->mq_order_start_date),'end_date'=>Utility::convertYMD($request->mq_order_end_date)];
						$webhook = ['status'=>'MD Order Created',"data"=>$data];
				        info($webhook);
                        
						$response = ThirdPartyWebHookHelper::sendWebHook($webhook);
                        $json = json_decode($response,true);
                   
                        if(isset($json['status']) && $json['status'] =='success'){
                            $createdBy = null;
                            if(isset($auth->id)){
                                $createdBy = $auth->id;
                            }
                            $saveResponse = [
                                'third_party_id'=>$getExistingRecord->link_third_party,
                                'send_response'=>serialize($data),
                                'return_response'=>serialize(array($json['data']['data'])),
                                'created_date'=>date('Y-m-d H:i:s'),
                                'created_by'=>$createdBy
                            ];
            
                            $saveData = new ThirdPartyWebhookLog($saveResponse);
                            $saveData->save();
                            $data['username'] = $auth->first_name.' '.$auth->last_name;
                            $messages = Utility::getHtmlContent('email_template.md_orders_created_template', $data);
                          
                            $finalArray = ['Rakesh@itptek.com','developer@nybestmedical.com','Pinak@nybestmedical.com'];
                            $subject = "MD Order Created";
                            $this->sendEmailNotificationSerivce->UserMailWithMultipleEmail($finalArray,"",$subject,$messages,"");
    
                        }
                        
					}
				}
                $this->sendAutoMailToMDO($getExistingRecord,$newDocument,$request->mq_order_start_date,$request->mq_order_end_date);
                return response()->json(['error_msg' => "MD Order successfully created", 'status' => 1, 'data' => array(array('appoinment_id' => $save))], 200);
            }else{
                return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => array()], 500);
            }
        }
    }

    public function edit(Request $request){
        $query = $this->mdOrderService->getDetailsById($request->id);
        return response()->json(['error_msg' => "MD Order successfully created", 'status' => 1, 'data' => $query], 200);
    }

    public  function updateMDOrder(Request $request){
        $validator = Validator::make($request->all(), [
			'mq_order_start_date' => 'required',
			'mq_order_end_date' => 'required',
		]);
		if ($validator->fails()) {
	
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
            $oldResponse = $this->mdOrderService->getDetailsById($request->id);
            $data = [
               
                'start_date'=>Utility::convertYMD($request->mq_order_start_date),
                'end_date'=>Utility::convertYMD($request->mq_order_end_date),
                'document_id'=>$request->mq_order_document_id,
            ];
            $save = $this->mdOrderService->update($data,array('id'=>$request->id));
            if($save){
                $newResponse = $this->mdOrderService->getDetailsById($request->id);
                $ipaddress = Utility::getIP();
                $insertLog = [
					'type' => 'Update MD Order',
					'link' => url('/update-patient-md-order'),
					'module' => 'Patient Appointment',
					'object_id' => $request->patient_id,
					'message' => 'MD Order update',
					'old_response' => serialize($oldResponse->toArray()),
					'new_response' => serialize($newResponse->toArray()),
                    'ip' => $ipaddress,
				];
				LogsService::save($insertLog);	
                return response()->json(['error_msg' => "MD Order successfully updated", 'status' => 1, 'data' => array(array('appoinment_id' => $save))], 200);
            }else{
                return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => array()], 500);
            }
        }
    }

    public function deleteMDOrder(Request $request){
        $insert = $this->mdOrderService->SoftDelete(array('del_flag' => 'Y'), array('id' => $request->id));
		if ($insert) {
			$ipaddress = Utility::getIP();
			$user = auth()->user();
			$insertLog = [
				'type' => 'Delete MD Order',
				'link' => url('/delete-patient-md-order'),
				'module' => 'Patient Appointment',
				'object_id' => $request->patient_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Delete MD Order',
				'new_response' => serialize(array('deleted_flag' => 'Y')),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
            return response()->json(['error_msg' => "MD Order successfully deleted", 'status' => 1, 'data' => array()], 200);
		
		} else {
            return response()->json(['error_msg' => "Sorry, something went wrong. Please try again", 'status' => 1, 'data' => array()], 500);

		}
    }

    public function sendAutoMailToMDO($patientData,$newDocument,$mq_order_start_date,$mq_order_end_date){
        $mailData= ['id' => $patientData->id,'first_name'=>$patientData->first_name,'last_name'=>$patientData->last_name,'document_name'=>$newDocument->document_name,'start_date'=>Utility::convertYMD($mq_order_start_date),'end_date'=>Utility::convertYMD($mq_order_end_date),'username' => auth()->user()->first_name.' '.auth()->user()->last_name];
        $subject = "MD Order Created";
        $messages = Utility::getHtmlContent('email_template.md_orders_created_template', $mailData);
        $documentName = $newDocument->document_name;
        $this->sendEmailNotificationSerivce->sendAgencyNotificationMailWithType($patientData->type, 'MDO Auto Notifications', $patientData->agency_id, $service_id=array(), "",$subject,$messages, $documentName);
    }
}