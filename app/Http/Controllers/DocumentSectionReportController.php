<?php
namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\DocumentPatientService;
use App\Services\DocumentUploadService;
use App\Agency;
use App\Helpers\UserHelper;
use App\Helpers\Utility;
use App\Services\PatientService;
use Illuminate\Support\Facades\Storage;
use App\Services\LogsService;
use App\Services\DynamicFormLogService;
use App\Services\LocationMasterService;
use App\Services\SendEmailNotificationSerivce;
use Illuminate\Support\Facades\Mail;
use App\Services\UserWiseAgencyService;
use App\Services\AgencyWiseServiceService;
use Illuminate\Support\Facades\Cache;
use App\Master;
use App\Services\UserDocQuestionMarkedService;
use App\Services\UserSendPatientDocumentLogService;
use App\User;
class DocumentSectionReportController extends BaseController{
    protected $documentPatientService,$documentUploadService,$patientService,$dynamicFormLogService,$locationMasterService,$sendEmailNotificationSerivce,$userWiseAgencyService,$agencyWiseServiceService,$userDocQuestionMarkedService,$userSendPatientDocumentLogService="";

    public function __construct(PatientService $patientService,DocumentPatientService $documentPatientService,DocumentUploadService $documentUploadService,DynamicFormLogService $dynamicFormLogService,LocationMasterService $locationMasterService,SendEmailNotificationSerivce $sendEmailNotificationSerivce,UserWiseAgencyService $userWiseAgencyService, AgencyWiseServiceService $agencyWiseServiceService,UserDocQuestionMarkedService $userDocQuestionMarkedService, UserSendPatientDocumentLogService $userSendPatientDocumentLogService)
    {
        $this->documentPatientService = $documentPatientService;
        $this->documentUploadService = $documentUploadService;
        $this->patientService = $patientService;
        $this->dynamicFormLogService = $dynamicFormLogService;
        $this->locationMasterService = $locationMasterService;
        $this->sendEmailNotificationSerivce = $sendEmailNotificationSerivce;
        $this->userWiseAgencyService = $userWiseAgencyService;
        $this->agencyWiseServiceService = $agencyWiseServiceService;
        $this->userDocQuestionMarkedService = $userDocQuestionMarkedService;
        $this->userSendPatientDocumentLogService = $userSendPatientDocumentLogService;
    }

    public function index(Request $request){
        $data['auth'] = auth()->user();
        if(in_array(auth()->user()->id,Utility::agencyPortalRolePermission())){
            abort(404);
        }
        $data['agencyList'] = Agency::getAgencyListWithUserAgency()->toArray();
        $data['services'] = Agency::getAgencyListWithUserAgency()->toArray();
        $data['agencyCnt'] = count($data['agencyList']);
        $user = auth()->user();
        $data['masterData'] = Cache::get('master_mdo_source',function(){
           return Master::getAllDataByMasterTypeFk(array(35));
        });
        $data['serviceList'] = Cache::get('patient_master_services', function ()  use ($user) {
			$agencyId = $user->agency_fk;
            $getAgencyWiseList = $this->agencyWiseServiceService->ServiceListNewFlagListNyBestUser($agencyId);
			if (!empty($getAgencyWiseList[0])) {
				return  $getAgencyWiseList;
			} else {
				return  Master::getServiceRequest(1);
			}
		}, 10 * 60);
        return view('documentReport.index',$data);
    }

    public function ajaxList(Request $request){
        $data['page'] = $request->page;

        $patientIds = [];
        $response = $this->documentPatientService->getPatientDocumentList($request->all(),$patientIds);

       if (!empty($response[0])) {
            foreach ($response as $val) {
                $val->patientDetails =$this->patientService->searchForOnlyDocumentReportById($val->patient_id);
            }
        }

        $data['document_list'] = $response;

        // Build a map of document_id => latest send-back-to-agency log
        $docIds = $response->pluck('id')->toArray();
        $data['sendMailLogs'] = $this->userSendPatientDocumentLogService->getLatestSendBackByDocIDs($docIds);

        return view('documentReport.ajax_list',$data);
    }

    public function exportCsvNWithoutServices(Request $request){

        $response = $this->documentPatientService->getDocumentsIds($request->all());
        $docIds = [];
        if(!empty($response[0])){
            foreach($response as $doc){
                $docIds[] = $doc->id;
            }
        }
        $documentDetails=[];
        $responseData = $this->documentUploadService->getDocumentListByDocumentIdAllDataIds($docIds);

        foreach($response as $val){
            if(in_array($val->id,$responseData->toArray())){
            }else{
                $documentDetails[] = $val;
            }
        }

        $response = $documentDetails;

        $filename = 'Patient' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);
        $columns = array('Portal ID', 'Agency Name','Type','Patient Name', 'Document Name', 'Attachment Service','Requested Id','Document Completion Date','Created Date', 'Created By','Assign Document User','Document Review Date','Document Review By','Internal Use Only');

       
		$callback = function () use ($response, $columns) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			$cnt = 1;
			foreach ($response as $list) {
                $serviceName = "";
                if(isset($final[$list->id])){
                    $serviceName = implode(',',$final[$list->id]);
                }
                $documentCompletedDate = "";
                if(isset($list->document_completed_date) !=NULL){
                    $documentCompletedDate = date('m/d/Y',strtotime($list->document_completed_date));
                }

                $assignUser ="";
                if(isset($list->assignUserReviewDocument->id)){
                    $assignUser = $list->assignUserReviewDocument->first_name.' '.$list->assignUserReviewDocument->last_name;
                }

                $reviewUser ="";
                if(isset($list->reviewUserDetails->id)){
                    $reviewUser = $list->reviewUserDetails->first_name.' '.$list->reviewUserDetails->last_name;
                }

                $document_review_date ="";
                if(isset($list->document_review_date) && $list->document_review_date !=NULL){
                    $document_review_date = date('m/d/Y h:i A',strtotime($list->document_review_date));
                }

                $internal_use ="";
                if($list->internal_use ==1){
                    $internal_use = "Internal Use Only";
                }

                fputcsv($file, array($list->patientDetails->id, $list->patientDetails->agencyDetail->agency_name,$list->patientDetails->type,$list->patientDetails->first_name.' '.$list->patientDetails->last_name,  $list->document_name, $serviceName,$list->request_service_id,$documentCompletedDate, date('m/d/Y h:i A',strtotime($list->created_date)),$list->userDetails->first_name.' '.$list->userDetails->last_name,$assignUser,$document_review_date,$reviewUser,$internal_use));
                }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function showAWSServiceLink(Request $request){
        $getDetails = $this->documentPatientService->getDetailsById($request->id);
        $url = "";
		if (isset($getDetails->patient_id)) {
			$getPatientDetails = $this->patientService->getDetailByIdNewAll($getDetails->patient_id);

			if (isset($getPatientDetails->agency_id)) {
				$file = public_path('/') . "/patientdocument/" . $getDetails->attachment;
				$headers = [];
				if (str_contains($getDetails->attachment, 'patientdocument')) {
					if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
						$url = url('patientdocument/' . $getDetails->attachment);
					} else {
						$url = Storage::disk('s3')->temporaryUrl($getDetails->attachment, now()->addMinutes(60));
					}
				} else {
					if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
						$url = url('patientdocument/' . $getDetails->attachment);
					} else {
						$url = Storage::disk('s3')->temporaryUrl('patientdocument/' . $getDetails->attachment, now()->addMinutes(60));
					}
				}
			}
		}

        return response()->json(['error_msg' => "Success", 'status' => 0, 'data' => array('url'=>$url)], 200);
    }

    function documentReview(Request $request){
        $query = $this->documentPatientService->getDocumentDetailsByIdOrPatientId($request->id);
        $query->services = '';
        if(isset($query->id)){
            $services = [];
            $serviceDetails = $this->documentUploadService->getDocumentListByDocumentId($query->id);
            if(count($serviceDetails) >0){
                foreach($serviceDetails as $srv){
                    $services[] = $srv->masterDetails->name;
                }
                $query->services  = implode(',',$services);
            }

            $file = public_path('/') . "/patientdocument/" . $query->attachment;
            $headers = [];
            if (str_contains($query->attachment, 'patientdocument')) {
                if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
                    $url = url('patientdocument/' . $query->attachment);
                } else {
                    $url = Storage::disk('s3')->temporaryUrl($query->attachment, now()->addMinutes(60));
                }
            } else {
                if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
                    $url = url('patientdocument/' . $query->attachment);
                } else {
                    $url = Storage::disk('s3')->temporaryUrl('patientdocument/' . $query->attachment, now()->addMinutes(60));
                }
            }

            $query->attachment  = $url;

            $ipaddress = Utility::getIP();
            $user = auth()->user();
            $insertLog = [
                'type' => 'View Document',
                'link' => url('document-review-by-id'),
                'module' => 'Patient Appointment',
                'object_id' => $query->patient_id,
                'message' => 'Document visited by '.$user->first_name.' '.$user->last_name ,
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
        }
        return response()->json(['error_msg' => "Success", 'status' => 0, 'data' =>$query], 200);

    }

    public function updateDocumentReview(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'document_id' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {
            $getDocumentDetails = $this->documentPatientService->getDetailsById($request->document_id);

            $status = "Rejected";
            if($request->status =='1'){
                $status = "Approved";
            }
            $update  = $this->documentPatientService->update(array('document_review_status'=>$status,'document_review_date'=>date('Y-m-d H:i:s'),'document_review_by'=>$user->id,'status_note'=>$request->note),array('id'=>$request->document_id));
            $newDocumentDetails = $this->documentPatientService->getDetailsById($request->document_id);
            $ipaddress =Utility::getIP();
            $insertLog = [
                'type' => 'Document Review',
                'link' =>  url('update-document-review'),
                'module' => 'Patient Appointment',
                'object_id' => $getDocumentDetails->patient_id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has updated the document review',

                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            $insertLog = [
                'type' => 'Change Document Status',
                'link' => url('update-document-review'),
                'module' => 'Document Section',
                'module_id' => $request->document_id,
                'new_response' => serialize($newDocumentDetails->toArray()),
                'old_response' => serialize($getDocumentDetails->toArray()),
                'is_status'=>'Change Document'
            ];
            $this->dynamicFormLogService->storeFormLog($insertLog);

            if(isset($request->questions)  && count($request->questions) > 0){
                foreach($request->questions as $que){
                    $docsData = array(
                        'user_id' => auth()->user()->id,
                        'question_id' => $que
                    );
                    $this->userDocQuestionMarkedService->save($docsData);
                }
            }

            $this->sendEmailNotificaitonNew($getDocumentDetails->patient_id, $request->document_id,"", $newDocumentDetails->document_name,$newDocumentDetails->internal_use,$request->status,$request->note);
            if($request->status ==1){
                $this->sendEmailNotificaiton($getDocumentDetails->patient_id,$getDocumentDetails->attachment,"", $newDocumentDetails->document_name,$newDocumentDetails->internal_use);
            }

            return response()->json(['error_msg' =>"Document review has been successfully ".$status, 'status' => 1, 'data' => array()], 200);
        }
    }

    private function sendEmailNotificaiton($id, $image, $name, $documentName,$internal_use,$doc_created_by="")
	{

		$authUser = auth()->user();
		$getNewResponse = $this->patientService->getDetailById($id);
		if (isset($getNewResponse->agency_id) && $getNewResponse->agency_id != '') {

			$query = Agency::getAllDetailsbyAgencyId($getNewResponse->agency_id);
			$emails = isset($query->nybest_email_notification) ? $query->nybest_email_notification : "";

			$emailss = explode(',', $emails);
			$allemails = array();
			$allemails[] =$authUser->email;
			foreach ($emailss as $ems) {
				if (trim($ems) != '') {
					if (trim($ems) != 'li@qualityny.com') {
						$allemails[] = trim($ems);
					}
				}
			}
			$location_list = $this->locationMasterService->getDetailbyId($getNewResponse->location_id);
			$address1 = isset($location_list->address1) ? $location_list->address1 : "";
			$address2 = isset($location_list->address2) ? $location_list->address2 : "";
			$city = isset($location_list->city) ? $location_list->city : "";
			$state = isset($location_list->state) ? $location_list->state : "";
			$zip_code = isset($location_list->zip_code) ? $location_list->zip_code : "";

			$localdetails = $address1 . ' ' . $address2 . ' ' . $city . ' ' . $state . ' ' . $zip_code;
			$name = isset($query->agency_name) ? $query->agency_name : "";
			$discipline =  $getNewResponse->diciplin;

			$subject = 'Document Approved';

			$emailData = array(
				'agencyname' => $name,
				'insert' => $id,
				'type' => $getNewResponse->type,
				'first_name' => $getNewResponse->first_name,
				'last_name' => $getNewResponse->last_name,
				'location' => $localdetails,
				'discipline' => $discipline,
				'document_name' => $documentName,
			);
			$messages = Utility::getHtmlContent('email_template.document_upload_patient', $emailData);

			/*Rohit Panchal code*/
			$notificationType = "Document Upload";
			$sendEmailNotication = [];
			if ($getNewResponse->type != "" && $getNewResponse->agency_id != "") {
				if($internal_use ==0){
					$sendEmailNotication = $this->sendEmailNotificationSerivce->sendEmailNotificationServicesDiscipline($getNewResponse->type, $notificationType, $getNewResponse->agency_id, $subject, $messages, $name, $image, $id);
				}
			}
			/*******Send Mail Notification for general user */
			$generalEmail = $this->sendEmailNotificationSerivce->sendGeneralNotificationWithEmail($getNewResponse->type, $notificationType, $subject, $messages,  $name, $image, $id);

			/*************************End Send Mail Notification for general user */
			$sendUserEmailNotication = [];
			if ($getNewResponse->type != "" && $getNewResponse->created_by != "") {
				$sendUserEmailNotication = $this->sendEmailNotificationSerivce->sendEmailNotificationUserWithEmail($getNewResponse->type, $notificationType, $getNewResponse->created_by, $subject, $messages, $name, $image);
			}
            if(isset($doc_created_by) && !empty($doc_created_by)){
                $userMail = UserHelper::getUserDetails($doc_created_by);
                $allemails[] = $userMail->email;
            }
			$finalArray = array_unique(array_merge($allemails,$sendEmailNotication,$generalEmail,$sendUserEmailNotication));
			if (!empty($finalArray[0])) {
				try {
					$this->sendEmailNotificationSerivce->UserMailWithMultipleEmail($finalArray,$image,$subject,$messages,$documentName);
				} catch (\Throwable $th) {
					//throw $th;
				}

			}

		}
	}

    public function pendingDocumentReport(Request $request){
        $data['auth'] = auth()->user();
        $data['agencyList'] = Agency::getAllAgencyListWithoutAnyCondition();
        return view('documentReport.pending_document_list',$data);
    }

    public function pendingDocumentAjaxList(Request $request){
        $data['page'] = $request->page;
        $getDocumentServiceList = $this->documentUploadService->getAllDocumentList();
        $final = [];

        if(count($getDocumentServiceList) >0){
            foreach($getDocumentServiceList as $doc){
                $temp = [];
                if(isset($temp[$doc->document_id])){
                    $temp[$doc->document_id][] = $doc->masterDetails;
                }else{
                    if(isset($doc->masterDetails->name)){
                        $temp[$doc->document_id] = [];

                        $temp[$doc->document_id] = $doc->masterDetails;
                    }

                }

                if(isset($temp[$doc->document_id])){
                    $final[$doc->document_id][] = $temp[$doc->document_id];
                }

            }
        }

       $response = $this->documentPatientService->getPatientPendingDocumentList($request->all());

       if (!empty($response[0])) {
            foreach ($response as $val) {
                $serviceArray = [];

                if(isset($final[$val->id])){
                    $serviceArray = $final[$val->id];
                }
                $val->services = $serviceArray;
            }
        }

        $data['document_list'] = $response;

        return view('documentReport.pending_ajax_list',$data);
    }

    public function getServicesOfDocs(Request $request){
        $doc_id = $request->document_id;
        $serviceArray = [];
        $services = $this->documentUploadService->getDocumentListByDocumentId($doc_id);
        if (!empty($services[0])) {
            foreach ($services as $ser) {
                if (isset($ser->masterDetails) && $ser->masterDetails != "") {
                    $serviceArray[] = $ser->masterDetails;
                }
            }
        }
        return response()->json(['error_msg' =>"", 'status' => 1, 'data' => $serviceArray], 200);
    }

    public function exportCsv(Request $request){
        //ini_set("memory_limit","4096M")
        $patientIds = [];
        $response = $this->documentPatientService->getPatientDocumentList($request->all(),$patientIds,'export');
        if(!empty($response) && count($response) > 0){
            $filename = 'Patient' . date("m-d-Y");
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0",
            );
            if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6){
                $columns = array('Portal ID', 'Agency Name','Type','Patient Name', 'Document Name','Requested Id','Document Completion Date','Status','Created Date', 'Created By','Modified Date','Modified By','Assign Document User','Document Review Date','Document Review By','Internal Use Only','Send Back To Agency','Send Back Date','Send Back By','Send Back Note');
            }else{
                $columns = array('Portal ID', 'Agency Name','Type','Patient Name', 'Document Name','Requested Id','Document Completion Date','Created Date', 'Created By','Modified Date','Modified By','Send Back To Agency','Send Back Date','Send Back By','Send Back Note');
            }

            $docIds = $response->pluck('id')->toArray();
            $sendMailLogs = $this->userSendPatientDocumentLogService->getLatestSendBackByDocIDs($docIds);

            $callback = function () use ($response, $columns, $sendMailLogs) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($response as $list) {
                    $documentCompletedDate = "";
                    if(isset($list->document_completed_date) !=NULL){
                        $documentCompletedDate = date('m/d/Y',strtotime($list->document_completed_date));
                    }

                    $assignUser = "";
                    if(isset($list->assignUserReviewDocument->first_name)  && isset($list->assignUserReviewDocument->last_name)){
                        $assignUser = $list->assignUserReviewDocument->first_name.' '.$list->assignUserReviewDocument->last_name;
                    }
                    $reviewUser = "";
                    if(isset($list->reviewUserDetails->first_name)  && isset($list->reviewUserDetails->last_name)){
                        $reviewUser = $list->reviewUserDetails->first_name.' '.$list->reviewUserDetails->last_name;
                    }

                    $document_review_date ="";
                    if(isset($list->document_review_date) && $list->document_review_date !=NULL){
                        $document_review_date = date('m/d/Y h:i A',strtotime($list->document_review_date));
                    }

                    $internal_use ="";
                    if($list->internal_use ==1 ){
                        $internal_use = "Internal Use Only";
                    }

                    $status ="";
                    if($list->document_review_status =="Approved"){
                        $status = "Approved";
                    }else if($list->document_review_status == "Rejected"){
                        $status = "Rejected";
                    }else{
                        $status = "Pending";
                    }

                    $uFname = $list->userDetails->first_name??'';
                    $uLname = $list->userDetails->last_name??'' ;
                    $agencyName = $list->patientDetails->agencyDetail->agency_name??'';
                    $type = $list->patientDetails->type;
                    $patient_name = "";
                    if(isset($list->patientDetails->first_name) && $list->patientDetails->last_name){
                        $patient_name = $list->patientDetails->first_name.' '.$list->patientDetails->last_name;
                    }
                    $updatedDate = "";
                    if(isset($list->updated_date) && $list->updated_date != NULL){
                        $updatedDate = Utility::convertMDYTime($list->updated_date);
                    }
                    $updatedBy = trim(($list->updatedUserDetails->first_name??'').' '.($list->updatedUserDetails->last_name??''));

                    $sendBackAddr = $sendBackDate = $sendBackBy = $sendBackNote = "";
                    if(isset($sendMailLogs[$list->id])){
                        $log = $sendMailLogs[$list->id];
                        $emails = json_decode($log->email, true);
                        $sendBackAddr = is_array($emails) ? implode(', ', $emails) : $log->email;
                        $sendBackDate = date('m/d/Y h:i A', strtotime($log->created_date));
                        $sendBackBy   = trim($log->first_name.' '.$log->last_name);
                        $sendBackNote = $log->note ?? '';
                    }

                    if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6){
                        fputcsv($file, array($list->patient_id, $agencyName,$type,$patient_name,  $list->document_name??'',$list->request_service_id??'',$documentCompletedDate,$status, Utility::convertMDYTime($list->created_date),$uFname.' '.$uLname,$updatedDate,$updatedBy,$assignUser,$document_review_date,$reviewUser,$internal_use,$sendBackAddr,$sendBackDate,$sendBackBy,$sendBackNote));
                    }else{
                        fputcsv($file, array($list->patient_id, $agencyName,$type,$patient_name,  $list->document_name??'',$list->request_service_id??'',$documentCompletedDate, Utility::convertMDYTime($list->created_date),$uFname.' '.$uLname,$updatedDate,$updatedBy,$sendBackAddr,$sendBackDate,$sendBackBy,$sendBackNote));
                    }
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        } else{
            return null;
        }
    }

    public function saveDocName(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'document_id' => 'required',
            'document_name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {
            $getDocumentDetails = $this->documentPatientService->getDetailsById($request->document_id);
            $document_name = $request->document_name;
            $this->documentPatientService->update(array('document_name'=>$document_name),array('id'=>$request->document_id));
            $newDocumentDetails = $this->documentPatientService->getDetailsById($request->document_id);
            $ipaddress =Utility::getIP();
            $insertLog = [
                'type' => 'Change Document Name',
                'link' =>  url('update-document-name'),
                'module' => 'Patient Appointment',
                'object_id' => $getDocumentDetails->patient_id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has updated the document name',
                'ip' => $ipaddress,
                'new_response' => serialize($newDocumentDetails->toArray()),
                'old_response' => serialize($getDocumentDetails->toArray()),
            ];
            LogsService::save($insertLog);

            $insertLog = [
                'type' => 'Change Document Name',
                'link' => url('update-document-name'),
                'module' => 'Document Section',
                'module_id' => $request->document_id,
                'new_response' => serialize($newDocumentDetails->toArray()),
                'old_response' => serialize($getDocumentDetails->toArray()),
                'is_status'=>'Change Document'
            ];
            $this->dynamicFormLogService->storeFormLog($insertLog);
            return response()->json(['error_msg' =>"Document name has been updated successfully.", 'status' => 1, 'data' => array()], 200);
        }
    }

    private function sendEmailNotificaitonNew($id, $docId, $name, $documentName,$internal_use,$status,$reason)
    {
        $authUser = auth()->user();
        $getNewResponse = $this->patientService->getDetailById($id);

        if (isset($getNewResponse->agency_id) && $getNewResponse->agency_id != '') {
            $getDocumentDetails = $this->documentPatientService->getDetailsById($docId);
            if($status ==1){
                $subject = 'Document Approved # '.$id;
            }else{
                $subject = 'Document Rejected # '.$id;
            }

            $getDetails = User::select('first_name','last_name','email')->where('id',$getDocumentDetails->created_by)->first();

            $fullName = "";
            $email = "";
            if(isset($getDetails->first_name)){
                $fullName = $getDetails->first_name.' '.$getDetails->last_name;
                $email = $getDetails->email;
            }
            $emailData = array(
                'agencyname'=>$fullName,
                'insert' => $id,
                'type' => $getNewResponse->type,
                'first_name' => $getNewResponse->first_name,
                'last_name' => $getNewResponse->last_name,
                'document_name' => $documentName,
                'status'=>$status,'reason'=>$reason
            );
            $messages = Utility::getHtmlContent('email_template.document_approved_patient', $emailData);

            $assignAgencyMail = $this->sendEmailNotificationSerivce->getAssignNyUserAgencyMail($getNewResponse->agency_id);
            $finalArray = array_unique(array_merge(array($email),$assignAgencyMail));
            if (!empty($finalArray[0])) {
                try {
                    $this->sendEmailNotificationSerivce->UserMailWithMultipleEmail($finalArray,"",$subject,$messages,$documentName);
                } catch (\Throwable $th) {
                    //throw $th;
                }

            }

        }
    }

    public function updateDocumentReviewInternal(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'document_id' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        } else {
            $getDocumentDetails = $this->documentPatientService->getDetailsById($request->document_id);

            $status = "Rejected";
            $internal_use = 0;
            if(isset($request->internal_use_only) && !empty($request->internal_use_only) && $request->status == 0){
                $internal_use = $request->internal_use_only;
            }
            if($request->status =='1'){
                $status = "Approved";
                $internal_use = 0;
            }
            $update  = $this->documentPatientService->update(array('document_review_status'=>$status,'document_review_date'=>date('Y-m-d H:i:s'),'document_review_by'=>$user->id,'status_note'=>$request->note,'internal_use' => $internal_use),array('id'=>$request->document_id));
            $newDocumentDetails = $this->documentPatientService->getDetailsById($request->document_id);
            $ipaddress =Utility::getIP();
            $insertLog = [
                'type' => 'Document '.$status,
                'link' =>  url('update-document-review'),
                'module' => 'Patient Appointment',
                'object_id' => $getDocumentDetails->patient_id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has updated the document review',

                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);

            $insertLog = [
                'type' => 'Change Document Status',
                'link' => url('update-document-review'),
                'module' => 'Document Section',
                'module_id' => $request->document_id,
                'new_response' => serialize($newDocumentDetails->toArray()),
                'old_response' => serialize($getDocumentDetails->toArray()),
                'is_status'=>'Change Document'
            ];
            $this->dynamicFormLogService->storeFormLog($insertLog);

            $this->sendEmailNotificaitonNew($getDocumentDetails->patient_id, $request->document_id,"", $newDocumentDetails->document_name,$newDocumentDetails->internal_use,$request->status,$request->note);
            if($request->status ==1){
                $this->sendEmailNotificaiton($getDocumentDetails->patient_id,$getDocumentDetails->attachment,"", $newDocumentDetails->document_name,$newDocumentDetails->internal_use);
            }
             // Send User notifictaion
             $getDocumentServiceList = $this->documentUploadService->getAllDocumentListId($request->document_id)->pluck('service_id')->toArray();
             $doc_data = array(
                 'doc_name' => $newDocumentDetails->document_name,
                 'doc_id' => $request->document_id,
                 'services' => $getDocumentServiceList
             );
             $this->sendNotificationToUser($getDocumentDetails->patient_id,$request->document_id,$request->status,$doc_data);
            return response()->json(['error_msg' =>"Document review has been successfully ".$status, 'status' => 1, 'data' => array()], 200);
        }
    }

    public function sendNotificationToUser($patientId,$docId,$status,$data)
	{
		$user = auth()->user();
		$getNewResponse = $this->patientService->getDetailById($patientId);
        $title = "";
        $createdByIds = [];
        if (isset($getNewResponse->agency_id) && $getNewResponse->agency_id != '') {
            $getDocumentDetails = $this->documentPatientService->getDetailsById($docId);
            if($status ==1){
                $title = 'Document Approved # '.$patientId;
            }else{
                $title = 'Document Rejected # '.$patientId;
            }
        }

        $createdByIds[] = $getDocumentDetails->created_by;
		$agency_fk = $getNewResponse->agency_id;
		$msg = "";
        $type = "Document";
		$serviceData = array();
        $msg = $data['doc_name'];
        $serviceData = $data['services'];
		// Get Group wise notification
		$userData = Utility::getGroupUsersData($agency_fk, $getNewResponse->type, $type, $createdByIds, $serviceData);
		$notificationData = array(
			'users' => $createdByIds,
			'agency_fk' => $agency_fk,
			'record_id' => $patientId,
			'title' => $title,
			'msg' => $msg,
			'type' => $type
		);
		Utility::insertNotificationsType($notificationData);
	}

    public function getInternalUseData(Request $request){
        $patient_id = $request->patient_id;
        $internalUseDocData = $this->documentPatientService->getInternalUseDocData($patient_id);
        return response()->json(['error_msg' =>"", 'status' => 1, 'data' => $internalUseDocData], 200);
    }

    public function exportCsvTwo(Request $request){
        //ini_set("memory_limit","4096M")
        $patientIds = [];
        $response = $this->documentPatientService->getPatientDocumentList($request->all(),$patientIds,'export');

        if(!empty($response) && count($response) > 0){
            $filename = 'Patient' . date("m-d-Y");
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0",
            );
            if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6){
                $columns = array('Document ID','Portal ID', 'Agency Name','Type','First Name','Last Name','Date Of Birth','Social Security','CIN','Medicare No','Document Name','Document Completion Date','Status','Modified Date','Modified By','Internal Use Only','Send Back To Agency','Send Back Date','Send Back By','Send Back Note');
            }else{
                $columns = array('Portal ID', 'Agency Name','Type','First Name','Last Name','Date Of Birth','Social Security','CIN','Medicare No','Document Name','Document Completion Date','Status','Modified Date','Modified By');
            }

            $docIds = $response->pluck('id')->toArray();
            $sendMailLogs = $this->userSendPatientDocumentLogService->getLatestSendBackByDocIDs($docIds);

            $callback = function () use ($response, $columns, $sendMailLogs) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($response as $list) {

                    $documentCompletedDate = "";
                    if(isset($list->document_completed_date) !=NULL){
                        $documentCompletedDate = date('m/d/Y',strtotime($list->document_completed_date));
                    }

                    $internal_use ="";
                    if($list->internal_use ==1){
                        $internal_use = "Internal Use Only";
                    }

                    $status ="";
                    if($list->document_review_status =="Approved"){
                        $status = "Approved";
                    }else if($list->document_review_status == "Rejected"){
                        $status = "Rejected";
                    }else{
                        $status = "Pending";
                    }

                    $agencyName = $list->patientDetails->agencyDetail->agency_name??'';
                    $type = $list->patientDetails->type;
                    $patient_name="";
                    if(isset($list->patientDetails->first_name) && $list->patientDetails->first_name){
                        $patient_name = $list->patientDetails->first_name;
                    }
                    $last_name="";
                    if(isset($list->patientDetails->last_name) && $list->patientDetails->last_name !=""){
                        $last_name = $list->patientDetails->last_name;
                    }
$dob="";
                    if(isset($list->patientDetails->dob) && $list->patientDetails->dob !=""){
                        $dob = date('m/d/Y',strtotime($list->patientDetails->dob));
                    }
$cin="";
                    if(isset($list->patientDetails->cin) && $list->patientDetails->cin !=""){
                        $cin = $list->patientDetails->cin;
                    }
$ssn="";
                    if(isset($list->patientDetails->ssn) && $list->patientDetails->ssn !=""){
                        $ssn = $list->patientDetails->ssn;
                    }
$medicare_no="";
                    if(isset($list->patientDetails->medicare_no) && $list->patientDetails->medicare_no !=""){
                        $medicare_no = $list->patientDetails->medicare_no;
                    }

                    $updatedDate = "";
                    if(isset($list->updated_date) && $list->updated_date != NULL){
                        $updatedDate = Utility::convertMDYTime($list->updated_date);
                    }
                    $updatedBy = trim(($list->updatedUserDetails->first_name??'').' '.($list->updatedUserDetails->last_name??''));

                    $sendBackAddr = $sendBackDate = $sendBackBy = $sendBackNote = "";
                    if(isset($sendMailLogs[$list->id])){
                        $log = $sendMailLogs[$list->id];
                        $emails = json_decode($log->email, true);
                        $sendBackAddr = is_array($emails) ? implode(', ', $emails) : $log->email;
                        $sendBackDate = Utility::convertMDYTime($log->created_date);
                        $sendBackBy   = trim($log->first_name.' '.$log->last_name);
                        $sendBackNote = $log->note ?? '';
                    }

                    if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6){
                        fputcsv($file, array($list->id,$list->patient_id, $agencyName,$type,$patient_name, $last_name,$dob,$ssn,$cin,$medicare_no, $list->document_name??'',$documentCompletedDate,$status,$updatedDate,$updatedBy,$internal_use,$sendBackAddr,$sendBackDate,$sendBackBy,$sendBackNote));
                    }else{
                        fputcsv($file, array($list->patient_id, $agencyName,$type,$patient_name, $last_name,$dob,$ssn,$cin,$medicare_no, $list->document_name??'',$documentCompletedDate,$status,$updatedDate,$updatedBy));
                    }
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        } else{
            return null;
        }
    }
}