<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Services\HHAPatientService;
use Illuminate\Routing\Controller as BaseController;
use App\Services\PatientService;
use App\Helpers\HHAAppointmentHelper;
use App\Services\HHAMDOService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Storage;
use App\Services\HhaMdoOrderReportLogService;
use App\Agency;
use Illuminate\Support\Facades\Cache;
use App\Master;
use App\Helpers\Utility;
use App\Services\LogsService;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Services\HHAMdoClientPatientLogService;
use App\Exceptions\MDOAuthenticationException;

class HHAMdoController extends BaseController
{

    protected $hhaPatientService;
    protected $patientService;
    protected $hhaMDOService;
    protected const DATEMMDDYY_FORMAT="m/d/Y H:i:s";
    protected $hhaMdoOrderReportLogService;
    protected $patientServicesRequest;
    protected $patientWiseServicesRequests;
    protected $hhaMdoClientPatientLogService;

    public function __construct(

        HHAPatientService $hhaPatientService,
        PatientService $patientService,
        HHAMDOService $hhaMDOService,
        HhaMdoOrderReportLogService $hhaMdoOrderReportLogService,
        PatientServicesRequest $patientServicesRequest,
        PatientWiseServicesRequests $patientWiseServicesRequests,
        HHAMdoClientPatientLogService $hhaMdoClientPatientLogService
    ){
        $this->middleware('auth');

        $this->hhaPatientService = $hhaPatientService;

        $this->patientService = $patientService;
        $this->hhaMDOService = $hhaMDOService;
        $this->hhaMdoOrderReportLogService = $hhaMdoOrderReportLogService;
        $this->patientServicesRequest = $patientServicesRequest;
        $this->patientWiseServicesRequests = $patientWiseServicesRequests;
        $this->hhaMdoClientPatientLogService = $hhaMdoClientPatientLogService;
        $this->middleware('permission:hha-patient-md-order-list|hha-patient-md-order-create', ['only' => ['mdoPatientList']]);
        $this->middleware('permission:hha-patient-md-order-create', ['only' => ['saveHHAMDOPatient']]);
    }

    public function mdoDocumentList(Request $request){
      
        $patientDetails = $this->commonPatientDetails($request->patient_id);
        if(isset($patientDetails['caregiverId'])){
            $hhaMDO = $this->hhaMDOService->getHHAMDoPatientDocument($patientDetails['getHHAPatientDetails'],$patientDetails['agencyId']);
            return response()->json(['error_msg'=>$hhaMDO['error_msg'],'data'=>$hhaMDO['data']],$hhaMDO['status_code']);
        }
        
    }

    public function downloadMDOrderDocument(Request $request)
    {
        $details = $this->commonPatientDetails($request->patient_id);

        if (empty($details['caregiverId'])) {
            return response()->json(['error' => 'Caregiver ID missing'], 400);
        }

        $documentData = $this->fetchDocumentData($details, $request->document_download_url);

        if (empty($documentData['fileResponse'])) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return $this->streamFileResponse($documentData['fileResponse'], $documentData['fileName']);
    }

    public function commonPatientDetails($patientID){
        $patientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($patientID);
        $caregiverID="";
        $getHHAPatientDetails="";
        $agencyId="";
        if(isset($patientDetails->id)){
            if(strtolower($patientDetails->type) == 'patient'){
                $caregiverID = $patientDetails->link_hha_patient;
                $getHHAPatientDetails = $this->hhaPatientService->getDetailsByPatientID($patientDetails->link_hha_patient,$patientDetails->agency_id);
            
            }else{
                if(isset($patientDetails->link_hha_caregiver) && $patientDetails->link_hha_caregiver !=""){
                    $caregiverID = $patientDetails->link_hha_caregiver;
                }else{
                    $getAppointmentDetails = HHAAppointmentHelper::getById($patientDetails->hha_id);
                    $caregiverID = $getAppointmentDetails->caregiver_id;
                }
            }

            $agencyId = $patientDetails->agency_id;
        }

        return ['caregiverId'=>$caregiverID,'getHHAPatientDetails'=>$getHHAPatientDetails,'agencyId'=>$agencyId];
    }

    public function sendMDOrderDocument(Request $request){
        
        $validator = Validator::make(
            $request->all(),
            [
                'agency_id' => 'required',
                'patient_id' => 'required',
                'document_id' => 'required',
                'mdo_signed_upload_document' => 'required',
            ],
            [
                'agency_id.required' => 'Agency ID is required.',
                'patient_id.required' => 'Patient ID is required.',
                'document_id.required' => 'Document ID is required.',
                'mdo_signed_upload_document.required' => 'Please upload the signed MDO document.',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        }else{
            $getDetails = $this->hhaMDOService->getAllClientDetailsByAgencyId($request->agency_id);
            $patientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->patient_id);
            if(strtolower($patientDetails->type) == 'patient'){
                $date = $request->mdo_signed_date.' '.date('H:i:s');
            
                $isoDate = Carbon::createFromFormat(self::DATEMMDDYY_FORMAT, $date,'America/New_York')->setTime(0, 0, 0)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
         
                $valueDateTime = Carbon::createFromFormat(self::DATEMMDDYY_FORMAT,  now()->format(self::DATEMMDDYY_FORMAT),'America/New_York')->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
                $file = $request->file('mdo_signed_upload_document');

                $finalResponse =[
                    'resourceType'=>'DocumentReference',
                    'identifier'=>[['system'=>env('HHA_MDO_FHIR'),'value'=>$getDetails->txtID]],
                    'subject'=>['reference'=>'Patient/'.$patientDetails->link_hha_patient],
                    'description'=>$file->getClientOriginalName(),
                    'content' => [
                        [
                            'attachment' => [
                                'data' =>base64_encode(file_get_contents($file->getRealPath())),
                                'contentType' =>$file->getMimeType()
                            ]
                        ]
                    ],
                    'date'=>$isoDate,
                    'extension' => [
                        [
                            'url' =>env('HHA_MDO_SIGNER_DATE'),
                            'valueDateTime' => $valueDateTime,
                        ]
                    ],
                ];
                
                $data = [
                    'agency_id'=>$request->agency_id,
                    'patient_id'=>$request->patient_id,
                    'link_hha_patient'=>$patientDetails->link_hha_patient,
                    'hha_document_id'=>$request->document_id,
                    'txtId'=>$getDetails->txtID,
                    
                    'final_data'=>$finalResponse
                ];
                $details = $this->hhaMDOService->saveDocument($data);
                
                if(isset($details['status_code']) && $details['status_code'] ==200){
                    $name = uniqid().'_'.$file->getClientOriginalName();
                    if(env('FILE_UPLOAD_PERMISSION') !="development"){
                        
                        Storage::disk('s3')->putFileAs('hhaMDO/'.$request->patient_id.'/'.$patientDetails->link_hha_patient, $file, $name);
                       
                    }else{
                        $destination1 = 'hhaMDO/'.$request->patient_id.'/'.$patientDetails->link_hha_patient;
                        $file->move($destination1, $name);
                    }

                    $saveResponse = array(
                            'agency_id'=>$request->agency_id,
                            'patient_id'=>$request->patient_id,
                            'hha_patient_id'=>$patientDetails->link_hha_patient,
                            'hha_document_id'=>$request->document_id,
                            'attachment'=>$name,
                            'send_response'=>serialize($data),
                            'return_response'=>serialize($details['data']),
                        );
                    $this->hhaMdoOrderReportLogService->save($saveResponse);
                }
                return response()->json(['error_msg'=>$details['error_msg'],'data'=>$details['data']],$details['status_code']);
            }
           
        }
    }

    /**
     * Fetch document data from service
     */
    private function fetchDocumentData(array $details, string $documentUrl): array
    {
        $result = $this->hhaMDOService->getDocumentListBatchSignle(
            $details['getHHAPatientDetails'],
            $details['agencyId'],
            [$documentUrl]
        );

        $successResponses = array_filter($result, fn($r) => isset($r['status']) && $r['status'] == 200);

        if (empty($successResponses) || empty($result[0]['data'][0]['resource']['content'][0]['attachment'])) {
            return ['fileResponse' => '', 'fileName' => ''];
        }

        $attachment = $result[0]['data'][0]['resource']['content'][0]['attachment'];

        return [
            'fileResponse' => $attachment['data'] ?? '',
            'fileName'     => $attachment['title'] ?? 'document.pdf',
        ];
    }

    /**
     * Stream binary file to response
     */
    private function streamFileResponse(string $fileResponse, string $fileName)
    {
        $binaryData = base64_decode($fileResponse);

        return response()->stream(function () use ($binaryData) {
            echo $binaryData;
        }, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename=' . $fileName,
            'Content-Length'      => strlen($binaryData),
        ]);
    }

    public function mdoPatientList(Request $request){
        $data['menu'] = "user";
        $data['auth'] = $user = auth()->user();

        if (empty($user)) {
            return redirect('/login');
        }

        if (in_array($user['user_type_fk'], array(5, 6))) {
            abort(404);
        }

        $data['agency_list'] =  Cache::get('hha_agency_table_list', function () {
            $final = [];
            $query =  Agency::getHHAMDOAgencyList();
            if(!empty($query[0])){
                foreach($query as $val){
                    $temp = [];
                    $temp['id'] = $val->id;
                    $temp['agency_name'] = $val->agency_name;
                    $final[] = $temp;
                }
            }

            return $final;
        }, 10 * 60);

        $data['selected_agency_id'] = $request->selected_agency_id;
        $data['serviceList'] =  Cache::get('hha_mdo_service_list', function () {
            return Master::getServiceRequestNew('Patient');
      
          }, 10 * 60);
        return view("hha_mdo_patient.hha_mdo_patient_list", $data);
    }

    public function ajaxList(Request $request){
        $page = $request->page ?? 1;
        if($request->agency_fk ==""){
            return response()->json(['data'=>[]],200);
        }
        $result = $this->hhaMDOService->getHHAMDOPatientList($request->agency_fk, $page);
        $patientList =[];
        $total = 0;
        if(isset($result['status_code']) && $result['status_code'] ==200){
            $patientList = $result['data']['entry'];
            $total = $result['data']['total'];
        }
       
        $getExistingPatientMDO = $this->hhaMdoClientPatientLogService->getMDOPatientList();
        $finalResponse = ['patient_list'=>$patientList,'total'=>$total,'existing_mdo'=>$getExistingPatientMDO];
        return response()->json(['data'=>$finalResponse],200);
    }

    public function saveHHAMDOPatient(Request $request){
        $user = auth()->user();
      
        $validator = $this->validateRequest($request);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        }

        $searchDetails = $this->prepareSearchDetails($request);
        $existingPatient = $this->patientService->getSearchPatientDetailsWithArchived($searchDetails);
        $statusServiceRequest = Utility::getStatusFromServiceId($request->service_id);
        if (!empty($existingPatient[0])) {
            $response = $this->handleExistingPatient($existingPatient[0], $request, $user);
        }else{
            $response = $this->handleNewPatient($request, $searchDetails, $statusServiceRequest, $user);
        }

        $this->hhaMdoClientPatientLogService->save(array('patient_id'=>$response['id'],'mdo_patient_id'=>$request->modal_patient_id));
       
        if ($response['update']) {
           $patientServiceLastId = $this->savePatientServices($response['id'], $request->service_id, $statusServiceRequest);
            $this->logActivity($response);
            try {
                Utility::saveResolutionLogForms($statusServiceRequest,$patientServiceLastId,$response['id']);
            } catch (\Throwable $th) {
                throw new MDOAuthenticationException($th->getMessage(), 0, $th);
            }
            return response()->json([
                'data' => [],
                'error_msg' => $response['message']
            ], 200);
        }
        return response()->json(['data'=>array(),'error_msg'=>"Sorry, something went wrong. Please try again."],500);
    }

    protected function validateRequest($request){
        return Validator::make(
            $request->all(),
            [
                'agency_id' => 'required',
                'modal_patient_id' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'gender' => 'required',
                'dob' => 'required',
                'service_id' => 'required',
            ],
            [
                'agency_id.required' => 'Agency ID is required.',
                'modal_patient_id.required' => 'Patient ID is required.',
                'first_name.required' => 'First Name is required.',
                'last_name.required' => 'Last Name is required.',
                'gender.required' => 'Gender is required.',
                'dob.required' => 'Date of Birth is required.',
                'service_id.required' => 'Service is required.',
            ]
        );
    }

    private function prepareSearchDetails($request)
    {
        $data = $request->all();
        $data['dob_id'] = str_replace('/', '-', $data['dob']);
        $data['agency_id'] = $request->agency_id;
        $data['type'] = "Patient";
        return $data;
    }

    private function handleExistingPatient($patient, $request, $user)
    {
        $id = $patient->id;

        $updated = ['link_hha_patient' => $request->modal_patient_id];
        $this->patientService->update($updated, ['id' => $id]);

        return [
            'id' => $id,
            'old' => [$patient],
            'new' => $updated,
            'update' => 1,
            'logType' => "Update HHA MDO Patient",
            'logMessage' => $user->first_name . ' ' . $user->last_name . ' has update hha mdo patient',
            'message' => "HHA Mdo successfully updated"
        ];
    }

    private function handleNewPatient($request, $searchDetails, $statusServiceRequest, $user)
    {
        $phone = isset($request->phone) ? str_replace('-', '', $request->phone) : "";
        $mobile = isset($request->mobile) ? str_replace('-', '', $request->mobile) : "";

        $final = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'full_name'  => $request->first_name . ' ' . $request->last_name,
            'dob'        => date('Y-m-d', strtotime(trim($searchDetails['dob']))),
            'gender'     => strtolower($searchDetails['gender']),
            'agency_id'  => $request->agency_id,
            'status'     => $statusServiceRequest,
            'service_id' => implode(',', $request->service_id),
            'mobile'     => $mobile,
            'phone'      => $phone,
            'type'       => 'Patient',
            'address1'   => $request->address1,
            'city'       => $request->city,
            'state'      => $request->state,
            'zip_code'   => $request->zip,
            'link_hha_patient' => $request->modal_patient_id
        ];
  
        $id = $this->patientService->save($final);

        return [
            'id' => $id,
            'new' => $final,
            'update' => $id ? 1 : 0,
            'logType' => "Add HHA MDO Patient",
            'logMessage' => $user->first_name . ' ' . $user->last_name . ' has hha mdo patient',
            'message' => "HHA Mdo successfully added"
        ];
    }

    private function savePatientServices($patientId, $serviceIds, $statusServiceRequest)
    {
        $lastId = $this->patientServicesRequest->save([
            'patient_id' => $patientId,
            'follow_up_date' => null,
            'due_date' => null,
            'status' => $statusServiceRequest
        ]);

        foreach ((array)$serviceIds as $serviceId) {
            if ($serviceId != "") {
                $this->patientWiseServicesRequests->save([
                    'patient_id' => $patientId,
                    'service_id' => $serviceId,
                    'patient_service_request_id' => $lastId,
                ]);
            }
        }
        return $lastId;
    }

    private function logActivity($response)
    {
        $ip = Utility::getIP();

       $log= [
            'type' => $response['logType'],
            'link' => url('/hha/hha-mdo/save-hha-mdo-patient'),
            'module' => 'Patient Appointment',
            'object_id' => $response['id'],
            'message' => $response['logMessage'],
            'new_response' => serialize($response['new']),
            'ip' => $ip,
       ];

       if(isset($response['old'])){
        $log['old_response'] = serialize($response['old']);
       }
        LogsService::save($log);
    }
}