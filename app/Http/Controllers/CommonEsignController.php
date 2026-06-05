<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\URL;
use App\Services\DocumentSendService;
use App\Services\TemplateService;
use App\Services\DocumentSignerService;
use App\Services\DocusignDetailService;
use App\Services\CommonEsignService;
use App\Services\SignatureUploadService;

use App\Helpers\EsignHelper;

use	App\Services\DocumentSendSmsLogService;
use App\Services\SMSTemplateService;
use App\User;
use App\Services\PatientService;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Common;
use Illuminate\Support\Facades\Storage;
use App\Helpers\AgencyAllForm;
use App\Model\ApproveStamp;
use App\Model\FieldMaster;
use App\Services\DynamicFormLogService;
use App\Services\DoctorService;
use App\Services\StampService;
use App\Helpers\Utility;
use App\Model\WriteDocument;
use App\Model\signatureUpload;
use App\DocusignDetail;
use Illuminate\Support\Facades\Validator;
use App\DocumentSentReport;
use App\Services\SmsService;
use Carbon\Carbon;
use App\Model\PDF;
use App\Services\LogsService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\AppointmentPortalMergeLogsService;
use App\Helpers\MergeUtilityHelper;
use App\Services\DocumentPatientService;
use App\Services\DocusignRecordResponsesLogService;
use App\Services\MultiplePatientDocApprovalService;
use App\Services\DocumentUploadService;
use App\Helpers\DocumentHelper;
use App\Services\LocationMasterService;
use App\Services\SendEmailNotificationSerivce;
use App\Model\LogsCreateEmailCheck;
use App\Helpers\UserHelper;
use App\Agency;
use App\Services\UserCreatorEmailNotificationService;
use Illuminate\Support\Facades\File;
use App\Services\WriteDocumentService;
use App\Services\StreamlinedEsignService;
use App\Services\DocumentWorkflowService;
use App\Events\SignerStatusUpdated;
class CommonEsignController extends Controller
{
	protected const ERROR_MSG="Sorry, something went wrong. Please try again.";
	protected $documentSignerService;
	protected $templateService;
	protected $documentSentReport;
	protected $docusignDetailService;
	protected $commonEsignService;
	protected $patientService;
	protected $documentSendSmsLogService;
	protected $stampService;
	protected $dynamicFormLogService;
	protected $doctorService;
	protected $signatureUploadService;
	protected $smsService;
	protected const ESIGN_DOCUMENT_UPLOAD_PATH="dosusinguploads/docusign";
	protected const ESIGN_WRITE_DOCUMENT_UPLOAD_PATH="patientWriteDocument";
	protected const DATE_FORMAT_YMD = "Y-m-d H:i:s";
	protected const MODULE_TYPE = "Patient Appointment";
	protected const SMS_ESIGN_LINK = 'esign/nye/';
	protected const LOG_ESIGN_SMS_LINK = '/esign/patient-send-sms-esign';
	protected const IMAGE_STAMP_PREG_MATCH = '/^data:image\/(\w+);base64,/';
	protected $appointmentMergeLogsService;
	protected $docusignRecordResponsesLogService;
	protected $multiplePatientDocApprovalService;
	protected $documentUploadService;
	protected $locationMasterService;
	protected $sendEmailNotificationSerivce;
	protected $userCreatorEmailNotificationService;
	protected $writeDocumentService;
	protected $streamlinedEsignService;
	protected $documentWorkflowService;

	public function __construct(DocumentSignerService $documentSignerService,TemplateService $templateService,DocumentSendService $documentSentReport,DocusignDetailService $docusignDetailService,CommonEsignService $commonEsignService,PatientService $patientService,DocumentSendSmsLogService $documentSendSmsLogService,StampService $stampService,DynamicFormLogService $dynamicFormLogService,DoctorService $doctorService,SignatureUploadService $signatureUploadService,SmsService $smsService,DocumentPatientService $documentPatientService,AppointmentPortalMergeLogsService $appointmentMergeLogsService,DocusignRecordResponsesLogService $docusignRecordResponsesLogService,MultiplePatientDocApprovalService $multiplePatientDocApprovalService,DocumentUploadService $documentUploadService,LocationMasterService $locationMasterService,SendEmailNotificationSerivce $sendEmailNotificationSerivce,UserCreatorEmailNotificationService $userCreatorEmailNotificationService,WriteDocumentService $writeDocumentService,StreamlinedEsignService $streamlinedEsignService,DocumentWorkflowService $documentWorkflowService)
	{
		$this->middleware('auth', ['except' => ['UserWiseTemplateList', 'UserWiseDocumentList', 'ViewDocusign', 'EsignSignature', 'docusignFormSubmit', 'emailSignShow', 'thankyou', 'getPdfForAws', 'viewDocusignNew','DocusignFormSubmitUpdate','uploadSignature','getPatientSignatures','deleteSignature']]);
		$this->documentSignerService = $documentSignerService;
		$this->templateService = $templateService;
		$this->documentSentReport = $documentSentReport;
		$this->docusignDetailService = $docusignDetailService;
		$this->commonEsignService = $commonEsignService;
		$this->patientService = $patientService;
		$this->documentSendSmsLogService = $documentSendSmsLogService;
		$this->stampService = $stampService;
		$this->dynamicFormLogService = $dynamicFormLogService;
		$this->doctorService = $doctorService;
		$this->signatureUploadService = $signatureUploadService;
		$this->smsService =$smsService;
		$this->appointmentMergeLogsService = $appointmentMergeLogsService;
		$this->documentPatientService = $documentPatientService;
		$this->docusignRecordResponsesLogService = $docusignRecordResponsesLogService;
		$this->multiplePatientDocApprovalService = $multiplePatientDocApprovalService;
		$this->documentUploadService = $documentUploadService;
		$this->locationMasterService = $locationMasterService;
		$this->sendEmailNotificationSerivce = $sendEmailNotificationSerivce;
		$this->userCreatorEmailNotificationService = $userCreatorEmailNotificationService;
		$this->writeDocumentService = $writeDocumentService;
		$this->streamlinedEsignService = $streamlinedEsignService;
		$this->documentWorkflowService = $documentWorkflowService;
	}

	public function DocumentSend(Request $request)
	{
		$auth = auth()->user();

		$query = $this->documentSignerService->getDocumentSignerMasterListById($request->input('template_id'));

		$sourceFile = $this->templateService->getDetailsById($request->input('template_id'));

		$rand = uniqid();
		$insertid = 0;
		$data_array = [];
		foreach ($query as $val) {
			$countArray = 'No';
			$pending = '';
			$sourceFiles = '';
			$user_id = $request->input('eid');
	
			$eidc = ($request->input('eidc') != null) ? $request->input('eidc') : $user_id;
			if (strtolower($val->name) == strtolower($query[0]->name)) {

				$pending = 'Pending';
				$sourceFiles = $sourceFile->upload_document;
				$user_id = $request->input('eid');
				if (strtolower($val->name) == 'officestaff') {
					$eidc = $val->user_id;
				} else {
					$eidc = $request->input('eidc');
				}
			}

			if (strtolower($val->name) == 'caregiver') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->input('receipt_name'),
					'templete_id' => $request->input('template_id'),

					'type' => $request->input('type'),
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'caregiver',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}

			if (strtolower($val->name) == 'officestaff') {
				$getUserDetails = User::getDetailsById($val->user_id);
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $val->user_id,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $getUserDetails->first_name . ' ' . $getUserDetails->last_name,
					'templete_id' => $request->input('template_id'),

					'type' => $request->input('type'),
					'sent_on' => 'OfficeStaff',
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $request->input('eid'),
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}

			if (strtolower($val->name) == 'other') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->input('receipt_name'),
					'templete_id' => $request->input('template_id'),

					'type' => $request->input('type'),
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'other',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')

				);
			}
			if (strtolower($val->name) == 'stampuser') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => $request->template_id,
					'type' => $request->type,
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'stampUser',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')

				);
			}
			if (strtolower($val->name) == 'patient') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => $request->template_id,
					'type' => $request->type,
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'patient',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')

				);
			}
			//new signer
			if (strtolower($val->name) == 'formfill') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => $request->template_id,
					'type' => $request->type,
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'formFill',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}
			if (strtolower($val->name) == 'sign') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => $request->template_id,
					'type' => $request->type,
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'sign',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}
			if (strtolower($val->name) == 'stamp') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => $request->template_id,
					'type' => $request->type,
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'stamp',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}

			if (strtolower($val->name) == 'sign&stamp') {
			
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => $request->template_id,
					'type' => $request->type,
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'sign&stamp',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}
			//end new signer
			if ($countArray == 'Yes') {

				$insertid = $this->documentSentReport->save($data_array);
			}
		}

		if ($insertid) {

			if ($request->input('flag') == 'ajax') {
				return 1;
			} else {
				return response()->json(['error_msg' => 'Document successfully sent.', 'status' => 1, 'data' => array()], 200);
			}
		} else {

			if ($request->input('flag') == 'ajax') {
				return 1;
			} else {
				return response()->json(['error_msg' => self::ERROR_MSG, 'status' => 0, 'data' => array()], 500);
			}
		}
	}

	public function singerAllowcateRequest(Request $request)
	{
		$auth = auth()->user();
		$groupId = $request->input('groupId');
	
		$query = $this->documentSentReport->GetDetailsbyGroupId($groupId);

		$updatedQuery = [];
		if (!empty($query[0])) {
			$updatedQuery = [];
			 foreach ($query as $val) {
				$sentOn = $val->sent_on;
				$val->sent_on = ucfirst($sentOn);
				
				$updatedQuery[] = $val;
			}
			$query = $updatedQuery;
		}

		return response()->json(['error_msg' => 'Success', 'status' => 1, 'data' => $query], 200);
	}

	/*Mobile related api function */
	public function UserWiseTemplateList(Request $request, $id)
	{

		$data['mobile_type'] = $request->input('mobiletype');
		$query = $this->UserService->getCaregiverDetailById($id);

		$data['mainid'] = $id;
		$data['mainids'] = $id;
		
		if (isset($query->id) && $query->id != '') {

			$newList = $this->documentSentReport->AssignTemplateList($query->code, 'caregiver');
			if (count($newList) > 0) {
				foreach ($newList as $iks) {
					$totalSigner = $this->documentSentReport->TotalSignerCount($iks->groupId);
					$iks->signerRemaining = $totalSigner[0]->total;
				}
			}

			$data['newList']  = $newList;
			return view('admin.docusign.caregiveresign.caregiveresign', $data);
		} else {
			return view('errorEsign');
		}
	}

	public function UserWiseDocumentList(Request $request, $groupId)
	{
		$data['user_list'] = $this->documentSentReport->GetDetailsbyGroupId($groupId);
		$data['mobile_type'] = $request->input('mobile_type');
		$data['mainids'] = $request->input('mainid');
		return view('admin.docusign.caregiveresign.useresign_list', $data);
	}

	public function ViewDocusign(Request $request, $id)
	{
		$data['document_all_details'] = $this->documentSentReport->getAllDetails($id);
		$data['department'] = 'mobile';
		$data['id'] = $data['document_all_details']->id;
		$data['mobile_type'] = $request->input('mobile_type');
		$data['sessionIds'] = $sessionId = $data['document_all_details']->caregiver_code;
		$data['sessionId'] = $sessionId;
		$data['groupId']  = $data['document_all_details']->groupId;
		$data['sent_on'] = ucfirst($data['document_all_details']->sent_on);

		$getGeneratePdfOrNot = $this->documentSentReport->pdfGenerateOrNot($data['document_all_details']->groupId);

		$generatePDF = '';
		if (isset($getGeneratePdfOrNot->pdf_generate) && $getGeneratePdfOrNot->pdf_generate != '') {
			$generatePDF = $getGeneratePdfOrNot->pdf_generate;
		}
		$data['document_all_details']->pdfgenerate = $generatePDF;

		$data['document_report_id'] = $id;
		$main_intake = $data['document_all_details']->main_intakeId;
		$response = unserialize($data['document_all_details']->response);
		$final_array = array();
		$signInsert = array();
		$data['docWidth1'] = $docWidth = $data['document_all_details']->docWidth;
		$max = array();
		$intakeArray = array();
		$subIntakeArray = array();
		$sourceFile = $this->templateService->getDetailsById($data['document_all_details']->templete_id);
		$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($main_intake);
		$maxs = 1;
		if (isset($response) && $response != '') {
			$final_array[] = $docWidth;
			$data['docWidth'] = $docWidth;
			foreach ($response as $val) {
				$val['checked_defualt'] = 0;
				$val['checked_defualt_radio'] = 0;
				$val['user_type'] = $getPatientDetails->type;
				$val['agency_form_id'] = $data['document_all_details']->agency_form_id;
				$final_array[] = $val;

				$max[] = $val['page'];
				$maxs = max($max);
				if ($val['temp1'] == 'caregiver' || $val['temp1'] == 'patient') {
					if ($val['temp3'] != '') {
						$subresponse = $this->caregiverFieldsResponse($main_intake, $val['temp3'], $sourceFile->custom_form_id, $getPatientDetails->agency_id, $data['document_all_details']->agency_form_id,$data['document_all_details']->doctor_id,$data['document_all_details']->groupId);
						$subIntakeArray[] = $subresponse;
					}
				} else {
					$dynamicDropdownId = isset($val['dynamicDropdownId']) ? $val['dynamicDropdownId'] : "";
					$dynamicDropdownIdVal = isset($val['dynamicDropdownIdVal']) ? $val['dynamicDropdownIdVal'] : "";

					if ($dynamicDropdownId != "") {
						$subresponse = $this->showOtherCheckBox($sourceFile->custom_form_id, $dynamicDropdownId, $main_intake, $data['document_all_details']->agency_form_id);

						if (isset($val['normalValue'])) {
							if (in_array($val['normalValue'], $subresponse)) {
								$val['checked_defualt'] = 1;
								$val['checked'] = 1;
							}
						}
					} elseif ($dynamicDropdownIdVal != "") {
						$subresponse = $this->showOtherRadio($sourceFile->custom_form_id, $dynamicDropdownIdVal, $main_intake, $data['document_all_details']->agency_form_id);

						if (isset($val['normalValueRadio'])) {
							if (is_array($subresponse)) {
								if (in_array($val['normalValueRadio'], $subresponse)) {
									$val['checked'] = 1;
									$val['checked_defualt_radio'] = 1;
								}
							} else {
								if ($val['normalValueRadio'] == $subresponse) {
									$val['checked'] = 1;
									$val['checked_defualt_radio'] = 1;
								}
							}
						}
					}
				}

				$signInsert[] = $val;
			}
		}
		$intakeArray = $subIntakeArray;

		if ($data['document_all_details']->status == 'Completed') {
			$final_array = [];
			$signInsert = [];
			$intakeArray = [];
		}

		$data['templateFields'] = json_encode($final_array, true);
		$data['Signinsert'] = json_encode($signInsert, true);

		$data['rand'] = rand(0000, 9999);
		$data['removeScript'] = 'docusign';
		$data['main_intakeId'] = $main_intake;
		$data['max'] = $maxs;
		$data['LookUpResponses'] = json_encode($intakeArray);
		$data['stamp_user'] = $this->stampService->getStampUser();

		/** signer error first */
		return view('docusign.caregiveresign.view_docusign', $data);
	}

	public function caregiverFieldsResponse($id, $keys, $formId="", $agencyId, $agency_form_id="",$doctor_id="",$groupId="")
	{
	
		$key = $keys;
		$user_id = $id;
		$explode  = explode('@', $key);
		$finalArray = array();

		$finalArray = array();
		if ($explode[0] == 'fm') {
			$caregiverDetails = AgencyAllForm::GetFormDetails($formId, $explode[1], $user_id, $agency_form_id);
		} elseif ($explode[0] == 'dr') {
			$caregiverDetails = AgencyAllForm::GetDoctorDetails($formId, $explode[1], $user_id, $agency_form_id,$doctor_id);
		} elseif ($explode[0] == 'ag') {
			$caregiverDetails = AgencyAllForm::GetAgencyDetails($explode[1], $agencyId);
		} else {
			$caregiverDetails = $this->patientService->GetCaregiverFormDetails($explode[1], $user_id,$groupId);
		}

		if ($explode[1] == 'dob' || $explode[1] == 'date_of_examination') {
			$date = "";
			if ($caregiverDetails != "") {
				$date = date('m/d/Y', strtotime($caregiverDetails));
			}
			$finalArray[$key] = $date;
		} else {
			$finalArray[$key] = $caregiverDetails ?? "";
		}

		return $finalArray;
	}
	public function EsignSignature(Request $request)
	{
		$file = $request->file('image');
		
		$newName = uniqid().''.time().".png";
		
		if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
			Storage::disk('s3')->putFileAs(self::ESIGN_DOCUMENT_UPLOAD_PATH, $file, $newName);
			$response = Storage::disk('s3')->temporaryUrl(
				self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $newName,
				Carbon::now()->addMinutes(10) // URL expires in 10 minutes
			);
		}else{
			$file->move(public_path() . '/'.self::ESIGN_DOCUMENT_UPLOAD_PATH, $newName);
			$newName = strip_tags($newName);
			$response = url('/').'/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $newName;
		}

		echo $response;
	}

	public function uploadSignature(Request $request)
	{
		$auth = auth()->user();
		$data['login_id'] = $auth['id'] ?? '';
		$type = $request->type;
		
		if ($request->hasFile('file_upload')) {
			$file = $request->file('file_upload');
			$newName = uniqid() . '.' . $file->getClientOriginalExtension();

			if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
				$file->move(public_path() . '/'.self::ESIGN_DOCUMENT_UPLOAD_PATH, $newName);
				$file_path = public_path() . '/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $newName;
				$url = URL::to('/').'/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/'.$newName;
			} else {
				
				Storage::disk('s3')->putFileAs(self::ESIGN_DOCUMENT_UPLOAD_PATH, $file, $newName);
				$file_path =  Storage::disk('s3')->temporaryUrl(
					self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' .  $newName,
					Carbon::now()->addMinutes(10) // URL expires in 10 minutes
				);
				$url = $file_path;
			}
			if(isset($data['login_id']) && $data['login_id']){
				$finalData = [
					'user_id'=>$request->login_id,
					'file_upload'=>$newName,
					'type'=>$type ?? 'esign',
					'created_at'=> date(self::DATE_FORMAT_YMD),
					'signature_name'=>$request->signature_name
				];

				$signature = new signatureUpload($finalData);
				$signature->save();
				$lastId = $signature->id;

				if($type){
					$url = $this->getAwsServerImages($lastId,$type);
				}else{
					$url = $this->getAwsServerImages($lastId,"esign");
				}
				$file_path = $url;
			}
			return response()->json(['url' => $url, 'path' => $file_path]);
		}

		return response()->json(['error' => 'No file uploaded'], 400);
	}

	public function docusignFormSubmit(Request $request)
	{
		
		$id = $request->input('id');
		$sessionId = $request->input('sessionId');
		$action =urldecode($request->action);
		$document_report_id = $request->input('document_report_id');
		$groupId = $request->input('groupId');

		if (empty($sessionId)) {
			$auth = auth()->user();
		
			if (!empty($auth->id)) {
				$sessionId = $auth->id;
			}
		}
		if ($id != ''  && $action != '' && $document_report_id != '') {
			$actions = json_decode($action, true);
			
			$templateResposne = $this->templateService->getDetailsById($id);
			
			$templateResponseData = unserialize($templateResposne->response);
		
			$templateArray = [];
			$templateXYPosArray = [];
			if(count($templateResponseData) > 0){
				foreach($templateResponseData as $val){
					if($val['type'] =='image'){
						$templateArray[$val['id']] = $val;
					}else{
						if($val['type'] !='image' && $val['type'] !="stamp"){
							$templateXYPosArray[$val['id']] = $val;
						}
					}
				}
			}
		
			$actionsTemp = [];
			foreach($actions as $obj){
				if(isset($templateArray[$obj['id']])){
					$obj['width'] = $templateArray[$obj['id']]['width'];
					$obj['height'] = $templateArray[$obj['id']]['height'];
				}

				if(isset($templateXYPosArray[$obj['id']])){
					$obj['xPos'] = $templateXYPosArray[$obj['id']]['xPos'];
					$obj['yPos'] = $templateXYPosArray[$obj['id']]['yPos'];
				}
				if($obj['type'] =='image'){
					$folder = basename(dirname($obj['text']));
					$folderFlag = 0;
					if(strtolower($folder) =='patientwritedocument'){
						$folderFlag = 1;
					}
					$finalName = basename($obj['text']);
					$obj['text'] = strtok($finalName, '?');
					$obj['updatedSelectType'] = $folderFlag;
				}

				if($obj['type'] =='stamp'){
					$finalName = basename($obj['text']);
					$obj['text'] = strtok($finalName, '?');
					$obj['updatedSelectType'] = 0;
				}

				
				$actionsTemp[] = $obj;
			}
			
			$actions = $actionsTemp;
		
			$actions = serialize($actions);

			/*insert docusign_detail table log table */
			$checkExistingDocument = $this->docusignDetailService->getDetailsByDocumentReportId($document_report_id);
			if($checkExistingDocument){
				$this->docusignDetailService->update(array('data' => $actions),array('id'=>$checkExistingDocument->id));
				$insert = $checkExistingDocument->id;
			}else{
				$insert = $this->docusignDetailService->save(array('document_report_id' => $document_report_id, 'template_id' => $id, 'user_id' => $sessionId, 'data' => $actions,'docWidth'=>$request->docWidth));
			}

			$this->documentSentReport->update(array('document_response_id'=>$insert),array('id' => $document_report_id));
			/*end */

			if ($insert) {
				/*get Pdf File*/
				$finalResponseFields = [
					'insert' => $insert,
					'id' => $id,
					'sessionId' => $sessionId,
					'document_report_id' => $document_report_id,
					'groupId' => $groupId,
					'sent_on' => $request->sent_on
				];
				$return = $this->commonEsignService->regeneratethepdf($finalResponseFields);
				$data = array(
					'id' => $return
				);
				
				$getDocumentDetails = $this->documentSentReport->findEsignDocumentById($groupId,$document_report_id);
				$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($getDocumentDetails->main_intakeId);
				if(isset($getPatientDetails->type) && strtolower($getPatientDetails->type) =='patient'){
					event(new SignerStatusUpdated($document_report_id, 'Completed', $groupId));
				}
				
				return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => array($data)]);
			} else {
				return response()->json(['status' => "0", 'error_msg' => "No record available.", 'data' => array()]);
			}
		} else {
			return response()->json(['status' => "0", 'error_msg' => "Parameter not found.", 'data' => array()]);
		}
	}
	/*End Mobile related api */

	public function caregiverDelete($id)
	{

		$auth = auth()->user();
		$getDocumentDetails = $this->documentSentReport->detailsByGroupId($id);
		$query = $this->documentSentReport->SoftDelete(array('del_flag' => 'Y', 'deleted_date' => date(self::DATE_FORMAT_YMD), 'deleted_by' => $auth->id), array('groupId' => $id));

		if($getDocumentDetails->templete_id != 0){
			$type = 'Delete Template From Appointment';
			$msg = $auth->first_name . ' ' . $auth->last_name . ' has deleted Template From Appointment';
		}else{
			$type = 'Delete Esign Document From Appointment';
			$msg = $auth->first_name . ' ' . $auth->last_name . ' has deleted Esign Document From Appointment';
		}
		if ($query) {
			$ipaddress = Utility::getIP();
			$messages = $msg;
			$insertLog = [
				'type' => $type,
				'link' => url('/patient/view/') . '/' . $getDocumentDetails->main_intakeId,
				'module' => self::MODULE_TYPE,
				'object_id' => $getDocumentDetails->main_intakeId,
				'message' => $messages,
				'old_response' => serialize($getDocumentDetails),
				'ip' => $ipaddress,
			];

			LogsService::save($insertLog);
			return response()->json(['status' => "1", 'error_msg' => "Document successfully deleted.", 'data' => array()], 200);
		} else {
			return response()->json(['status' => "0", 'error_msg' => self::ERROR_MSG, 'data' => array()], 500);
		}
	}
	public function download($groupId)
	{
		$res = EsignHelper::getDownloadPdf($groupId);
	}

	public function caregiverSendSMSOld(Request $request)
	{
		$groupId = $request->groupId;
		$email = $request->email;
		$notes = $request->message;
		

	
		if (isset($request->document_send_type) && $request->document_send_type == 'single') {
			$query = $this->documentSentReport->getGroupPendingNew($groupId);
			$groupId = $query->groupId;
		} else {
			$query = $this->documentSentReport->getGroupPending($groupId);
		}


		if (isset($query->id)) {
			$link = URL::to(self::SMS_ESIGN_LINK) . '/' . $query->id . '?id=' . $groupId;

			if(count($request->sendType) >0){
				foreach($request->sendType as $smnt){
					
					if($smnt =='email'){
						$emailData = array(
							'link' => $link,
							'notes'=>$notes
						);
						$message = Utility::getHtmlContent('email_template.email_esign_link_template', $emailData);
					
						$data = array('to' => strtolower($email), 'subject' => 'Esign', 'messages' => $message);
						
						Mail::mailer('second')->send([], [], function ($message) use ($data) {
							$message->subject($data['subject']);
							$message->to($data['to']);
							$message->html($data['messages']);
						});
					}

					if($smnt =='mobile'){
						if (isset($request->mobile) && $request->mobile != "") {
							$smsMessage = "Dear,\n";
							$smsMessage .= 'Please complete esign from below link  ';
							$smsMessage .= '<b>Notes :</b>'.$notes.'\n  ';
							$smsMessage .= $link;
							
							$this->smsService->AgencyWiseSmsDynamic($query->main_intakeId,$request->mobile, $smsMessage);
							// $sms = Common::sendTwillioSms($request->mobile, $smsMessage);
							// if($sms){
								
							// }
						}
					}
				}
			}

			$responsesEmail = in_array('email',$request->sendType);
			$emailsUpdate = '';
			$mobileUpdate = '';
			$updatedData = [
				'document_id' => $query->id,
				'caregiver_id' => $request->hhaCaregiverId,
				'message' => $notes
			];
			if($responsesEmail){
				$updatedData['email'] = $email;
				$emailsUpdate = $email;
			}

			$responsesMobile = in_array('mobile',$request->sendType);
			if($responsesMobile){
				$updatedData['mobile'] = $request->mobile;
				$mobileUpdate = $request->mobile;
			}
			$this->documentSendSmsLogService->save($updatedData);
			$this->documentSentReport->update(array('email'=>$emailsUpdate,'sms'=>$mobileUpdate),array('id' => $query->id));
			
			$ipaddress = Utility::getIP();
			$user = auth()->user();
			$insertLog = [
				'type' => 'Send Esign SMS',
				'link' => url(self::LOG_ESIGN_SMS_LINK),
				'module' => self::MODULE_TYPE,
				'object_id' => $request->hhaCaregiverId,
				'message' => $user->first_name . ' ' . $user->last_name . ' has sent Esign message',
				'new_response' => serialize(array('email'=>$emailsUpdate,'sms'=>$mobileUpdate)),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			
			return 1;
		} else {

			return 0;
		}
	}

	public function caregiverSendSMS(Request $request)
	{
		$query = $this->getDocumentQuery($request);
		if (!isset($query->id)) {
			return 0;
		}

		$link = $this->prepareLink($query->id, $query->groupId);
		$request->newGroupId = $query->groupId;
		$emailsUpdate = '';
		$mobileUpdate = '';

		foreach ((array) $request->sendType as $type) {
			if ($type === 'email') {
				$this->sendEmail($request->email, $link, $request->message);
				$emailsUpdate = $request->email;
			}

			if ($type === 'mobile' && !empty($request->mobile)) {
				$this->sendSMS($query->main_intakeId, $request->mobile, $link, $request->message);
				$mobileUpdate = $request->mobile;
			}
		}

		$this->updateLogsAndRecords($query->id, $request, $emailsUpdate, $mobileUpdate);

		return 1;
	}

	private function getDocumentQuery(Request $request)
	{
		if (isset($request->document_send_type) && $request->document_send_type == 'single') {
			return $this->documentSentReport->getGroupPendingNew($request->groupId);
		}else{
			return $this->documentSentReport->getGroupPending($request->groupId);
		}
	}

	private function prepareLink($docId, $groupId)
	{
		return URL::to(self::SMS_ESIGN_LINK) . '/' . $docId . '?id=' . $groupId;
	}

	private function sendEmail($email, $link, $notes)
	{
		$emailData = ['link' => $link, 'notes' => $notes];
		$message = Utility::getHtmlContent('email_template.email_esign_link_template', $emailData);

		$data = ['to' => strtolower($email), 'subject' => 'Esign', 'messages' => $message];
		try {
			Mail::mailer('second')->send([], [], function ($message) use ($data) {
				$message->subject($data['subject']);
				$message->to($data['to']);
				$message->html($data['messages']);
			});
		} catch (\Throwable $th) {
			//throw $th;
		}
		
	}

	private function sendSMS($intakeId, $mobile, $link, $notes)
	{
		$smsMessage = "Dear,\nPlease complete esign from below link. Notes: {$notes}\n{$link}";
		$this->smsService->AgencyWiseSmsDynamic($intakeId, $mobile, $smsMessage);
	}

	private function updateLogsAndRecords($docId, Request $request, $email, $mobile)
	{
		$updatedData = [
			'document_id' => $docId,
			'caregiver_id' => $request->hhaCaregiverId,
			'message' => $request->message,
			'email' => $email,
			'mobile' => $mobile,
		];

		$this->documentSendSmsLogService->save($updatedData);
		$saveResponse = ['email' => $email, 'sms' => $mobile];
		$this->documentSentReport->update($saveResponse, ['id' => $docId]);

		$user = auth()->user();
		$message = $user->first_name . ' ' . $user->last_name . ' has sent Esign message';
		$saveResponse['message'] = $request->message;
		$insertLog = [
			'type' => 'Send Esign SMS',
			'link' => url(self::LOG_ESIGN_SMS_LINK),
			'module' => self::MODULE_TYPE,
			'object_id' => $request->hhaCaregiverId,
			'message' =>$message,
			'new_response' => serialize($saveResponse),
			'ip' => Utility::getIP(),
		];

		LogsService::save($insertLog);

		$insertLog = [
			'type' => 'Send Esign SMS',
			'link' => url(self::LOG_ESIGN_SMS_LINK),
			'module' => 'Esign Section',
			'module_id' => $request->newGroupId,
			'new_response' => serialize($saveResponse),
			'message'=>$message,
			'is_status'=>'Send SMS - Email'
		];
		$this->dynamicFormLogService->storeFormLog($insertLog);
		
	}

	public function esignPatientList(Request $request)
	{
		$data['record'] = $record = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->patient_id);
		$ids = [$request->patient_id];

		if (isset($record->merge_appointment_id) && $record->merge_appointment_id != "") {
			$mergeData = ['merge_id'=>$record->merge_appointment_id,'currentId'=>$request->patient_id,'del_flag'=>$record->deleted_flag];

			$ids = MergeUtilityHelper::convertData($mergeData);
		}
		
		$response = $this->documentSentReport->caregiverWiseEsignTemplateList($request->type, $ids, 'id', 'desc');

		if (!empty($response[0])) {
			foreach ($response as $val) {
				$totalSigner = $this->documentSentReport->TotalSignerCount($val->groupId);

				$query = $this->documentSentReport->GetDetailsbyGroupId($val->groupId);
				$sentOnCount = 0;
				$completedCount = 0;
				$pendingCount = 0;
				if (!empty($query)) {
					foreach ($query as $queryVal) {
						if (!empty($queryVal->sent_on)) {
							$sentOnCount++;
						}
						if($queryVal->status !=""){
							if (strtolower($queryVal->status) == 'completed') {
								$completedCount++;
							}
						}
						
					}
				}
				$val->sentOnCount = $sentOnCount;
				$val->completedCount = $completedCount;
				$val->signerRemaining = $totalSigner[0]->total;

				$completed_on = "";
				if ($val->completed_on != "") {
					$completed_on = date('m/d/Y h:i A', strtotime($val->completed_on));
				}
				$val->completed_on = $completed_on;
				$val->created_date = date('m/d/Y h:i A', strtotime($val->created_date));
				if($val->template_details){
					$val->template_details->response='';
				}

				$review_first_name = "";
				if(isset($val->reviewDetails->first_name) && $val->reviewDetails->first_name !=""){
					$review_first_name = $val->reviewDetails->first_name.' '.$val->reviewDetails->last_name;
				}

				$val->review_first_name = $review_first_name;

				$user_first_name = "";
				if(isset($val->userDetails->first_name) && $val->userDetails->first_name !=""){
					$user_first_name = $val->userDetails->first_name.' '.$val->userDetails->last_name;
				}

				$val->user_first_name = $user_first_name;
			}
		}
		$page = $request->page;
		$esignView = auth()->user()->can('esign-view');
		$esignDelete = auth()->user()->can('esign-delete');
		$esignSendSms = auth()->user()->can('esign-send-sms');
		$esignViewLog = auth()->user()->can('esign-view-log');
		$esignPdfDownload = auth()->user()->can('esign-pdf-download');
		$esignMoveDocument = auth()->user()->can('esign-move-document');
		$esignRevert = auth()->user()->can('esign-revert');
		$esignReview = auth()->user()->can('esign-review');
		return view('patient/_partial/esign/esign_ajax_list',compact('response','page','esignView','esignDelete','esignSendSms','esignViewLog','esignPdfDownload','esignMoveDocument','esignRevert','esignReview','record'));
	}

	public function thankyou()
	{
		return view('thankyouesign');
	}

	public function emailSignShow(Request $request, $docId)
	{
		$auth = auth()->user();
		$data['login_id'] = $auth->id??"";
		$esigData = $this->documentSentReport->getAllDetails($docId);
		$esigDatanew = $this->documentSentReport->findEsignDocumentById($request->id, $docId);

		if (isset($esigDatanew->id)) {
			$sourceFile = $this->templateService->getDetailsById($esigDatanew->templete_id);

			$data['document_all_details'] = $this->documentSentReport->getAllDetails($esigData->id);
			$data['department'] = 'web';
			$data['id'] = $data['document_all_details']->id;
			$data['mobile_type'] = "web";
			$data['sessionIds'] = $data['document_all_details']->caregiver_code;
			$data['groupId']  = $data['document_all_details']->groupId;
			$data['sent_on'] = ucfirst($data['document_all_details']->sent_on);
			$getGeneratePdfOrNot = $this->documentSentReport->pdfGenerateOrNot($data['document_all_details']->groupId);

			$generatePDF = '';
			if (isset($getGeneratePdfOrNot->pdf_generate) && $getGeneratePdfOrNot->pdf_generate != '') {
				$generatePDF = $getGeneratePdfOrNot->pdf_generate;
			}
			$data['document_all_details']->pdfgenerate = $generatePDF;

			$data['document_report_id'] = $data['document_all_details']->id;
			$main_intake = $data['document_all_details']->main_intakeId;
			$response = unserialize($data['document_all_details']->response);
			$final_array = array();
			$signInsert = array();
			$data['docWidth'] = $docWidth = $data['document_all_details']->docWidth;
			$max = array();
			$intakeArray = array();
			$subIntakeArray = array();
			$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($main_intake);
			$data['isPatient'] = (isset($getPatientDetails->type) && strtolower($getPatientDetails->type) == 'patient') ? true : false;
			$data['portal_type'] = $getPatientDetails->type??"";
			if (isset($response) && $response != '') {
				$final_array[] = $docWidth;

				foreach ($response as $val) {
					$val['checked_defualt'] = 0;
					$val['checked_defualt_radio'] = 0;
					$val['user_type'] = $getPatientDetails->type;
					$final_array[] = $val;

					$max[] = $val['page'];
					$maxs = max($max);
					if ($val['temp1'] == 'caregiver' || $val['temp1'] == 'patient') {
						if ($val['temp3'] != '') {
							$subresponse = $this->caregiverFieldsResponse($main_intake, $val['temp3'], $sourceFile->custom_form_id, $getPatientDetails->agency_id, $esigData->agency_form_id,$esigData->doctor_id,$esigData->groupId);
							$subIntakeArray[] = $subresponse;
						}
					} else {
						$dynamicDropdownId = isset($val['dynamicDropdownId']) ? $val['dynamicDropdownId'] : "";
						$dynamicDropdownIdVal = isset($val['dynamicDropdownIdVal']) ? $val['dynamicDropdownIdVal'] : "";

						if ($dynamicDropdownId != "") {
							$subresponse = $this->showOtherCheckBox($sourceFile->custom_form_id, $dynamicDropdownId, $main_intake, $esigData->agency_form_id);

							if (isset($val['normalValue'])) {
								if (in_array($val['normalValue'], $subresponse)) {
									$val['checked_defualt'] = 1;
									$val['checked'] = 1;
								}
							}
						} elseif ($dynamicDropdownIdVal != "") {
							$subresponse = $this->showOtherRadio($sourceFile->custom_form_id, $dynamicDropdownIdVal, $main_intake, $esigData->agency_form_id);

							if (isset($val['normalValueRadio'])) {
								if (is_array($subresponse)) {
									if (in_array($val['normalValueRadio'], $subresponse)) {
										$val['checked'] = 1;
										$val['checked_defualt_radio'] = 1;
									}
								} else {
									if ($val['normalValueRadio'] == $subresponse) {
										$val['checked'] = 1;
										$val['checked_defualt_radio'] = 1;
									}
								}
							}
						}
					}
					$signInsert[] = $val;
				}
			}

			$intakeArray = $subIntakeArray;

			if ($data['document_all_details']->status == 'Completed') {
				$final_array = [];
				$signInsert = [];
				$intakeArray = [];
			}
			$data['templateFields'] = json_encode($final_array, true);
			$data['Signinsert'] = json_encode($signInsert, true);
			$data['rand'] = rand(0000, 9999);
			$data['removeScript'] = 'docusign';
			$data['main_intakeId'] = $main_intake;
			$data['max'] = $maxs;
			$data['LookUpResponses'] = json_encode($intakeArray);
			$data['stamp_user'] = $this->stampService->getStampUser();

			//new
			$labels = [];
			$placeholderCounts = [];

			foreach ($signInsert as $item) {
				$temp3 = isset($item['temp3']) ? $item['temp3'] : null;
				$labelValue = null;
				$labelId = isset($item['id']) ? $item['id'] : null;
				$name = isset($item['name']) ? $item['name'] : null;
				$page = isset($item['page']) ? $item['page'] : null;
				$required = isset($item['required']) ? $item['required'] : null;
				$type = isset($item['type']) ? $item['type'] : null;

				$isFilled = '';

				foreach ($intakeArray as $value) {
					foreach ($value as $k => $v) {
						if ($temp3 == $k) {
							$isFilled = !empty(trim($v)) ? 1 : 0;
						}
					}
				}

				if (isset($item['type']) && $item['type'] === 'text') {
					if (strpos($temp3, 'fm@') !== false) {
						$extracted = explode('@', $temp3)[1];
						$filedMaster = FieldMaster::find($extracted);
						$labelValue = $filedMaster ? $filedMaster->label : null;
					} elseif (strpos($temp3, 'dr@') !== false) {
						$extracted = explode('@', $temp3)[1];
						$labelValue = $item['placeHolder'];
					} elseif (strpos($temp3, 'ag@') !== false) {
						$extracted = explode('@', $temp3)[1];
						$labelValue = $item['placeHolder'];
					} elseif (strpos($temp3, 'pm@') !== false) {
						$extracted = explode('@', $temp3)[1];
						$labelValue = $item['placeHolder'];
					} else {
						if ($item['text'] != '' && $item['placeHolder'] != 'Date Signed') {
							$placeHolder = $item['text'];
							if (isset($placeholderCounts[$placeHolder])) {
								$placeholderCounts[$placeHolder]++;
							} else {
								$lastCount = !empty($placeholderCounts) ? max($placeholderCounts) : 0;
								$placeholderCounts[$placeHolder] = $lastCount + 1;
							}
							$labelValue = $placeHolder . ' #' . $placeholderCounts[$placeHolder];
						} else {
							$placeHolder = $item['placeHolder'];
							if (isset($placeholderCounts[$placeHolder])) {
								$placeholderCounts[$placeHolder]++;
							} else {
								$lastCount = !empty($placeholderCounts) ? max($placeholderCounts) : 0;
								$placeholderCounts[$placeHolder] = $lastCount + 1;
							}
							$labelValue = $placeHolder . ' #' . $placeholderCounts[$placeHolder];
						}
					}
				} elseif (isset($item['type']) && $item['type'] === 'checkbox') {
					$dynamicDropdownId = isset($item['dynamicDropdownId']) ? $item['dynamicDropdownId'] : null;
					if ($dynamicDropdownId) {
						$filedMaster = FieldMaster::find($dynamicDropdownId);
						$labelValue = $filedMaster ? $filedMaster->label : null;
					} else {
						$labelValue = $item['normalValue'];
					}
				} elseif (isset($item['type']) && $item['type'] === 'radio') {
					$dynamicDropdownIdVal = isset($item['dynamicDropdownIdVal']) ? $item['dynamicDropdownIdVal'] : null;
					if ($dynamicDropdownIdVal) {
						$filedMaster = FieldMaster::find($dynamicDropdownIdVal);
						$labelValue = $filedMaster ? $filedMaster->label : null;
					} else {
						$labelValue = $item['normalValueRadio'] ?? '' ;
					}
				} elseif (isset($item['type']) && $item['type'] === 'image') {
					$placeHolder = 'Signature';
					if (isset($placeholderCounts[$placeHolder])) {
						$placeholderCounts[$placeHolder]++;
					} else {
						$placeholderCounts[$placeHolder] = 1;
					}
					$labelValue = $placeHolder . ' #' . $placeholderCounts[$placeHolder];
				}elseif (isset($item['type']) && $item['type'] === 'stamp') {
					$placeHolder = 'Stamp';
					if (isset($placeholderCounts[$placeHolder])) {
						$placeholderCounts[$placeHolder]++;
					} else {
						$placeholderCounts[$placeHolder] = 1;
					}
					$labelValue = $placeHolder . ' #' . $placeholderCounts[$placeHolder];
				} elseif (isset($item['type']) && $item['type'] === 'dropdown') {
					$placeHolder = 'Select Value';
					if (isset($placeholderCounts[$placeHolder])) {
						$placeholderCounts[$placeHolder]++;
					} else {
						$placeholderCounts[$placeHolder] = 1;
					}
					$labelValue = $placeHolder . ' #' . $placeholderCounts[$placeHolder];
				}
				if (isset($item['type']) && in_array($item['type'], ['checkbox', 'radio'])) {
					if ($labelValue && !in_array($labelValue, array_column($labels, 'label'))) {
						$labels[] = [
							'id' => $labelId,
							'label' => $labelValue,
							'page' => $page,
							'isFilled' => $isFilled,
							'isRequired' => $required,
							'name' => $name,
							'type' => $type
						];
					}
				} else {
					$labels[$labelId] = [
						'id' => $labelId,
						'label' => $labelValue,
						'page' => $page,
						'isFilled' => $isFilled,
						'isRequired' => $required,
						'name' => $name,
						'type' => $type

					];
				}
			}
			$data['labels'] = $labels;

			return view('docusign.caregiveresign.view_docusign_new', $data);
		} else {
			return view('errorEsign');
		}
	}

	public function pdfRegenerate(Request $request)
	{
		$this->commonEsignService->tempRegeratePdf(10065);
	}

	public function getPdfForAws(Request $request)
	{
		$id = request('template_id');
		$query = $this->documentSentReport->getDetailsById($id);

		$dir = public_path() . '/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $query->sourceFile;

		if (file_exists($dir)) {
			if(isset($query->sourceFile) && $query->sourceFile !=""){
				$dir = public_path() . '/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $query->sourceFile;

				$headers = [];
				return response()->download($dir, $query->sourceFile, $headers);
			}
			
		} else {
			
			if(isset($query->sourceFile) && $query->sourceFile !=""){
						return  Storage::disk('s3')->get('/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $query->sourceFile);
			}

		}
	}

	public function getPdfForAwsEdit(Request $request)
	{
		$id = request('template_id');
		$query = $this->templateService->getDetailsById($id);

		$dir = public_path() . '/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $query->upload_document;
		if (file_exists($dir)) {
			$dir = public_path() . '/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $query->upload_document;

			$headers = [];

			return response()->download($dir, $query->upload_document, $headers);
		} else {
			return Storage::disk('s3')->download('/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $query->upload_document);
		}
	}

	public function showOtherCheckBox($formId, $fieldId, $patient_id, $id)
	{
		$query = AgencyAllForm::GetFormDetails($formId, $fieldId, $patient_id, $id);

		$data = unserialize($query);

		$final = [];
		if (!empty($data[0])) {
			foreach ($data as $val) {
				if ($val != 'null') {
					$final[] = $val;
				}
			}
		}

		return $final;
	}

	public function showOtherRadio($formId, $fieldId, $patient_id, $id)
	{
		return AgencyAllForm::GetFormDetails($formId, $fieldId, $patient_id, $id);
	}

	public function EsignSignatureWriteDocument(Request $request)
	{
		$file = $request->file('image');
		$newName = uniqid() . time() . ".png";
		if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
			Storage::disk('s3')->putFileAs(self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH, $file, $newName);
			$response = Storage::disk('s3')->temporaryUrl(
				self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/' . $newName,
				Carbon::now()->addMinutes(10) // URL expires in 10 minutes
			);
		}else{
			$file->move(public_path() . '/'.self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH, $newName);
			$response = url('/'.self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH) . '/' . $newName;
		}

		echo $response;
	}

	public function uploadSignatureWriteDocument(Request $request)
	{
		$auth = auth()->user();
		$data['login_id'] = $auth['id'] ?? '';
		$type = $request->type;

		if ($request->hasFile('file_upload')) {
			$file = $request->file('file_upload');
			$newName = uniqid().'.'.$file->getClientOriginalExtension();
			$url="";
			if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
				$file->move(public_path(self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH), $newName);
				$file_path = public_path(self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/' . $newName);
				$url = url(self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/' . $newName);
			} else {
				Storage::disk('s3')->putFileAs(self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/', $file, $newName);
				Storage::disk('s3')->url(self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/'.$newName);
				
			}

			if($data['login_id']){
				$signature = new signatureUpload();
				$signature->user_id = $request->login_id;
				$signature->file_upload = $newName;
				$signature->type = $type ?? '';
				$signature->created_at = date(self::DATE_FORMAT_YMD);
				$signature->save();
				$lastId = $signature->id;

				if($type){
					$url = $this->getAwsServerImages($lastId,$type);
				}else{
					$url = $this->getAwsServerImages($lastId);
				}
				$file_path = $url;
			}
			
			return response()->json(['url' => $url, 'path' => $file_path]);
		}

		return response()->json(['error' => 'No file uploaded'], 400);
	}

	public function DocusignFormSubmitWriteDocoument(Request $request)
	{
	
		$id = $request->input('id');
		$action = $request->input('action');

		if ($id != ''  && $action != '') {
			$actions = json_decode($action, true);
			$actions = serialize($actions);

			/****Remove COde for TemplateControllerwriteDocumentSend */
			$document_key = request("docusignKey");
			$actions = request("docusignAction");
			$docWidth = request("docusignWidth");
			$docHeight = request("docusignHeight");
			$signing_key = request("docusignSigningKey");

			$documentDetails = json_decode($actions, true);
			$tesmps = serialize($documentDetails);

			$this->documentSentReport->updateDocumentResponse($document_key, $tesmps, $docWidth);
			// Update WriteDocument with latest response, docWidth and docHeight so PDF generation uses correct scale
			$this->writeDocumentService->update([
				'docHeight' => $docHeight
			],['id'=>$id]);
			
			/*get Pdf File*/
			$return = $this->commonEsignService->regeneratethepdfWriteDocument($id);
			
			return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $return]);
		} else {
			return response()->json(['status' => "0", 'error_msg' => "Parameter not found.", 'data' => array()]);
		}
	}

	public function previewPdfPatient(Request $request)
	{
		$data['url'] = "";
		$getDetails = $this->documentSentReport->getDetailsByIdNew($request->id);
		$getPdfDetail = $this->documentSentReport->getDetailsByGroup($request->group_id);
		$data['getDetails'] = $getDetails;
		if (isset($getPdfDetail->main_intakeId)) {
			$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($getPdfDetail->main_intakeId);
			$data['patient_view_details'] = $getPatientDetails;
			if (isset($getPatientDetails->agency_id)) {
				if (str_contains($getPdfDetail->pdf_generate, self::ESIGN_DOCUMENT_UPLOAD_PATH)) {
					if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
						$data['url'] = url(self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $getPdfDetail->pdf_generate);
					} else {
						$data['url'] = Storage::disk('s3')->temporaryUrl($getPdfDetail->pdf_generate, now()->addMinutes(60));
					}
				} else {
					if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
						$data['url'] = url(self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $getPdfDetail->pdf_generate);
					} else {
						$data['url'] = Storage::disk('s3')->temporaryUrl(self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $getPdfDetail->pdf_generate, now()->addMinutes(60));
					}
				}
			}
		}
		return view('docusign.caregiveresign.view_pdf_iframe', $data);
	}

	public function pdfUpdateStatus(Request $request)
	{
		$auth = auth()->user();
		$request->validate([
			'pdf_status' => 'required|in:0,1',
		]);

		$document = $this->documentSentReport->getDetailsByIdAllLimited($request->document_id);
		$oldResponse = $document->toArray();
		$docData = array(
						'pdf_status'        => $request->pdf_status,
						'pdf_status_reason' => $request->pdf_status_reason,
						'review_date'       => date(self::DATE_FORMAT_YMD),
						'review_by'         => $auth->id
					);
		$this->documentSentReport->update($docData,array('id' => $request->document_id));
		$newResponse = $this->documentSentReport->getDetailsByIdAllLimited($request->document_id)->toArray();
		// Insert form Log into Dynamic form log table
		$message ='';
		$status = $request->pdf_status == 1 ? 'Approved' : 'Rejected';
		$getTemplateDetails = $this->templateService->getDetailsById($document->templete_id);
		$message = "";
		if(isset(auth()->user()->id)){
			$message = auth()->user()->first_name . ' ' . auth()->user()->last_name . " has ".$status." the E-Sign document using the ".$getTemplateDetails->template_name;
		}
		unset($newResponse->template_details);
		$insertLog = [
			'type' => 'Esign Document '.$status,
			'link' => url('/esign/pdf/update-status'),
			'module' => 'Esign Section',
			'module_id' => $document->groupId,
			'new_response' => serialize($newResponse),
			'old_response' => serialize($oldResponse),
			'is_status' => $request->pdf_status == 1 ? 'Approved' : 'Rejected',
			'message'=>$message,

		];
		$this->dynamicFormLogService->storeFormLog($insertLog);

		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Update Esign Form',
			'link' =>url('esign/pdf/update-status'),
			'module' => self::MODULE_TYPE,
			'object_id' => $document->main_intakeId,
			'message' => $message,
			'new_response' => serialize($newResponse),
			'old_response' => serialize($oldResponse),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);

		return response()->json(['success' => true, 'error_msg' => 'Status updated successfully.']);
	}

	public function pdfUndoStatus(Request $request)
	{
		$auth = auth()->user();
		$document = $this->documentSentReport->getDetailsByIdAllLimited($request->document_id);
		$oldResponse = $document->toArray();
		$this->documentSentReport->update(['pdf_status'        => null,
											'pdf_status_reason' => null,
											'review_date'       => null,
											'review_by'         => null,
											'is_undo'           => 1,
											'is_undo_date'      => date(self::DATE_FORMAT_YMD)]
										,array('id' => $request->document_id));
		$newResponse = $this->documentSentReport->getDetailsByIdAllLimited($request->document_id)->toArray();
		$newResponse['undo_by_name'] = $auth->full_name ?? '';
		// Insert form Log into Dynamic form log table
		$templateDetails = $this->templateService->getDetailsById($document->templete_id);
		$message="";
		if(isset(auth()->user()->id)){
			$message = auth()->user()->first_name . ' ' . auth()->user()->last_name . " has reverted the E-Sign Template using the ".$templateDetails->template_name;
		}

		$insertLog = [
			'type' => 'Esign Document Revert',
			'link' => url('/esign/pdf/undo-status'),
			'module' => 'Esign Section',
			'module_id' => $document->groupId,
			'new_response' => serialize($newResponse),
			'old_response' => serialize($oldResponse),
			'is_status' => 'Revert',
			'message' => $message,
		];
		$this->dynamicFormLogService->storeFormLog($insertLog);

		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Esign Document Revert',
			'link' =>url('/esign/pdf/undo-status'),
			'module' => self::MODULE_TYPE,
			'object_id' => $document->main_intakeId,
			'message' => $message,
			'new_response' => serialize($newResponse),
			'old_response' => serialize($oldResponse),
			'ip' => $ipaddress,
		];

		LogsService::save($insertLog);
		if($request->esignType !=0){
			event(new SignerStatusUpdated($request->document_id, 'Pending', $document->groupId));
		}
		return response()->json(['success' => true, 'error_msg' => 'Status Undo successfully.']);
	}
	public function getLogDetails(Request $request)
	{
		$module = "Esign Section";
		$logs = $this->dynamicFormLogService->getLogDataWithEsign($request->document_id, $module);
		$document = $this->documentSentReport->detailsByGroupId($request->document_id);

		$modalTitle = '';
		if ($request->template_id != 0) {
			$templateData = $this->templateService->getDetailsById($request->template_id);
			$modalTitle = $templateData->template_name;
		} else {
			$writeDocData = WriteDocument::where('document_patient_id', $document->id)->first();
			$modalTitle = $writeDocData->document_name;
		}
		$newResponse = [];
		
		foreach ($logs as $log) {
			$log->new_reponses = unserialize($log->new_response);
			$log->created_date = date('m/d/Y h:i A',strtotime($log->created_at));
			$log->message = $log->message ?? "";
			if (isset($log->userDetails)) {
				$log->added_by_name = $log->userDetails->first_name . ' ' . $log->userDetails->last_name;
			} else {
				$log->added_by_name = ''; // or ''
			}
			unset($log->userDetails);
			if($log->is_status =='Approved'){
				$log->review_by_name = $log->added_by_name;
				$log->review_date = $log->created_date;
			}
			
			if($log->is_status =='Rejected'){
				$log->review_by_name = $log->added_by_name;
				$log->review_date = $log->created_date;
				$log->pdf_status_reason = $log->new_reponses['pdf_status_reason'];
			}

			if($log->is_status =='Revert'){
				$log->undo_by_name = $log->added_by_name;
				$log->is_undo_date = $log->created_date;
			}

			if($log->is_status =='Download'){
				$log->download_by_name = $log->added_by_name;
				$log->download_date = $log->created_date;
			}
			if($log->is_status =='Send SMS - Email'){
				$log->download_by_name = $log->added_by_name;
				$log->download_date = $log->created_date;
			}
			$statusArray = ['formFill','sign','stamp','other','caregiver','stampUser','patient','OfficeStaff','Form Edit','signStamp'];
			if(in_array($log->is_status,$statusArray)){
				$log->completed_by_name = $log->added_by_name;
				$log->completed_on = $log->created_date;
				$log->completed_date = $log->created_date;
			}

			$log->esign_old_response = unserialize($log->esign_old_response)??[];

			$newResponse[] = $log;
		}
		
		return response()->json(['logs' => $newResponse, 'template_name' => $modalTitle]);
	}

	public function viewDocusignNew(Request $request, $id)
	{
		
		$auth = auth()->user();
		$data['login_id'] = $auth['id'] ?? '';
		$data['document_all_details'] = $this->documentSentReport->getAllDetails($id);
		
		$data['department'] = 'mobile';
		$data['id'] = $data['document_all_details']->id;
		$data['mobile_type'] = $request->input('mobile_type');
		$data['sessionIds'] = $sessionId = $data['document_all_details']->caregiver_code;
		$data['sessionId'] = $sessionId;
		$data['groupId']  = $data['document_all_details']->groupId;
		$data['sent_on'] = ucfirst($data['document_all_details']->sent_on);

		$GetGeneratePdfOrNot = $this->documentSentReport->pdfGenerateOrNot($data['document_all_details']->groupId);

		$generatePDF = '';
		if (isset($GetGeneratePdfOrNot->pdf_generate) && $GetGeneratePdfOrNot->pdf_generate != '') {
			$generatePDF = $GetGeneratePdfOrNot->pdf_generate;
		}
		$data['document_all_details']->pdfgenerate = $generatePDF;

		$data['document_report_id'] = $id;
		$main_intake = $data['document_all_details']->main_intakeId;
		$response = unserialize($data['document_all_details']->response);
		$final_array = array();
		$signInsert = array();
		$data['docWidth1'] = $docWidth = $data['document_all_details']->docWidth;
		$max = array();
		$intakeArray = array();
		$subIntakeArray = array();
		$sourceFile = $this->templateService->getDetailsById($data['document_all_details']->templete_id);
		$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($main_intake);
		$data['portal_type'] = $getPatientDetails->type??"";
		
		$maxs = 1;

		if (isset($response) && $response != '') {
			$final_array[] = $docWidth;
			$data['docWidth'] = $docWidth;
			
			foreach ($response as $val) {
				$val['checked_defualt'] = 0;
				$val['checked_defualt_radio'] = 0;
				$val['user_type'] = $getPatientDetails->type;
				$val['agency_form_id'] = $data['document_all_details']->agency_form_id;
				$final_array[] = $val;

				$max[] = $val['page'];
				$maxs = max($max);

				
				if ($val['temp1'] == 'caregiver' || $val['temp1'] == 'patient') {
					if ($val['temp3'] != '') {
						$subresponse = $this->caregiverFieldsResponse($main_intake, $val['temp3'], $sourceFile->custom_form_id, $getPatientDetails->agency_id, $data['document_all_details']->agency_form_id,$data['document_all_details']->doctor_id,$data['document_all_details']->groupId);
						$subIntakeArray[] = $subresponse;
					}
				} else {
					$dynamicDropdownId = isset($val['dynamicDropdownId']) ? $val['dynamicDropdownId'] : "";
					$dynamicDropdownIdVal = isset($val['dynamicDropdownIdVal']) ? $val['dynamicDropdownIdVal'] : "";

					if ($dynamicDropdownId != "") {
						$subresponse = $this->showOtherCheckBox($sourceFile->custom_form_id, $dynamicDropdownId, $main_intake, $data['document_all_details']->agency_form_id);

						if (isset($val['normalValue'])) {
							if (in_array($val['normalValue'], $subresponse)) {
								$val['checked_defualt'] = 1;
								$val['checked'] = 1;
							}
						}
					} elseif ($dynamicDropdownIdVal != "") {
						$subresponse = $this->showOtherRadio($sourceFile->custom_form_id, $dynamicDropdownIdVal, $main_intake, $data['document_all_details']->agency_form_id);

						if (isset($val['normalValueRadio'])) {
							if (is_array($subresponse)) {
								if (in_array($val['normalValueRadio'], $subresponse)) {
									$val['checked'] = 1;
									$val['checked_defualt_radio'] = 1;
								}
							} else {
								if ($val['normalValueRadio'] == $subresponse) {
									$val['checked'] = 1;
									$val['checked_defualt_radio'] = 1;
								}
							}
						}
					}
				}

				$signInsert[] = $val;
			}
		}
		
		$intakeArray = $subIntakeArray;

		if ($data['document_all_details']->status == 'Completed') {
			$final_array = [];
			$signInsert = [];
			$intakeArray = [];
		}

		$data['templateFields'] = json_encode($final_array, true);
		$data['Signinsert'] = json_encode($signInsert, true);
		$data['rand'] = rand(0000, 9999);
		$data['removeScript'] = 'docusign';
		$data['main_intakeId'] = $main_intake;
		$data['isPatient'] = (isset($getPatientDetails->type) && strtolower($getPatientDetails->type) == 'patient') ? true : false;
		$data['max'] = $maxs;
		$data['LookUpResponses'] = json_encode($intakeArray);
		$data['stamp_user'] = $this->stampService->getStampUser();

		$labels = [];
		$placeholderCounts = [];

		foreach ($signInsert as $item) {
			$temp3 = isset($item['temp3']) ? $item['temp3'] : null;
			$labelValue = null;
			$labelId = isset($item['id']) ? $item['id'] : null;
			$name = isset($item['name']) ? $item['name'] : null;
			$page = isset($item['page']) ? $item['page'] : null;
			$required = isset($item['required']) ? $item['required'] : null;
			$type = isset($item['type']) ? $item['type'] : null;

			$isFilled = '';

			foreach ($intakeArray as $value) {
				foreach ($value as $k => $v) {
					if ($temp3 == $k) {
						$isFilled = !empty(trim($v)) ? 1 : 0;
					}
				}
			}

			if (isset($item['type']) && $item['type'] === 'text') {
				if (strpos($temp3, 'fm@') !== false) {
					$extracted = explode('@', $temp3)[1];
					$filedMaster = FieldMaster::find($extracted);
					$labelValue = $filedMaster ? $filedMaster->label : null;
				} elseif (strpos($temp3, 'dr@') !== false) {
					$extracted = explode('@', $temp3)[1];
					$labelValue = $item['placeHolder'];
				} elseif (strpos($temp3, 'ag@') !== false) {
					$extracted = explode('@', $temp3)[1];
					$labelValue = $item['placeHolder'];
				} elseif (strpos($temp3, 'pm@') !== false) {
					$extracted = explode('@', $temp3)[1];
					$labelValue = $item['placeHolder'];
				} else {
					if ($item['text'] != '' && $item['placeHolder'] != 'Date Signed') {
						$placeHolder = $item['text'];
						if (isset($placeholderCounts[$placeHolder])) {
							$placeholderCounts[$placeHolder]++;
						} else {
							$lastCount = !empty($placeholderCounts) ? max($placeholderCounts) : 0;
							$placeholderCounts[$placeHolder] = $lastCount + 1;
						}
						$labelValue = $placeHolder . ' #' . $placeholderCounts[$placeHolder];
					} else {
						$placeHolder = $item['placeHolder'];
						if (isset($placeholderCounts[$placeHolder])) {
							$placeholderCounts[$placeHolder]++;
						} else {
							$lastCount = !empty($placeholderCounts) ? max($placeholderCounts) : 0;
							$placeholderCounts[$placeHolder] = $lastCount + 1;
						}
						$labelValue = $placeHolder . ' #' . $placeholderCounts[$placeHolder];
					}
				}
			} elseif (isset($item['type']) && $item['type'] === 'checkbox') {
				$dynamicDropdownId = isset($item['dynamicDropdownId']) ? $item['dynamicDropdownId'] : null;
				if ($dynamicDropdownId) {
					$filedMaster = FieldMaster::find($dynamicDropdownId);
					$labelValue = $filedMaster ? $filedMaster->label : null;
				} else {
					$labelValue = $item['normalValue']??"";
				}
			} elseif (isset($item['type']) && $item['type'] === 'radio') {
				$dynamicDropdownIdVal = isset($item['dynamicDropdownIdVal']) ? $item['dynamicDropdownIdVal'] : null;
				if ($dynamicDropdownIdVal) {
					$filedMaster = FieldMaster::find($dynamicDropdownIdVal);
					$labelValue = $filedMaster ? $filedMaster->label : null;
				} else {
					$labelValue = $item['normalValueRadio'] ?? '';
				}
			} elseif (isset($item['type']) && $item['type'] === 'image') {
				$placeHolder = 'Signature';
				if (isset($placeholderCounts[$placeHolder])) {
					$placeholderCounts[$placeHolder]++;
				} else {
					$placeholderCounts[$placeHolder] = 1;
				}
				$labelValue = $placeHolder . ' #' . $placeholderCounts[$placeHolder];
			} elseif (isset($item['type']) && $item['type'] === 'dropdown') {
				$placeHolder = 'Select Value';
				if (isset($placeholderCounts[$placeHolder])) {
					$placeholderCounts[$placeHolder]++;
				} else {
					$placeholderCounts[$placeHolder] = 1;
				}
				$labelValue = $placeHolder . ' #' . $placeholderCounts[$placeHolder];
			}
			if (isset($item['type']) && in_array($item['type'], ['checkbox', 'radio'])) {
				if ($labelValue && !in_array($labelValue, array_column($labels, 'label'))) {
					$labels[] = [
						'id' => $labelId,
						'label' => $labelValue,
						'page' => $page,
						'isFilled' => $isFilled,
						'isRequired' => $required,
						'name' => $name,
						'type' => $type
					];
				}
			} else {
				$labels[$labelId] = [
					'id' => $labelId,
					'label' => $labelValue,
					'page' => $page,
					'isFilled' => $isFilled,
					'isRequired' => $required,
					'name' => $name,
					'type' => $type

				];
			}
		}
		$data['labels'] = $labels;
		//new

$completedSigners = $this->completedDocumentSignerListWithCurrentId($data['document_all_details']->id,$data['document_all_details']->groupId);
$completedSignersSentOn = array_column($completedSigners, 'sent_on');
			$completedSignersSentOn[] = strtolower($data['document_all_details']->sent_on);

		/** signer error first */
		return view('docusign.caregiveresign.view_docusign_new', $data);
	}

	public function loadDoctorList()
	{
		$data =  $this->doctorService->getDoctorList();
		return response()->json(['status' => true, 'data' => $data]);
	}

	public function DocumentSendNew(Request $request)
	{
		$auth = auth()->user();

		$query = $this->documentSignerService->getDocumentSignerMasterListById($request->input('template_id'));

		$sourceFile = $this->templateService->getDetailsById($request->input('template_id'));
		
		$rand = uniqid();
		$insertid = 0;
		$data_array = [];

		foreach ($query as $val) {
			$countArray = 'No';
			$pending = '';
			$sourceFiles = '';
			$user_id = $request->input('eid');

			$eidc = ($request->input('eidc') != null) ? $request->input('eidc') : $user_id;
			if (strtolower($val->name) == strtolower($query[0]->name)) {

				$pending = 'Pending';
				$sourceFiles = $sourceFile->upload_document;
				$user_id = $request->input('eid');
				if (strtolower($val->name) == 'officestaff') {
					$eidc = $val->user_id;
				} else {
					$eidc = $request->input('eidc');
				}
			}

			if (strtolower($val->name) == 'caregiver') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->input('receipt_name'),
					'templete_id' => $request->input('template_id'),

					'type' => $request->input('type'),
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'caregiver',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}

			if (strtolower($val->name) == 'officestaff') {
				$getUserDetails = User::getDetailsById($val->user_id);
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $val->user_id,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $getUserDetails->first_name . ' ' . $getUserDetails->last_name,
					'templete_id' => $request->input('template_id'),

					'type' => $request->input('type'),
					'sent_on' => 'OfficeStaff',
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $request->input('eid'),
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}

			if (strtolower($val->name) == 'other') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->input('receipt_name'),
					'templete_id' => $request->input('template_id'),

					'type' => $request->input('type'),
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'other',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}
			if (strtolower($val->name) == 'stampuser') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => $request->template_id,
					'type' => $request->type,
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'stampUser',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}
			if (strtolower($val->name) == 'patient') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => $request->template_id,
					'type' => $request->type,
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'patient',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}
			if (strtolower($val->name) == 'formfill') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => $request->template_id,
					'type' => $request->type,
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'formFill',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}
			if (strtolower($val->name) == 'sign') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => $request->template_id,
					'type' => $request->type,
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'sign',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}
			if (strtolower($val->name) == 'stamp') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => $request->template_id,
					'type' => $request->type,
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'stamp',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}

			if (strtolower($val->name) == 'signstamp') {
				$countArray = 'Yes';
				$data_array = array(
					'sender_name' => $auth['first_name'] . ' ' . $auth['last_name'],
					'caregiver_code' => $eidc,
					'status' => $pending,
					'sender_id' => $auth['id'],
					'receipt_name' => $request->receipt_name,
					'templete_id' => $request->template_id,
					'type' => $request->type,
					'sourceFile' => $sourceFiles,
					'main_intakeId' => $user_id,
					'sent_on' => 'signStamp',
					'groupId' => $rand,
					'template_response' => $sourceFile->response,
					'doctor_id' => $request->input('doctor_id')
				);
			}
			if ($countArray == 'Yes') {

				$insertid = $this->documentSentReport->save($data_array);
			}
		}

		$documentRes = $this->documentSentReport->getAllDetailsByGroupId($rand);
		$logDetails = [];
		$groupId = "";
		if(count($documentRes) >0){
			foreach($documentRes as $val){
				$val->template_name = $val->templateDetails->template_name;
				$val->added_by_name = $val->userDetails->first_name.' '.$val->userDetails->last_name;

				unset($val->templateDetails, $val->userDetails);
				$logDetails[] = $val;
				$groupId = $val->groupId;
			}
		}
		
		
		// Insert form Log into Dynamic form log table
		$ipaddress = Utility::getIP();
		$message = auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has create a new esign template';
		$insertLog = [
			'type' => 'Added',
			'link' => url('/esign/template/docusign-sent-new'),
			'module' => 'Esign Section',
			'module_id' => $groupId,
			'new_response' => serialize($logDetails),
			'old_response' => '',
			'is_status' => 'Added',
			'ip_address'=>$ipaddress,
			'message'=>$message,
		];
		$this->dynamicFormLogService->storeFormLog($insertLog);
		$data = $request->except('_token');
		
		$insertLog = [
			'type' => 'Add Esign Template',
			'link' => url('/esign/template/docusign-sent-new'),
			'module' => self::MODULE_TYPE,
			'object_id' => $request->eid,
			'message' =>$message,
			'new_response' => serialize($data),
			'ip' => $ipaddress,
			
		];
		LogsService::save($insertLog);

		if ($insertid) {
			if ($request->input('flag') == 'ajax') {
				return 1;
			} else {
				return response()->json(['error_msg' => 'Document successfully sent.', 'status' => 1, 'data' => array()], 200);
			}
		} else {

			if ($request->input('flag') == 'ajax') {
				return 1;
			} else {
				return response()->json(['error_msg' => self::ERROR_MSG, 'status' => 0, 'data' => array()], 500);
			}
		}
	}

	public function showSMSEsignHistory(Request $request){
		$query = $this->documentSendSmsLogService->getListByDocumentWise($request->documentId,$request->caregiver_id);
		return response()->json(['error_msg' => '', 'data' => $query], 200);
	}

	public function getDocumentSentReportDataOld(Request $request){
		$auth = auth()->user();
		$data['document_all_details'] = $this->documentSentReport->getAllGroupIdData($request->groupId);
		
		$finaldocumentId = [];
		$tempId = "";
		
		foreach ($data['document_all_details'] as $details) {
			$finaldocumentId[] = $details->id;
			$tempId = $details->templete_id;
			
		}
		
		$responseData = DocusignDetail::select('id','data','docWidth','del_flag')->where('del_flag', 'N')->whereIn('document_report_id',$finaldocumentId)->where('updated_flag',1)->get();
		$docId = [];
		if (isset($responseData) && isset($responseData[0]) && !empty($responseData[0])) {
			$docId[] = $responseData[0]->id;
		}else{
			$responseData = DocusignDetail::select('id')->where('del_flag', 'N')->whereIn('document_report_id',$finaldocumentId)->get();
			foreach($responseData as $val){
				$docId[] = $val->id;
			}
		}
		$responseData = DocusignDetail::select('id','data','docWidth','del_flag')->where('del_flag', 'N')->whereIn('id',$docId)->get();

		$sourceFile = $this->templateService->getDetailsById($tempId);
		
		$data['template_id'] = $tempId;
		$data['document_report_id'] =$finaldocumentId[count($finaldocumentId) - 1];
		$templateResponse = unserialize($sourceFile->response);
		$existingIds = [];
		$finalResponse = [];
		$finalResponseImages = [];
		$widthDoc = $sourceFile->docWidth;
		if(!empty($responseData[0])){
			foreach($responseData as $dat){
				$finalResponseImages[]=unserialize($dat->data);
			}
		}
		$finalAll = [];
		if(!empty($responseData[0])){
			foreach($responseData as $dat){
				if($dat['docWidth'] !=""){
					$widthDoc = $dat['docWidth'];
				}
				$response = unserialize($dat->data);
				
				$finalAll['new'] =$response;
				
				if(!empty($response[0])){
					foreach($response as $vs){
						$flag=0;
						if($vs['type'] =='image'){
							if(isset($vs['text'])){
								if (preg_match(self::IMAGE_STAMP_PREG_MATCH, $vs['text'], $matches)) {
									$imageType = $matches[1];
									$base64Data = substr($vs['text'], strpos($vs['text'], ',') + 1);
									$decodedImage = base64_decode($base64Data);
								
									if ($decodedImage !== false) {
										$filename = uniqid().''.time().'.png';
										if(env('FILE_UPLOAD_PERMISSION')  != 'development'){
											Storage::disk('s3')->put(self::ESIGN_DOCUMENT_UPLOAD_PATH.'/'.$filename, $decodedImage);
											
										}else{
											$destination = public_path('/').self::ESIGN_DOCUMENT_UPLOAD_PATH.'/'.$filename;
											file_put_contents($destination,$decodedImage);
										}
										$img = $filename;
									}
								}else{
									$vs['text'] = str_replace('https://nybest-data.s3.us-east-2.amazonaws.com/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/','',$vs['text']);
							
									if(env('FILE_UPLOAD_PERMISSION')  != 'development'){
										if (strpos($vs['text'], env('HOST_WEB_URL')) !== false) {
											
											$img = str_replace(env('HOST_WEB_URL').self::ESIGN_DOCUMENT_UPLOAD_PATH.'/','',$vs['text']);
										
										}else{
											if (str_contains($vs['text'], self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH)) {
												$img = str_replace('https://nybest-data.s3.us-east-2.amazonaws.com/'.self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/','',$vs['text']);
												$imgs = explode('?',$img);
												$img = $imgs[0];
												$flag=1;
											}else{
												$img = str_replace('https://nybest-data.s3.us-east-2.amazonaws.com/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/','',$vs['text']);
												$imgs = explode('?',$img);
												$img = $imgs[0];
												$flag=0;
											}
										}
									}else{
										if (strpos($vs['text'], env('HOST_WEB_URL')) !== false) {
											$img = basename($vs['text']);
										}else{
											if (str_contains($vs['text'], self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH)) {
												$img = str_replace('https://nybest-data.s3.us-east-2.amazonaws.com/'.self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/','',$vs['text']);
												$imgs = explode('?',$img);
												$img = $imgs[0];
												$flag=1;
											}else{
												$img = str_replace('https://nybest-data.s3.us-east-2.amazonaws.com/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/','',$vs['text']);
												$imgs = explode('?',$img);
												$img = $imgs[0];
												$flag=0;
											}
										}
									}
								}
								if($flag==1){
									$imgs = $this->docusingImagesShowpatientWriteDocument($img);
								}else{
									$imgs = $this->docusingImagesShow($img);
								}
								$vs['text'] =$imgs;
							}
						}
						
						if($vs['type'] =='stamp'){
							if (preg_match(self::IMAGE_STAMP_PREG_MATCH, $vs['text']??"", $matches)) {
								$imageType = $matches[1];
								$text = $vs['text']??"";
								$base64Data = substr($text, strpos($text, ',') + 1);
								$decodedImage = base64_decode($base64Data);
								if ($decodedImage !== false) {
									$filename = uniqid().''.time().'.png';
									if(env('FILE_UPLOAD_PERMISSION')  != 'development'){
										Storage::disk('s3')->put(self::ESIGN_DOCUMENT_UPLOAD_PATH.'/'.$filename, $decodedImage);
									}else{
										$destination = public_path('/').self::ESIGN_DOCUMENT_UPLOAD_PATH.'/'.$filename;
										file_put_contents($destination,$decodedImage);
									}
									$img = $filename;
								}
							}else{
								$text = $vs['text']??"";
								if(env('FILE_UPLOAD_PERMISSION')  != 'development'){

									if (strpos($text, env('HOST_WEB_URL').self::ESIGN_DOCUMENT_UPLOAD_PATH) !== false) {
										$img = str_replace(env('HOST_WEB_URL').self::ESIGN_DOCUMENT_UPLOAD_PATH,'',$text);
									}else{
										if (str_contains($vs['text'], self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH)) {
											$img = str_replace('https://nybest-data.s3.us-east-2.amazonaws.com/'.self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/','',$text);
											$imgs = explode('?',$img);
											$img = $imgs[0];
											$flag=1;
										}else{
											$img = str_replace('https://nybest-data.s3.us-east-2.amazonaws.com/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/','',$text);
											$imgs = explode('?',$img);
											$img = $imgs[0];
											$flag=0;
										}
									}
								}else{
									if (strpos($text, env('HOST_WEB_URL').self::ESIGN_DOCUMENT_UPLOAD_PATH) !== false) {
										$img = basename($text);
									}else{
										if (str_contains($vs['text'], self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH)) {
											$img = str_replace('https://nybest-data.s3.us-east-2.amazonaws.com/'.self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/','',$text);
											$imgs = explode('?',$img);
											$img = $imgs[0];
											$flag=1;
										}else{
											$img = str_replace('https://nybest-data.s3.us-east-2.amazonaws.com/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/','',$text);
											$imgs = explode('?',$img);
											$img = $imgs[0];
											$flag=0;
										}
									}
								}
							}

							if($flag ==1){
								$vs['text'] = $this->docusingImagesShowpatientWriteDocument($img);
							}else{
								$vs['text'] = $this->docusingImagesShow($img);
							}
						}
						
						if(!in_array($vs['id'],$existingIds)){
							
							if($vs['type'] =="checkbox" || $vs['type'] =='radio'){
							
								$finalResponse[$vs['id']] = $vs['checked'];
							}else{
								
								$width = 100;
								if(isset($vs['width'])){
									$width = $vs['width'];
								}

								$height = 100;
								if(isset($vs['height'])){
									$height = $vs['height'];
								}
								
								$temp = ['xPos'=>$vs['xPos'],'yPos'=>$vs['yPos'],'text'=>$vs['text']??"",'width'=>$width,'height'=>$height,'page'=>$vs['page']??""];
								$finalResponse[$vs['id']] = $temp;
							}
							
							$existingIds[] = $vs['id'];
							
							if($vs['type'] == "stamp"){
								$finalResponseImages[] = $vs['type'];
							}
						
						}else{
							if($vs['type'] =='image' || $vs['type'] == "stamp"){
								if($vs['type'] == "stamp"){
									$finalResponse[$vs['id']]['xPos'] = $vs['xPos'];
									$finalResponse[$vs['id']]['yPos'] = $vs['yPos'];
									$finalResponse[$vs['id']]['width'] = $vs['width'];
									$finalResponse[$vs['id']]['height'] = $vs['height'];
								}

								$finalResponse[$vs['id']]['text'] = $vs['text'];
							}
						}
					
					}
				}
			}
		}

		$flagImage = 0;
		if (isset($finalResponseImages) && isset($finalResponseImages[0]) && !empty($finalResponseImages[0])) {
			$flagImage = 1;
		}

		$data['department'] = 'mobile';
		$data['id'] = $data['document_all_details'][0]->id;
		$data['mobile_type'] = 'web';
		$data['sessionIds'] = $sessionId = $data['document_all_details'][0]->main_intakeId;
		$data['sessionId'] = $sessionId;
		$data['groupId']  = $data['document_all_details'][0]->groupId;
		
		$generatePDF = '';
		if (isset($data['document_all_details'][0]->upload_document) && $data['document_all_details'][0]->upload_document != '') {
			$generatePDF = $data['document_all_details'][0]->upload_document;
		}
		$data['document_all_details']->pdfgenerate = $generatePDF;
		$data['document_report_id'] = $finaldocumentId[count($finaldocumentId) - 1];
		$main_intake = $data['document_all_details'][0]->main_intakeId;
		$final_array = array();
		$signInsert = array();

		$max = array();
		$intakeArray = array();
		$subIntakeArray = array();
		$data['sent_on']="all";
		$data['old_pdf_data'] =$sourceFile->upload_document;
		$data['docWidth'] = $sourceFile->docWidth;
	
		$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($main_intake);
		$maxs = 1;
		$editOldResponse = [];
		if(!empty($templateResponse[0])){
			foreach($templateResponse as $val){
				$val['checked_defualt'] = 0;
				$val['checked_defualt_radio'] = 0;
				$val['user_type'] = $getPatientDetails->type;
				$val['agency_form_id'] = $data['document_all_details'][0]->agency_form_id;
				$val['page'] = $finalResponse[$val['id']]['page']??$val['page'];
				if($val['type'] =="checkbox" || $val['type'] =='radio'){
					$val['checked'] = $finalResponse[$val['id']]??'';
				}elseif($val['type'] =="image"){
	
					$val['image'] = $finalResponse[$val['id']]['text']??'';
					$val['text'] = $finalResponse[$val['id']]['text']??'';
				}elseif($val['type'] =="stamp"){
					if($flagImage == 1){
						$xpos = $finalResponse[$val['id']]['xPos']??'';
						$yPos = $finalResponse[$val['id']]['yPos']??'';
						$val['xPos'] = $xpos;
						$val['yPos'] = $yPos;
					}
					
					$val['image'] = $finalResponse[$val['id']]['text']??'';
					$val['width'] = $finalResponse[$val['id']]['width']??'';
					$val['height'] = $finalResponse[$val['id']]['height']??'';
				}else{
					$val['text'] = $finalResponse[$val['id']]['text']??'';
				}
				$final_array[] = $val;

				$max[] = $val['page'];
				$maxs = max($max);
				
				if($val['type'] =='checkbox' || $val['type'] =='radio'){
					$editOldResponse[$val['id']] = $val['checked'];
				}else{
					if($val['type'] =='image' || $val['type'] =='stamp'){
						$editOldResponse[$val['id']] = basename($val['image']);
					}else{
						$editOldResponse[$val['id']] = $val['text'];
					}
				}
				$signInsert[] = $val;
			}
		}
		
		$intakeArray = $subIntakeArray;
		
		$data['templateFields'] = json_encode($templateResponse, true);
		$data['Signinsert'] = json_encode($signInsert, true);
		$data['rand'] = rand(0000, 9999);
		$data['removeScript'] = 'docusign';
		$data['main_intakeId'] = $main_intake;
		$data['max'] = $maxs;
		$data['LookUpResponses'] = json_encode($intakeArray);
		$data['stamp_user'] = $this->stampService->getStampUser();
		$data['login_id'] = $auth->id??"";
		/** signer error first */
		return view('docusign.caregiveresign.edit_pdf_data', $data);
	}

	public function DocusignFormSubmitUpdate(Request $request)
	{
		$id = $request->input('id');
		$sessionId = $this->resolveSessionId($request->input('sessionId'));
		$action = urldecode($request->action);
	
		$documentReportId = $request->input('document_report_id');
		$groupId = $request->input('groupId');

		if (!$this->isValidRequest($id, $action, $documentReportId)) {
			return $this->errorResponse("Parameter not found.");
		}

		$preparedActions = $this->prepareActions($id, $action);
	
		$insertId = $this->saveOrUpdateDetails($id, $documentReportId, $sessionId, $preparedActions, $request->docWidth);
		$result = $this->commonEsignService->editRegeneratethepdf(
			$insertId,
			$id,
			$sessionId,
			$documentReportId,
			$groupId,
			$request->sent_on,
			$request->old_response
		);

		if ($result) {
			event(new SignerStatusUpdated($documentReportId, 'Completed', $groupId));
		}

		return $result
        ? $this->successResponse($result)
        : $this->errorResponse(self::ERROR_MSG, 500);
	}

	public function deleteEsignPatientList(Request $request)
	{
		$response = $this->documentSentReport->caregiverWiseEsignTemplateList($request->type, [$request->patient_id], 'id', 'desc');

		if (!empty($response[0])) {
			foreach ($response as $val) {
				$totalSigner = $this->documentSentReport->TotalSignerCount($val->groupId);

				$query = $this->documentSentReport->GetDetailsbyGroupId($val->groupId);
				$sentOnCount = 0;
				$completedCount = 0;
				if (!empty($query)) {
					foreach ($query as $queryVal) {
						if (!empty($queryVal->sent_on)) {
							$sentOnCount++;
						}
						if ($queryVal->status == 'Completed') {
							$completedCount++;
						}
					}
				}
				$val->sentOnCount = $sentOnCount;
				$val->completedCount = $completedCount;
				$val->signerRemaining = $totalSigner[0]->total;

				$completed_on = "";
				if ($val->completed_on != "") {
					$completed_on = date('m/d/Y h:i A', strtotime($val->completed_on));
				}
				$val->completed_on = $completed_on;
				$val->created_date = date('m/d/Y h:i A', strtotime($val->created_date));
			}
		}

		return response()->json(['status' => true, 'data' => $response]);
	}

	public function previewPdfupdate(Request $request)
	{
		$data['url'] = "";
		$getDetails = $this->documentSentReport->getDetailsByIdNew($request->id);
		$getPdfDetail = $this->documentSentReport->getDetailsByGroup($request->group_id);
		$data['getDetails'] = $getDetails;

		if (isset($getPdfDetail->main_intakeId)) {
			$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($getPdfDetail->main_intakeId);
			if (isset($getPatientDetails->agency_id)) {
				
				if (str_contains($getPdfDetail->pdf_generate, self::ESIGN_DOCUMENT_UPLOAD_PATH)) {
					if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
						$data['url'] = url(self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $getPdfDetail->pdf_generate);
					} else {
						$data['url'] = Storage::disk('s3')->temporaryUrl($getPdfDetail->pdf_generate, now()->addMinutes(60));
					}
				} else {
					if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
						$data['url'] = url(self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $getPdfDetail->pdf_generate);
					} else {
						$data['url'] = Storage::disk('s3')->temporaryUrl(self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $getPdfDetail->pdf_generate, now()->addMinutes(60));
					}
				}
			}
		}

		return response()->json(['url' => $data['url']]);
	}

	public function getPatientSignatures(Request $request)
	{
		
		$loginId = $request->login_id;
		$type = $request->type;
		if($type){
			$signatures =$this->signatureUploadService->getAllDetailsTypeWise($loginId,$type);
		}else{
			$signatures =$this->signatureUploadService->getAllDetails($loginId);
		}

		foreach ($signatures as $signature) {
			$signature_name="";
			if($signature->signature_name !=""){
				$signature_name=$signature->signature_name;
			}

			$signature->signature_name = $signature_name;
			$signature->file_upload = $this->getAwsServerImages($signature->id,$signature->type);
		}

		return response()->json([
			'status' => true,
			'data' => $signatures,
		]);
	}

	public function deleteSignature(Request $request)
	{
		$signatureId = $request->signature_id;

		$signature = $this->signatureUploadService->findById($signatureId);

		if ($signature) {
			$signature->delete();

			return response()->json(['status' => true]);
		}

		return response()->json(['status' => false, 'message' => 'Signature not found']);
	}

	public function getAwsServerImages($id,$type="")
	{

		$getDetails = $this->signatureUploadService->findById($id);
		if($type !=""){
			if(isset($getDetails->file_upload)){
				return $this->docusingImagesShow($getDetails->file_upload);
			}
		}else{

			if(isset($getDetails->file_upload)){
				return $this->docusingImagesShowpatientWriteDocument($getDetails->file_upload);
			}
		}
	}

	public function docusingImagesShow($image){
		$file = public_path('/') .self::ESIGN_DOCUMENT_UPLOAD_PATH.'/'.$image;
		if(file_exists($file)){
			return URL::to('/').'/'.self::ESIGN_DOCUMENT_UPLOAD_PATH.'/'.$image;
		}else{
			if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
				return Storage::disk('s3')->temporaryUrl(
					self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' .  $image,
					Carbon::now()->addMinutes(10)
				);
			
			}
		}
	}
	
	public function updateDocumentPatient(Request $request)
    {
		$auth = auth()->user();
        $document_key = $request->document_key;
		$attachment = $request->file_name;
		$existingFile = "";
        $writeDocumentDataUpdated = $this->documentSentReport->getWriteDataByUniqueId($document_key);

        if ($writeDocumentDataUpdated && $writeDocumentDataUpdated->file_upload != '') {
            $fileName = $writeDocumentDataUpdated->file_upload;

            if (env('FILE_UPLOAD_PERMISSION') == 'development') {
                $existingFile = public_path(self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/' . $fileName);
                $existingFileData = file_get_contents($existingFile);

				$dirName = dirname($fileName);
				$destinationFile = public_path('patientdocument/').$dirName;
				
				if (!File::exists($destinationFile)) {
					File::makeDirectory($destinationFile, 0777, true, true);
				}

                file_put_contents(public_path('patientdocument/').'/'.$fileName, $existingFileData);
                $attachment =$fileName;
            } else {
				$fileGetContain = Storage::disk('s3')->get('/'.self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/'.$fileName);
				Storage::disk('s3')->put('patientdocument/'.$fileName, $fileGetContain);
				$attachment = $fileName;
            }
        }
        if ($attachment) {
			if($writeDocumentDataUpdated->type =='Document'){
				$oldResponse = $this->documentPatientService->getDocumentDetailsById($writeDocumentDataUpdated->document_patient_id);
				$this->documentPatientService->update([
					'attachment' => $attachment,
					'sign_stamp_status'=> 1,
				],array('id'=>$writeDocumentDataUpdated->document_patient_id));
				$newResponse = $this->documentPatientService->getDocumentDetailsById($writeDocumentDataUpdated->document_patient_id);
				$message = $auth->first_name.' '.$auth->last_name.' has added a stamp to the document.';
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Add Stamp',
					'link' => url('/esign/update-document-patient'),
					'module' => self::MODULE_TYPE,
					'object_id' => $oldResponse->patient_id,
					'message' =>$message,
					'new_response' => serialize($newResponse->toArray()),
					'old_response' =>serialize($oldResponse->toArray()),
					'ip' => $ipaddress,
					
				];
				LogsService::save($insertLog);

				// Document Workflow: auto-trigger completed after stamp is applied
				try {
					$this->documentWorkflowService->markAsCompleted($writeDocumentDataUpdated->document_patient_id);
				} catch (\Throwable $th) {
					// Silently fail to not break stamp process
				}
			}

        }
		if (env('FILE_UPLOAD_PERMISSION') != 'development') {

			if($existingFile !=""){
				if(file_exists($existingFile)){
					unlink($existingFile);
				}
			}

		}
        return response()->json(['status' => 'success']);
    }

	public function undoEsignData(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        }else{
			$getDetails = $this->documentSentReport->getDetailsByIdNew($request->id);
			$getAllDocDetails = $this->documentSentReport->GetDetailsbyGroupId($getDetails->groupId);
			$ids = [];
			foreach($getAllDocDetails as $docDetails){
				$ids[] = $docDetails->id;
			}
			$index = array_search($request->id, $ids);
			foreach($getAllDocDetails as $key=>$docDetails){
				if($index < $key && $docDetails->status != ''){
					
					$this->documentSentReport->update(array('status' => '','document_submit_status'=>0,'pdf_generate' => null,'sourceFile' => null,'old_pdf'=>$getDetails->pdf_generate,'document_response_id'=>null,'rejected_date'=>date(self::DATE_FORMAT_YMD),'rejected_by'=>auth()->user()->id),array('id' => $docDetails->id));
					$this->docusignDetailService->update(array('del_flag' => 'Y'),array('document_report_id' => $docDetails->id));

				}
			}
			
			$this->documentSentReport->update(array('status' => 'Pending','document_submit_status'=>0,'pdf_generate' => null,'old_pdf'=>$getDetails->pdf_generate,'document_response_id'=>null,'rejected_date'=>date(self::DATE_FORMAT_YMD),'rejected_by'=>auth()->user()->id),array('id' => $request->id));
			$this->docusignDetailService->update(array('del_flag' => 'Y'),array('document_report_id' => $request->id));
			$message ='';
			
			$getTemplateDetails = $this->templateService->getDetailsById($getDetails->templete_id);
			$message = "";
			if(isset(auth()->user()->id)){
				$message = auth()->user()->first_name . ' ' . auth()->user()->last_name . " has performed an undo action on the E-Sign data using the ".$getTemplateDetails->template_name;
			}

			$insertLog = [
				'type' => 'Esign Reverted ',
				'link' => url('/esign/undo-esign-data'),
				'module' => 'Esign Section',
				'module_id' => $getDetails->groupId,
				'is_status' =>"Revert",
				'message'=>$message,

			];
			$this->dynamicFormLogService->storeFormLog($insertLog);

			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Esign Reverted',
				'link' =>url('esign/undo-esign-data'),
				'module' => self::MODULE_TYPE,
				'object_id' => $getDetails->main_intakeId,
				'message' => $message,
				'ip' => $ipaddress,
			];

			LogsService::save($insertLog);

			return response()->json(['error_msg' => 'Data Undone Successfully', 'status' => 1, 'data' => array()]);
		}
	}

	public function generatePatientEsignLink(Request $request){
		$validator = Validator::make($request->all(), [
			'groupId' => 'required',
			
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			$query = $this->documentSentReport->getGroupPending($request->groupId);
			if(isset($query->id)){
				$link = URL::to(self::SMS_ESIGN_LINK) . '/' . $query->id . '?id=' . $request->groupId;
				return response()->json(['error_msg' => "", 'status' => 0, 'data' => array('link'=>$link)], 200);
			}else{
				
				return response()->json(['error_msg' => "E-signature has already been completed.", 'status' => 0, 'data' => array()], 200);
			}
			
		}
	}

	public function updateSignatureName(Request $request){

		$validator = Validator::make($request->all(), [
            'signature_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        }else{
			$getSignature = signatureUpload::where('id',$request->id)->first();
			signatureUpload::where('id',$request->id)->update(array('signature_name'=>$request->signature_name));
			$ipaddress = Utility::getIP();
			$user = auth()->user();
			$insertLog = [
				'type' => 'Signature Name Update',
				'link' => url('/esign/update-signature-name'),
				'module' => self::MODULE_TYPE,
				'object_id' => $request->main_intakeId,
				'message' => $user->first_name . ' ' . $user->last_name . ' has update signature name',
				'old_response'=>serialize($getSignature->toArray()),
				'new_response' => serialize(array('id'=>$request->id,'signature_name'=>$request->signature_name)),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			return response()->json(['error_msg' => "", 'status' => 0, 'data' => array('signature_name'=>$request->signature_name)], 200);
		}
	}

	public function generateQrCodeLink(Request $request){
		$query = $this->documentSentReport->findEsignDocumentById($request->groupId,$request->id);
		if (isset($query->id)) {
			$link = URL::to(self::SMS_ESIGN_LINK) . '/' . $query->id . '?id=' . $request->groupId;
			$qrCodeHtml = '<div style="text-align: center;">'.(string) QrCode::size(250)->generate($link).'</div>';
			return response()->json([
				'success' => true,
				'qr_html' => $qrCodeHtml,
			]);
		}
	}

	public function docusingImagesShowpatientWriteDocument($image){
		$file = public_path('/') . self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/'. $image;
	
		if(file_exists($file)){
			return URL::to('/').'/'.self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/'.$image;
		}else{
			
			if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
				return Storage::disk('s3')->temporaryUrl(
					self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/' .  $image,
					Carbon::now()->addMinutes(10)
				);
			
			}
		}
	}

	private function resolveSessionId(?string $sessionId): ?string
	{
		if (!empty($sessionId)) {
			return $sessionId;
		}

		$auth = auth()->user();
		return isset($auth->id) ? $auth->id : null;
	}

	private function isValidRequest($id, $action, $documentReportId): bool
	{
		return !empty($id) && !empty($action) && !empty($documentReportId);
	}

	private function prepareActions($templateId, $action): string
	{
		
		$templateDetails = $this->templateService->getDetailsById($templateId);
		$templateResponse = unserialize($templateDetails->response) ?? [];

		$existingResponse = [];
		foreach ($templateResponse as $vals) {
			if ($vals['type'] !== "stamp") {
				$existingResponse[$vals['id']] = $vals;
			}
		}

		$decodedActions = json_decode($action, true) ?? [];
		$finalActions = [];

		foreach ($decodedActions as $act) {
			if (isset($existingResponse[$act['id']])) {
				$act['xPos'] =$existingResponse[$act['id']]['xPos'];
				$act['yPos'] =$existingResponse[$act['id']]['yPos'];
				$act['width'] =$existingResponse[$act['id']]['width'];
				$act['height'] =$existingResponse[$act['id']]['height'];
			}

			if($act['type'] =='image'){
				$folder = basename(dirname($act['text']));
				$folderFlag = 0;
				if(strtolower($folder) =='patientwritedocument'){
					$folderFlag = 1;
				}
				$finalName = basename($act['text']);
				$act['text'] = strtok($finalName, '?');
				$act['updatedSelectType'] = $folderFlag;
			}

			if($act['type'] =='stamp'){
				$finalName = basename($act['text']);
				$act['text'] = strtok($finalName, '?');
			}
			$finalActions[] = $act;
		}

		return serialize($finalActions);

	}

	private function saveOrUpdateDetails($templateId, $documentReportId, $sessionId, $actions, $docWidth)
	{
		$subQuery = DocusignDetail::select('id','data','docWidth','del_flag')
        ->where('del_flag', 'N')
        ->where('document_report_id', $documentReportId)
        ->where('updated_flag', 1)
        ->get();

		if ($subQuery->isEmpty()) {
			return $this->docusignDetailService->save([
				'updated_flag' => 1,
				'document_report_id' => $documentReportId,
				'template_id' => $templateId,
				'user_id' => $sessionId,
				'data' => $actions,
				'docWidth' => $docWidth
			]);
		}

		$getDetails = $this->docusignDetailService->getDetailsByUpdateFlag($documentReportId);

		$this->docusignDetailService->update([
			'updated_flag' => 1,
			'data' => $actions,
			'docWidth' => $docWidth,
			'old_response' => $getDetails->data
		], [
			'id' => $getDetails->id
		]);

		return $getDetails->id;
	}

	private function successResponse($id)
	{
		return response()->json([
			'status' => "1",
			'error_msg' => "Success.",
			'data' => [['id' => $id]]
		]);
	}

	private function errorResponse($message, $code = 200)
	{
		return response()->json([
			'status' => "0",
			'error_msg' => $message,
			'data' => []
		], $code);
	}

	public function viewEsignLog(Request $request){
		$module = "Esign Section";
		$logs = $this->dynamicFormLogService->getDetailsById($request->id, $module);
		$logs->old_response = unserialize($logs->old_response);
		
		$logs->new_response = unserialize($logs->new_response);

		if(gettype($logs->new_response) =='object'){
			$logs->new_response = [$logs->new_response];
		}
	
		return response()->json(['data' => $logs]);
	}

	public function viewEsignResponseLog(Request $request){
		$module = "Esign Section";
		$logs = $this->dynamicFormLogService->getDetailsById($request->id, $module);
		$logs->old_response =unserialize($logs->esign_old_response);
	
		$logs->new_response = [unserialize($logs->esign_new_response)];
		return response()->json(['data' => $logs]);
	}

	public  function orientation($width, $height) {
        if ($width > $height) {
            return "L";
        }else{
            return "P";
        }
    }

	public function getDocumentSentReportData(Request $request){
		$auth = auth()->user();
		$data['document_all_details'] = $this->documentSentReport->getAllGroupIdData($request->groupId);

		$finaldocumentId = [];
		$tempId = "";
		
		foreach ($data['document_all_details'] as $details) {
			$finaldocumentId[] = $details->id;
			$tempId = $details->templete_id;
		}
		
		$responseData = DocusignDetail::select('id','data','docWidth','del_flag')->where('del_flag', 'N')->whereIn('document_report_id',$finaldocumentId)->where('updated_flag',1)->get();
		$docId = [];
		if(!empty($responseData[0])){
			$docId[] = $responseData[0]->id;
		}else{
			$responseData = DocusignDetail::select('id')->where('del_flag', 'N')->whereIn('document_report_id',$finaldocumentId)->get();
			foreach($responseData as $val){
				$docId[] = $val->id;
			}
		}
		$responseData = DocusignDetail::select('id','data','docWidth','del_flag')->where('del_flag', 'N')->whereIn('id',$docId)->get();

		$sourceFile = $this->templateService->getDetailsById($tempId);
		
		$data['template_id'] = $tempId;
		$data['document_report_id'] =$finaldocumentId[count($finaldocumentId) - 1];
		$templateResponse = unserialize($sourceFile->response);

		$existingIds = [];
		$finalResponse = [];
		$finalResponseImages = [];
	
		if(!empty($responseData[0])){
			foreach($responseData as $dat){
				$finalResponseImages[]=unserialize($dat->data);
			}
		}

		$finalAll = [];
		if(!empty($responseData[0])){
			foreach($responseData as $dat){
				$response = unserialize($dat->data);
		
				$finalAll['new'] =$response;
				if(!empty($response[0])){
					foreach($response as $vs){
						$explodeId = explode('_',$vs['id']);
						if(isset($explodeId[0]) && $explodeId[0] =='datesigned'){
							if(isset($vs['text']) && $vs['text'] ==""){
								$vs['text'] = Utility::convertMDY($existingDate);
							}
						}
						$flag=0;
						if($vs['type'] =='image' && isset($vs['text'])){
							$text = $vs['text'];
							$flag = 0;
							$img  = '';
							if (preg_match(self::IMAGE_STAMP_PREG_MATCH, $text)) {

								$base64Data   = substr($text, strpos($text, ',') + 1);
								$decodedImage = base64_decode($base64Data);
						
								if ($decodedImage !== false) {
						
									$filename = uniqid() . time() . '.png';
						
									if (env('FILE_UPLOAD_PERMISSION') != 'development') {
										Storage::disk('s3')->put(self::ESIGN_DOCUMENT_UPLOAD_PATH . '/' . $filename, $decodedImage);
									} else {
										$destination = public_path('/') . self::ESIGN_DOCUMENT_UPLOAD_PATH . '/' . $filename;
										file_put_contents($destination, $decodedImage);
									}
						
									$img  = $filename;
									$flag = 0;
								}
						
							}else{
								$cleanUrl = str_replace(
									'https://nybest-data.s3.us-east-2.amazonaws.com/' . self::ESIGN_DOCUMENT_UPLOAD_PATH . '/',
									'',
									$text
								);

								$img = strtok(basename($cleanUrl), '?');
								if (isset($vs['updatedSelectType'])) {
									$flag = ($vs['updatedSelectType'] == 1) ? 1 : 0;
								} else {
									if (strpos($cleanUrl, env('HOST_WEB_URL')) !== false ||
										str_contains($cleanUrl, 'patientWriteDocument')
									) {
										$flag = 1;
									} else {
										$flag = 0;
									}

								}
							}
							$vs['text'] = ($flag == 1)
							? $this->docusingImagesShowpatientWriteDocument($img)
							: $this->docusingImagesShow($img);
							
						
						}
						
						if($vs['type'] =='stamp'){
							if (preg_match(self::IMAGE_STAMP_PREG_MATCH, $vs['text']??"", $matches)) {
								$text = $vs['text']??"";
								$base64Data = substr($text, strpos($text, ',') + 1);
								$decodedImage = base64_decode($base64Data);
								if ($decodedImage !== false) {
									$filename = uniqid().''.time().'.png';
									if(env('FILE_UPLOAD_PERMISSION')  != 'development'){
										Storage::disk('s3')->put(self::ESIGN_DOCUMENT_UPLOAD_PATH.'/'.$filename, $decodedImage);
									}else{
										$destination = public_path('/').self::ESIGN_DOCUMENT_UPLOAD_PATH.'/'.$filename;
										file_put_contents($destination,$decodedImage);
									}
									
									$img = $filename;
								}
								
							}else{
								$text = $vs['text']??"";
								if(env('FILE_UPLOAD_PERMISSION')  != 'development'){

									if (strpos($text, env('HOST_WEB_URL').self::ESIGN_DOCUMENT_UPLOAD_PATH) !== false) {
										$img = strtok(basename($text), '?');
									}else{
										if (str_contains($vs['text'], 'patientWriteDocument')) {
											$img = strtok(basename($text), '?');
											$flag=1;
										}else{
											$img = strtok(basename($text), '?');
											$flag=0;
										}
									}
								}else{
									if (strpos($text, env('HOST_WEB_URL').self::ESIGN_DOCUMENT_UPLOAD_PATH) !== false) {
										$img = strtok(basename($text), '?');
									}else{
										if (str_contains($vs['text'], 'patientWriteDocument')) {
											$img = strtok(basename($text), '?');
											$flag=1;
										}else{
											$img = strtok(basename($text), '?');
											$flag=0;
										}
									}
								}
							}

							if($flag ==1){
								$vs['text'] = $this->docusingImagesShowpatientWriteDocument($img);
							}else{
								$vs['text'] = $this->docusingImagesShow($img);
							}
						}
						
						if(!in_array($vs['id'],$existingIds)){
							
							if($vs['type'] =="checkbox" || $vs['type'] =='radio'){
							
								$finalResponse[$vs['id']] = $vs['checked'];
							}else{
								
								$width = 100;
								if(isset($vs['width'])){
									$width = $vs['width'];
								}

								$height = 100;
								if(isset($vs['height'])){
									$height = $vs['height'];
								}
								
								$temp = ['xPos'=>$vs['xPos'],'yPos'=>$vs['yPos'],'text'=>$vs['text']??"",'width'=>$width,'height'=>$height,'page'=>$vs['page']??"",'updatedSelectType'=>$vs['updatedSelectType']??0];
								$finalResponse[$vs['id']] = $temp;
							
							}
							$existingIds[] = $vs['id'];
							if($vs['type'] == "stamp"){
								$finalResponseImages[] = $vs['type'];
							}
						}else{
							if($vs['type'] =='image' || $vs['type'] == "stamp"){
								if($vs['type'] == "stamp"){
									$finalResponse[$vs['id']]['xPos'] = $vs['xPos'];
									$finalResponse[$vs['id']]['yPos'] = $vs['yPos'];
									$finalResponse[$vs['id']]['width'] = $vs['width'];
									$finalResponse[$vs['id']]['height'] = $vs['height'];
								}
								$finalResponse[$vs['id']]['text'] = $vs['text'];
							}
						}
					}
				}
			}
		}

		$flagImage = 0;
		if (isset($finalResponseImages) && isset($finalResponseImages[0]) && !empty($finalResponseImages[0])) {
			$flagImage = 1;
		}

		$data['department'] = 'mobile';
		$data['id'] = $data['document_all_details'][0]->id;
		$data['mobile_type'] = 'web';
		$data['sessionIds'] = $sessionId = $data['document_all_details'][0]->main_intakeId;
		$data['sessionId'] = $sessionId;
		$data['groupId']  = $data['document_all_details'][0]->groupId;
		
		$generatePDF = '';
		if (isset($data['document_all_details'][0]->upload_document) && $data['document_all_details'][0]->upload_document != '') {
			$generatePDF = $data['document_all_details'][0]->upload_document;
		}
		$data['document_all_details']->pdfgenerate = $generatePDF;
		$data['document_report_id'] = $finaldocumentId[count($finaldocumentId) - 1];
		$main_intake = $data['document_all_details'][0]->main_intakeId;
		$final_array = array();
		$signInsert = array();

		$max = array();
		$intakeArray = array();
		$subIntakeArray = array();
		$data['sent_on']="all";
		$data['old_pdf_data'] =$sourceFile->upload_document;
		$data['docWidth'] = $sourceFile->docWidth;
	
		$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($main_intake);
		$maxs = 1;
		$editOldResponse = [];
		
		$existingDate = $data['document_all_details'][0]->completed_on;
		if(!empty($templateResponse[0])){
			foreach($templateResponse as $key=>$val){
				$val['checked_defualt'] = 0;
				$val['checked_defualt_radio'] = 0;
				$val['user_type'] = $getPatientDetails->type;
				$val['agency_form_id'] = $data['document_all_details'][0]->agency_form_id;
				$val['page'] = $finalResponse[$val['id']]['page']??$val['page'];
				$explodeId = explode('_',$val['id']);
				if(isset($explodeId[0]) && $explodeId[0] =='datesigned'){
					
					if(isset($finalResponse[$val['id']]['text']) &&  $finalResponse[$val['id']]['text'] ==""){
						$val['text'] = Utility::convertMDY($existingDate);
						$templateResponse[$key]['text'] =Utility::convertMDY($existingDate);
			
					}else{
						$val['text'] = $finalResponse[$val['id']]['text'];
						$templateResponse[$key]['text'] = $finalResponse[$val['id']]['text'];
					}
				}
				if($val['type'] =="checkbox" || $val['type'] =='radio'){
					$val['checked'] = $finalResponse[$val['id']]??'';
				}elseif($val['type'] =="image"){
	
					$val['image'] = $finalResponse[$val['id']]['text']??'';
					$val['text'] = $finalResponse[$val['id']]['text']??'';
				}elseif($val['type'] =="stamp"){
					if($flagImage == 1){
						$xpos = $finalResponse[$val['id']]['xPos']??'';
						$yPos = $finalResponse[$val['id']]['yPos']??'';
					
						$val['xPos'] = $xpos;
						$val['yPos'] = $yPos;
					}
					
					$val['image'] = $finalResponse[$val['id']]['text']??'';
					$val['width'] = $finalResponse[$val['id']]['width']??'';
					$val['height'] = $finalResponse[$val['id']]['height']??'';
				}else{
					if($explodeId[0] !='datesigned'){
						$val['text'] = $finalResponse[$val['id']]['text']??'';
					}
				}
				
				$val['updatedSelectType'] =$finalResponse[$val['id']]['updatedSelectType']??'0';
				$final_array[] = $val;

				$max[] = $val['page'];
				$maxs = max($max);
				
				if($val['type'] =='checkbox' || $val['type'] =='radio'){
					$editOldResponse[$val['id']] = $val['checked'];
				}else{
					if($val['type'] =='image' || $val['type'] =='stamp'){
						$editOldResponse[$val['id']] = basename($val['image']);
					}else{
						$editOldResponse[$val['id']] = $val['text'];
					}
				}

				$signInsert[] = $val;
			}
		}

		$intakeArray = $subIntakeArray;
		
		$data['templateFields'] = json_encode($templateResponse, true);
		$data['Signinsert'] = json_encode($signInsert, true);
		$data['rand'] = rand(0000, 9999);
		$data['removeScript'] = 'docusign';
		$data['main_intakeId'] = $main_intake;
		$data['max'] = $maxs;
		$data['LookUpResponses'] = json_encode($intakeArray);
		$data['stamp_user'] = $this->stampService->getStampUser();
		$data['login_id'] = $auth->id??"";
		/** signer error first */
		return view('docusign.caregiveresign.edit_pdf_data', $data);
	}

	public function allUpdateResponse(Request $request){
		echo "<pre>";print_R($request->all());die();
		/***********************Update Esign Template Response***** */
		$this->updateEsignTemplateResponse($request->all());

	}

	private function updateEsignTemplateResponse($data){
		$auth = auth()->user();

		$document_key = $data['docusignKey'];
		$oldResponse = $this->documentSendService->getDetailsByIdNew($document_key);
		$writeDocumentData = $this->documentSendService->getWriteDataByIDWithoutCondition($document_key);
		$documentDetails = json_decode($data['docusignAction'], true);
		if(isset($writeDocumentData->type) && $writeDocumentData->type == 'Esign'){
			$logResponse = [];
			if(count($documentDetails) >0){
				foreach($documentDetails as $val){
					$explode = explode('_',$val['id']);
					if($val['type'] =='image'){
						$logResponse[$val['id']] = $val['image'];
					}
					if($val['type'] =='text'){
						$logResponse[$val['id']] = $val['text'];
					}
					if($val['type'] =='stamp'){
						$logResponse[$val['id']] = $val['image'];
					}
					if($explode[0] =='datesigned'){
						$logResponse[$val['id']] = date('m/d/Y');
					}
					
				}
			}
			$this->documentSendService->markDocumentAsCompleted($document_key,$writeDocumentData->file_upload);
			$documentRes = $this->documentSendService->getDetailsByIdNew($document_key);
			$newResponse = $documentRes->toArray();
			$newResponse['write_completed_by_name'] = $auth->full_name ?? '';
			// Insert form Log into Dynamic form log table
			$message = $newResponse['write_completed_by_name'].' has filled Esign document';

			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Update',
				'link' => url('/esign/write_document_send'),
				'module' => 'Esign Section',
				'module_id' => $documentRes->groupId,
				'new_response' => serialize($newResponse),
				'old_response' =>serialize($oldResponse),
				'is_status' => 'Completed',
				'message'=>$message,
				'ip_address'=>$ipaddress,
				'esign_new_response'=>serialize($logResponse),
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);

			$insertLog = [
				'type' => 'Filled Document',
				'link' => url('/esign/write_document_send'),
				'module' => self::MODULE_TYPE,
				'object_id' => $documentRes->main_intakeId,
				'message' =>$message,
				'new_response' => serialize($newResponse),
				'old_response' =>serialize($oldResponse),
				'ip' => $ipaddress,
				
			];
			LogsService::save($insertLog);

			// Get Group wise notification
			$signerAction = $this->getSignerAction($documentRes->sent_on);
			$patientId = $documentRes->main_intakeId ?? '';
			$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($patientId);
			$agencyFk = $getPatientDetails->agency_id ?? '';
			$userType = $getPatientDetails->type ?? '';
			$templeteName =  $writeDocumentData->document_name ?? '';
			$title = 'Esign '.'('.$signerAction.')';
			$msg = '<br><b>Document Name : </b>'.($templeteName).' ('.date('m/d/Y h:i A',strtotime($documentRes->created_date)).')';

			$this->notificationSend('Esign',$title,$msg,$patientId,$agencyFk,$userType);
		}
	}

	public function notificationSend($type,$title,$msg,$patientId,$agencyFk,$userType,$userData=[],$serviceData=[]){
        $userData = Utility::getGroupUsersData($agencyFk,$userType,$type,$userData,$serviceData);
		$notificationData = array(
			'users' => $userData,
			'agency_fk' => $agencyFk ?? '',
			'record_id' => $patientId ?? '',
			'title' => $title,
			'msg' => $msg,
			'type' => $type
		);
		Utility::insertNotificationsType($notificationData);

    }

	public function updateDocumentPatientOld(Request $request)
    {
        $document_key = $request->document_key;
        $attachment = null;

        $writeDocumentDataUpdated = $this->documentSentReport->getWriteDataByUniqueId($document_key);

        if ($writeDocumentDataUpdated && $writeDocumentDataUpdated->file_upload != '') {
            $fileName = $writeDocumentDataUpdated->file_upload;

            if (env('FILE_UPLOAD_PERMISSION') == 'development') {
                $existingFile = public_path(self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/' . $fileName);
                $existingFileData = file_get_contents($existingFile);
                $destinationFile = public_path('patientdocument/' . $fileName);
                file_put_contents($destinationFile, $existingFileData);
                $attachment =$fileName;
            } else {
				$fileGetContain = Storage::disk('s3')->get('/'.self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH.'/'.$fileName);
				Storage::disk('s3')->put('patientdocument/'.$fileName, $fileGetContain);
				$attachment = $fileName;
            }
        }
        if ($attachment) {
            $this->documentSentReport->updateDocumentPatient($writeDocumentDataUpdated->document_patient_id, $attachment);
			
        }

        return response()->json(['status' => 'success']);
    }

	public function editSign(Request $request, $id)
	{
		$getDocumentDetails = $this->documentSentReport->findEsignDocumentById($request->groupId, $id);
		if (isset($getDocumentDetails->id)) {
			$completedSigners = $this->completedDocumentSignerListWithCurrentId($getDocumentDetails->id, $request->groupId);

			$completedSignersSentOn = array_column($completedSigners, 'sent_on');
			$completedSignersSentOn[] = strtolower($getDocumentDetails->sent_on);
			$templateResponse = $this->templateResponse($getDocumentDetails->templete_id);

			$getDocumentDetails->upload_document = $templateResponse->upload_document;
			$getDocumentDetails->source = $templateResponse->upload_document;
			$data['document_all_details'] = $getDocumentDetails;

			$docIds = array_column($completedSigners, 'id');
			$docIds[] = $id;
	
			$getExistingResponse = $this->docusingResponse($docIds, $id);

			$data['document_all_details']->stamp_docWidth = $getExistingResponse['docWidth'];

			$data['document_all_details']->docWidth = $templateResponse->docWidth;
			$mergeRecord = $this->mergeRecord($getExistingResponse['response'], unserialize($templateResponse->response));

			$data['signInsert'] = json_encode($mergeRecord['response'], true);

			$data['lookUpResponses'] = json_encode($mergeRecord['response']);
			$data['sessionIds'] = $sessionId = ($data['document_all_details']->caregiver_code != "") ? $data['document_all_details']->caregiver_code : $data['document_all_details']->main_intakeId;
			$data['sessionId'] = $sessionId;
			$data['max'] = $mergeRecord['max'];
			$data['sent_on'] = $data['document_all_details']->sent_on;
			$data['sent_on1'] = $completedSignersSentOn;
			$data['rand'] = rand(0000, 9999);
			$data['templateFields'] = json_encode($mergeRecord['response'], true);
			$data['id'] = $data['document_all_details']->id;
			$data['docusign_id'] = $getExistingResponse['docusign_id'];

			return view('docusign.caregiveresign.record_wise_esign', $data);
		} else {
			abort(404);
		}
	}

	protected function docusingResponse($docIds, $currentDocumentId)
	{
		$responses = $this->getDocusignResponseDetails($docIds);
	
		$responsesArray = $responses->toArray();
	
		$finalResponse = [];
		$docWidth = "";
		$docusign_id = "";
		$existingIds = [];
		if (!empty($responsesArray[0])) {
			foreach ($responsesArray as $response) {
				if ($response['document_report_id'] == $currentDocumentId) {
					$docusign_id = $response['id'];
				}
				
				$this->mergeResponseData($response, $finalResponse, $existingIds);

				if (!empty($response['docWidth'])) {
					$docWidth = $response['docWidth'];
				}
			}
		}


		return ['response' => $finalResponse, 'docWidth' => $docWidth, 'docusign_id' => $docusign_id];
	}

	protected function templateResponse($templateId)
	{
		return $this->templateService->getDetailsById($templateId);
	}

	protected function mergeRecord($esignResponse, $templateResponse)
	{
		$final = [];
		$max = array();
		$maxs = 1;

		if (!empty($templateResponse[0])) {
			foreach ($templateResponse as $val) {

				if (isset($esignResponse[$val['id']])) {
					$max[] = $val['page'];
					$maxs = max($max);
					$val['text'] = $esignResponse[$val['id']]['text'] ?? "";
					$val['checked'] = $esignResponse[$val['id']]['checked'] ?? "";
					$val['updatedSelectType'] = $esignResponse[$val['id']]['updatedSelectType'] ?? 0;
					if ($val['type'] == 'stamp') {
						$val['width'] = $esignResponse[$val['id']]['width'];
						$val['height'] = $esignResponse[$val['id']]['height'];
						$val['xPos'] = $esignResponse[$val['id']]['xPos'];
						$val['yPos'] = $esignResponse[$val['id']]['yPos'];
						$val['text'] = EsignHelper::getEsignImagesStorage($val['text'], $val['updatedSelectType']);
					}

					if ($val['type'] == 'image') {
						$val['text'] = EsignHelper::getEsignImagesStorage($val['text'], $val['updatedSelectType']);
					}
					$final[] = $val;
				}
				
			}
		}

		return ['max' => $maxs, 'response' => $final];
	}

	public function updateEsignFormSubmit(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'docusign_id' => 'required',
			'id' => 'required',
			'sessionId' => 'required',
			'document_report_id' => 'required',
			'groupId' => 'required',
			'sent_on' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			$templateResposne = $this->templateResponse($request->id);
			$prepareArray = $this->parepareXAndYPos(unserialize($templateResposne->response), json_decode(urldecode($request->action), true));

			$actions = serialize($prepareArray);
			$getDocumentDetails = $this->documentSentReport->findEsignDocumentById($request->groupId, $request->document_report_id);
			$getExistingResponse = $this->getDocusignResponseDetails([$request->document_report_id]);
			$saveResponse = [
				'docusign_id' => $request->docusign_id,
				'document_id' => $request->document_report_id,
				'old_pdf' => $getDocumentDetails->pdf_generate,
				'old_response' => $getExistingResponse[0]->data,
				'new_response' => $actions
			];

			$save = $this->docusignRecordResponsesLogService->save($saveResponse);
			if ($save) {
				/********Update docusign response */
				$docusignUpdateDetails = [
					'data' => $actions,
					'old_response' => $getExistingResponse[0]->data
				];

				if (!empty($request->docWidth)) {
					$docusignUpdateDetails['docWidth'] = $request->docWidth;
				}

				$completedSigners = $this->completedDocumentSignerListWithCurrentId($request->document_report_id, $request->groupId);
				$docIds = array_column($completedSigners, 'id');
				$docIds[] = $request->document_report_id;
				$getExistingResponse1 = $this->docusingResponse($docIds, $request->document_report_id);

				$this->docusignDetailService->update($docusignUpdateDetails, array('id' => $request->docusign_id, 'document_report_id' => $request->document_report_id));
				$finalResponseFields = [
					'insert' => $request->docusign_id,
					'id' => $request->id,
					'sessionId' => $request->sessionId,
					'document_report_id' => $request->document_report_id,
					'groupId' => $request->groupId,
					'sent_on' => $request->sent_on,
					'submitType' => "edit",
					'existingOldResponse' => $getExistingResponse1['response']
				];

				$return = $this->commonEsignService->regeneratethepdf($finalResponseFields);
			
				$data = array(
					'id' => $return
				);
				return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => array($data)]);
			} else {
				return response()->json(['error_msg' => self::ERROR_MSG, 'status' => 0, 'data' => array()], 500);
			}
		}
	}

	protected function parepareXAndYPos($templateResponse, $postResponse)
	{

		[$templateArray, $templateXYPosArray] = $this->buildTemplateMaps($templateResponse);

		$actionsTemp = [];
		foreach ($postResponse as $obj) {
			$this->applyImageDimensions($obj, $templateArray);
			
			$this->applyXYPositions($obj, $templateXYPosArray);
			$this->processImageType($obj);
			$this->processStampType($obj);

			$actionsTemp[] = $obj;
		}
	
		return $actionsTemp;
	}

	protected function buildTemplateMaps($templateResponse)
	{
		$templateArray = [];
		$templateXYPosArray = [];

		foreach ($templateResponse as $val) {

			if ($val['type'] === 'image') {
				$templateArray[$val['id']] = $val;
				continue;
			}

			if (!in_array($val['type'], ['image', 'stamp'])) {
				$templateXYPosArray[$val['id']] = $val;
			}
		}

		return [$templateArray, $templateXYPosArray];
	}

	protected function applyImageDimensions(&$obj, $templateArray)
	{
		
		if (
			!isset($templateArray[$obj['id']]) ||
			$templateArray[$obj['id']]['width'] === $templateArray[$obj['id']]['height']
		) {
			return;
		}

		$obj['width']  = $templateArray[$obj['id']]['width'];
		$obj['height'] = $templateArray[$obj['id']]['height'];
	}

	protected function applyXYPositions(&$obj, $templateXYPosArray)
	{
		if (!isset($templateXYPosArray[$obj['id']])) {
			return;
		}

		$obj['xPos'] = $templateXYPosArray[$obj['id']]['xPos'];
		$obj['yPos'] = $templateXYPosArray[$obj['id']]['yPos'];
	}

	protected function processImageType(&$obj)
	{
		if ($obj['type'] !== 'image') {
			return;
		}

		$folder = strtolower(basename(dirname($obj['text'])));
		$obj['updatedSelectType'] = ($folder === 'patientwritedocument') ? 1 : 0;

		$obj['text'] = strtok(basename($obj['text']), '?');
	}

	protected function processStampType(&$obj)
	{
		if ($obj['type'] !== 'stamp') {
			return;
		}

		$obj['text'] = strtok(basename($obj['text']), '?');
		$obj['updatedSelectType'] = 0;
	}

	protected function completedDocumentSignerListWithCurrentId($id, $groupId)
	{
		$getAllDocumentSigner = $this->documentSentReport->getCompletedDocumentSignersExcludingCurrentId($id, $groupId);
		$final = [];
		if (!empty($getAllDocumentSigner[0])) {
			foreach ($getAllDocumentSigner as $doc) {
				$temp = [];
				$temp['id'] = $doc->id;
				$temp['sent_on'] = strtolower($doc->sent_on);
				$final[] = $temp;
			}
		}

		return $final;
	}

	private function getDocusignResponseDetails($docIds)
	{
		return $this->docusignDetailService->getDetailsByDocumentReportIds($docIds);
	}

	protected function mergeResponseData($response, array &$finalResponse, array &$existingIds)
	{
		$data = unserialize($response['data']);

		if (empty($data)) {
			return;
		}

		$tempText = [];

		$cleanedArray = [];
		foreach ($data as $val) {
			$existingIds[$val['id']][] =  $val['text'] ?? '';

		}
		foreach ($existingIds as $key => $values) {
			// Remove empty values
			$filtered = array_filter($values, function ($val) {
				return !is_null($val) && $val !== '';
			});

			// Re-index array (optional but recommended)
			$filtered = array_values($filtered);

			// Only keep non-empty arrays
			if (!empty($filtered)) {
				$cleanedArray[$key] = end($filtered);
			}
		}

		foreach ($data as $val) {
			
			$finalResponse[$val['id']] = [
				'type'              => $val['type'],
				'xPos'              => $val['xPos'],
				'yPos'              => $val['yPos'],
				'width'             => $val['width'],
				'height'            => $val['height'],
				'text'              => $cleanedArray[$val['id']] ?? '',
				'checked'           => $val['checked'] ?? '',
				'updatedSelectType' => $val['updatedSelectType'],
				'bold'              => $val['bold'] ?? '',
				'temp1'              => $val['temp1'] ?? '',
				'temp2'              => $val['temp2'] ?? '',
				'temp3'              => $val['temp3'] ?? '',
			];
		}
	}

	private function parepareLogDetailResponse($logs)
	{
		$newResponse = [];
		if (!empty($logs[0])) {
			foreach ($logs as $log) {
				$this->prepareBaseLogFields($log);
				$this->prepareUserFields($log);
				$this->prepareStatusSpecificFields($log);
				unset($log->userDetails);
				$newResponse[] = $log;
			}
		}

		return $newResponse;
	}

	private function prepareBaseLogFields($log)
	{
		$log->new_reponses = unserialize($log->new_response);
		$log->created_date = date('m/d/Y h:i A', strtotime($log->created_at));
		$log->message = $log->message ?? "";
	}

	private function prepareUserFields($log)
	{
		$log->added_by_name = isset($log->userDetails)
			? trim($log->userDetails->first_name . ' ' . $log->userDetails->last_name)
			: null;
	}

	private function prepareStatusSpecificFields($log)
	{
		$status = $log->is_status;

		$reviewStatuses = ['Approved', 'Rejected'];
		$downloadStatuses = ['Download', 'Send SMS - Email'];
		$completedStatuses = [
			'formFill',
			'sign',
			'stamp',
			'other',
			'caregiver',
			'stampUser',
			'patient',
			'OfficeStaff',
			'Form Edit'
		];

		if (in_array($status, $reviewStatuses)) {
			$log->review_by_name = $log->added_by_name;
			$log->review_date = $log->created_date;
		}

		if ($status === 'Rejected') {
			$log->pdf_status_reason = $log->new_reponses['pdf_status_reason'] ?? null;
		}

		if ($status === 'Revert') {
			$log->undo_by_name = $log->added_by_name;
			$log->is_undo_date = $log->created_date;
		}

		if (in_array($status, $downloadStatuses)) {
			$log->download_by_name = $log->added_by_name;
			$log->download_date = $log->created_date;
		}

		if (in_array($status, $completedStatuses)) {
			$log->completed_by_name = $log->added_by_name;
			$log->completed_on = $log->created_date;
			$log->completed_date = $log->created_date;
		}
	}

	public function getReturnFile(Request $request){
		$getPdfDetail = $this->documentSentReport->getDetailsByGroup($request->group_id);
		if (!isset($getPdfDetail->main_intakeId)) {
			abort(404);
		}

		if (str_contains($getPdfDetail->pdf_generate, self::ESIGN_DOCUMENT_UPLOAD_PATH)) {
			$filePath = $getPdfDetail->pdf_generate;
		} else {
			$filePath = self::ESIGN_DOCUMENT_UPLOAD_PATH . '/' . $getPdfDetail->pdf_generate;
		}

		if (env('FILE_UPLOAD_PERMISSION') == 'development') {
			$localPath = public_path($filePath);
			if (file_exists($localPath)) {
				return response()->file($localPath, [
					'Content-Type' => 'application/pdf',
					'Content-Disposition' => 'inline',
				]);
			}
			abort(404);
		} else {
			$url = Storage::disk('s3')->temporaryUrl($filePath, now()->addMinutes(60));
			return redirect($url);
		}
	}

	public function esignMoveDocumentStore(Request $request)
	{
		$user = auth()->user();
		$this->validateRequest($request);
 		$context = $this->prepareContext($request, $user);
		$pdfName = $this->handlePdfMove($request, $context);
		$data = $this->prepareDocumentData($request, $context, $pdfName);
		
	 	$insertId = $this->documentPatientService->save($data['data']);

		if (!$insertId) {
			return response()->json([
				'status' => false,
				'error_msg' => 'Sorry, something went wrong. Please try again'
			], 500);
		}
		
		$this->afterDocumentSave($request, $context, $data, $insertId);
		return response()->json(['status' => true, 'error_msg' => 'Document  successfully uploaded'], 200);
	}

	private function validateRequest($request)
	{
		$validator = Validator::make($request->all(), [
			'template_id' => 'required',
		]);

		if ($validator->fails()) {
			abort(response()->json([
				'error_msg' => $validator->errors()->first(),
				'status' => false,
			], 422));
		}
	}

	private function prepareContext($request, $user)
	{
		$context = [];

		$context['user'] = $user;
		$context['patient'] = $this->patientService->getDetailByIdNew($request->id);
		$context['writeDoc'] = $this->documentPatientService->getWriteDocData($request->esign_doc_id);
		$context['template'] = $this->documentPatientService->getTemplateData($request->template_id);
		$context['report'] = $this->documentPatientService->getDocumentSentReportData($request->group_id);

		$context['document_name'] = $request->esign_document_name ?? '';

		if ($request->template_id == 0) {
			$context['typeLog'] = 'Move document from Esign document';
			$context['messageLog'] = $user->full_name . ' has moved document from Esign document';
		} else {
			$context['typeLog'] = 'Move document from template';
			$context['messageLog'] = $user->full_name . ' has moved document from Esign template';
		}

		return $context;
	}

	private function handlePdfMove($request, $context)
	{
		if ($request->template_id == 0) {
			return $this->moveFromWriteDocument($context['writeDoc']);
		}

		return $this->moveFromTemplate($context['report']);
	}

	private function moveFromWriteDocument($writeDoc)
	{
		if (empty($writeDoc) || empty($writeDoc->file_upload)) {
			return "";
		}

		$source = public_path('/patientWriteDocument/' . $writeDoc->file_upload);

		if (file_exists($source)) {
			file_put_contents(
				public_path('patientdocument/' . $writeDoc->file_upload),
				file_get_contents($source)
			);
			return $writeDoc->file_upload;
		}
		return $this->moveFromS3('/patientWriteDocument/', $writeDoc->file_upload);
	}

	private function moveFromS3($path, $file)
	{
		if (env('FILE_UPLOAD_PERMISSION') == "development") {
			return "";
		}

		$content = Storage::disk('s3')->get($path . $file);
		Storage::disk('s3')->put('patientdocument/' . $file, $content);

		return $file;
	}

	private function moveFromTemplate($report)
	{
		if (empty($report) || empty($report->pdf_generate)) {
			return "";
		}

		$source = public_path('/dosusinguploads/docusign/' . $report->pdf_generate);

		if (file_exists($source)) {
			file_put_contents(
				public_path('patientdocument/' . $report->pdf_generate),
				file_get_contents($source)
			);
			return $report->pdf_generate;
		}

		return $this->moveFromS3('/dosusinguploads/docusign/', $report->pdf_generate);
	}

	private function prepareDocumentData($request, $context, $pdfName){
		$request_service = '';
		if (!empty($request->esign_request_service_id[0])) {
			$request_service = implode(", ", $request->esign_request_service_id);
		}

		$allCheckboxTags = $this->getAllCheckboxTags($request);
	
		$data = array(
			'document_name' => $context['document_name'],
			'attachment' => $pdfName,
			'patient_id' => $request->id,
			'request_service_id' => $request_service,
			'templete_id' => $request->template_id,
			'agency_form_id' => $request->agency_form_id,
			'internal_use' =>$allCheckboxTags['internal_use'],
			'assign_document_review' => $this->getApprovalUser($request),
			'old_attachment'=>$pdfName,
			'medication_list' => $allCheckboxTags['medication_list'],
			'insurance_elg' => $allCheckboxTags['insurance_elg'],
			'mdo_tag' => $allCheckboxTags['mdo_tag'],
			'mdo_source' => $allCheckboxTags['mdo_source'],
			'info_only' => $allCheckboxTags['esign_upload_info_only'],
		);
		$getAllStatusData = $this->getAllStatusWiseDataPrepare($request);
		$data['document_review_status'] = $getAllStatusData['document_review_status'];
		$data['document_completed_date'] = $getAllStatusData['document_completed_date'];

		return ['newResponse'=>$data,'data'=>$data];
	}

	private function getApprovalUser($request){
		return ($request->esign_document_approval == 1)
        ? $request->esign_document_approval_user_id
        : null;
	}

	private function getAllCheckboxTags($request){

		$internal_use = !empty($request->esign_internal_use_esign) && $request->esign_internal_use_esign == 1 ? 1 : 0;

		if (strtolower($request->type) == 'patient' && auth()->user()->agency_fk == '') {
			$internal_use = 1;
		}
		$medication_list = !empty($request->esign_medication_list) && $request->esign_medication_list == 1 ? 1 : 0;
		$insurance_elg = !empty($request->esign_insurance_eligibility) && $request->esign_insurance_eligibility == 1 ? 1 : 0;
		$mdo_tag = !empty($request->esign_mdo_tag) && $request->esign_mdo_tag == 1 ? 1 : 0;
		$mdo_source = !empty($request->esign_mdo_tag) && $request->esign_mdo_tag == 1 ? $request->esign_mdo_source : null;
		$esign_upload_info_only = !empty($request->esign_upload_info_only) && $request->esign_upload_info_only == 1 ? $request->esign_upload_info_only : 0;
		return ['internal_use'=>$internal_use,'medication_list'=>$medication_list,'insurance_elg'=>$insurance_elg,'mdo_tag'=>$mdo_tag,'mdo_source'=>$mdo_source,'esign_upload_info_only'=>$esign_upload_info_only];
	}

	private function getAllStatusWiseDataPrepare($request){
		
		$document_review_status = "Approved";
		if (isset($request->esign_document_review) && $request->esign_document_review == 1) {
			$document_review_status = "Pending";
		}

		$document_completed_date = null;
		if (isset($request->esign_document_completed_date) && $request->esign_document_completed_date != "") {
			$document_completed_date = date('Y-m-d', strtotime($request->esign_document_completed_date));
		}

		$info_only= 0;
		if (isset($request->esign_upload_for_info_only) && $request->esign_upload_for_info_only == 1) {
			$info_only = $request->esign_upload_for_info_only;
		}
		
		return ['document_review_status'=>$document_review_status,'document_completed_date'=>$document_completed_date,'info_only'=>$info_only];
	}

	private function afterDocumentSave($request, $context, $data, $insertId)
	{
		$this->saveServices($request, $insertId,$data);
		$this->logActivity($request, $context, $data);
		$this->sendNotifications($request, $data['data']);
		$this->saveWriteDocument($request,$insertId,$data['data']);
		$this->handleApprovals($request, $insertId);
		$this->sendUserNotificationAgencyId($request,$data['data']);

		// Document Workflow: auto-trigger signature_required when document is moved (not info_only)
		try {
			$infoOnly = $data['data']['info_only'] ?? 0;
			if ($insertId && $infoOnly != 1) {
				$this->documentWorkflowService->markAsSignatureRequired($insertId);
			}
		} catch (\Throwable $th) {
			// Silently fail to not break esign move
		}
	}

	private function saveServices($request, $documentId,$dataResponse){
		if (!empty($request->esign_document_service_id[0])) {
			foreach ($request->esign_document_service_id as $serviceId) {
				$data = [
					'patient_id' => $request->input('id'),
					'document_id' => $documentId,
					'service_id' => $serviceId,
				];

				$this->documentUploadService->save($data);
			}
		}
		DocumentHelper::updatePatientDocumentCounts($request->id,$dataResponse['data']['medication_list'],$dataResponse['data']['insurance_elg'], 0, 0,$dataResponse['data']['mdo_tag'],0);
	}

	private function logActivity($request, $context, $data){
		$auth = auth()->user();
		$ipaddress = Utility::getIP();
		$insertLogs = [
			'type' => $context['typeLog']??'',
			'link' => url('/patient/view/') . '/' . $request->id,
			'module' => self::MODULE_TYPE,
			'object_id' => $request->id,
			'new_response' => serialize($data['data']),
			'message' => $context['messageLog']??'',
			'ip' => $ipaddress,
		];

		LogsService::save($insertLogs);
		$data['data']['document_service_id'] = $request->esign_document_service_id;

		$insertLog = [
			'type' => $context['typeLog']??'',
			'link' => url('/esign-move-document'),
			'module' => 'Esign Section',
			'module_id' => $request->group_id,
			'new_response' => serialize($data['data']),
			'old_response' => '',
			'is_status' => 'Move To Document',
			'message'=>$context['messageLog']??''
		];
		$this->dynamicFormLogService->storeFormLog($insertLog);
	}

	private function sendNotifications($request,$data){
	
		$documentReportData = $this->documentPatientService->getAllDocumentSentReportData($request->group_id);
		$patientId = $request->id ?? '';

		$patient = $this->patientService->getPatientDetailsByIdWhitoutAgency($patientId);
		
		$signerAction = 'Move To Document Done';
		$title = 'Esign ' . '(' . $signerAction . ')';
		$msg = '<br><b>Document Name : </b>' . ($data['document_name'] ?? '') . ' (' . date('m/d/Y h:i A', strtotime($documentReportData->created_date)) . ')';
		
		$this->notificationSend(
        'Esign',
			$title,
			$msg,
			$request->id,
			$patient->agency_id ?? '',
			$patient->type ?? '',
			[],
			$request->esign_document_service_id ?? []
		);
	}

	private function saveWriteDocument($request,$insertId,$data){
		$newDocument  = $this->documentPatientService->getDetailsById($insertId);
		$documentApprovalUserIds = [];
		if ($request->esign_document_review == 1) {
			$documentApprovalUserIds = explode(',', $request->esign_document_approval_user_id);
		}

		if ($data['internal_use'] == 0) {
			$this->sendEmailNotificaiton($request->id,$newDocument->attachment, "", $newDocument->document_name, $data['internal_use'], $documentApprovalUserIds);
		}

		$doc_data = array(
			'doc_name' => $newDocument->document_name,
			'doc_id' => $insertId,
			'services' => $request->esign_document_service_id
		);
		if ($data['internal_use'] == 0) {
			$this->sendNotificationToUser($newDocument->patient_id, "Document", "", $doc_data);
		}

		return $this->saveWriteDocumentData('Document', $insertId, $newDocument->attachment,  $newDocument->document_name);
	}

	private function handleApprovals($request, $documentId){
		if ($request->esign_document_review == 1) {
			$users = explode(',', $request->esign_document_approval_user_id);
			foreach ($users as $user) {
				$udata = array(
					'user_id' => $user,
					'patient_id' => $request->id,
					'document_id' => $documentId
				);
				$this->multiplePatientDocApprovalService->save($udata);
			}
		}
		if (isset($request->questions)) {
			$questions = explode(',', $request->questions);
			foreach ($questions as $que) {
				$docsData = array(
					'user_id' => auth()->user()->id,
					'question_id' => $que
				);
				$this->userDocQuestionMarkedService->save($docsData);
			}
		}
	}

	private function sendUserNotificationAgencyId($request,$data){
		$getExistingRecord = $this->patientService->getDetailByIdNew($request->id);
				
		try {
			if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
				$agencyNotifyData = array(
					'agencyid' => $getExistingRecord->agency_id,
					'title' => 'Uploaded new document',
					'record_id' => $getExistingRecord->id,
					'record_type' => 'Document',
					'msg' => '',
					'res_data' => serialize($data['data']),
				);
				Common::insertAgencyNotificationsOfUser($agencyNotifyData);
			}
		} catch (\Throwable $th) {}
	}

	private function sendEmailNotificaiton($id, $image, $name, $documentName, $internal_use, $documentApprovalUserIds = [])
	{
		
		$getNewResponse = $this->patientService->getDetailById($id);
		if (isset($getNewResponse->agency_id) && $getNewResponse->agency_id != '') {

			$query = Agency::getAllDetailsbyAgencyId($getNewResponse->agency_id);

			$allemails = $this->getAgencyWithEmail($query);
			
			$name = isset($query->agency_name) ? $query->agency_name : "";
			$subject = 'Notification from NY BEST MEDICAL RESULTS ARE UPLOADED';
			$notificationType = "Document Upload";
			$messages = $this->getAllEmailDetails($getNewResponse,$name,$id,$documentName);

			$disciplineContext = [
				'patient'=>$getNewResponse,
				'notification_type'=>$notificationType,
				'subject'=>$subject,
				'message'=>$messages,
				'agency_name'=>$name,
				'image'=>$image,
				'id'=>$id,
				'internal_use'=>$internal_use
			];

			$sendEmailNotication = $this->sendEmailNotificationByDiscipline($disciplineContext);

			/*******Send Mail Notification for general user */
			$generalEmail = $this->sendEmailNotificationSerivce->sendGeneralNotificationWithEmail($getNewResponse->type, $notificationType, $subject, $messages, $name, $image, $id);
			/*************************End Send Mail Notification for general user */
			
			$sendUserEmailNotication = $this->sendUserWithEmail($disciplineContext);
		
			$assignDocumentUserMail = [];
			if (count($documentApprovalUserIds) > 0) {
				$assignUserEmail = UserHelper::getDetailsByUserids($documentApprovalUserIds);
				foreach ($assignUserEmail as $docApIds) {
					$assignDocumentUserMail[] = $docApIds->email;
				}
			}

			$this->logCheckCreate($getNewResponse,$id);

			$userEmail = $this->creatorEmailToogle($getNewResponse);
			
			
			$assignAgencyMail = $this->sendEmailNotificationSerivce->getAssignNyUserAgencyMail($getNewResponse->agency_id);
			$finalArray = array_unique(array_merge($allemails, $sendEmailNotication, $generalEmail, $sendUserEmailNotication, $assignDocumentUserMail,$assignAgencyMail,$userEmail));

			if (!empty($finalArray[0])) {

				try {
					$this->sendEmailNotificationSerivce->UserMailWithMultipleEmail($finalArray, $image, $subject, $messages, $documentName);
				} catch (\Throwable $th) {
					//throw $th; 
				}
			}
		}
	}

	private function getAllEmailDetails($patient,$agencyName,$id,$documentName){
		$locationDetails = $this->locationDetails($patient);
			
		$discipline = $patient->diciplin;

		$emailData = array(
			'agencyname' => $agencyName,
			'insert' => $id,
			'type' => $patient->type,
			'first_name' => $patient->first_name,
			'last_name' => $patient->last_name,
			'location' => $locationDetails,
			'document_name' => $documentName,
			'discipline' => $discipline,
		);
		
		return Utility::getHtmlContent('email_template.document_upload_patient', $emailData);

	}
	private function locationDetails($details){
		$location_list = $this->locationMasterService->getDetailbyId($details->location_id);
			$address1 = isset($location_list->address1) ? $location_list->address1 : "";
			$address2 = isset($location_list->address2) ? $location_list->address2 : "";
			$city = isset($location_list->city) ? $location_list->city : "";
			$state = isset($location_list->state) ? $location_list->state : "";
			$zip_code = isset($location_list->zip_code) ? $location_list->zip_code : "";

		return $address1 . ' ' . $address2 . ' ' . $city . ' ' . $state . ' ' . $zip_code;

	}

	private function sendEmailNotificationByDiscipline($disciplineContext){
		/*Rohit Panchal code*/
		$patient = $disciplineContext['patient'];
		$internal_use = $disciplineContext['internal_use'];
		$notificationType = $disciplineContext['notification_type'];
		$subject = $disciplineContext['subject'];
		$messages = $disciplineContext['message'];
		$name = $disciplineContext['agency_name'];
		$image = $disciplineContext['image'];
		$id = $disciplineContext['id'];
		
		$sendEmailNotication = [];
		if ($patient->type != "" && $patient->agency_id != "") {
			
			if ($internal_use == 0) {
				$sendEmailNotication = $this->sendEmailNotificationSerivce->sendEmailNotificationServicesDiscipline($patient->type, $notificationType, $patient->agency_id, $subject, $messages, $name, $image, $id);
			}
		}

		return $sendEmailNotication;
	}

	private function sendUserWithEmail($data){
		$sendUserEmailNotication = [];
		$patient = $data['patient'];
		$notificationType = $data['notification_type'];
		$subject = $data['subject'];
		$messages = $data['message'];
		$name = $data['agency_name'];
		$image = $data['image'];
		
		if ($patient->type != "" && $patient->created_by != "") {
			$sendUserEmailNotication = $this->sendEmailNotificationSerivce->sendEmailNotificationUserWithEmail($patient->type, $notificationType, $patient->created_by, $subject, $messages, $name, $image);
		}

		return $sendUserEmailNotication;
	}

	private function getAgencyWithEmail($query){
		$authUser = auth()->user();
		$emails = isset($query->nybest_email_notification) ? $query->nybest_email_notification : "";
		$emailss = explode(',', $emails);
		$allemails = array();
		$allemails[] = $authUser->email;
		foreach ($emailss as $ems) {
			if (trim($ems) != '') {
				if (trim($ems) != 'li@qualityny.com') {
					$allemails[] = trim($ems);
				}
			}
		}
		
		return $allemails;
	}

	private function logCheckCreate($patient,$id){
		$authUser = auth()->user();
		LogsCreateEmailCheck::insert([
			'created_at' => date(self::DATE_FORMAT_YMD),
			'created_by' => $authUser->id??'',
			'patient_id' => $id,
			'patient_created_by' => $patient->created_by??'',
			'type' => 'Document'
		]);
	}

	private function creatorEmailToogle($patient){
		$userEmail = [];
		$getCreatedUserd = UserHelper::getUserDetails($patient->created_by);
		if(isset($getCreatedUserd->creator_email_noti_toggle) && $getCreatedUserd->creator_email_noti_toggle == 1)
		{
			
			$getCreatedUserEnabledOrNot = $this->userCreatorEmailNotificationService->getAddOrNotUserEmailNotification($patient->agency_id,'Document Upload');
			if($getCreatedUserEnabledOrNot ==1){
				$userEmail = array($getCreatedUserd->email);
			}
		}
		return $userEmail;
	}

	private function sendNotificationToUser($patientId, $type, $message = "", $data = "")
	{
		$user = auth()->user();
		$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($patientId);

		if ($getPatientDetails->assign_user_id != "") {
			$userData = [$getPatientDetails->assign_user_id];
		} else {
			$query = User::select('id')->where('delete_flag', 'N')->where('agency_fk', $getPatientDetails->agency_id);
			if ($user['agency_fk'] != "") {
				$query->where('id', '!=', auth()->user()->id);
			}

			$query->whereRaw('(record_access="All" or record_access="'.$getPatientDetails->type.'")');
			$userData = $query->pluck('id')->toArray();
		
		}

		$agency_fk = $getPatientDetails->agency_id;

		$title = "";
		$msg = "";
		$serviceData = array();
		if ($type == "Document") {
			$title = "New Document Added";
			$msg = $data['doc_name'];
			$serviceData = $data['services'];
		} else if ($type == "Assign Appointment") {
			$type = "Assign Appointment";
			$title = "Assign Appointment ";
			$assignUser = User::where('id', $getPatientDetails->assign_user_id)->first();

			$msg = 'Assigned To: ' . $assignUser->full_name;
		} else if ($type == "Notes") {
			$type = "Notes";
			$title = "New Note Added";
			$msg = $message;
		}

		// Get Group wise notification
		$userData = Utility::getGroupUsersData($agency_fk, $getPatientDetails->type, $type, $userData, $serviceData);

		$notificationData = array(
			'users' => $userData,
			'agency_fk' => $agency_fk,
			'record_id' => $patientId,
			'title' => $title,
			'msg' => $msg,
			'type' => $type
		);
		Utility::insertNotificationsType($notificationData);
	}

	private function saveWriteDocumentData($type, $documentPatientId, $fileUpload, $documentName)
	{
		$data = [
			'document_name' => $documentName,
			'type' => $type,
			'document_patient_id' => $documentPatientId,
			'file_upload' => $fileUpload,
			'created_at' => date(self::DATE_FORMAT_YMD),
		];
		$this->documentSentReport->saveWriteDocumentData($data);
	}

	public function getWriteReturnFile(Request $request){
		$getPdfDetail = $this->documentSentReport->getWriteDataByID($request->group_id);
		
		if (str_contains($getPdfDetail->file_upload, self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH)) {
			$filePath = $getPdfDetail->file_upload;
		} else {
			$filePath = self::ESIGN_WRITE_DOCUMENT_UPLOAD_PATH . '/' . $getPdfDetail->file_upload;
		}

		if (env('FILE_UPLOAD_PERMISSION') == 'development') {
			$localPath = public_path($filePath);
			if (file_exists($localPath)) {
				return response()->file($localPath, [
					'Content-Type' => 'application/pdf',
					'Content-Disposition' => 'inline',
				]);
			}
			abort(404);
		} else {
			$url = Storage::disk('s3')->temporaryUrl($filePath, now()->addMinutes(10));
			return redirect($url);
		}
	}

	public function redirectionNextSigner($id){
		$query = $this->documentSentReport->getGroupPending($id);
		return redirect('esign/docusign/viewNew/'.$query->id.'?mobile_type=web');
	}
}