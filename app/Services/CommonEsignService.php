<?php

namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

use App\Services\DocusignDetailService;
use App\Services\TemplateService;
use App\Services\DocumentSendService;
use App\Model\PDF;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Services\PatientService;
use App\Helpers\Common;
use	App\Services\DocumentSendSmsLogService;
use App\Services\DynamicFormLogService;

use App\Helpers\Utility;
use App\Model\WriteDocument;
use App\Model\signatureUpload;
use Carbon\Carbon;
use App\DocusignDetail;
use App\Services\DocumentPatientService;
use App\Services\AssignNyBestUserService;
use App\Services\UserService;
use Illuminate\Support\Facades\File;
use App\Services\EsignErrorLogService;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;

use App\Model\Doctor;
use App\Services\ResolutionService;
class CommonEsignService
{
    protected $docusignDetailService;
	protected $templateService;
	protected $documentSendService;
	protected $patientService;
	protected $documentSendSmsLogService;
	protected $dynamicFormLogService;
	protected $assignNyBestUserService;
	protected $userService;
	protected const DATE_FORMAT_MDYHIS = 'm/d/Y h:i:s';
	protected const ESIGN_DATE_FORMAT = 'm/d/Y h:i A';
	protected const DOCUSIGN_FOLDER = 'dosusinguploads/docusign';
	protected const PORTAL_VERIFIED_BY = 'Verified by Nybest Portal';
	protected const PORTAL_FAVICON = 'img/favicon.png';
	protected const DATE_FORMAT_MDY="m/d/Y";
	protected const PORTAL_ASSETS_FONT="assets/fonts/ARIAL.TTF";
	protected const PORTAL_ELECTRONIC_SIGN="electronically signed";
	protected const ESIGN_PATIENT_WRITE_DOCUMENT="patientWriteDocument";
	protected $documentPatientService;
	protected $esignErrorLogService;
	protected $patientServicesRequest;
	protected $patientWiseServicesRequests;
	protected const DATE_FORMAT_YMD = "Y-m-d H:i:s";

	public function __construct(DocusignDetailService $docusignDetailService,TemplateService $templateService,DocumentSendService $documentSendService,PatientService $patientService,DocumentSendSmsLogService $documentSendSmsLogService,DynamicFormLogService $dynamicFormLogService,DocumentPatientService $documentPatientService, AssignNyBestUserService $assignNyBestUserService, UserService $userService,EsignErrorLogService $esignErrorLogService,PatientServicesRequest $patientServicesRequest,PatientWiseServicesRequests $patientWiseServicesRequests)
	{
		$this->docusignDetailService = $docusignDetailService;
		$this->templateService = $templateService;
		$this->documentSendService = $documentSendService;
		$this->patientService = $patientService;
		$this->documentSendSmsLogService = $documentSendSmsLogService;
		$this->dynamicFormLogService = $dynamicFormLogService;
		$this->documentPatientService = $documentPatientService;
		$this->assignNyBestUserService = $assignNyBestUserService;
		$this->userService = $userService;
		$this->esignErrorLogService = $esignErrorLogService;
		$this->patientServicesRequest = $patientServicesRequest;
		$this->patientWiseServicesRequests = $patientWiseServicesRequests;
	}
	
	public function regeneratethepdf($searchField){
		
		/***,$insert,$id,$sessionId,$document_report_id,$groupId,$sent_on="",$submitType="",$existingOldResponse = [] */
		$insert = $searchField['insert'];
		$id = $searchField['id'];
		$document_report_id = $searchField['document_report_id'];
		$groupId = $searchField['groupId'];
		$sent_on = $searchField['sent_on'];
		$submitType = $searchField['submitType']??"";

		$existingOldResponse = $searchField['existingOldResponse']??[];

		$normalImageArray =[];
		$auth = auth()->user();

		$mainResponse = $this->docusignDetailService->getDetailsById($insert);
		$inaction = unserialize($mainResponse->data);
		$spurcePdf =$this->documentSendService->getDocumentServiceInApi($document_report_id);
		$doctorName = "";
		if(isset($spurcePdf->doctor_id) && $spurcePdf->doctor_id !=""){
			$doctorDetails = Doctor::select('full_name')->where('id',$spurcePdf->doctor_id)->first();
			$doctorName = $doctorDetails->full_name??"";
		}

		try{
			$oldEsignResponse = [];
	
			$headers=array();
			
			foreach($inaction as $obj){
				if(isset($obj['permission'])){
					$headers=$obj['permission'];
				}
			}
			$conditionalField=array();
			if(!empty($headers)){
				foreach($headers as  $obj){
					if($obj["type"] =='checkbox'){
						$conditionalField[$obj["ReceiverDivId"]]=$obj["SenderDivId"];
					}else{
						$conditionalField[$obj["ReceiverDivId"]]=$obj["SenderId"];
					}
				}
			}

			$elementValue=array();
			foreach($inaction as $obj){
				$elementValue[$obj["id"]]=$obj;
			}
			$documents =$this->templateService->getDetailsById($id);
			if($submitType =='edit'){
				$spurcePdf->sourceFile = $documents->upload_document;
			}
			$checkbox_mark_flag = 0;
			if(isset($documents->checkbox_mark_flag) && $documents->checkbox_mark_flag ==1){
				$checkbox_mark_flag = 1;
			}

			$oldResponseData =$this->documentSendService->getDocumentServiceData($document_report_id);

			/*end */
			$pdf = new PDF(null, 'px');
			$pdf->SetAutoPageBreak(false, 0);

			$inputPath =public_path()."/".self::DOCUSIGN_FOLDER.'/'.$spurcePdf->sourceFile;
			
			if(file_exists($inputPath)){
				$inputPath =public_path()."/".self::DOCUSIGN_FOLDER."/".$spurcePdf->sourceFile;
			}else{
				if (env('FILE_UPLOAD_PERMISSION')  == 'development') {

				file_put_contents(public_path(self::DOCUSIGN_FOLDER.'/' . $spurcePdf->sourceFile), $inputPath);
				$inputPath =public_path()."/".self::DOCUSIGN_FOLDER."/".$spurcePdf->sourceFile;
				}else{
					$expiry = Carbon::now()->addMinutes(10);
					$path = self::DOCUSIGN_FOLDER.'/' . $spurcePdf->sourceFile;
					$inputPath = Storage::disk('s3')->temporaryUrl($path, $expiry);
					$moveToLocalServerFile = $this->moveToLocalServerFile($inputPath,'E-'.$id);

					$inputPath = $moveToLocalServerFile['inputPath'];
				}
			}
			$pageCount = $pdf->setSourceFile($inputPath);
			$templateId = $pdf->importPage(1);
			$size = $pdf->getTemplateSize($templateId);

			$folder = 'E-'.$insert;
			$inputPath = $this->commonGsPath($folder,$inputPath,$size);
		

			$pdf->numPages = $pdf->setSourceFile($inputPath);

			$signed = $updatedFields = $editted = false;
			$esignResponseLogs = [];
			foreach(range(1, $pdf->numPages, 1) as $page) {
				
				$rotate = false;
				$degree = 0;
				$pdf->_tplIdx = $pdf->importPage($page);
				
				foreach($inaction as $action) {
					if(((int) $action['page']) === $page && $action['type'] == "rotate") {
						$rotate = $editted = true;
						$degree = $action['degree'];
						break;
					}
					
				}
				
				$size = $pdf->getTemplateSize($pdf->_tplIdx);
				if ($documents->docWidth != 0) {
					$scale = round($size['w'] / $documents->docWidth, 3);
				} else {
					$scale = 0;
				}

				$pdf->AddPage(self::orientation($size['w'], $size['h']), array($size['w'], $size['h'], 'Rotate'=>$degree), true);
				$pdf->useTemplate($pdf->_tplIdx);
				$checkPermissionId= array();
				$checkedRadio= array();
				foreach($inaction as $action) {
					$checkPermissionId[$action['id']] = $action['id'];
					if($action['type'] =='radio'&& $action['checked'] !=''){
						$checkedRadio["checked"] = $action['checked'];
					}
				}
		
				foreach($inaction as $action) {
					if(((int) $action['page']) === $page) {
					
						if ($action['type'] == "image" || $action['type'] == "signature") {
							$esignResponseLogs[$action['id']] =basename($action['text']);
							if(isset($existingOldResponse[$action['id']]['text'])){
								$oldEsignResponse[$action['id']] = isset($existingOldResponse[$action['id']]['text'])?basename($existingOldResponse[$action['id']]['text']):"";
							}
							
							if($action['text'] !=''){
								if(isset($mainResponse->docWidth) && $mainResponse->docWidth !=""){
									if ($mainResponse->docWidth != 0) {
										$scale = round($size['w'] / $mainResponse->docWidth, 3);
									} else {
										if ($documents->docWidth != 0) {
											$scale = round($size['w'] / $documents->docWidth, 3);
										} else {
											$scale = 0;
										}
									}
								}else{
									if ($documents->docWidth != 0) {
										$scale = round($size['w'] / $documents->docWidth, 3);
									} else {
										$scale = 0;
									}
								}

								$editted = true;

								$imageArray = $this->getAwsDocusignImages($action['text'],$action['updatedSelectType']);

								$imgdata = base64_decode(base64_encode($imageArray));
								
								if(strtolower($sent_on) =='stampuser'){
									$pdf->Image($imgdata, self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), self::scale($action['width'], $scale), self::scale($action['height']+25, $scale), '', '', '', false);
								}else{
									$height = $action['height'];
									$width=$action['width'];
									
									$pdf->Image($imgdata, self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), self::scale($width, $scale), self::scale($height, $scale), '', '', '', false);
								
									if(isset($documents->show_verify_by) && $documents->show_verify_by =="Y"){
										
										$pageWidth = $pdf->getPageWidth();
									
										/**********X position */
										if((self::scale($action['xPos'], $scale) + self::scale($width, $scale) +140 ) < $pageWidth ){
											$relativeX = self::scale($action['xPos'], $scale) + self::scale($width, $scale)+5;
										}else{
											$relativeX = self::scale($action['xPos'], $scale) - 140-5;
										}
		
										if(self::scale($action['yPos'], $scale) - 40 > 0){
											$relativeY = self::scale($action['yPos'], $scale) - 40;
										}else{
											$relativeY = self::scale($action['yPos'], $scale) +self::scale($height, $scale) + 5;
										}

										$x =$relativeX;
										
										$y = $relativeY;
										$w = self::scale($width, $scale);
										$h = self::scale($height, $scale);

										$logoWidth = 30;
										$logoHeight = 30;
										$pdf->Image(env('HOST_WEB_URL').self::PORTAL_FAVICON, $x+3, $y + 3, $logoWidth, $logoHeight, '', '', '', false);
										$font = public_path().'/'.self::PORTAL_ASSETS_FONT;
										$fontname = \TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);
										$pdf->SetFont($fontname, '', 9, '', true);

										$pdf->SetTextColor(40, 90, 140);

										// Verified by text
										$pdf->SetXY($x + $logoWidth + 3, $y + 3);
										$verifiedByText = self::PORTAL_VERIFIED_BY;
										
										$pdf->Cell($w - $logoWidth - 5, 5, $verifiedByText, 0, 1, 'L', false);
										$font = public_path().'/'.self::PORTAL_ASSETS_FONT;
										$fontname = \TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);
										
										$pdf->SetFont($fontname, '', 9, '', true);
										
										$pdf->SetTextColor(0, 150, 0); // Green text
										$pdf->SetXY($x + $logoWidth + 3, $y + 20);
										$pdf->Cell($w - $logoWidth - 5, 10, date(self::ESIGN_DATE_FORMAT), 0, 1, 'L', false);

										\TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);
										$pdf->SetFont('helvetica', 'BI', 10);
										$pdf->SetTextColor(80, 80, 80);
										$pdf->SetXY($x, $y + 35);
										$electronicSignText = self::PORTAL_ELECTRONIC_SIGN;
										if (!empty($doctorName)) {
											$electronicSignText .= ' by ' . $doctorName.' '.date('m/d/y h:i A');
										}
										
										//$pdf->Cell(140, 5, $electronicSignText, 0, 1, 'C');
										$pdf->MultiCell(140, 5, $electronicSignText, 0, 'C', false, 1);
										$pdf->SetLineStyle([
											'width' => 0.5,
											'dash' => '2,2',
											'color' => [160, 160, 160]
										]);

										$pdf->RoundedRect($x, $y, 140, 35, 2.5, '1111');
									}
								
								}
								$normalImageArray[] = $action['text'];
							}
						}elseif ($action['type'] == "stamp") {

							if(isset($existingOldResponse[$action['id']]['text'])){
								$oldEsignResponse[$action['id']] = isset($existingOldResponse[$action['id']]['text'])?basename($existingOldResponse[$action['id']]['text']):"";
							}
							
							$esignResponseLogs[$action['id']] =basename($action['text']);
							if($action['text'] !=''){
								
								$stampScale = 0;
								
								if(isset($mainResponse->docWidth) && $mainResponse->docWidth !=""){
									if ($mainResponse->docWidth != 0) {
										$stampScale = round($size['w'] / $mainResponse->docWidth, 3);
									}
								}
								
								$editted = true;

								$imageArray = $this->getAwsDocusignImages($action['text'],0);

								$imgdata = base64_decode(base64_encode($imageArray));
								
								$pdf->Image($imgdata, self::scale($action['xPos'], $stampScale), self::scale($action['yPos'], $stampScale), self::scale($action['width'], $stampScale),self::scale($action['height'], $stampScale), '', '', '', false);

								$normalImageArray[] = $action['text'];
							}
						}elseif ($action['type'] == "text") {
							if ($documents->docWidth != 0) {
								$scale = round($size['w'] / $documents->docWidth, 3);
							} else {
								$scale = 0;
							}
			
							$pdf->SetTextColor(0, 0, 0);
							$editted = true;
							if($action['font'] == 'undefined'){
								$font = 10;
							}else{
								$font = $action['font'];
							}
							$pdf->setFontSize($font);
							$pdf->SetFont('helvetica', '', $font);
							$explodes = explode('_',$action['id']);
							if(isset($action['text']) && $action['text'] !=''){
								$text = $action['text'];
							}else{
							
								if($explodes[0] =='intake'){
									$text='';
								}elseif($explodes[0] =='caregiver'){
									$text='';
								}else{
									
									if(isset($action['readonly'])){
										if(trim($action['readonly']) && $action['readonly'] =='readonly'){
											$text = $action['placeHolder'];
										}
									}else{
										if($explodes[0] =='datesigned'){
											$text="";
											if(isset($action['text'])){
												if($action['text'] !=""){
													$text = "";
												}else{
													$text = date(self::DATE_FORMAT_MDY);
												}
											}

										}else{
											$text = '';
										}
									}
								}
							}
							$showText=true;

							if( isset($conditionalField[$action['id']])){
								$showText=false;
								
								$recivedObj=isset($elementValue[$conditionalField[$action['id']]]) ? $elementValue[$conditionalField[$action['id']]] :" ";
								if(isset($recivedObj['type']) && $recivedObj['type']=="radio"){
									if($recivedObj["checked"]!=""){
										$showText=true;
									}
								}
								elseif( isset($recivedObj['type']) && $recivedObj['type']=="dropdown"){
									foreach($recivedObj['permission'] as $permission){
										if($permission['SenderId']==$recivedObj['id'] &&  $permission['value']==$recivedObj["text"]){
											$showText=true;
										}
									}
								}elseif( isset($recivedObj['type']) && $recivedObj['type']=="checkbox" &&  $recivedObj["checked"]!=""){
									$showText=true;
								}
							}
							
							if($showText){
								$safeText = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
								$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale) - 3, self::scale($action['yPos'], $scale), $safeText, 0, 0, false, true, '', true );
							}
							$esignResponseLogs[$action['id']] = $safeText;

							if(isset($existingOldResponse[$action['id']]['text'])){
								$oldEsignResponse[$action['id']] = isset($existingOldResponse[$action['id']]['text'])?$existingOldResponse[$action['id']]['text']:"";
							}
							
						}elseif ($action['type'] == "dropdown") {
							if ($documents->docWidth != 0) {
								$scale = round($size['w'] / $documents->docWidth, 3);
							} else {
								$scale = 0;
							}
			
							$pdf->SetTextColor(0, 0, 0);
							$pdf->SetFont('helvetica', '', 9);
							$editted = true;
							if(isset($action['text']) && $action['text'] !='Select'){
								$text =  $action['text'];
							}else{
								$text =  '';
							}
							if($id =='17'){
								$action['width'] = $action['width']+200;
							}
							
							$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale) - 3, self::scale($action['yPos'], $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true );
							$esignResponseLogs[$action['id']] = str_replace("%22", '"', $text);

							if(isset($existingOldResponse[$action['id']]['text'])){
								$oldEsignResponse[$action['id']] = isset($existingOldResponse[$action['id']]['text'])?$existingOldResponse[$action['id']]['text']:"";
							}
							
						}elseif ($action['type'] == "radio") {
							if ($documents->docWidth != 0) {
								$scale = round($size['w'] / $documents->docWidth, 3);
							} else {
								$scale = 0;
							}
			
							$pdf->SetTextColor(0, 0, 0);
							$pdf->SetFont('helvetica', '', 9);

							$checked=false;
							if($action['checked']=='1'){
								$checked=true;
								
								$pdf->SetFont('dejavusans', '', 15, '', true);
                                $pdf->Text(self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale) , '✓');
							}
							$esignResponseLogs[$action['id']] = $action['checked'];

							if(isset($existingOldResponse[$action['id']]['checked'])){
								$oldEsignResponse[$action['id']] = isset($existingOldResponse[$action['id']]['checked'])?$existingOldResponse[$action['id']]['checked']:"";
							}
							
						}
						elseif ($action['type'] == "checkbox") {
							if ($documents->docWidth != 0) {
								$scale = round($size['w'] / $documents->docWidth, 3);
							} else {
								$scale = 0;
							}
			
							$pdf->SetTextColor(0, 0, 0);
							$pdf->SetFont('helvetica', '', 9);
							
							$checked=false;
							if(isset($action['checked']) && $action['checked']=='1'){
								$checked=true;
							}
							if($checked){
								
								if($checkbox_mark_flag == 1){
									$pdf->Rect(self::scale($action['xPos'], $scale),self::scale($action['yPos'], $scale),10, 10, 'F');
								}else{
								
									$font = public_path().'/assets/fonts/arialuni.ttf';
									$fontname = \TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);
									$pdf->SetFont($fontname, '', 15, '', true); 
									$pdf->Text(self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale) , '✓');
								}
							}
							$esignResponseLogs[$action['id']] =$action['checked'];

							if(isset($existingOldResponse[$action['id']]['checked'])){
								$oldEsignResponse[$action['id']] = isset($existingOldResponse[$action['id']]['checked'])?$existingOldResponse[$action['id']]['checked']:"";
							}
						}
					}
				}
			}

			$outputName =time().'-'.$document_report_id.'-'.uniqid().".pdf";
			if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
				$fileGetContain=	$pdf->Output('', 'S');
				Storage::disk('s3')->put(self::DOCUSIGN_FOLDER.'/'.$outputName,$fileGetContain);
			}else{
				$outputPath = public_path()."/".self::DOCUSIGN_FOLDER."/". $outputName;
				$pdf->Output($outputPath, 'F');
			}

			$compTDate =date('Y-m-d h:i:s');
			$authUserId = null;
			if(isset(auth()->user()->id)){
				$authUserId = auth()->user()->id;
			}
			if($submitType !="edit"){

				$this->documentSendService->update(array('document_submit_status'=>1,'pdf_generate'=>$outputName,'status'=>'Completed','completed_on'=>$compTDate,'completed_by'=>$authUserId),array('id'=>$document_report_id));
			}else{
				$documentDetails = $this->documentSendService->getGroupPending($groupId);
					$updateNew = array(
						'sourceFile'=>$outputName
					);
					if(isset($documentDetails->id)){
						$this->documentSendService->update($updateNew,array('id'=>$documentDetails->id));
					}
					
					$this->documentSendService->update(array('pdf_generate'=>$outputName),array('id'=>$document_report_id));
			}
			
			//new design log
			$newResponseData =$this->documentSendService->getDocumentServiceData($document_report_id);
			$newResponse = $newResponseData->toArray();
			$newResponseLogArray = $newResponse;
			unset($newResponseLogArray['template_details']);

			//new design log
			$isStatus = '';
			if($newResponseData->sent_on != ''){
				$isStatus = $newResponseData->sent_on;
			}else{
				$isStatus = 'Completed';
			}
			// Insert form Log into Dynamic form log table
			$message =$newResponseData->sent_on."  E-Sign details have been successfully submitted using the ".$documents->template_name.' Template.';
			if(isset(auth()->user()->id)){
				if($submitType !='edit'){
					$message = auth()->user()->first_name.' '.auth()->user()->last_name." has successfully submitted the ".$newResponseData->sent_on." E-Sign details  using the ".$documents->template_name.' Template.' ;
				}else{
					$message = auth()->user()->first_name.' '.auth()->user()->last_name." has successfully updated the ".$newResponseData->sent_on." E-Sign details  using the ".$documents->template_name.' Template.' ;
				}
			}
			$ipaddress = Utility::getIP();
			unset($oldResponseData->templateDetails);

			$dynamicFormLogArray =[
				'type' => 'Esign Filled',
				'link' => url('esign/docusign/view/' . $document_report_id),
				'module' => 'Esign Section',
				'module_id' => $newResponseData->groupId,
				'new_response' => serialize($newResponseLogArray),
				'old_response' => serialize($oldResponseData),
				'is_status' => $isStatus,
				'esign_new_response'=>serialize($esignResponseLogs),
				'esign_old_response'=>serialize($oldEsignResponse),
				'message'=>$message,
			];
			$this->logDynamicForm($dynamicFormLogArray);
			
			$logServiceArray = [
				'type' => 'Esign Filled',
				'link' =>url('esign/docusign/view/' . $document_report_id),
				'module' => 'Patient Appointment',
				'object_id' => $oldResponseData->main_intakeId,
				'message' => $message,
				'new_response' => serialize($newResponseLogArray),
				'old_response' => serialize($oldResponseData),
				'ip' => $ipaddress,
			];

			$this->logAction($logServiceArray);
			
			//new design log
			if($submitType !="edit"){
				
				$secondDataArray = [
					'id' => $id,
					'groupId' => $groupId,
					'outputName'=>$outputName,
				];

				$this->secondEsignResponse($secondDataArray);

				$signerStatusEmailNotificationArray = [
					'groupId'=>$groupId,
					'type' => strtolower($spurcePdf->type),
					'email' => $documents->email_notification,
					'outputName'=>$outputName,
				];

				$returnResponse = $this->signerStatusUpdateWithEmailNotification($signerStatusEmailNotificationArray);
				/*   end **/
				$autoSendMailByCaregiverData = [
					'maintotal' => $returnResponse['maintotal'],
					'send_caregiver_email' => $documents->send_caregiver_email,
					'patient_id' => $oldResponseData->main_intakeId,
					'template_name'=>$documents->template_name,
					'compTDate' => $compTDate,
					'outputName' => $outputName,
				];
				$this->autoSendMailByCaregiver($autoSendMailByCaregiverData);
			}

			$templateTypes = explode(',',$documents->template_signer_type);
			$patientId = $newResponseData->main_intakeId ?? '';
			$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($patientId);
			$agencyFk = $getPatientDetails->agency_id ?? '';
			if(in_array(strtolower($spurcePdf->sent_on), $templateTypes)){
				$mail_send_user_id_array[] = $spurcePdf->created_by;
				
				$laison_email = $this->assignNyBestUserService->getAssignNybestUserId($agencyFk);
				$allemail = array();
				foreach($laison_email as $em){
					$mail_send_user_id_array[] = $em->nybest_user_id;
				}
				$userMails = $this->userService->getUserDetails($mail_send_user_id_array);
				foreach($userMails as $em){
					$allemail[] = $em->email;
				}
				$subject = ucwords(strtolower($spurcePdf->sent_on)).' signer has completed their E-Sign details.';
				$emailData = array(
					'msg' => 'Signer completion',
					'portal_id'=>$getPatientDetails->id,
					'full_name'=>$getPatientDetails->first_name.' '.$getPatientDetails->last_name,
					'template_name'=>$documents->template_name,
					'submitted_date'=>Utility::convertMDYTime($compTDate),
				);
				$message = Utility::getHtmlContent('email_template.email_doc_sign',$emailData);

				if(env('FILE_UPLOAD_PERMISSION')  != 'development'){
					$fileContent = Storage::disk('s3')->get(self::DOCUSIGN_FOLDER.'/' .  $outputName);
					$mimeType = Storage::disk('s3')->mimeType(self::DOCUSIGN_FOLDER.'/' .  $outputName);
					$url = $fileContent;
				}else{
					$url = URL::to('/').'/'.self::DOCUSIGN_FOLDER.'/'.$outputName;
					$mimeType = 'pdf';
				}
					
				$data = array('subject'=>$subject,'message'=>$message,'dd'=>$url,'to'=>$allemail,'document_name'=>$documents->template_name,'mimeType'=>$mimeType);
					
				$this->commonMail($data);
			}
			
			$patientId = $newResponseData->main_intakeId ?? '';

			$this->userNotifications($newResponseData,$patientId,$submitType);
			// Get Group wise notification
			
			if(file_exists($inputPath)){
				$directoryName = dirname($inputPath);
				File::deleteDirectory($directoryName);
			}

			if(isset($documents->resolution_update) && $documents->resolution_update =='Y'){
				if($submitType !="edit"){
					$this->updateStatusForResolution($groupId);
				}
			}
			return  $insert;
		} catch (\Throwable $th) {
			$auth = auth()->user();
			$userId = null;
			if(isset($auth['id'])){
				$userId =$auth['id'];
			}
			$this->esignErrorLogService->save([
				'error_log' => $th->getMessage(),
				'esign_response' => $mainResponse->data,
				'line'    => $th->getLine(),
				'trace'   => $th->getTraceAsString(),
				'created_date' =>date('Y-m-d H:i:s'),
				'document_id'=>$insert,
				'record_id'=>$spurcePdf->main_intakeId,
				'template_id'=>$id,
				'created_by'=>$userId,
				'type'=>'Esign'
			]);

		}
		
	}
	/*** end Docusign insert code of ios and android */
	 public  function orientation($width, $height) {
        if ($width > $height) {
            return "L";
        }else{
            return "P";
        }
    }
	
	/**
     * Scale element dimension
     * 
     * @param   int $dimension
     * @return  int
     */
    public static function scale($dimension, $scale) {
        return round($dimension * $scale);
    }
    
    /**
     * Scale position on axis
     * 
     * @param   int $position
     * @return  int
     */
    public static function adjustPositions($position) {
        return round($position - 83);
    }
	
	public function getEmailbyCompleted($esign_pdf_id,$email,$fullname){
		$docList = array();
		$cnt=0;
		$data['templete_report'] = $this->documentSendService->CompleteEmail($esign_pdf_id);
	
		if(!empty($data['templete_report'])){
				foreach($data['templete_report'] as $key){
					if($key->status !='' ){
						if(isset($key->pdf_generate) && $key->pdf_generate !=''){
							$docList[$cnt]['doc'] = $key->pdf_generate;
							$docList[$cnt]['docname'] = $key->template_name;
						$cnt++;
						}
						
					}
				}
			}

			foreach($docList as $list){
				$subject = $list['docname']." ".date(self::DATE_FORMAT_MDYHIS);
				
				$emailData = array(
					'fullname' => $fullname,
					'docname' => $list['docname']
				);
				$message = Utility::getHtmlContent('email_template.email_esign_attachment_sign',$emailData);
			
				$url = URL::to('/').'/'.self::DOCUSIGN_FOLDER.'/'.$list['doc'];
					$from = '';
					
					$emails = explode(',',$email);
					$allemail =array();
					foreach($emails as  $esz){
						if(trim($esz) !=''){
							$allemail[] = trim(str_replace(' ','',$esz));
						}
					}
					$data = array('subject'=>$subject,'message'=>$message,'dd'=>$url,'from'=>$from,'to'=>$allemail);
					Mail::send([], $data, function($message)use($data) {
					
						$message->to($data['to'], $data['message'])->subject
						($data['subject']);
						$message->attach($data['dd']);
					});
			}
			return 1;
	}
		
	public function downloadPDF($response,$id){
		ini_set('memory_limit', '1024M');
		$inaction = $response;
		$headers=array();

		foreach($inaction as $obj){
			if(isset($obj['permission'])){
				
				$headers=$obj['permission'];
			}
		}
		$conditionalField=array();
		if(!empty($headers)){
			foreach($headers as  $obj){
				if($obj["type"] =='checkbox'){
					$conditionalField[$obj["ReceiverDivId"]]=$obj["SenderDivId"];
				}else{
					$conditionalField[$obj["ReceiverDivId"]]=$obj["SenderId"];
				}
			}
		}
		$elementValue=array();
		foreach($inaction as $obj){
			$elementValue[$obj["id"]]=$obj;
		}
		$documents =$this->templateService->getDetailsById($id);
		
		/*end */
		$pdf = new PDF(null, 'px');
		
		$inputPath1 =public_path()."/".self::DOCUSIGN_FOLDER."/".$documents->upload_document;

		if(file_exists($inputPath1)){
			$inputPath = $inputPath1;
		}else{
			if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
				$inputPath = Storage::disk('s3')->get('/'.self::DOCUSIGN_FOLDER.'/' . $documents->upload_document);
			}
			file_put_contents(public_path(self::DOCUSIGN_FOLDER.'/' . $documents->upload_document), $inputPath);
			$inputPath =public_path()."/".self::DOCUSIGN_FOLDER."/".$documents->upload_document;
		}
		$pdf->numPages = $pdf->setSourceFile($inputPath);

		$signed = $updatedFields = $editted = false;

		foreach(range(1, $pdf->numPages, 1) as $page) {
			
			$rotate = false;
			$degree = 0;
			try {
				$pdf->_tplIdx = $pdf->importPage($page);
			}
			catch(\Exception $e) {
				return false;
			}
			
			foreach($inaction as $action) {
				if(((int) $action['page']) === $page && $action['type'] == "rotate") {
					$rotate = $editted = true;
					$degree = $action['degree'];
					break;
				}
			}
			
			$size = $pdf->getTemplateSize($pdf->_tplIdx);
			$docWidth = 636;
			if(isset($documents->docWidth) && $documents->docWidth !=""){
				$docWidth = $documents->docWidth;
			}
			$scale = round($size['w'] /$docWidth, 3);
			
			$pdf->AddPage(self::orientation($size['w'], $size['h']), array($size['w'], $size['h'], 'Rotate'=>$degree), true);
			$pdf->useTemplate($pdf->_tplIdx);
			$checkPermissionId= array();
			$checkedRadio= array();
			foreach($inaction as $action) {
				$checkPermissionId[$action['id']] = $action['id'];
				if($action['type'] =='radio'&& $action['checked'] !=''){
					$checkedRadio["checked"] = $action['checked'];
				}
				
			}
			foreach($inaction as $action) {
				if(((int) $action['page']) === $page) {
				
					if ($action['type'] == "image" || $action['type'] == "signature") {
						if($action['text'] !=''){
							$editted = true;
							$imageArray = base64_encode($action['text']);
							$imgdata = base64_decode($imageArray);
							
							$pdf->Image($imgdata, self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), 100, self::scale($action['height'], $scale), '', '', '', false);
						}
					}elseif ($action['type'] == "text") {
					
						$editted = true;
						if($action['font'] == 'undefined'){
							$font = 10;
						}else{
							$font = $action['font'];
						}
						$pdf->setFontSize($font);
						$explodes = explode('_',$action['id']);
						if(isset($action['text']) && $action['text'] !=''){
							$text = $action['text'];
						}else{
						
							if($explodes[0] =='intake'){
								$text='';
							}elseif($explodes[0] =='caregiver'){
								$text='';
							}else{
								if(trim($action['placeHolder']) != 'Textbox'  && trim($action['placeHolder']) != 'Date Signed'){ 
									$text = $action['placeHolder'];
								}else{
									$text = '';
								}
							}

						}
						
						$showText=true;
						
						if( isset($conditionalField[$action['id']])){
							$showText=false;
							$recivedObj=isset($elementValue[$conditionalField[$action['id']]]) ? $elementValue[$conditionalField[$action['id']]] :" ";
							if(isset($recivedObj['type']) && $recivedObj['type']=="radio"){
								if($recivedObj["checked"]!=""){
									$showText=true;
									
								}
							}
							elseif( isset($recivedObj['type']) && $recivedObj['type']=="dropdown"){
								foreach($recivedObj['permission'] as $permission){
									if($permission['SenderId']==$recivedObj['id'] &&  $permission['value']==$recivedObj["text"]){
										$showText=true;
									}
								}
							}elseif( isset($recivedObj['type']) && $recivedObj['type']=="checkbox" && $recivedObj["checked"] != ""){
								$showText=true;
							}
						}
						
						if($showText){
							$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale) - 3, self::scale($action['yPos'], $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true );
						}
						
					}elseif ($action['type'] == "dropdown") {
						$editted = true;
						if($action['text'] !='Select'){
							$text =  $action['text'];
						}else{
							$text =  '';
						}
						$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale) - 3, self::scale($action['yPos'], $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true );
					}elseif ($action['type'] == "radio") {

						$checked=false;
						if($action['checked']=='1'){
							$checked=true;
							$pdf->Circle(self::scale($action['xPos'], $scale)+5,self::scale($action['yPos'], $scale)+5,5, 0, 360, 'F');
						}
					}
					elseif ($action['type'] == "checkbox") {
						
						$checked=false;
						if(isset($action['checked']) && $action['checked']=='1'){
						
							$checked=true;
						}
						if($checked){
							
							$pdf->Rect(self::scale($action['xPos'], $scale),self::scale($action['yPos'], $scale),10, 10, 'F');
						}
						
					}

				}
			}
		
		}
		$outputName =time().uniqid().".pdf";
		$pdf->Output($outputName, 'D');
		if(file_exists($inputPath)){
			unlink($inputPath);
		}
		return  1;
	}

	public function tempRegeratePdf($id,$pdfFile=""){
		ini_set('memory_limit', '1024M');
		$mainResponse = $this->docusignDetailService->getDetailsById($id);
	
		$inaction = unserialize($mainResponse->data);
		
		$spurcePdf =$this->documentSendService->getDocumentServiceInApi($mainResponse->document_report_id);
		$documents =$this->templateService->getDetailsById($mainResponse->template_id);

		/*end */
		$pdf = new PDF();
		
		$inputPath =public_path()."".$pdfFile;

		$pdf->numPages = $pdf->setSourceFile($inputPath);
		
		$signed = $updatedFields = $editted = false;
		foreach(range(1, $pdf->numPages, 1) as $page) {
			
			$rotate = false;
			$degree = 0;
			try {
				$pdf->_tplIdx = $pdf->importPage($page);
				
			}
			catch(\Exception $e) {
			return false;
			}
			
			foreach($inaction as $action) {
				if(((int) $action['page']) === $page && $action['type'] == "rotate") {
					$rotate = $editted = true;
					$degree = $action['degree'];
					break;
				}
			}
			
			$size = $pdf->getTemplateSize($pdf->_tplIdx);
			$docWidth = 636;
			if(isset($documents->docWidth) && $documents->docWidth !=""){
				$docWidth = $documents->docWidth;
			}
			$scale = round($size['w'] /$docWidth, 3);
			
			$pdf->AddPage(self::orientation($size['w'], $size['h']), array($size['w'], $size['h'], 'Rotate'=>$degree), true);
			$pdf->useTemplate($pdf->_tplIdx);
			$checkPermissionId= array();
			$checkedRadio= array();
			foreach($inaction as $action) {
				$checkPermissionId[$action['id']] = $action['id'];
				if($action['type'] =='radio'&& $action['checked'] !=''){
					$checkedRadio["checked"] = $action['checked'];
				}
				
			}
			foreach($inaction as $action) {
				if(((int) $action['page']) === $page) {
				
					if ($action['type'] == "image" || $action['type'] == "signature") {
						if($action['text'] !=''){
							$editted = true;
							$imageArray = base64_encode($action['text']);
							$imgdata = base64_decode($imageArray);
							
							$pdf->Image($imgdata, self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), 100, self::scale($action['height'], $scale), '', '', '', false);
						}
					}elseif ($action['type'] == "text") {
					
						$editted = true;
						if($action['font'] == 'undefined'){
							$font = 10;
						}else{
							$font = $action['font'];
						}
						$pdf->setFontSize($font);
						$explodes = explode('_',$action['id']);
						if(isset($action['text']) && $action['text'] !=''){
							$text = $action['text'];
						}else{
						
							if($explodes[0] =='intake'){
								$text='';
							}elseif($explodes[0] =='caregiver'){
								$text='';
							}else{
								if(trim($action['placeHolder']) != 'Textbox'  && trim($action['placeHolder']) != 'Date Signed'){ 
									$text = $action['placeHolder'];
								}else{
									$text = '';
								}
							}

						}
						
						$showText=true;
						if( isset($conditionalField[$action['id']])){
							$showText=false;
							
							$recivedObj=isset($elementValue[$conditionalField[$action['id']]]) ? $elementValue[$conditionalField[$action['id']]] :" ";
							if(isset($recivedObj['type']) && $recivedObj['type']=="radio"){
								if($recivedObj["checked"]!=""){
									$showText=true;
									
								}
							}
							elseif( isset($recivedObj['type']) && $recivedObj['type']=="dropdown"){
								foreach($recivedObj['permission'] as $permission){
									if($recivedObj['SenderId']==$recivedObj['id'] &&  $permission['value']==$recivedObj["text"]){
										$showText=true;
									}
								}
							}elseif( isset($recivedObj['type']) && $recivedObj['type']=="checkbox" && $recivedObj["checked"] != ""){
								$showText=true;
							}
						}
						
						if($showText){
							$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale) - 3, self::scale($action['yPos'], $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true );
						}
						
					}elseif ($action['type'] == "dropdown") {
						$editted = true;
						if($action['text'] !='Select'){
							$text =  $action['text'];
						}else{
							$text =  '';
						}
						$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale) - 3, self::scale($action['yPos'], $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true );
					}elseif ($action['type'] == "radio") {
						
						$checked=false;
						if($action['checked']=='1'){
							$checked=true;
							$pdf->Circle(self::scale($action['xPos'], $scale)+5,self::scale($action['yPos'], $scale)+5,5, 0, 360, 'F');
						}
					}
					elseif ($action['type'] == "checkbox") {
						$checked=false;
						if(isset($action['checked']) && $action['checked']=='1'){
						
							$checked=true;
						}
						if($checked){
							
							$pdf->Rect(self::scale($action['xPos'], $scale),self::scale($action['yPos'], $scale),10, 10, 'F');
						}
						
					}

				}
			}
		
		}
		$outputName =time().uniqid().".pdf";
		$outputPath = public_path()."/".self::DOCUSIGN_FOLDER."/". $outputName;
		$pdf->Output($outputName, 'D');
	}

	public function tempRegeratePdfNew($response,$templateId,$files){
		ini_set('memory_limit', '1024M');
		$inaction = $response;
		$headers=array();

		foreach($inaction as $obj){
			if(isset($obj['permission'])){
				
				$headers=$obj['permission'];
			}
		}
		$conditionalField=array();
		if(!empty($headers)){
			foreach($headers as  $obj){
				if($obj["type"] =='checkbox'){
					$conditionalField[$obj["ReceiverDivId"]]=$obj["SenderDivId"];
				}else{
					$conditionalField[$obj["ReceiverDivId"]]=$obj["SenderId"];
				}
			}
		}
		$elementValue=array();
		foreach($inaction as $obj){
			$elementValue[$obj["id"]]=$obj;
		}
		$documents =$this->templateService->getDetailsById($templateId);
		
		/*end */
		$pdf = new PDF(null, 'px');
		
		$inputPath =public_path()."".$files;

		$pdf->numPages = $pdf->setSourceFile($inputPath);
		
		$signed = $updatedFields = $editted = false;

		foreach(range(1, $pdf->numPages, 1) as $page) {
			
			$rotate = false;
			$degree = 0;
			try {
				$pdf->_tplIdx = $pdf->importPage($page);
				
			}
			catch(\Exception $e) {
			return false;
			}
			
			foreach($inaction as $action) {
				if(((int) $action['page']) === $page && $action['type'] == "rotate") {
					$rotate = $editted = true;
					$degree = $action['degree'];
					break;
				}
			}
			
			$size = $pdf->getTemplateSize($pdf->_tplIdx);
			$docWidth = 636;
			if(isset($documents->docWidth) && $documents->docWidth !=""){
				$docWidth = $documents->docWidth;
			}
			$scale = round($size['w'] /$docWidth, 3);
			
			$pdf->AddPage(self::orientation($size['w'], $size['h']), array($size['w'], $size['h'], 'Rotate'=>$degree), true);
			$pdf->useTemplate($pdf->_tplIdx);
			$checkPermissionId= array();
			$checkedRadio= array();
			foreach($inaction as $action) {
				$checkPermissionId[$action['id']] = $action['id'];
				if($action['type'] =='radio'&& $action['checked'] !=''){
					$checkedRadio["checked"] = $action['checked'];
				}
				
			}
			foreach($inaction as $action) {
				if(((int) $action['page']) === $page) {
				
					if ($action['type'] == "image" || $action['type'] == "signature") {
						if($action['text'] !=''){
							$editted = true;
							$imageArray = base64_encode($action['text']);
							$imgdata = base64_decode($imageArray);
							
							$pdf->Image($imgdata, self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), 100, self::scale($action['height'], $scale), '', '', '', false);
						}
					}elseif ($action['type'] == "text") {
					
						$editted = true;
						if($action['font'] == 'undefined'){
							$font = 10;
						}else{
							$font = $action['font'];
						}
						$pdf->setFontSize($font);
						$explodes = explode('_',$action['id']);
						if(isset($action['text']) && $action['text'] !=''){
							$text = $action['text'];
						}else{
						
							if($explodes[0] =='intake'){
								$text='';
							}elseif($explodes[0] =='caregiver'){
								$text='';
							}else{
								if(trim($action['placeHolder']) != 'Textbox'  && trim($action['placeHolder']) != 'Date Signed'){ 
									$text = $action['placeHolder'];
								}else{
									$text = '';
								}
							}

						}
						
						$showText=true;
					
						
						
						if( isset($conditionalField[$action['id']])){
							$showText=false;
							
							$recivedObj=isset($elementValue[$conditionalField[$action['id']]]) ? $elementValue[$conditionalField[$action['id']]] :" ";
							if(isset($recivedObj['type']) && $recivedObj['type']=="radio"){
								if($recivedObj["checked"]!=""){
									$showText=true;
									
								}
							}
							elseif( isset($recivedObj['type']) && $recivedObj['type']=="dropdown"){
								foreach($recivedObj['permission'] as $permission){
									if($permission['SenderId']==$recivedObj['id'] &&  $permission['value']==$recivedObj["text"]){
										$showText=true;
									}
								}
							}elseif( isset($recivedObj['type']) && $recivedObj['type']=="checkbox" && $recivedObj["checked"]!=""){
								$showText=true;
							}

						}
						
						if($showText){
							$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale) - 3, self::scale($action['yPos'], $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true );
							
						}
						
					}elseif ($action['type'] == "dropdown") {
						$editted = true;
						if($action['text'] !='Select'){
							$text =  $action['text'];
						}else{
							$text =  '';
						}
						$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale) - 3, self::scale($action['yPos'], $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true );
					}elseif ($action['type'] == "radio") {
						
						$checked=false;
						if($action['checked']=='1'){
							$checked=true;
							$pdf->Circle(self::scale($action['xPos'], $scale)+5,self::scale($action['yPos'], $scale)+5,5, 0, 360, 'F');
						}
					}
					elseif ($action['type'] == "checkbox") {
						
						$checked=false;
						if(isset($action['checked']) && $action['checked']=='1'){
						
							$checked=true;
						}
						if($checked){
							
							$pdf->Rect(self::scale($action['xPos'], $scale),self::scale($action['yPos'], $scale),30, 30, 'F');
						}
						
					}

				}
			}

		}
		$outputName =time().uniqid().".pdf";
		$pdf->Output($outputName, 'D');
		
		return  1;
	}

	public function regeneratethepdfWriteDocument($id){
		
		$imagesArray = [];
		$mainResponse = WriteDocument::where('id', $id)->first();
		if($mainResponse->type =='Document'){
			$getDetails = $this->documentPatientService->getDocumentDetailsById($mainResponse->document_patient_id);
			$pid = $getDetails->patient_id;
		}else{
			$getDetails = $this->documentSendService->getGroupPendingNew($mainResponse->document_patient_id);
			$pid = $getDetails->main_intakeId;
		}

		$getPatientDetailsNew = $this->patientService->getPatientDetailsByIdWhitoutAgency($pid);
		
		$inaction = unserialize($mainResponse->response);
		try{
	
		
			$headers=array();
			foreach($inaction as $obj){
				if(isset($obj['permission'])){
					
					$headers=$obj['permission'];
				}
			}
			$conditionalField=array();
			if(!empty($headers)){
				foreach($headers as  $obj){
					if($obj["type"] =='checkbox'){
						$conditionalField[$obj["ReceiverDivId"]]=$obj["SenderDivId"];
					}else{
						$conditionalField[$obj["ReceiverDivId"]]=$obj["SenderId"];
					}
				}
			}
			$elementValue=array();
			foreach($inaction as $obj){
				$elementValue[$obj["id"]]=$obj;
			}
			$pdf = new PDF(null, 'px');
			$pdf->SetAutoPageBreak(false, 0);
			$pdf->SetMargins(0, 0, 0);
			$pdf->SetHeaderMargin(0);
			$pdf->SetFooterMargin(0);
			$inputPathLocal =public_path()."/".self::ESIGN_PATIENT_WRITE_DOCUMENT."/".$mainResponse->file_upload;
			$demoFile = "";

			if(file_exists($inputPathLocal)){
				$inputPath = $inputPathLocal;
			}else{
				if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
					$expiry = Carbon::now()->addMinutes(10);
					$path = self::ESIGN_PATIENT_WRITE_DOCUMENT.'/' .  $mainResponse->file_upload;
					$inputPath = Storage::disk('s3')->temporaryUrl($path, $expiry);

					$moveToLocalServerFile = $this->moveToLocalServerFile($inputPath,'D-'.$id);

					$inputPath = $moveToLocalServerFile['inputPath'];
				}
			}

			$pageCount = $pdf->setSourceFile($inputPath);
			$templateId = $pdf->importPage(1);
			$size = $pdf->getTemplateSize($templateId);
			$folder = 'D-'.$id;
			$converted = $this->commonGsPath($folder,$inputPath,$size);

			$pdf->numPages = $pdf->setSourceFile($converted);
			
			$signed = $updatedFields = $editted = false;
		
			foreach(range(1, $pdf->numPages, 1) as $page) {
				$rotate = false;
				$degree = 0;
				try {
					$pdf->_tplIdx = $pdf->importPage($page);
				}
				catch(\Exception $e) {
					return false;
				}
			
				foreach($inaction as $action) {
					if(((int) $action['page']) === $page && $action['type'] == "rotate") {
						$rotate = $editted = true;
						$degree = $action['degree'];
						break;
					}
				}

				$size = $pdf->getTemplateSize($pdf->_tplIdx);
				// Separate X/Y scales: converts CSS pixel coordinates to PDF page coordinates
				$scaleX = 1;
				if (!empty($mainResponse->docWidth) && $mainResponse->docWidth != 0) {
					$scaleX = $size['w'] / $mainResponse->docWidth;
				}

				$scaleY = $scaleX; // fallback to uniform scale if no height stored

				if (!empty($mainResponse->docHeight) && $mainResponse->docHeight != 0) {
					$scaleY = $size['h'] / $mainResponse->docHeight;
				}

				$pdf->AddPage(self::orientation($size['w'], $size['h']), array($size['w'], $size['h'], 'Rotate'=>$degree), true);
				$pdf->useTemplate($pdf->_tplIdx);

				$checkPermissionId= array();
				$checkedRadio= array();

				foreach($inaction as $action) {
					$checkPermissionId[$action['id']] = $action['id'];
					if($action['type'] =='radio'&& $action['checked'] !=''){
						$checkedRadio["checked"] = $action['checked'];
					}
				}

				foreach($inaction as $action) {

					if(((int) $action['page']) === $page) {

						if ($action['type'] == "image" || $action['type'] == "signature") {

							$action['text'] = $action['image'];
							if($action['text'] !=''){

								$imageData = str_replace("https://", "http://", $action['text']);
								$editted = true;
								if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
									$imageArray = base64_encode($imageData);
								}else{
									$imageArray = base64_encode($action['text']);
								}

								$imgdata = base64_decode($imageArray);

								$imgX = self::scale($action['xPos'], $scaleX);
								$imgY = self::scale($action['yPos'], $scaleY);
								$imgW = self::scale($action['width'], $scaleX);
								$imgH = self::scale($action['height'], $scaleY);
								// Lock cursor position before placing image to prevent TCPDF internal offset
								$pdf->SetXY($imgX, $imgY);
								$pdf->Image($imgdata, $imgX, $imgY, $imgW, $imgH, '', '', '', false);

								$width = $action['width'];
								$height = $action['height'];
								if(isset($action['verified']) && $action['verified'] =="1"){
									$pageWidth = $pdf->getPageWidth();

									/**********X position */
									if(($imgX + $imgW + 140) < $pageWidth){
										$relativeX = $imgX + $imgW + 5;
									}else{
										$relativeX = $imgX - 140 - 5;
									}

									if($imgY - 40 > 0){
										$relativeY = $imgY - 40;
									}else{
										$relativeY = $imgY + $imgH + 5;
									}

									$x = $relativeX;
									$y = $relativeY;
									$w = $imgW;
									$h = $imgH;
									$logoWidth = 30;
									$logoHeight = 30;
									$pdf->SetXY($x + 3, $y + 3);
									$pdf->Image(env('HOST_WEB_URL').self::PORTAL_FAVICON, $x+3, $y + 3, $logoWidth, $logoHeight, '', '', '', false);
									$font = public_path().'/'.self::PORTAL_ASSETS_FONT;
									$fontname = \TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);
									$pdf->SetFont($fontname, '', 9, '', true);
									$pdf->SetTextColor(40, 90, 140);

									// Verified by text
									$pdf->SetXY($x + $logoWidth + 3, $y + 3);
									$verifiedByText = self::PORTAL_VERIFIED_BY;
									
									$pdf->Cell($w - $logoWidth - 5, 5, $verifiedByText, 0, 1, 'L', false);
									$font = public_path().'/'.self::PORTAL_ASSETS_FONT;
									$fontname = \TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);

									$pdf->SetFont($fontname, '', 9, '', true);
									
									$pdf->SetTextColor(0, 150, 0); // Green text
									$pdf->SetXY($x + $logoWidth + 3, $y + 20);
									$pdf->Cell($w - $logoWidth - 5, 10, date(self::ESIGN_DATE_FORMAT), 0, 1, 'L', false);

									\TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);
									$pdf->SetFont('helvetica', 'BI', 10);
									$pdf->SetTextColor(80, 80, 80);
									$pdf->SetXY($x, $y + 35);
									$electronicSignText = self::PORTAL_ELECTRONIC_SIGN;
									if (!empty($action['doctor_name'])) {
										$electronicSignText .= ' by ' . $action['doctor_name'].' '.date('m/d/y h:i A');
									}
									// $pdf->Cell(140, 5, $electronicSignText, 0, 1, 'C');
									$pdf->MultiCell(140, 5, $electronicSignText, 0, 'C', false, 1);

									$pdf->SetLineStyle([
										'width' => 0.5,
										'dash' => '2,2',
										'color' => [160, 160, 160]
									]);
									$pdf->RoundedRect($x, $y, 140, 35, 2.5, '1111');
									
								}
								$imagesArray[] = $action['text'];
							}
							
						}elseif ($action['type'] == "stamp") {

							$pdf->SetTextColor(0, 0, 0);
							$pdf->SetFont('helvetica', '', 9);
							$action['text'] = $action['image'];
							if($action['text'] !=''){
								$imageData = str_replace("https://", "http://", $action['text']);
								$editted = true;
								if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
									$imageArray = base64_encode($imageData);
								}else{
									$imageArray = base64_encode($action['text']);
								}
								$imgdata = base64_decode($imageArray);
								$stampX = self::scale($action['xPos'], $scaleX);
								$stampY = self::scale($action['yPos'], $scaleY);
								$pdf->SetXY($stampX, $stampY);
								$pdf->Image($imgdata, $stampX, $stampY, self::scale($action['width'], $scaleX), self::scale($action['height'], $scaleY), '', '', '', false);

								$normalImageArray[] = $action['text'];
							}
						}elseif ($action['type'] == "text") {
							$pdf->SetTextColor(0, 0, 0);
							$editted = true;
							if($action['font'] == 'undefined'){
								$font = 10;
							}else{
								$font = $action['font'];
							}
							$pdf->setFontSize($font);
							$pdf->SetFont('courier', '', $font);
							$explodes = explode('_',$action['id']);
							if(isset($action['text']) && $action['text'] !=''){
								$text = $action['text'];
							}else{
							
								if($explodes[0] =='intake'){
									$text='';
								}elseif($explodes[0] =='caregiver'){
									$text='';
								}else{
									if(trim($action['placeHolder']) != 'Textbox'  && trim($action['placeHolder']) != 'Date Signed'){ 
										$text = $action['placeHolder'];
									}else{
										if($explodes[0] =='datesigned'){
											$text="";
											if(isset($action['text'])){
												if($action['text'] !=""){
													$text = "";
												}else{
													$text = date(self::DATE_FORMAT_MDY);
												}
											}
										}else{
											$text = '';
										}
									}
								}
							}
							$showText=true;
							if( isset($conditionalField[$action['id']])){
								$showText=false;
								$recivedObj=isset($elementValue[$conditionalField[$action['id']]]) ? $elementValue[$conditionalField[$action['id']]] :" ";
								if(isset($recivedObj['type']) && $recivedObj['type']=="radio"){
									if($recivedObj["checked"]!=""){
										$showText=true;
									}
								}
								elseif( isset($recivedObj['type']) && $recivedObj['type']=="dropdown"){
									foreach($recivedObj['permission'] as $permission){
										if($permission['SenderId']==$recivedObj['id'] &&  $permission['value']==$recivedObj["text"]){
											$showText=true;
										}
									}
								}elseif( isset($recivedObj['type']) && $recivedObj['type']=="checkbox" && $recivedObj["checked"] != ""){
									$showText=true;
								}
							}
							if($showText){
								$textX = self::scale($action['xPos'], $scaleX) - 3;
								$textY = self::scale($action['yPos'], $scaleY);
								$pdf->SetXY($textX, $textY);
								$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scaleX), self::scale($action['height'], $scaleY), $textX, $textY, str_replace("%22", '"', $text), 0, 0, false, true, '', true );
							}
						}elseif ($action['type'] == "dropdown") {
							$pdf->SetTextColor(0, 0, 0);
							$pdf->SetFont('helvetica', '', $font);
							$editted = true;
							if($action['text'] !='Select'){
								$text =  $action['text'];
							}else{
								$text =  '';
							}
							if($id =='17'){ //Add comment for info Document Id
								$action['width'] = $action['width']+200;
							}

							$dropX = self::scale($action['xPos'], $scaleX) - 3;
							$dropY = self::scale($action['yPos'], $scaleY);
							$pdf->SetXY($dropX, $dropY);
							$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scaleX), self::scale($action['height'], $scaleY), $dropX, $dropY, str_replace("%22", '"', $text), 0, 0, false, true, '', true );
						}elseif ($action['type'] == "radio") {
							$pdf->SetTextColor(0, 0, 0);
							$pdf->SetFont('helvetica', '', $font);

							$checked=false;
							if($action['checked']=='1'){
								$checked=true;
								$radioX = self::scale($action['xPos'], $scaleX) + 5;
								$radioY = self::scale($action['yPos'], $scaleY) + 5;
								$pdf->SetXY($radioX, $radioY);
								$pdf->Circle($radioX, $radioY, 5, 0, 360, 'F');
							}
						}
						elseif ($action['type'] == "checkbox") {
							$pdf->SetTextColor(0, 0, 0);
							$pdf->SetFont('times', '', $font);

							$checked=false;
							if(isset($action['checked']) && $action['checked']=='1'){
								$checked=true;
							}
							if($checked){
								$cbX = self::scale($action['xPos'], $scaleX);
								$cbY = self::scale($action['yPos'], $scaleY);
								$pdf->SetXY($cbX, $cbY);
								$pdf->Rect($cbX, $cbY, 10, 10, 'F');
							}

						}elseif ($action['type'] == "sign_verify") {
							$width = $action['width'];
							$height = $action['height'];
							if(1){
								$x = self::scale($action['xPos'], $scaleX);
								$y = self::scale($action['yPos'], $scaleY);
								$w = self::scale($width, $scaleX);
								$h = self::scale($height, $scaleY);

								$logoWidth = 30;
								$logoHeight = 30;
								$pdf->SetXY($x + 3, $y + 3);
								$pdf->Image(env('HOST_WEB_URL').self::PORTAL_FAVICON, $x+3, $y + 3, $logoWidth, $logoHeight, '', '', '', false);
								$font = public_path().'/'.self::PORTAL_ASSETS_FONT;
								$fontname = \TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);
								$pdf->SetFont($fontname, '', 9, '', true);
								
								$pdf->SetTextColor(40, 90, 140);
								// Verified by text
								$pdf->SetXY($x + $logoWidth + 3, $y + 3);
								$verifiedByText = self::PORTAL_VERIFIED_BY;
								
								$pdf->Cell($w - $logoWidth - 5, 5, $verifiedByText, 0, 1, 'L', false);
								$font = public_path().'/'.self::PORTAL_ASSETS_FONT;
								$fontname = \TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);
								$pdf->SetFont($fontname, '', 9, '', true);
								$pdf->SetTextColor(0, 150, 0); // Green text
								$pdf->SetXY($x + $logoWidth + 3, $y + 20);
								$pdf->Cell($w - $logoWidth - 5, 10, date(self::ESIGN_DATE_FORMAT), 0, 1, 'L', false);
								
								\TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);
								$pdf->SetFont('helvetica', 'BI', 10);
								$pdf->SetTextColor(80, 80, 80);
								$pdf->SetXY($x, $y + 35);
								$electronicSignText = self::PORTAL_ELECTRONIC_SIGN;
								if (!empty($action['doctor_name'])) {
									$electronicSignText .= ' by ' . $action['doctor_name'].' '.date('m/d/y h:i A');
								}
								// $pdf->Cell(140, 5, $electronicSignText, 0, 1, 'C');
								$pdf->MultiCell(140, 5, $electronicSignText, 0, 'C', false, 1);
								$pdf->SetLineStyle([
									'width' => 0.5,
									'dash' => '2,2',
									'color' => [160, 160, 160]
								]);
								$pdf->RoundedRect($x, $y, 140, 35, 2.5, '1111');
							
							}
						}
					}
				}
			}

			$folderName = $getPatientDetailsNew->agency_id.'/'.$getPatientDetailsNew->id;
				
			$dateTime = date('mdy').time();
			$outputName =$folderName.'/'.$dateTime.uniqid().".pdf";
			if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
				$pdfContain = $pdf->Output("", 'S');
				Storage::disk('s3')->put(self::ESIGN_PATIENT_WRITE_DOCUMENT.'/' . $outputName,$pdfContain);
				$expiry = Carbon::now()->addMinutes(10);
					$path = self::ESIGN_PATIENT_WRITE_DOCUMENT.'/' . $outputName;
					$file_data = Storage::disk('s3')->temporaryUrl($path, $expiry);
			}else{
				$fileDir = public_path()."/".self::ESIGN_PATIENT_WRITE_DOCUMENT."/". $folderName;
				if (!File::exists($fileDir)) {
					File::makeDirectory($fileDir, 0777, true, true);
				}
				
				$outputPath = public_path()."/".self::ESIGN_PATIENT_WRITE_DOCUMENT.'/'.$outputName;
				$pdf->Output($outputPath, 'F');
				$file_data = URL::to('/')."/".self::ESIGN_PATIENT_WRITE_DOCUMENT."/". $outputName;
			}

			if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
				if(!empty($imagesArray[0])){
					foreach($imagesArray as $img){
						$imgs = str_replace(env('HOST_WEB_URL'),'',$img);
						$img = public_path('/').''.$imgs;
						if(file_exists($img)){
							unlink($img);
						}

					}
				}
			}

			if($converted){
				$directory = dirname($converted);
				if (File::isDirectory($directory)) {
					File::deleteDirectory($directory);
				}
			}
			return  ['id'=>$id,'file'=>$file_data,'file_name'=>$outputName,'demo_file'=>$demoFile,'converted'=>$converted];
		} catch (\Throwable $th) {
			$auth = auth()->user();
			$userId = null;
			if(isset($auth['id'])){
				$userId =$auth['id'];
			}
			DB::table('esign_error_log')->insert([
				'error_log' => $th->getMessage(),
				'esign_response' => $mainResponse->response,
				'line'    => $th->getLine(),
				'trace'   => $th->getTraceAsString(),
				'created_date' =>date('Y-m-d H:i:s'),
				'document_id'=>$mainResponse->id,
				'record_id'=>null,
				'template_id'=>$id,
				'created_by'=>$userId,
				'type'=>'Upload Esign Document'
			]);
		}
	}

	public function editRegeneratethepdf($insert,$id,$sessionId,$document_report_id,$groupId,$sent_on="",$existingResponse=""){
		
		$mainResponse = $this->docusignDetailService->getDetailsById($insert);
		$inaction = unserialize($mainResponse->data);
		
		$headers=array();
			
		foreach($inaction as $obj){
			if(isset($obj['permission'])){
				
				$headers=$obj['permission'];
			}
		}

		$conditionalField=array();
		if(!empty($headers)){
			foreach($headers as  $obj){
				if($obj["type"] =='checkbox'){
					$conditionalField[$obj["ReceiverDivId"]]=$obj["SenderDivId"];
				}else{
					$conditionalField[$obj["ReceiverDivId"]]=$obj["SenderId"];
				}
			}
		}

		$elementValue=array();
		foreach($inaction as $obj){
			$elementValue[$obj["id"]]=$obj;
		}
			
		$documents =$this->templateService->getDetailsById($id);
			
		$checkbox_mark_flag = 0;
		if(isset($documents->checkbox_mark_flag) && $documents->checkbox_mark_flag ==1){
			$checkbox_mark_flag = 1;
		}

		/*end */
		$pdf = new PDF(null, 'px');
		$pdf->SetAutoPageBreak(false, 0);
		$inputPath =public_path()."/".self::DOCUSIGN_FOLDER."/".$documents->upload_document;
	
		if(file_exists($inputPath)){
			$inputPath =public_path()."/".self::DOCUSIGN_FOLDER."/".$documents->upload_document;
		}else{
			if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
			file_put_contents(public_path(self::DOCUSIGN_FOLDER.'/' .$documents->upload_document), $inputPath);
			$inputPath =public_path()."/".self::DOCUSIGN_FOLDER."/".$documents->upload_document;
			}else{
				$expiry = Carbon::now()->addMinutes(10);
				$path = self::DOCUSIGN_FOLDER.'/' . $documents->upload_document;
				$inputPath = Storage::disk('s3')->temporaryUrl($path, $expiry);
			}
		}
	
		$pdf->numPages = $pdf->setSourceFile($inputPath);

		$signed = $updatedFields = $editted = false;
		$finalNewResponse = [];
		foreach(range(1, $pdf->numPages, 1) as $page) {
				
			$rotate = false;
			$degree = 0;
			try {
				$pdf->_tplIdx = $pdf->importPage($page);
				
			}
			catch(\Exception $e) {
				return false;
			}
				
			foreach($inaction as $action) {
				if(((int) $action['page']) === $page && $action['type'] == "rotate") {
					$rotate = $editted = true;
					$degree = $action['degree'];
					break;
				}
			}
				
			$size = $pdf->getTemplateSize($pdf->_tplIdx);
				
			if ($documents->docWidth != 0) {
				$scale = round($size['w'] / $documents->docWidth, 3);
			} else {
				$scale = 0;
			}
				
			$pdf->AddPage(self::orientation($size['w'], $size['h']), array($size['w'], $size['h'], 'Rotate'=>$degree), true);
			$pdf->useTemplate($pdf->_tplIdx);
			$checkPermissionId= array();
			$checkedRadio= array();

			foreach($inaction as $action) {
				$checkPermissionId[$action['id']] = $action['id'];
				if($action['type'] =='radio'&& $action['checked'] !=''){
					$checkedRadio["checked"] = $action['checked'];
				}
			}
		
			foreach($inaction as $action) {
				if(((int) $action['page']) === $page) {
					
					if ($action['type'] == "image" || $action['type'] == "signature") {
						$finalNewResponse[$action['id']] = basename($action['text']);
						if($action['text'] !=''){
							if ($documents->docWidth != 0) {
								$scale = round($size['w'] / $documents->docWidth, 3);
							} else {
								$scale = 0;
							}

							$editted = true;
							
							$imageArray = $this->getAwsDocusignImages($action['text'],$action['updatedSelectType']);
							$imgdata = base64_decode(base64_encode($imageArray));
							$height = 100;
							if(isset($action['height'])){
								$height = $action['height'];
							}
							if(strtolower($sent_on) =='stampuser'){
								$pdf->Image($imgdata, self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), self::scale($action['width'], $scale), self::scale($action['height']+25, $scale), '', '', '', false);
							}else{
								$height = $action['height'];
								$width=$action['width'];
								
								$pdf->Image($imgdata, self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), self::scale($width, $scale), self::scale($height, $scale), '', '', '', false);
							
								if(isset($documents->show_verify_by) && $documents->show_verify_by =="Y"){
									$pageWidth = $pdf->getPageWidth();
									
									/**********X position */
									if((self::scale($action['xPos'], $scale) + self::scale($width, $scale) +140 ) < $pageWidth ){
										$relativeX = self::scale($action['xPos'], $scale) + self::scale($width, $scale)+5;
									}else{
										$relativeX = self::scale($action['xPos'], $scale) - 140-5;
									}

									if(self::scale($action['yPos'], $scale) - 40 > 0){
										$relativeY = self::scale($action['yPos'], $scale) - 40;
									}else{
										$relativeY = self::scale($action['yPos'], $scale) +self::scale($height, $scale) + 5;
									}

									$x =$relativeX;
									
									$y = $relativeY;
									$w = self::scale($width, $scale);
									$h = self::scale($height, $scale);

									$pdf->SetLineStyle([
										'width' => 1,
										'dash' => '2,2',
										'color' => [100, 100, 100]
									]);
									$pdf->Rect($x, $y, 140, 35);

									$logoWidth = 30;
									$logoHeight = 30;
									$pdf->Image(env('HOST_WEB_URL').self::PORTAL_FAVICON, $x+3, $y + 3, $logoWidth, $logoHeight, '', '', '', false);
									$font = public_path().'/'.self::PORTAL_ASSETS_FONT;
									$fontname = \TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);
									$pdf->SetFont($fontname, '', 9, '', true);

									$pdf->SetTextColor(40, 90, 140);

									// Verified by text
									$pdf->SetXY($x + $logoWidth + 3, $y + 3);
									$verifiedByText = self::PORTAL_VERIFIED_BY;
									
									$pdf->Cell($w - $logoWidth - 5, 5, $verifiedByText, 0, 1, 'L', false);
									$font = public_path().'/'.self::PORTAL_ASSETS_FONT;
									$fontname = \TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);
									
									$pdf->SetFont($fontname, '', 9, '', true);
									
									$pdf->SetTextColor(0, 150, 0); // Green text
									$pdf->SetXY($x + $logoWidth + 3, $y + 20);
									$pdf->Cell($w - $logoWidth - 5, 10, date(self::DATE_FORMAT_MDY), 0, 1, 'L', false);
								}
							
							}
							$normalImageArray[] = $action['text'];
						}
					}elseif ($action['type'] == "stamp") {
					
						$finalNewResponse[$action['id']] = basename($action['text']);
						if($action['text'] !=''){
							if(isset($mainResponse->docWidth) && $mainResponse->docWidth !=""){
								if ($mainResponse->docWidth != 0) {
									$scale = round($size['w'] / $mainResponse->docWidth, 3);
								} else {
									if ($documents->docWidth != 0) {
										$scale = round($size['w'] / $documents->docWidth, 3);
									} else {
										$scale = 0;
									}
								}
							}else{
								if ($documents->docWidth != 0) {
									$scale = round($size['w'] / $documents->docWidth, 3);
								} else {
									$scale = 0;
								}
							}

							// $imageData = str_replace("https://", "http://", $action['text']);
							$editted = true;
							$imageArray = $this->getAwsDocusignImages($action['text'],0);

							$imgdata = base64_decode(base64_encode($imageArray));
							$pdf->Image($imgdata, self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale), self::scale($action['width'], $scale), self::scale($action['height'], $scale), '', '', '', false);

							$normalImageArray[] = $action['text'];
						}

					}elseif ($action['type'] == "text") {
						if ($documents->docWidth != 0) {
							$scale = round($size['w'] / $documents->docWidth, 3);
						} else {
							$scale = 0;
						}

						$pdf->SetTextColor(0, 0, 0);
						$editted = true;
						if($action['font'] == 'undefined'){
							$font = 10;
						}else{
							$font = $action['font'];
						}

						$pdf->setFontSize($font);
						$explodes = explode('_',$action['id']);
						if(isset($action['text']) && $action['text'] !=''){
							$text = $action['text'];
						}else{
						
							if($explodes[0] =='intake'){
								$text='';
							}elseif($explodes[0] =='caregiver'){
								$text='';
							}else{
								if(isset($action['readonly'])){
									if(trim($action['readonly']) && $action['readonly'] =='readonly'){
										$text = $action['placeHolder'];
									}
								}else{
									if($explodes[0] =='datesigned'){
										$text="";
										if(isset($action['text'])){
											if($action['text'] !=""){
												$text = $action['text'];
											}else{
												$text = date(self::DATE_FORMAT_MDY);
											}
										}
									}else{
										$text = '';
									}
								}
							}
						}
							
						$showText=true;
						
						if( isset($conditionalField[$action['id']])){
							$showText=false;
							
							$recivedObj=isset($elementValue[$conditionalField[$action['id']]]) ? $elementValue[$conditionalField[$action['id']]] :" ";
							if(isset($recivedObj['type']) && $recivedObj['type']=="radio"){
								if($recivedObj["checked"]!=""){
									$showText=true;
								}
							}
							elseif( isset($recivedObj['type']) && $recivedObj['type']=="dropdown"){
								foreach($recivedObj['permission'] as $permission){
									if($permission['SenderId']==$recivedObj['id'] &&  $permission['value']==$recivedObj["text"]){
										$showText=true;
									}
								}
							}elseif( isset($recivedObj['type']) && $recivedObj['type']=="checkbox" && $recivedObj["checked"] !=""){
								$showText=true;
							}
						}
							
						if($showText){
							$safeText = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
							$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale) - 3, self::scale($action['yPos'], $scale), $safeText, 0, 0, false, true, '', true );
							
						}
						$finalNewResponse[$action['id']] = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

					}elseif ($action['type'] == "dropdown") {
						if ($documents->docWidth != 0) {
							$scale = round($size['w'] / $documents->docWidth, 3);
						} else {
							$scale = 0;
						}

						$pdf->SetTextColor(0, 0, 0);
						$editted = true;
						if($action['text'] !='Select'){
							$text =  $action['text'];
						}else{
							$text =  '';
						}

						if($id =='17'){
							$action['width'] = $action['width']+200;
						}
							
						$pdf->writeHTMLCell( self::scale($action['width'] + 50, $scale), self::scale($action['height'], $scale), self::scale($action['xPos'], $scale) - 3, self::scale($action['yPos'], $scale), str_replace("%22", '"', $text), 0, 0, false, true, '', true );
						$finalNewResponse[$action['id']] = $text;

					}elseif ($action['type'] == "radio") {
							
						if ($documents->docWidth != 0) {
							$scale = round($size['w'] / $documents->docWidth, 3);
						} else {
							$scale = 0;
						}
							
						$pdf->SetTextColor(0, 0, 0);
							
						$checked=false;
						if($action['checked']=='1'){
							$checked=true;
							
							$pdf->SetFont('dejavusans', '', 15, '', true);
							$pdf->Text(self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale) , '✓');
						}

						$finalNewResponse[$action['id']] = $action['checked'];
					}elseif ($action['type'] == "checkbox") {

						if ($documents->docWidth != 0) {
							$scale = round($size['w'] / $documents->docWidth, 3);
						} else {
							$scale = 0;
						}

						$pdf->SetTextColor(0, 0, 0);
					
						$checked=false;
						if(isset($action['checked']) && $action['checked']=='1'){
							$checked=true;
						}
						if($checked){
							
							if($checkbox_mark_flag == 1){
								$pdf->Rect(self::scale($action['xPos'], $scale),self::scale($action['yPos'], $scale),10, 10, 'F');
							}else{
								$font = public_path().'/assets/fonts/arialuni.ttf';
								$fontname = \TCPDF_FONTS::addTTFfont($font, 'TrueTypeUnicode', '', 32);
								$pdf->SetFont($fontname, '', 15, '', true);
								$pdf->Text(self::scale($action['xPos'], $scale), self::scale($action['yPos'], $scale) , '✓');
								
							}
						}
						$finalNewResponse[$action['id']] = $action['checked'];
					}

				}
			}
			
		}
		$outputName =time().uniqid().".pdf";
		
		if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
			$fileGetContain = $pdf->Output('', 'S');
			Storage::disk('s3')->put(self::DOCUSIGN_FOLDER.'/'.$outputName,$fileGetContain);
			
		}else{
			$outputPath = public_path()."/".self::DOCUSIGN_FOLDER."/". $outputName;
			$pdf->Output($outputPath, 'F');
		}
		
		$document = $this->documentSendService->getById($groupId);
		
		$oldPdf = $document->pdf_generate;
		//new design log
		$oldResponseData =$this->documentSendService->getDocumentServiceData($document->id);
		//new design log

		$this->documentSendService->update(array('old_pdf' => $oldPdf,'pdf_generate'=>$outputName),array('id'=>$document->id));
		
		//new design log
		$newResponseData =$this->documentSendService->getDocumentServiceData($document->id);
		$newResponse = $newResponseData->toArray();
		$newResponseLogArray = $newResponse;
		unset($newResponseLogArray->templateDetails);
	
		$message = "E-Sign details have been successfully updated usign the ".$documents->template_name.' template';
		if(isset(auth()->user()->id)){
			$message = auth()->user()->first_name.' '.auth()->user()->last_name." has successfully filled the E-Sign updated usign the ".$documents->template_name.' template';
		}
		unset($oldResponseData->templateDetails);
		$insertLog = [
			'type' => 'Update Esign Form',
			'link' => url('esign/docusign/update-form'),
			'module' => 'Esign Section',
			'module_id' => $newResponseData->groupId,
			'new_response' => serialize($newResponseLogArray),
			'old_response' => serialize($oldResponseData),
			'is_status' => "Form Edit",
			'message' => $message,
			'esign_old_response'=>serialize(json_decode($existingResponse,true)),
			'esign_new_response'=>serialize($finalNewResponse),
		];
		$this->dynamicFormLogService->storeFormLog($insertLog);

		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Update Esign Form',
			'link' =>url('esign/docusign/update-form'),
			'module' => 'Patient Appointment',
			'object_id' => $oldResponseData->main_intakeId,
			'message' => $message,
			'new_response' => serialize($newResponseLogArray),
			'old_response' => serialize($oldResponseData),
			'ip' => $ipaddress,
		];

		$this->logAction($insertLog);

		// Get Group wise notification
		$signerAction = 'Form Edit Done';
		$patientId = $newResponseData->main_intakeId ?? '';
		$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($patientId);
		$agencyFk = $getPatientDetails->agency_id ?? '';
		$userType = $getPatientDetails->type ?? '';
		$title = 'Esign '.'('.$signerAction.')';
		$msg = '<br><b>Document Name : </b>'.($newResponseData->templateDetails->template_name ?? '').' ('.date(self::DATE_FORMAT_MDYHIS,strtotime($newResponseData->created_date)).')';
		
		$requestParam =[
			'type'=>'Esign',
			'title'=>$title,
			'msg'=>$msg,
			'patientId'=>$patientId,
			'agencyFk'=>$agencyFk,
			'userType'=>$userType,
			'userData'=>[],
			'serviceData'=>[],
			'sms'=>$newResponseData->sms,
			'email'=>$newResponseData->email,
		];
		$this->notificationSend($requestParam);
			// Get Group wise notification
		return  $insert;
		
	}

	public function notificationSend(array $requestParam){
		$type =$requestParam['type'];
		$title =$requestParam['title'];
		$msg =$requestParam['msg'];
		$patientId =$requestParam['patientId'];
		$agencyFk =$requestParam['agencyFk'];
		$userType =$requestParam['userType'];
		$userData =$requestParam['userData'];
		$serviceData =$requestParam['serviceData'];
		$sms =$requestParam['sms'];
		$email =$requestParam['email'];

        $userData = Utility::getGroupUsersData($agencyFk,$userType,$type,$userData,$serviceData);
		$notificationData = array(
			'users' => $userData,
			'agency_fk' => $agencyFk ?? '',
			'record_id' => $patientId ?? '',
			'title' => $title,
			'msg' => $msg,
			'type' => $type,
			'sms'=>$sms,
			'email'=>$email
		);
		Utility::insertNotificationsType($notificationData);

    }

	public function getSignerAction($sentOn) {
		$actions = [
			'formFill'   => 'Form Fill Done',
			'sign'       => 'Signature Done',
			'stamp'      => 'Stamp Done',
			'other'      => 'Other User Sign Done',
			'caregiver'  => 'Caregiver Signature Done',
			'stampUser'  => 'Stamp User Done',
			'patient'    => 'Patient Signature Done',
			'OfficeStaff'=> 'Office Staff Signature Done',
		];

		return $actions[$sentOn] ?? 'Signature Done';
	}

	public function removeStorageSignatureImages(){
		$auth = auth()->user();
		if(isset($auth->id)){
			$query = signatureUpload::select('id','file_upload')->where('user_id',$auth->id)->get();
			if(count($query) >0){
				foreach($query as $val){
					$file = public_path('/') . "/".self::ESIGN_PATIENT_WRITE_DOCUMENT."/" . $val->file_upload;
					if(file_exists($file)){
						unlink($file);
					}
				}
			}
		}
	}

	public function getAwsDocusignImages($images,$updateSelectType){
		if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
			if($updateSelectType ==1){
				$url = url('/').'/'.self::ESIGN_PATIENT_WRITE_DOCUMENT.'/'.$images;
			}else{
				$url = url('/').'/'.self::DOCUSIGN_FOLDER.'/'.$images;
			}
			
		}else{
			$expiry = Carbon::now()->addMinutes(10);
			if($updateSelectType ==1){
				
				$path = self::ESIGN_PATIENT_WRITE_DOCUMENT.'/' . $images;
				$url = Storage::disk('s3')->temporaryUrl($path, $expiry);
			}else{
				$path = self::DOCUSIGN_FOLDER.'/' . $images;
				$url = Storage::disk('s3')->temporaryUrl($path, $expiry);
			}
		}

		return $url;
	}

	private function commonGsPath($id,$inputPath,$size){
		
		$convertedDir = public_path().'/tempPDFGenerate/temp_server/'.$id;

		if (!File::exists($convertedDir)) {
			File::makeDirectory($convertedDir, 0777, true, true);
		}
		$converted = $convertedDir.'/' .  uniqid().'.pdf';
		if (env('FILE_UPLOAD_PERMISSION')  != 'development') {
			$gsPath = "gs";
		}else{
			$gsPath = '"C:\Program Files\gs\gs10.07.0\bin\gswin64c.exe"';
		}

		$flag = 1;
		if(isset($size['w'])){
			if($size['w'] < $size['h']){
				$flag = 0;
			}
		}

		if($flag ==1){
			$cmd = $gsPath
    . " -sDEVICE=pdfwrite"
    . " -dNOPAUSE"
    . " -dQUIET"
    . " -dBATCH"
    . " -dEmbedAllFonts=true"
    . " -dSubsetFonts=true"
    . " -sOutputFile=" . escapeshellarg($converted)
    . " " . escapeshellarg($inputPath)
    . " 2>&1";


			exec($cmd, $output, $returnVar);
			return $converted;
		}
		
		$cmd = $gsPath
    . " -sDEVICE=pdfwrite"
    . " -dNOPAUSE"
    . " -dQUIET"
    . " -dBATCH"
    . " -sPAPERSIZE=letter"
    . " -dPDFFitPage"
    . " -dEmbedAllFonts=true"
    . " -dSubsetFonts=true"
    . " -sOutputFile=" . escapeshellarg($converted)
    . " " . escapeshellarg($inputPath)
    . " 2>&1";
				exec($cmd, $output, $returnVar);
				return $converted;
	}

	/**
	 * Public wrapper for commonGsPath (used by eraserApplyToPdf).
	 */
	public function commonGsPathPublic($id, $inputPath)
	{
		$pdf = new PDF(null, 'px');
		$pageCount = $pdf->setSourceFile($inputPath);
		$templateId = $pdf->importPage(1);
		$size = $pdf->getTemplateSize($templateId);
		return $this->commonGsPath($id, $inputPath,$size);
	}

	private function moveToLocalServerFile($filePaths,$id){
		$filePath = public_path().'/tempPDFGenerate/temp_server/'.$id;
		$tmpFile = $filePath.'/'.uniqid().'.pdf';
		if (!File::exists($filePath)) {
			File::makeDirectory($filePath, 0777, true, true);
		}

		file_put_contents($tmpFile,file_get_contents($filePaths));

		return ['inputPath'=>$tmpFile];
	}

	private function userNotifications($newResponseData,$patientId,$submitType){
		// Get Group wise notification
		$signerAction = $this->getSignerAction($newResponseData->sent_on);
					
		$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($patientId);
		$agencyFk = $getPatientDetails->agency_id ?? '';
		$userType = $getPatientDetails->type ?? '';
		$title = ($submitType !== 'edit' ? 'Esign ' : 'Update Esign ') . "($signerAction)";

		$msg = '<br><b>Document Name : </b>'.($newResponseData->templateDetails->template_name ?? '').' ('.date(self::DATE_FORMAT_MDYHIS,strtotime($newResponseData->created_date)).')';

		$requestParam =[
			'type'=>'Esign',
			'title'=>$title,
			'msg'=>$msg,
			'patientId'=>$patientId,
			'agencyFk'=>$agencyFk,
			'userType'=>$userType,
			'userData'=>[],
			'serviceData'=>[],
			'sms'=>$newResponseData->sms,
			'email'=>$newResponseData->email,
		];

		$this->notificationSend($requestParam);
	}

	private function logDynamicForm($dynamicFormLogArray){
		$insertLog = [
			'type' => $dynamicFormLogArray['type'],
			'link' => $dynamicFormLogArray['link'],
			'module' => $dynamicFormLogArray['module'],
			'module_id' => $dynamicFormLogArray['module_id'],
			'new_response' => $dynamicFormLogArray['new_response'],
			'old_response' => $dynamicFormLogArray['old_response'],
			'is_status' => $dynamicFormLogArray['is_status'],
			'esign_new_response'=>$dynamicFormLogArray['esign_new_response'],
			'esign_old_response'=>$dynamicFormLogArray['esign_old_response'],
			'message'=>$dynamicFormLogArray['message'],
		];
		$this->dynamicFormLogService->storeFormLog($insertLog);
	}

	private function logAction($logServiceArray){
		$logServiceArray = [
			'type' => $logServiceArray['type'],
			'link' =>$logServiceArray['link'],
			'module' => $logServiceArray['module'],
			'object_id' => $logServiceArray['object_id'],
			'message' => $logServiceArray['message'],
			'new_response' => $logServiceArray['new_response'],
			'old_response' => $logServiceArray['old_response'],
			'ip' => $logServiceArray['ip'],
		];

		LogsService::save($logServiceArray);
	}

	private function secondEsignResponse($data){
		$second =$this->documentSendService->GetNextSignerDetails($data['id'],$data['groupId']);
		if($second !=''){

			$updateNew = array('status'=>'Pending','sourceFile'=>$data['outputName']);
			$this->documentSendService->update($updateNew,array('id'=>$second->id));

			if($second->templete_id ==8){
				$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($second->main_intakeId);
				$link = URL::to('/nye/').'/' . $second->id .'?id='.$data['groupId'];
				$emailData = array(
					'link' => $link
				);

				$message = Utility::getHtmlContent('email_template.email_esign_link_template',$emailData);
				$data = array('to'=>$getPatientDetails->email,'subject'=>'Esign','messages'=>$message);

				if(isset($getPatientDetails->email) && $getPatientDetails->email !=""){
					Mail::send([],[],function($message) use($data){
				
						$message->subject($data['subject']);
						$message->to($data['to']);
						$message->html($data['messages']);
					});
				}
				
				$phoneNo = "";
				if(isset($getPatientDetails->phone) && $getPatientDetails->phone !=""){
					$phoneNo = $getPatientDetails->phone;
				}
				if(isset($getPatientDetails->mobile) && $getPatientDetails->mobile !=""){
					$phoneNo = $getPatientDetails->mobile;
				}
				if($phoneNo !=""){
					$smsMessage = "Dear,<br>";
					$smsMessage .='Please complete esign from below link <br>';
					$smsMessage .=$link;
					Common::sendTwillioSms($phoneNo,$smsMessage);
				}
				$this->documentSendSmsLogService->save(array('document_id'=>$second->id,'caregiver_id'=>$second->main_intakeId,'email'=>$getPatientDetails->email,'mobile'=>$phoneNo));
			}
		}
	}

	private function signerStatusUpdateWithEmailNotification($data){
		/*Docusign pdf upload in attachment section 	*/
		$getTotalSigner = $this->documentSendService->getTotalSigner($data['groupId']);
		$getTotalComplete = $this->documentSendService->getTotalComplete($data['groupId']);
		$maintotal =$getTotalSigner - $getTotalComplete;
		$from = 'no-reply@cdpany.com';
		if(in_array($data['type'], ['caregiver', 'applicant']) && $maintotal ==0){
			$this->documentSendService->update(array('singstatus'=>'Yes'),array('groupId'=>$data['groupId']));
			
			$template_email = '';
			if(isset($data['email']) && $data['email'] !=''){
				$template_email = explode(',',$data['email']);
				$allemail = array();
				foreach($template_email as $em){
					$allemail[] = $em;
				}
				$subject ='Docusign';

				$emailData = array(
					'msg' => 'Design E-sign'
				);
				$message = Utility::getHtmlContent('email_template.email_doc_sign',$emailData);

				$url = URL::to('/').'/'.self::DOCUSIGN_FOLDER.'/'.$data['outputName'];
				$data = array('subject'=>$subject,'message'=>$message,'dd'=>$url,'from'=>$from,'to'=>$allemail);
					
				Mail::send([], $data, function($message)use($data,$from) {
				
					$message->to($data['to'], $data['message'])->subject
					($data['subject']);
					$message->from($from);
					$message->attach($data['dd']);
				});
			}
		}

		return ['maintotal'=>$maintotal];
	}

	private function autoSendMailByCaregiver($data){
	
		if(isset($data['send_caregiver_email']) && $data['send_caregiver_email'] =="Y" && $data['maintotal'] == 0){
		
			$getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($data['patient_id']);
			
			if(isset($getPatientDetails->email) && $getPatientDetails->email !=""){
				
				$emailData = array(
					'msg' => "Hello",
					'portal_id'=>$getPatientDetails->id,
					'full_name'=>$getPatientDetails->first_name.' '.$getPatientDetails->last_name,
					'template_name'=>$data['template_name'],
					'submitted_date'=>Utility::convertMDYTime($data['compTDate']),
				);
				$message = Utility::getHtmlContent('email_template.email_doc_sign',$emailData);

				if(env('FILE_UPLOAD_PERMISSION')  != 'development'){
					
					$fileContent = Storage::disk('s3')->get(self::DOCUSIGN_FOLDER.'/' .  $data['outputName']);
					$mimeType = Storage::disk('s3')->mimeType(self::DOCUSIGN_FOLDER.'/' .  $data['outputName']);
					$url = $fileContent;
				
				}else{
					$url = URL::to('/').'/'.self::DOCUSIGN_FOLDER.'/'. $data['outputName'];
					$mimeType = 'pdf';
				}
				
				$data = array('subject'=>"E-Sign Form Completion Notification",'message'=>$message,'dd'=>$url,'to'=>$getPatientDetails->email,'document_name'=>$data['template_name'],'mimeType'=>$mimeType);
				$this->commonMail($data);
			}
		}
	}

	private function commonMail($data){
		try {
			Mail::mailer('second')->send([], [], function ($messages) use ($data) {

				$messages->to($data['to'], "")->subject($data['subject'])->html($data['message']);
				if(isset($data['dd']) && $data['dd'] !=""){
					$messages->attachData($data['dd'], $data['document_name'], [
						'mime' => $data['mimeType']
					]);
				}
			});
		} catch (\Throwable $th) {
			\Log::error('Mail send failed', [
				'message' => $th->getMessage(),
				'file' => $th->getFile(),
				'line' => $th->getLine(),
			]);
		}
		

	}

	private function updateStatusForResolution($groupId){
		$totalSigner = $this->documentSendService->GetDetailsbyGroupId($groupId);
		$totalComplete = [];
		if(!empty($totalSigner[0])){
			foreach($totalSigner as $val){
				if(strtolower($val->status) =='completed'){
					$totalComplete[] = $val->status;
				}
			}
		}

		if(count($totalComplete) == count($totalSigner)){
			$this->updatePatientStatusFormCompleted($totalSigner[0]->main_intakeId);
		}else{
			if(count($totalComplete) ==1){
				$this->updatePatientStatusTelehelathCompleted($totalSigner[0]->main_intakeId);
			}
		}
	}

	private function updatePatientStatusTelehelathCompleted($patientId){
		$this->commonUpdateStatus('Telehealth Completed , Pending Forms',$patientId);
	}

	private function updatePatientStatusFormCompleted($patientId){
		$this->commonUpdateStatus('Form Completed',$patientId);
	}

	private function commonUpdateStatus($statusSave,$patientId){
		$status = Utility::getStatusData();
		if(in_array($statusSave,$status)){
			$resolutionData = array(
				'status' => $statusSave,
				'patient_id' => $patientId,
			);
			$this->statusUpdateResolution($resolutionData);
		}
	}
	public function statusUpdateResolution($data){
		$status = $data['status'];
		$user = auth()->user();
		$getOldResponse = $this->patientService->getPatientDetailsByIdWhitoutAgency($data['patient_id']);
		$oldArray = array();
		$oldArray['oldresponse'] = $getOldResponse;
		
		$data_array['status'] = $status;
		$data_array['prev_status'] = $getOldResponse->status ?? "";
		$data_array['last_status_update'] = date('Y-m-d H:i:s');
		$data_array['last_status_update_by'] = auth()->check()?? auth()->user()->id??482;

		$this->patientService->update($data_array, array('id' => $data['patient_id']));
		// last service check and update status
		$getLastServiceRequestID = $this->patientServicesRequest->lastServiceRequestedByPatientId($data['patient_id']);
		$res_service_request_id = $this->saveResolutionServiceRequest($data['patient_id'], $data_array['status'],$getLastServiceRequestID['id']??"");
		$this->saveChartResolutionLog($data['patient_id'],$status,$getLastServiceRequestID);
		$ipaddress = Utility::getIP();
		$fullName = auth()->check() ? $user->first_name . ' ' . $user->last_name 
    : "System Admin";
		$insertLog = [
			'type' => 'Status Appointment',
			'module' => 'Patient Appointment',
			'link' => url('/'),
			'object_id' => $data['patient_id'],
			'message' => $fullName . ' has ' . $data_array['status'] . ' Updated Appointment',
			'new_response' => serialize($data_array),
			'old_response' => serialize($getOldResponse->toArray()),
			'ip' => $ipaddress,
		];
		$this->logAction($insertLog);
		return array('status' => $data_array['status'], 'requested_id' => $res_service_request_id);
	}

	private function saveResolutionServiceRequest($id, $status,$service_requested_id)
	{
		$res_service_request_id = "";
		$auth = auth()->user();
		$checkServices = $this->patientServicesRequest->getPatientService($id);

		if (count($checkServices) > 0) {
			$getExistingRecord = $this->patientService->getPatientDetailsByIdWhitoutAgency($id);
			$completedDate = null;
			$completed_by = "";
			$statusName = $status;

			if ($status == 'booked') {
				$statusName = "Booked";
			}
			if ($status == 'complete') {
				$statusName = "completed";
			}
			if ($status == 'missed') {
				$statusName = "missed";
			}
			if ($status == 'hospitalized') {
				$statusName = "hospitalized/rehab";
			}

			if ($status == 'unableToContact') {
				$statusName = "unableToContact";
			}
			if ($status == 'cancelled') {
				$statusName = "cancelled";
			}

			if ($status == 'noshow') {
				$statusName = "noshow";
			}

			if ($status == 'checkin') {
				$statusName = "arrived";
			}
			if ($status == 'processing') {
				$statusName = "processing";
			}
			if ($status == 'refused') {
				$statusName = "refused";
			}

			if ($status == 'pending') {
				$statusName = "pending";
			}

			if ($status == 'PendingTermination') {
				$statusName = "Pending Termination";
			}

			if ($status == 'Onhold') {
				$statusName = "On Hold";
			}

			if ($status == 'Onleave') {
				$statusName = "On Leave";
			}

			if ($status == 'Terminated') {
				$statusName = "Terminated";
			}
			if ($status == 'complete') {
				$completedDate = date(self::DATE_FORMAT_YMD);
				$completed_by = $auth->id;
			}
			if ($status == '1st Attempt - Unable to Contact' || $status == '2nd Attempt - Unable to Contact' || $status == '3rd Attempt - Unable to Contact' || $status == 'Patient Deceased' || $status == 'Signed' || $status == 'Signed & Sent Back to the Agency' || $status == 'Telehealth Completed , Pending Forms' || $status == 'Appointment was missed' || $status == 'Patient Asked to Reschedule'|| $status == 'Appointment Missed' || $status == 'New Order Received' || $status == 'New Form Requested' || $status == "Form Completed" || $status == "Service Provided" || $status == "Closed Temporarily") {
				$statusName = $status;
			}
			$updateStatus = array(
				'status' => $statusName,
				'completed_date' => $completedDate,
				'completed_by' => $completed_by,
				'last_status_update_by' => $auth->id??482,
				'last_status_update' => date(self::DATE_FORMAT_YMD)
			);
			
			if ($status == 'refused' || $status == 'cancelled') {
				$updateStatus['reason_id'] = $getExistingRecord->reason_id;
				$updateStatus['other_reason'] = !empty($getExistingRecord->other_reason)?$getExistingRecord->other_reason:null;
			}else{
				$updateStatus['reason_id'] = null;
				$updateStatus['other_reason'] = null;
			}
			if(isset($service_requested_id) && !empty($service_requested_id)){
				$getLastServiceId = $service_requested_id;
				$oldServiceRequestRes = $this->patientServicesRequest->patientServiceRequestData($getLastServiceId);
				$this->patientServicesRequest->update($updateStatus, array('id' => $getLastServiceId));
				$ipaddress = Utility::getIP();
				$fullName = auth()->check() ? auth()->user()->first_name . ' ' . auth()->user()->last_name 
    : "System Admin";
				$insertLog = [
					'type' => 'update service requested from esign',
					'link' => url('/esign/docusign/submit-form'),
					'module' => 'Patient Appointment',
					'object_id' =>$id,
					'message' => $fullName . ' has updated service requested status from esign',
					'old_response' => serialize($oldServiceRequestRes),
					'new_response' => serialize(array('service_id'=>$getLastServiceId,'status'=>$updateStatus)),
					'ip' => $ipaddress,
				];
				$this->logAction($insertLog);
			}
		} else {
			$checkForDocument = $this->documentPatientService->getDetailsByPatientId(array($id));
			$getPatientServices = $this->patientService->getPatientId($id);
			$servicesId = explode(',', $getPatientServices->service_id);

			if (!empty($servicesId[0])) {
				$finalData = [
					'patient_id' => $id,
					'fu_date' => $getPatientServices->fu_date,
					'due_date' => $getPatientServices->due_date
				];

				if (!empty($checkForDocument[0])) {
					$finalData['created_at'] = Utility::convertDays('-1 day');
					$finalData['updated_flag'] = "1";
					$finalData['flag'] = "1";
				} else {
					$finalData['flag'] = "2";
				}

				$patientServiceLastId = $this->patientServicesRequest->save($finalData);
				if ($patientServiceLastId) {

					foreach ($servicesId as $serviceId) {
						$patientWiseServiceRequest = [
							'patient_id' => $id,
							'service_id' => $serviceId,
							'patient_service_request_id' => $patientServiceLastId,
							'deleted_by' => 3
						];
						$this->patientWiseServicesRequests->save($patientWiseServiceRequest);
					}
				}
			}
			$res_service_request_id = $patientServiceLastId;
		}
		return $res_service_request_id;
	}

	private function saveChartResolutionLog($patient_id,$status,$ser_req_id){
	
		$authId = auth()->check() ? auth()->id() : 482;
		$fullName = auth()->check() ? auth()->user()->first_name . ' ' . auth()->user()->last_name 
    : "System Admin";
		$resData = array(
            'patient_id' => $patient_id,
            'team' =>"Medgen Team",
            'resolution' => $status,
            'notes' =>"",
            'service_request_id' => $ser_req_id,//Last service requested id
            'auto_created_by'=>$authId,//Auth check or system id
        );
  
        ResolutionService::saveResImportPatientServices($resData);
        $ipaddress = Utility::getIP();
        
        $insertLog = [
            'type' => 'Saved resolution data for services',
            'link' => url('/save-pateint-service-requested'),
            'module' => 'Patient Appointment',
            'object_id' => $patient_id,
            'message' => $fullName . ' has Saved Resolution data for services.',
            'new_response' => serialize($resData),
            'ip' => $ipaddress,
        ];
 
        LogsService::save($insertLog);
	}
}
