<?php

namespace App\Services;

use App\Model\DocumentPatient;
use App\User;
use App\Helpers\Utility;
use App\Services\DocumentPatientService;
use App\Services\LogsService;
use App\Notifications\DocumentSignatureRequiredNotification;
use App\DocumentSentReport;
use App\Template;
use App\DocumentSignerMaster;
use App\Mail\EsignWorkflowSignerNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Services\DocumentSendService;
use App\Services\PatientService;
use App\Services\TemplateService;
use URL;
use Carbon\Carbon;
use App\Services\DocusignDetailService;
use App\Services\DynamicFormLogService;
use App\Services\AssignEsignDocumentService;
use App\Services\AssignEsignDocumentUserService;
use App\Model\DefualtAssignEsignUser;
use App\Services\EsignErrorLogService;

class DocumentWorkflowService
{
	protected const DATE_FORMAT_YMD = 'Y-m-d H:i:s';
	protected $documentPatientService;
	protected $documentSendService;
	protected $patientService;
	protected $templateService;
	protected const ESIGN_DOCUMENT_UPLOAD_PATH="dosusinguploads/docusign";
	protected const PATIENT_DOCUMENT_UPLOAD_PATH="patientdocument";
	protected const MODULE_TYPE = "Patient Appointment";
	protected const URL_SEND_DATA = '/esign/document-workflow/streamlined';
	protected $docusignDetailService;
	protected const PATIENT_WRITE_DOCUMENT_UPLOAD_PATH="patientWriteDocument";
	protected $esignErrorLogService;

	public function __construct(DocumentPatientService $documentPatientService,DocumentSendService $documentSendService,PatientService $patientService,TemplateService $templateService,DocusignDetailService $docusignDetailService,EsignErrorLogService $esignErrorLogService)
	{
		$this->documentPatientService = $documentPatientService;
		$this->documentSendService = $documentSendService;
		$this->patientService = $patientService;
		$this->templateService = $templateService;
		$this->docusignDetailService = $docusignDetailService;
		$this->esignErrorLogService = $esignErrorLogService;
	}

	public function markAsSignatureRequired($documentId)
	{
		$auth = auth()->user();
		$document = $this->documentSendService->getDetailsById($documentId);

		if (!$document) {
			return ['status' => false, 'error_msg' => 'Document not found.'];
		}

		// Check esign_workflow from template_master via document_sent_report
	
		if ($document) {
			$template =$this->templateService->getDetailsById($document->templete_id);

			if ($template) {
				if ($template->esign_workflow === 'form_complete') {
					return $this->markAsApproved($documentId, $document, $auth);
				}

				if ($template->esign_workflow === 'form_complete_with_sign') {
					return $this->handleSignedWorkflow($documentId);
				}
			}
		}

		return ['status' => true, 'error_msg' => 'Document marked as signature required and notifications sent.'];
	}

	private function markAsApproved($documentId, $document = null, $auth = null)
	{
		
		$auth = $auth ?: auth()->user();
	
		try {
			$document = $document ?: $this->documentSendService->getDetailsById($documentId);

			if (!$document) {
				return ['status' => false, 'error_msg' => 'Document not found.'];
			}

			$getDetails = $this->docusignDetailService->getDetailsByDocumentReportId($documentId);
			// Update document_sent_report status to Approved
			$getFirstRecord = $this->documentSendService->getAllDetailsByGroupId($document->groupId);
			DocumentSentReport::where('id', $getFirstRecord[0]['id'])
			->where('del_flag', 'N')
			->update([
				'approved_date' => date(self::DATE_FORMAT_YMD),
				'approved_by' => $auth->id??"",
				'review_date' => date(self::DATE_FORMAT_YMD),
				'review_by' => $auth->id??"",
				'pdf_status' => 1,
			]);
			$documentNew = $this->documentSendService->getDetailsById($documentId);
			$template = $this->templateService->getDetailsById($document->templete_id);
			$documentNew->template_type = $template->esign_workflow;
			$document->template_type = $template->esign_workflow;
			$message = "Guest User has Approved the E-Sign document using the ".$template->template_name;
			if(isset(auth()->user()->id)){
				$message = auth()->user()->first_name . ' ' . auth()->user()->last_name . " has Approved the E-Sign document using the ".$template->template_name;
			}
			
			$esignLogResponse = [
				'type' => 'Update Esign Form',
				'link' =>url(self::URL_SEND_DATA),
				'module' => self::MODULE_TYPE,
				'object_id' => $document->main_intakeId,
				'message' => $message,
				'new_response' => serialize($documentNew->toArray()),
				'old_response' => serialize($document->toArray()),
				
			];
			$this->logAction($esignLogResponse);

			$dynamicLogDetails  = [
				'type' => 'Esign Document Approved',
				'link' => url(self::URL_SEND_DATA),
				'module' => 'Esign Section',
				'module_id' => $document->groupId,
				'new_response' => serialize($documentNew->toArray()),
				'old_response' => serialize($document->toArray()),
				'is_status' => 'Approved',
				'message'=>$message,
				'esign_new_response'=>$getDetails->data
			];

			$this->logDynamicDocument($dynamicLogDetails);
			$this->moveToDocumentForPatient($documentId);
			return ['status' => true, 'error_msg' => 'Document approved successfully (no signer required).'];
		} catch (\Throwable $th) {
			$auth = auth()->user();
			$userId = null;
			if(isset($auth['id'])){
				$userId =$auth['id'];
			}
			
			$this->esignErrorLogService->save([
				'error_log' => $th->getMessage(),
				'esign_response' => $getDetails->data,
				'line'    => $th->getLine(),
				'trace'   => $th->getTraceAsString(),
				'created_date' =>date('Y-m-d H:i:s'),
				'document_id'=>$documentId,
				'record_id'=>$document->main_intakeId,
				'template_id'=>$document->templete_id,
				'created_by'=>$userId,
				'type'=>'Esign'
			]);
		}
	}

	public function handleSignedWorkflow($documentId, $auth = null)
	{

		$document = $this->documentSendService->getDetailsById($documentId);

		try {
			if (!$document) {
				return ['status' => false, 'error_msg' => 'Document not found.'];
			}
			$template = $this->templateService->getDetailsById($document->templete_id);
			$totalSigner = $this->documentSendService->getGroupPending($document->groupId);
		
			if(!isset($totalSigner->id)){
				return $this->markAsApproved($documentId,$document);
			}
		
			$save = $this->assignDocumentUser($document,$template);
			if($save){
				return ['status' => true, 'error_msg' => 'Document sent for signature and next signer notified via email.'];
			}else{
				return ['status' => false];
			}
			
		} catch (\Throwable $th) {
			$auth = auth()->user();
			$userId = null;
			if(isset($auth['id'])){
				$userId =$auth['id'];
			}
			
			$this->esignErrorLogService->save([
				'error_log' => $th->getMessage(),
				'line'    => $th->getLine(),
				'trace'   => $th->getTraceAsString(),
				'created_date' =>date('Y-m-d H:i:s'),
				'document_id'=>$documentId ?? null,
				'record_id'=>$document->main_intakeId,
				'template_id'=>$document->templete_id,
				'created_by'=>$userId,
				'type'=>'Esign'
			]);
		}
	}

	private function notifyNextSigner($userId, $document,$template)
	{
		$user = User::where('id', $userId)
				->where('delete_flag', 'N')
				->first();
		$recipientEmail = null;
		$signerName = "";
		if ($user && !empty($user->email)) {
			$recipientEmail = $user->email;
			$signerName = $user->first_name . ' ' . $user->last_name;
		}
		
		$documentName = $template->template_name ?? ('Document #' . $document->id);
		$actionUrl = url('/esign/docusign/viewNew/').'/'.$document->id;

		Mail::to($recipientEmail)->send(
			new EsignWorkflowSignerNotification($documentName, $signerName, $actionUrl)
		);
	}

	private function moveDocumentToFinalStorage($document,$patient)
	{
		
		if (empty($document->pdf_generate)) {
			return null;
		}

		$fileName = $document->pdf_generate;
		$newFile = uniqid().''.date('mdY').'.pdf';
		$file = $patient->agency_id.'/'.$patient->id.'/'.$newFile;

		if (env('FILE_UPLOAD_PERMISSION') == 'development') {
			$this->moveToLocalService($fileName,$file);
			
		} else {
			$this->moveToAwsServer($fileName,$file);
		}

		return $file;
	}

	private function logAction($data)
	{
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => $data['type'],
			'link' =>  $data['link'],
			'module' =>  $data['module'],
			'object_id' => $data['object_id'],
			'message' =>$data['message'],
			'ip' => $ipaddress,
			'new_response'=>$data['new_response']
		];

		if(isset($data['old_response'])){
			$insertLog['old_response'] = $data['old_response'];
		}
		LogsService::save($insertLog);
	}

	private function moveToDocumentForPatient($documentId)
	{
		// Step 1: Fetch document details from document_sent_report
		$document = $this->documentSendService->getDetailsById($documentId);

		if (!$document || empty($document->main_intakeId)) {
			return;
		}
		// Step 2: Fetch template details from template_master
		$template = $this->templateService->getDetailsById($document->templete_id);
		if (!$template) {
			return;
		}

		
		// Step 3: Validate patient exists and type = 'Patient'
		$patient = $this->patientService->getPatientDetailsByIdWhitoutAgency($document->main_intakeId);
		
		if (!$patient) {
			return;
		}
		// Step 4: Get the PDF file URL
		$attachment = $this->moveDocumentToFinalStorage($document, $patient);
	
		// Step 5: Insert record into document_patient table
		$auth = auth()->user();
		$data = [
			'patient_id' => $document->main_intakeId,
			'document_name' => $template->template_name,
			'attachment' => $attachment,
			'templete_id' => $document->templete_id,
			'document_review_status' => 'Approved',
			'document_completed_date' => date(self::DATE_FORMAT_YMD),
			'old_attachment'=>$attachment,
			'is_esign_form_complete'=>1,
			'created_date' => date(self::DATE_FORMAT_YMD),
		];

		$save = $this->documentPatientService->save($data);
		$dataWrite = [
			'document_name' => $template->template_name,
			'type' =>"Document",
			'document_patient_id' => $save,
			'file_upload' => $attachment,
			'created_at' => date('Y-m-d H:i:s'),
		];
		$this->documentSendService->saveWriteDocumentData($dataWrite);

		$message = "Guest User  has moved document from Esign template";
		if(isset($auth->id)){
			$message = $auth->full_name . ' has moved document from Esign template';
		}
		$logResponse = [
			'type'=>'Move document from template',
			'link'=>url(self::URL_SEND_DATA),
			'object_id'=>$document->main_intakeId,
			'module' =>self::MODULE_TYPE,
			'new_response'=>serialize($data),
			'message' => $message,
		];
		$this->logAction($logResponse);

		$insertLog = [
			'type' => 'Move document from template',
			'link' =>url(self::URL_SEND_DATA),
			'module' => "Esign Section",
			'module_id' => $document->groupId,
			'new_response' =>  serialize($data),
			
			'is_status' => "Move To Document",
			'message' => $message,
		];

		$this->logDynamicDocument($insertLog);

		return $save;
	}

	public function testingAccount($id){
		$this->moveToDocumentForPatient($id);
	}

	private function logDynamicDocument($data)
	{
		
		$insertLog = [
			'type' => $data['type'],
			'link' => $data['link'],
			'module' => $data['module'],
			'module_id' => $data['module_id'],
			'is_status' => $data['is_status'],
			'message'=>$data['message'],
		];

		if(isset($data['new_response'])){
			$insertLog['new_response'] =  $data['new_response'];
		}

		if(isset($data['old_response'])){
			$insertLog['old_response'] =  $data['old_response'];
		}

		if(isset($data['esign_old_response'])){
			$insertLog['esign_old_response'] =  $data['esign_old_response'];
		}

		if(isset($data['esign_new_response'])){
			$insertLog['esign_new_response'] =  $data['esign_new_response'];
		}
	
		return DynamicFormLogService::storeFormLog($insertLog);
	}

	private function moveToLocalService($fileName,$file){
		$sourcePath = public_path(self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $fileName);
		
		$destinationDir = public_path(self::PATIENT_DOCUMENT_UPLOAD_PATH);
		
		if (!File::exists($destinationDir)) {
			File::makeDirectory($destinationDir, 0777, true, true);
		}

		$destinationPath = $destinationDir . '/' . $file;

		if (file_exists($sourcePath) && !file_exists($destinationDir)) {
			file_put_contents($destinationPath, file_get_contents($sourcePath));
		}
		
	}

	private function moveToAwsServer($fileName,$file){
		$sourceKey = self::ESIGN_DOCUMENT_UPLOAD_PATH.'/' . $fileName;
		$destinationKey = self::PATIENT_DOCUMENT_UPLOAD_PATH.'/' . $file;
		
		if (Storage::disk('s3')->exists($sourceKey)) {
			$fileContent = Storage::disk('s3')->get($sourceKey);
			Storage::disk('s3')->put($destinationKey, $fileContent);
		}
	}

	private function assignDocumentUser($document,$template = nulll){
		try {
			$getNextSigner = $this->documentSendService->getGroupPending($document->groupId);
	
			$data =[
				'template_id'=>$getNextSigner->templete_id,
				'esign_document_id'=>$getNextSigner->id,
			];
			
			$lastId = AssignEsignDocumentService::save($data);
			if($lastId){
				
				$getDetails = DefualtAssignEsignUser::select('user_id')->where('del_flag','N')->get();
				
				if(!empty($getDetails[0])){
					foreach($getDetails as $us){
						
						AssignEsignDocumentUserService::save(['user_id'=>$us->user_id,'assign_esign_document_id'=>$lastId]);

						try {
							$this->notifyNextSigner($us->user_id, $getNextSigner, $template);
						} catch (\Throwable $th) {
							//throw $th;
						}
					}
				}
				return 1;
			}else{
				return 0;
			}
		} catch (\Throwable $th) {
			$auth = auth()->user();
			$userId = null;
			if(isset($auth['id'])){
				$userId =$auth['id'];
			}
			
			$this->esignErrorLogService->save([
				'error_log' => $th->getMessage(),
				'line'    => $th->getLine(),
				'trace'   => $th->getTraceAsString(),
				'created_date' =>date('Y-m-d H:i:s'),
				'document_id'=>$document->id ?? null,
				'record_id'=>$document->main_intakeId,
				'template_id'=>$document->templete_id,
				'created_by'=>$userId,
				'type'=>'Esign'
			]);
		}
	}
}
