<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Services\DocumentPatientService;
use Illuminate\Support\Facades\URL;

use Illuminate\Support\Facades\Storage;

use App\Helpers\EFaxHelper;
use App\Services\PatientService;
use App\Services\EFaxLogService;
use App\Services\DocumentSendThirdPartyAPILogService;
use App\Helpers\DocumentSendThirdPartyAPIHelper;
use App\Services\AgencyWiseThirdPartyAPIService;
use App\GenerateAgencyToken;

use App\Services\ThirdPartyPatientMasterService;
use App\Helpers\Utility;
use App\Services\LogsService;

class PatientDocumentController extends BaseController
{
    protected $documentPatientService,$patientService,$efaxLogService,$documentSendThirdPartyAPILogService,$agencyWiseThirdPartyAPIService,$thirdPartyPatientMasterService="";
    public function __construct(DocumentPatientService $documentPatientService,PatientService $patientService,EFaxLogService $efaxLogService,DocumentSendThirdPartyAPILogService $documentSendThirdPartyAPILogService,AgencyWiseThirdPartyAPIService $agencyWiseThirdPartyAPIService,ThirdPartyPatientMasterService $thirdPartyPatientMasterService)
    {
        $this->middleware('auth');
        $this->documentPatientService = $documentPatientService;
        $this->patientService = $patientService;
        $this->efaxLogService = $efaxLogService;
        $this->documentSendThirdPartyAPILogService = $documentSendThirdPartyAPILogService;
        $this->agencyWiseThirdPartyAPIService = $agencyWiseThirdPartyAPIService;
        $this->patientService = $patientService;
        $this->thirdPartyPatientMasterService = $thirdPartyPatientMasterService;
    }

    public function sendEFaxDocument(Request $request){
       
        $validator = Validator::make(
            $request->all(),
            [
                'fax_no' => 'required',
            ],
            [
                'fax_no.required' => 'E-Fax No is required.',
            ]
        );
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
           
            $getDocumentDetails = $this->documentPatientService->getDocumentDetailsById($request->document_id);
            $removeFile = 0;
            if(isset($getDocumentDetails->id)){
                $file = public_path('/patientdocument').'/'.$getDocumentDetails->attachment;
                if(file_exists($file)){
                  $file =URL::to('/patientdocument').'/'.$getDocumentDetails->attachment;
                }else{
                    $file = Storage::disk('s3')->temporaryUrl(
                        'patientdocument/' . $getDocumentDetails->attachment,
                        now()->addMinutes(5) // valid for 5 minutes
                    );

                    // $fileGetContain = Storage::disk('s3')->get('patientdocument/'.$getDocumentDetails->attachment);
                  
                    // $destination = public_path('/').'tempefax/'.$getDocumentDetails->attachment;
                    // file_put_contents($destination,$fileGetContain);
                    // $file =URL::to('/tempefax').'/'.$getDocumentDetails->attachment;
                  //  $removeFile=1;
                }

                $getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->patient_id);
                $sendResponse = ['file_path'=>$file,'file_name'=>$getDocumentDetails->attachment,'fax_no'=>$request->fax_no,'subject'=>"Confidential: Document for ".$getPatientDetails->first_name.' '.$getPatientDetails->last_name,'to_name'=>'Tilin','to_company'=>'Nybest Medical'];
                $response = EFaxHelper::sendEFex($sendResponse);
                $saveData = [
                    'document_id'=>$request->document_id,
                    'patient_id'=>$request->patient_id,
                    'fax_no'=>$request->fax_no,
                    'send_response'=>serialize($sendResponse),
                    'return_response'=>serialize($response)
                ];
                $this->efaxLogService->save($saveData);
                if($removeFile ==1){
                    $obj = public_path('/').'tempefax/'.$getDocumentDetails->attachment;
                    if(file_exists($obj)){
                        unlink($obj);
                    }
                   
                }

                $ipaddress = Utility::getIP();
                $user = auth()->user();
                $insertLog = [
                    'type' => 'Document Efax Sent',
                    'link' => url('send-e-fax-document'),
                    'module' => 'Patient Appointment',
                    'object_id' => $request->patient_id,
                    'message' => 'Document Efax sent by '.$user->first_name.' '.$user->last_name ,
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
                
                return response()->json([
                    'status' => 'success',
                    'error_msg' => 'Fax sent successfully.',
                ]);
              
            }
        }
    }

    public function sendDocumentCaresphere(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
                'documentId' => 'required',
                'id' => 'required',
            ],
            [
                'documentId.required' => 'Document Id is required.',
                'id.required' => 'Third Party Id is required.',
            ]
        );
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {

       
            $getURLDetails = $this->agencyWiseThirdPartyAPIService->getDetailsById($request->id);
            $getAgencyToken = GenerateAgencyToken::select('token')->where('agency_id',$getURLDetails->agency_id)->where('delete_flag','N')->first();
            $getDocumentDetails = $this->documentPatientService->getDocumentDetailsById($request->documentId);
            $removeFile=0;
            if(isset($getDocumentDetails->id)){
                $getPatientDetails = $this->patientService->getPatientDetailsById($getDocumentDetails->patient_id,$getURLDetails->agency_id);
                $file = public_path('/patientdocument').'/'.$getDocumentDetails->attachment;
                if(file_exists($file)){
                  $file =URL::to('/patientdocument').'/'.$getDocumentDetails->attachment;
                }else{
                    // $fileGetContain = Storage::disk('s3')->get('patientdocument/'.$getDocumentDetails->attachment);
                  
                    // $destination = public_path('/').'tempefax/'.$getDocumentDetails->attachment;
                    // file_put_contents($destination,$fileGetContain);
                    // $file =URL::to('/tempefax').'/'.$getDocumentDetails->attachment;
                    // $removeFile=1;

                    $file = Storage::disk('s3')->temporaryUrl(
                        'patientdocument/' . $getDocumentDetails->attachment,
                        now()->addMinutes(5) // valid for 5 minutes
                    );
                    
                    // $file = Storage::disk('s3')->temporaryUrl(
                    //     'patientdocument/' . $getDocumentDetails->attachment,
                    //     now()->addMinutes(5) // valid for 5 minutes
                    // );
                }
                
                $completedDate = "";
                if(isset($getDocumentDetails->document_completed_date) && $getDocumentDetails->document_completed_date !=""){
                    $completedDate = date('m/d/Y',strtotime($getDocumentDetails->document_completed_date));
                }

                $getThirdPartyDetails = $this->thirdPartyPatientMasterService->getPatientDetails($getPatientDetails->link_third_party,$getPatientDetails->agency_id);
             
               
                $data = ['file_path'=>$file,'link_third_party'=>$getPatientDetails->link_third_party,'platform_id'=>$getThirdPartyDetails->platform_id,'document_name'=>$getDocumentDetails->document_name,'agency_token'=>$getAgencyToken->token,'document_completed_date'=>$completedDate,'patient_code'=>$getPatientDetails->patient_code];
           
                $response = DocumentSendThirdPartyAPIHelper::sendThirdParty($data);
                if($removeFile==1){
                    $file = public_path('/tempefax').'/'.$getDocumentDetails->attachment;
                    if(file_exists($file)){
                        unlink(public_path('/').'tempefax/'.$getDocumentDetails->attachment);
                    }
                    
                }
                if(isset($response['receivedJsonData'])){
                    $this->documentSendThirdPartyAPILogService->save(array('third_party_api_id'=>$request->id,'patient_id'=>$getDocumentDetails->patient_id,'document_id'=>$request->documentId,'send_response'=>serialize($data),'return_response'=>serialize($response)));
                    return response()->json([
                        'status' => 'success',
                        'error_msg' =>$response['message'],
                    ],200);
                }else{
                    return response()->json([
                        'status' => 'error',
                        'error_msg' => $response['message'],
                    ],500); 
                }
                
               
            }else{
                return response()->json([
                    'status' => 'error',
                    'error_msg' => 'Invalid document id',
                ],500);
            }

        }
    }
}