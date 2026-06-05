<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;
use App\Helpers\Common;
use App\Agency;
use App\Services\PatientService;
use Illuminate\Support\Facades\Storage;
use App\Services\LocationMasterService;
use App\Services\DocumentPatientService;
use App\Services\DoctorService;
use App\Services\LocationScheduleService;
use App\Services\PatientSMSLogService;
use App\Services\NyBestReminderNotificationService;
use App\Services\AssignNyBestUserService;

use App\Attachment;
use App\Record;
use App\DocumentSentReport;
use App\Model\PatientNotes;
use App\User;

use URL;
use App\Services\AppointmentImportFileService;
use App\Services\CommonLogService;
use App\Services\PatientNotesService;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use App\Helpers\EsignHelper;
use App\Services\PatientDocumentSentReportService;
use App\Template;
use App\DocumentSignerMaster;
use App\Services\StampService;
use App\Services\CommonEsignService;
use App\Services\AgencyAllFormService;
use App\Model\AgencyMaster;
use App\Helpers\AgencyAllForm;
use App\Helpers\AwsHelper;
use App\Model\Doctor;
use App\Model\WriteDocument;
use App\Services\EbookService;
use App\Services\EventService;
use App\Services\DocumentSendService;
use App\Services\DynamicFormLogService;
use Illuminate\Support\Facades\Response;
use App\Services\TextMessageService;
use App\Services\LogsService;
use App\Services\RobortService;
use App\Helpers\RobortHelper;
use Carbon\Carbon;
use App\Model\PDF;
class DownloadController extends BaseController
{ 

    protected $stampService,$commonEsignService,$agencyAllFormService,$patientService,$eventService, $ebookService,$documentSendService,$dynamicFormLogService,$textMessageService="";
    protected  $robortService;
    protected const ESIGN_PATIENT_WRITE_DOCUMENT="patientWriteDocument";
    public function __construct(PatientNotesService $PatientNotesService, AppointmentImportFileService $AppointmentImportFileService, PatientService $patientService, DocumentPatientService $DocumentPatientService, DoctorService $DoctorService, LocationMasterService $LocationMasterService, LocationScheduleService $LocationScheduleService, PatientSMSLogService $PatientSMSLogService, CommonLogService $CommonLogService, NyBestReminderNotificationService $nyBestReminder,StampService $stampService, CommonEsignService $commonEsignService,AgencyAllFormService $agencyAllFormService, EventService $eventService, EbookService $ebookService,DocumentSendService $documentSendService,DynamicFormLogService $dynamicFormLogService,TextMessageService $textMessageService,RobortService $robortService)
    {
        $this->middleware('auth', ['except' => ['AppointmentsSave', 'documentView', 'GeneratePdf', 'documentInsertView', 'documentViewNews', 'thankyou', 'patientStatus', 'AppointmentsUpdate', 'patientAppointments', 'expired', 'nyThankyou','stampImages','eventImages','ebookVideo']]);
        $this->patientService = $patientService;
        $this->DocumentPatientService = $DocumentPatientService;
        $this->DoctorService = $DoctorService;
        $this->LocationMasterService = $LocationMasterService;
        $this->LocationScheduleService = $LocationScheduleService;
        $this->PatientSMSLogService = $PatientSMSLogService;
        $this->AppointmentImportFileService = $AppointmentImportFileService;
        $this->PatientNotesService = $PatientNotesService;
        $this->CommonLogService = $CommonLogService;
        $this->nyBestReminder = $nyBestReminder;
        $this->stampService = $stampService;
        $this->commonEsignService = $commonEsignService;
        $this->agencyAllFormService = $agencyAllFormService;
        $this->eventService = $eventService;
        $this->ebookService = $ebookService;
        $this->documentSendService = $documentSendService;
		$this->dynamicFormLogService = $dynamicFormLogService;
        $this->textMessageService = $textMessageService;
        $this->robortService = $robortService;
    }


    public function showImage($id,Request $request)
    {
        $auth = auth()->user();
        $getDetails = $this->DocumentPatientService->getDetailsBydocId($id);
        if (isset($getDetails->patient_id)) {
            if($request->merge =='merge'){
                $getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($getDetails->patient_id);
            }else{
                $getPatientDetails = $this->patientService->getDetailsWithDocumentDownload($getDetails->patient_id);
            }
            

            if (isset($getPatientDetails->agency_id)) {

                $file = public_path('/') . "/patientdocument/" . $getDetails->attachment;
                $headers = [];

                $extension = pathinfo($getDetails->attachment, PATHINFO_EXTENSION);
                $rawName = ($getDetails->document_name != '')
                    ? $getDetails->document_name . ($extension ? '.' . $extension : '')
                    : $getDetails->attachment;
                $downloadName = str_replace(['/', '\\'], '-', $rawName);

                if (str_contains($getDetails->attachment, 'patientdocument')) {
                    if (file_exists($file)) {
                        return response()->download($file, $downloadName, $headers);
                    } else {
                        return Storage::disk('s3')->download($getDetails->attachment, $downloadName);
                    }
                } else {
                    if (file_exists($file)) {
                        return response()->download($file, $downloadName, $headers);
                    } else {
                        return Storage::disk('s3')->download('patientdocument/' . $getDetails->attachment, $downloadName);
                    }
                }
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function downloadAttachment($id)
    {


        $getPatientDetails = $this->patientService->getDetailByIdNew($id);
        if (isset($getPatientDetails->agency_id)) {
            $file = public_path('/') . "/patientattachment/" . $getPatientDetails->attachment_document;
            $headers = [];
            if (str_contains($getPatientDetails->attachment_document, 'patientattachment')) {
                return   Storage::disk('s3')->download($getPatientDetails->attachment_document);
                die();
            } else {
                return   Storage::disk('s3')->download('patientattachment/' . $getPatientDetails->attachment_document);
                die();
            }

            abort(404);
        } else {
            abort(404);
        }
    }
    public function esignDocusign($id)
    {
        $auth = auth()->user();

        $PatientEsignDetail = PatientDocumentSentReportService::getPdfGenerate($id);

        if (isset($PatientEsignDetail->caregiver_code)) {
            $getPatientDetails = $this->patientService->getDetailByIdNew($PatientEsignDetail->caregiver_code);
            if (isset($getPatientDetails->agency_id)) {
                $file = public_path('/') . "dosusinguploads/docusign/" . $PatientEsignDetail->pdf_generate;
                $headers = [];

                return response()->download($file, $PatientEsignDetail->pdf_generate, $headers);
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }
    public function showAttachmentForRecord($id)
    {
        $auth = auth()->user();

        $getDetails = Attachment::getDataAttachmentById($id);
        if (isset($getDetails->record_id)) {
            $getPatientDetails = Record::getDetailsByRecordidWithAgency($getDetails->record_id);
            if (isset($getPatientDetails->id)) {
                $file = public_path('/') . "/uploadedfiles/attachment/" . $getDetails->file_name;
                $headers = [];

                return response()->download($file, $getDetails->file_name, $headers);
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }
    public function esignRecord($id, Request $request)
    {
        $auth = auth()->user();

        // $hcspM11qId = env('HCSP_M11Q_ID');
       
        // $doh4359Id = env('DOH_4359_ID');
        
        $PatientEsignDetail = DocumentSentReport::getDetails($id);

        // if($PatientEsignDetail->templete_id ==$hcspM11qId){
        //     return $this->downloadMSQ(29,19,$PatientEsignDetail->main_intakeId,$PatientEsignDetail->id,$PatientEsignDetail->agency_form_id);
            
            
        // }elseif ($PatientEsignDetail->templete_id == $doh4359Id) {
        //     return $this->downloadDOHPDF(28,20,$PatientEsignDetail->main_intakeId,$PatientEsignDetail->id,$PatientEsignDetail->agency_form_id);
           
        // }else{

        // }
        
        // if (file_exists($file)) {
        //     return response()->download($file, basename($file));
        // } else {  
            
        // }
        $esignDetail = DocumentSentReport::getAllDetails($id);

        $newResponse = $esignDetail->toArray();
        $newResponse['download_by_name'] = $auth->full_name ?? '';
        $newResponse['download_date'] = date('Y-m-d H:i:s');
        // Insert form Log into Dynamic form log table
        if(isset(auth()->user()->id)){
            $message = auth()->user()->first_name . ' ' . auth()->user()->last_name . " has downloaded the Template";
        }
        $insertLog = [
            'type' => 'Download',
            'link' => url('/dre'),
            'module' => 'Esign Section',
            'module_id' => $esignDetail->groupId,
            'new_response' => serialize($newResponse),
            'old_response' => '',
            'message' => $message??'',
            'is_status'=>'Download',
        ];
        $this->dynamicFormLogService->storeFormLog($insertLog);

        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'Download',
            'link' => url('/dre'),
            'module' => 'Patient Appointment',
            'object_id' => $esignDetail->main_intakeId,
            'message' => $message??'',
            'new_response' => serialize($newResponse),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);

        if (isset($PatientEsignDetail->main_intakeId)) {
            $file = public_path('/') . "dosusinguploads/docusign/" . $PatientEsignDetail->pdf_generate;

            if (file_exists($file)) {
                $headers = [];
                return response()->download($file, $PatientEsignDetail->pdf_generate, $headers);
            } else {
                return Storage::disk('s3')->download('/dosusinguploads/docusign/' . $PatientEsignDetail->pdf_generate);
            }
        } else {
            abort(404);
        }
    }

    public function stampImages($id)
    {
        $stampDetails = $this->stampService->getDetailById($id);
        if (isset($stampDetails->id)) {
            return AwsHelper::getImagesFromAWS('stamp-image','stamp-image',$stampDetails->image);
        } else {
            abort(404);
        }
    }

    public function downloadMSQ($templateId,$formId,$patientId,$documentId,$agency_form_id){
        $hcspM11qUrl = env('HCSP_M11Q_URL');
        $file = public_path($hcspM11qUrl); 
        $formId = $formId;
        $templateId = $templateId;
        $agencyId = "";
        $patient_id = $patientId;

        $templateDetails = $this->agencyAllFormService->getTemplate($templateId, $formId);
        
        $response = unserialize($templateDetails->response);
        $SubIntakeArray = [];
        if (isset($response) && $response != '') {
            $final_array[] = $templateDetails->docWidth;
            $data['docWidth'] = $templateDetails->docWidth;
            $data['sent_on'] = "Caregiver";

            foreach ($response as $val) {
                $final_array[] = $val;
                $Signinsert[] = $val;
                $max[] = $val['page'];
                $maxs = max($max);
                
                if (isset($val['placeHolder']) && $val['placeHolder'] != '') {
                    $val['placeHolder'] = str_replace('%22', '', $val['placeHolder']);
                }

                $val['font'] =25;

                if ($val['temp1'] == 'caregiver' || $val['temp1'] == 'patient') {
                    if ($val['temp3'] != '') {
                        $subresponse = $this->caregiverFieldsResponse($formId, $patient_id, $val['temp3'], $agencyId,$agency_form_id);
                        $val['text'] = $subresponse[$val['temp3']];
                    }
                } else {
                    $dynamicDropdownId = isset($val['dynamicDropdownId']) ? $val['dynamicDropdownId'] : "";
					$dynamicDropdownIdVal = isset($val['dynamicDropdownIdVal']) ? $val['dynamicDropdownIdVal'] : "";

                    if ($dynamicDropdownId != "") {
							
                            $subresponse = $this->showOtherCheckBox($formId, $dynamicDropdownId, $patient_id, $agency_form_id);
                        
                            if (isset($val['normalValue'])) {
                                if (in_array($val['normalValue'], $subresponse)) {
                                    $val['checked'] = 1;
                                }
                            }
                    }else if ($dynamicDropdownIdVal != "") {
                        $subresponse = $this->showOtherRadio($formId, $dynamicDropdownIdVal, $patient_id, $agency_form_id);
                        if (isset($val['normalValueRadio'])) {
							if (is_array($subresponse)) {
								if (in_array($val['normalValueRadio'], $subresponse)) {
									$val['checked'] = 1;
								}
							} else {
								if ($val['normalValueRadio'] == $subresponse) {
									$val['checked'] = 1;
								}
							}
						}
                    }
                }
                $SubIntakeArray[] = $val;
            }
        }

        $this->commonEsignService->tempRegeratePdfNew($SubIntakeArray, $templateId,$hcspM11qUrl);
        // $getDetails = DB::table('docusign_detail')->where('document_report_id',$documentId)->orderBy('id','desc')->first();
        // return $this->commonEsignService->tempRegeratePdf($getDetails->id,env('HCSP_M11Q_URL'));
      

    }

    public function downloadDOHPDF($templateId,$formId,$patientId,$documentId,$agency_form_id){
        $doh4359Url = env('DOH_4359_URL');
        $file = public_path($doh4359Url);

        $formId = $formId;
        $templateId = $templateId;
        $agencyId = "";
        $patient_id = $patientId;

        $templateDetails = $this->agencyAllFormService->getTemplate($templateId, $formId);
        $response = unserialize($templateDetails->response);

        $SubIntakeArray = [];
        if (isset($response) && $response != '') {
            $final_array[] = $templateDetails->docWidth;
            $data['docWidth'] = $templateDetails->docWidth;
            $data['sent_on'] = "Caregiver";

            foreach ($response as $val) {
                $final_array[] = $val;
                $Signinsert[] = $val;
                $max[] = $val['page'];
                $maxs = max($max);
                if (isset($val['placeHolder']) && $val['placeHolder'] != '') {
                    $val['placeHolder'] = str_replace('%22', '', $val['placeHolder']);
                }
                $val['font'] =35;
                if ($val['temp1'] == 'caregiver' || $val['temp1'] == 'patient') {
                    if ($val['temp3'] != '') {
                        $subresponse = $this->caregiverFieldsResponse($formId, $patient_id, $val['temp3'], $agencyId,$agency_form_id);
                        $val['text'] = $subresponse[$val['temp3']];
                    }
                } else {
                    $val['height']=30;
                    $dynamicDropdownId = isset($val['dynamicDropdownId']) ? $val['dynamicDropdownId'] : "";
					$dynamicDropdownIdVal = isset($val['dynamicDropdownIdVal']) ? $val['dynamicDropdownIdVal'] : "";

                    if ($dynamicDropdownId != "") {
                        $subresponse = $this->showOtherCheckBox($formId, $dynamicDropdownId, $patient_id, $agency_form_id);
                        if (isset($val['normalValue'])) {
                            if (in_array($val['normalValue'], $subresponse)) {
                                $val['checked'] = 1;
                            }
                    }
                }else if ($dynamicDropdownIdVal != "") {
                    $subresponse = $this->showOtherRadio($formId, $dynamicDropdownIdVal, $patient_id, $agency_form_id);
                    if (isset($val['normalValueRadio'])) {
                        if (is_array($subresponse)) {
                            if (in_array($val['normalValueRadio'], $subresponse)) {
                                $val['checked'] = 1;
                            }
                        } else {
                            if ($val['normalValueRadio'] == $subresponse) {
                                $val['checked'] = 1;
                            }
                        }
                    }
                }
                $SubIntakeArray[] = $val;
            }
        }
        }
        $this->commonEsignService->tempRegeratePdfNew($SubIntakeArray, $templateId,$doh4359Url);
    }

    function caregiverFieldsResponse($formId="", $id, $keys, $agencyId,$agency_form_id="")
    {
        $key = $keys;
        $user_id = $id;
        $explode  = explode('@', $key);

        $finalArray = array();
        if ($explode[0] == 'fm') {
            $caregiverDetails = AgencyAllForm::GetFormDetails($formId, $explode[1], $user_id,$agency_form_id);
        } else if ($explode[0] == 'dr') {
            $caregiverDetails = AgencyAllForm::GetDoctorDetails($formId, $explode[1], $user_id,$agency_form_id);
        } else if ($explode[0] == 'ag') {
            $caregiverDetails = AgencyAllForm::GetAgencyDetails($explode[1], $agencyId);
        } else {
            $caregiverDetails = $this->patientService->GetCaregiverFormDetails($explode[1], $user_id);
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

    public function showOtherCheckBox($formId, $fieldId, $patient_id,$id)
    {
		
        $query = AgencyAllForm::GetFormDetails($formId, $fieldId, $patient_id,$id);

        $data = unserialize($query);

        $final = [];
        if(!empty($data[0])){
            foreach ($data as $val) {
                if ($val != 'null') {
                    $final[] = $val;
                }
            }
        }
        
        return $final;
    }

    public function showOtherRadio($formId, $fieldId, $patient_id,$id)
    {
        $query = AgencyAllForm::GetFormDetails($formId, $fieldId, $patient_id,$id);

        return $query;
    }
    
    public function doctorImages(Request $request,$id){
        $query = Doctor::where('id',$id)->first();
        if (isset($query->id)) {
            if($request->type =='stamp'){
               return AwsHelper::commonGetImagesFromAws('dosusinguploads/docusign',$query->stamp_upload);
            }else{
                return AwsHelper::commonGetImagesFromAws('dosusinguploads/docusign',$query->signature_upload);
            }
        } else {
            abort(404);
        }
    }
    
    public function downloadAgencyImages(Request $request){
        $query = Agency::getDetailsByAgencyId($request->id);
        return AwsHelper::getImagesFromAWS('allupload','agency-image',$query->agency_logo);
    }

    public function eventImages(Request $request,$id){
        $query = $this->eventService->getDetailById($id);
        if (isset($query->id)) {
            if($request->type =='event'){
                return AwsHelper::getImagesFromAWS('event-image','event-image',$query->image);
            }else{
                abort(404);
            }
        } else {
            abort(404);
        }
    } 

    function ebookVideo(Request $request)
	{
		$data['url'] = "";
		$query = $this->ebookService->getDetailById($request->id);
		if (isset($query->id) && $request->type =='ebook') {
            $data['url'] = "/ebook-video/" . $query->video;
            $headers = [];
            if (str_contains($query->video, 'ebook-video')) {
                $data['url'] = Storage::disk('s3')->temporaryUrl($query->video, now()->addMinutes(60));
            } else if(env('FILE_UPLOAD_PERMISSION')  != 'development') {
                $data['url'] = Storage::disk('s3')->temporaryUrl('ebook-video/' . $query->video, now()->addMinutes(60));
            }
		}
		return view('ebook._partial.view_video_iframe', $data);
	}

    public function esignRecordWriteDocument($id, Request $request)
    {
        $auth = auth()->user();

        $PatientEsignDetail =  $this->documentSendService->getWriteDataByID($id);
        $documentRes = $this->documentSendService->getDetailsByIdNew($id);
			$newResponse = $documentRes->toArray();
			// $newResponse['download_by_name'] = $auth->full_name ?? '';
            // $newResponse['download_date'] = date('Y-m-d H:i:s');
			// Insert form Log into Dynamic form log table
            if(isset(auth()->user()->id)){
                $message = auth()->user()->first_name . ' ' . auth()->user()->last_name . " has downloaded the E-Sign document";
            }
			$insertLog = [
				'type' => 'Download',
				'link' => url('/dre-write-document'),
				'module' => 'Esign Section',
				'module_id' => $documentRes->groupId,
				'new_response' => serialize(array('id'=>$PatientEsignDetail->id,'file_name'=>$PatientEsignDetail->file_upload)),
				'old_response' =>serialize($PatientEsignDetail->toArray()),
				'is_status'=>'Download',
                'message' => $message??''
                
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);

            $ipaddress = Utility::getIP();
            $insertLog = [
				'type' => 'Download',
                'link' => url('/dre-write-document'),
                'module' => 'Patient Appointment',
                'object_id' => $documentRes->main_intakeId,
                'message' => $message??'',
                'new_response' => serialize(array('id'=>$PatientEsignDetail->id,'file_name'=>$PatientEsignDetail->file_upload)),
                'old_response' => serialize($PatientEsignDetail->toArray()),
                'ip' => $ipaddress,
			];
			LogsService::save($insertLog);

            $file = public_path('/') . "patientWriteDocument/" . $PatientEsignDetail->file_upload;
            if (file_exists($file)) {
                $headers = [];
                return response()->download($file, $PatientEsignDetail->file_upload, $headers);
            } else {
                return Storage::disk('s3')->download('/patientWriteDocument/' . $PatientEsignDetail->file_upload);
            }
    }

    public function downloadTextMessageImage(Request $request){
        $auth = auth()->user();

        $query = $this->textMessageService->getDetailsIdWithoutUserRelation($request->id);
        $ipaddress = Utility::getIP();
        if(isset($query->id) && $query->id !=""){
        
            $insertLog = [
                'type' => 'Download',
                'link' => url('/dpp-text-message'),
                'module' => 'Patient Appointment',
                'object_id' => $query->case_id,
    
                'message'=>$auth->first_name . ' ' . $auth->last_name . ' has downloaded the file',
                'ip' => $ipaddress,
            ];
    
            LogsService::save($insertLog);
            $file = public_path('/') . "/twillio/" . $query->message_file;
            if (file_exists($file)) {
                $headers = [];
            return response()->download($file, $query->message_file, $headers);
            }else{
                return Storage::disk('s3')->download('/twillio/' . $query->message_file);
           
            }
        }else{
            abort(404);
        }
    }

    public function sendEmmacareDocumentReferralId(Request $request){
        if(isset($request->uid) && $request->uid !=""){
            $uploadEmmacarePortal = [
                'document'=>$request->upload_document,
                'note'=>$request->note
            ];
            $getDetails = RobortHelper::uploadDocument($request->agency_id,$uploadEmmacarePortal,$request->uid);
            $error = "";
          
            if(isset($getDetails['errors'])){
                $cnt =1;
                foreach($getDetails['errors'] as $errr){
                    $error .=$cnt.'.'.$errr[0].'<br>';
                    $cnt++;
                }

                return response()->json(['error_msg' => $error, 'data' =>array()], 500);
            }
            
            if(isset($getDetails['message'])){
                return response()->json(['error_msg' =>$getDetails['message'], 'data' =>array()], 500);
            }else{
                return 1;
            }
        }
    }

    public function sendEmmacareDocument(Request $request){
        if(isset($request->referral_id) && $request->referral_id !=""){
            $uploadEmmacarePortal = [
                'document'=>$request->upload_document,
                'note'=>$request->note
            ];
            $getDetails = RobortHelper::uploadDocument($request->agency_id,$uploadEmmacarePortal,$request->referral_id);
            $error = "";
          
            if(isset($getDetails['errors'])){
                $cnt =1;
                foreach($getDetails['errors'] as $errr){
                    $error .=$cnt.'.'.$errr[0].'<br>';
                    $cnt++;
                }

                return response()->json(['error_msg' => $error, 'data' =>array()], 500);
            }else{
                if(isset($getDetails['message'])){
                    return response()->json(['error_msg' =>$getDetails['message'], 'data' =>array()], 500);
                }else{
                    return 1;
                }
            }
        }
    }

    public function sampleDownloadPDFFile(Request $request)
    {
        $file = public_path('/') . "patientdocument/" . $request->file_upload;
        if (file_exists($file)) {
            $headers = [];
            return response()->download($file, $request->file_upload, $headers);
        } else {
            return Storage::disk('s3')->download('/patientdocument/' . $request->file_upload);
        }
    }

    
	public function createImagicPdf(Request $request){
        
        $documentDetails =  $this->DocumentPatientService->getDocumentDetailsByIdOrPatientId($request->id);
        $getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($documentDetails->patient_id);
        $response = $this->documentOtherPdfRegenerate($documentDetails,$getPatientDetails->agency_id);

        $this->DocumentPatientService->update(array('attachment'=>$response),array('id'=>$request->id));
        $newdocumentDetails =  $this->DocumentPatientService->getDocumentDetailsByIdOrPatientId($request->id);
        $ipaddress = Utility::getIP();
        $message = auth()->user()->first_name.' '.auth()->user()->last_name.' has regenerate the document pdf';
        $insertLog = [
            'type' => 'Regenerate PDF',
            'link' => url('/imagick-temp-pdf'),
            'module' => 'Patient Appointment',
            'object_id' => $documentDetails->patient_id,
            'message' => $message??'',
            'new_response' => serialize($newdocumentDetails->toArray()),
            'old_response' => serialize($documentDetails->toArray()),
            'ip' => $ipaddress,
        ];
        LogsService::save($insertLog);
        return response()->json(['error_msg' =>"Successfully regenerated", 'data' =>array()], 200);
	}
    private function documentOtherPdfRegenerate($requestData,$agencyId){
        ini_set('memory_limit', '1024M');
		$expiry = Carbon::now()->addMinutes(10);
        $path = 'patientdocument/' .  $requestData->attachment;
        $inputPath = Storage::disk('s3')->temporaryUrl($path, $expiry);
      
		$imagick = new \Imagick();
		
		$imagick->setResolution(300, 300);
		$imagick->setBackgroundColor(new \ImagickPixel('white'));
		$imagick->setResourceLimit(\Imagick::RESOURCETYPE_MAP, 4096);
		$imagick->readImage($inputPath);
	
		$path = public_path('/tempPDFGenerate');
		if (!file_exists($path)) {
			mkdir($path, 0755, true); // true = recursive
		}

		$pdf = new PDF(null, 'mm','legel');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
	
		$pdf->SetMargins(0, 0, 0);
		$pdf->SetHeaderMargin(0);
		$pdf->SetAutoPageBreak(false, 0);
	
		$a4Width = 210;
		$a4Height = 297;
		$dpi = 300;
	
		foreach ($imagick as $i => $page) {
			// Transform colorspace to sRGB to prevent color inversion
			$page->transformImageColorspace(\Imagick::COLORSPACE_SRGB);

			// Set white background color before removing transparency
			$page->setImageBackgroundColor(new \ImagickPixel('white'));

			// Ensure image format is PNG with 8-bit depth
			$page->setImageFormat('png');
			// $page->setImageDepth(8);

			// Flatten alpha channel onto white background (removes transparency)
			// $page->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);

			// Create a new white canvas and composite the processed image
			$flattened = new \Imagick();
			$flattened->newImage($page->getImageWidth(), $page->getImageHeight(), new \ImagickPixel('white'));
			// $flattened->setImageColorspace(\Imagick::COLORSPACE_SRGB);
			// $flattened->setImageFormat('png');
			$flattened->compositeImage($page, \Imagick::COMPOSITE_DEFAULT, 0, 0);

			// Save to temp folder
			$uniqueId = uniqid();
			$tmpPath = $path . "/page_{$i}_{$uniqueId}.png";
			$flattened->writeImage($tmpPath);
		
			list($imgWidthPx, $imgHeightPx) = getimagesize($tmpPath);
		
			// Convert pixels to mm based on 300 DPI
			$imgWidthMm = ($imgWidthPx / $dpi) * 25.4;
			$imgHeightMm = ($imgHeightPx / $dpi) * 25.4;
		
			$imgRatio = $imgWidthMm / $imgHeightMm;
			$pageRatio = $a4Width / $a4Height;
		
			if ($imgRatio > $pageRatio) {
				$width = $a4Width;
				$height = $a4Width / $imgRatio;
			} else {
				$height = $a4Height;
				$width = $a4Height * $imgRatio;
			}
		
			$orientation = ($width > $height) ? 'L' : 'P';
			$pdf->AddPage($orientation);
		
			$x = ($a4Width - $width) / 2;
			$y = ($a4Height - $height) / 2;
		
			$pdf->Image($tmpPath, $x, $y, $width, $height, 'PNG', '', '', false, $dpi, '', false, false, 0, false, false, false);
		
			// Optionally delete temporary file after adding to PDF
			unlink($tmpPath);
		
			// Clear memory
			$flattened->clear();
			$flattened->destroy();
		}
		
		$imagick->clear();
		$imagick->destroy();
		$uniqueId = uniqid();
		
		$outputPath = public_path('/').'/tempPDFGenerate/generated_pdf_'.$uniqueId.'.pdf';
	
		$pdf->Output($outputPath, 'F');
		$name = $agencyId.'/'.$requestData->patient_id.'/'.uniqid() . time() . '.pdf';
		$destination1 = public_path('patientdocument');
		$destination2 = public_path('patientWriteDocument');
	
		$contain = file_get_contents($outputPath);
		if (env('FILE_UPLOAD_PERMISSION') == 'development') {
			
			file_put_contents($destination1 . '/' . $name, $contain);
			\File::copy($destination1 . '/' . $name, $destination2 . '/' . $name);
			$image = $name;
		} else {
			Storage::disk('s3')->put('patientdocument/' . $name, $contain);
			Storage::disk('s3')->put('patientWriteDocument/' . $name, $contain);
			
			$image = $name;
		}
	
		unlink($outputPath);
		return $image;
	}
}