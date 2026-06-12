<?php

namespace App\Http\Controllers;
use App\Agency;
use App\Model\AlayacareEmployee;

use App\Services\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\AlayacareService;
use App\Helpers\AlayacareHelper;
use App\Helpers\Utility;
use App\Helpers\Common;
use App\Model\AlayacareEmployeeSkill;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Services\AgencyService;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\LogsService;
use App\Services\MasterService;
use App\Services\DocumentPatientService;
use App\Model\AlayacareCronLog;

class AlaycareEmpController extends Controller
{
    protected $alayacareService;
    protected $patientService;
    protected $patientServicesRequest;
    protected $patientWiseServicesRequests="";
    protected $agencyService;
    protected $masterService;
    protected $documentPatientService;
    public function __construct(PatientService $patientService,AlayacareService $alayacareService,PatientServicesRequest $patientServicesRequest,PatientWiseServicesRequests $patientWiseServicesRequests,AgencyService $agencyService,MasterService $masterService,DocumentPatientService $documentPatientService)
	{
        $this->middleware('permission:alayacare-employee-list|alayacare-employee-add-appointment|alayacare-client-list-export', ['only' => ['getAlaycareEmpList','empAddAppointment','alaycareEmployeeExport']]);
        $this->middleware('permission:alayacare-employee-list', ['only' => ['getAlaycareEmpList','getAlaycareEmpListAjax']]);
        $this->middleware('permission:alayacare-employee-add-appointment', ['only' => ['empAddAppointment']]);
		$this->patientService = $patientService;
        $this->alayacareService = $alayacareService;
        $this->patientServicesRequest = $patientServicesRequest;
        $this->patientWiseServicesRequests = $patientWiseServicesRequests;
        $this->agencyService = $agencyService;
        $this->masterService = $masterService;
        $this->documentPatientService = $documentPatientService;
	}

    public function getAlaycareEmpList(Request $request){
        $data['menu'] = "user";

		$data['user'] = $user = auth()->user();
        $data['agencyList'] = $this->agencyService->getAlayacareAgencyList();
		$data['masterData'] = Cache::get('alayacare-emp-discipline', function (){
            return $this->masterService->getAllDataByMasterTypeFk([26]);
        }, 10 * 60);
        return view('alaycare-employee.index',$data);

    }

    public function getAlaycareEmpListAjax(Request $request){
        $data['query'] = $this->alayacareService->getAllAlaycareEmployee($request->all());
        return view('alaycare-employee.list-ajax',$data);
    }

    public function alaycareEmployeeExport(Request $request){
        
        $user = auth()->user();
        $users =$this->alayacareService->getAllAlaycareEmployee($request->all(),'export');
        

        $filename = 'Alaycare Employee' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $columns = array('Email', 'First Name', 'Last Name', 'Job Title','Phone No','Branch Name','Agency Name','Employee Status');

        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($users as $list) {


                fputcsv($file, array($list->email, $list->first_name, $list->last_name, $list->job_title,$list->phone,$list->branch_name,$list->agency_name,ucfirst($list->status ?? '')));
            }

            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function empAddAppointment(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
			'service_id' => 'required',
			'ids' => 'required',
		]);
 
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		}else{
            $ids = $request->input('ids');
            $idsArray = explode(",", $ids);
            $insert= 0;
            if(!empty($idsArray[0])){
                foreach($idsArray as $id){
                    $empDetailsById = AlayacareEmployee::find($id);
                    
                    $data = [
                        'first_name' => $empDetailsById->first_name,
                        'last_name' => $empDetailsById->last_name,
                        'full_name' => $empDetailsById->first_name.' '.$empDetailsById->last_name,
                        'type' => 'Caregiver',
                        'dob' => $empDetailsById->birthday,
                        'phone' => $empDetailsById->phone,
                        'mobile' => $empDetailsById->phone,
                        'agency_id' => $empDetailsById->agency_id,
                        'gender' => $empDetailsById->gender,
                        'service_id' => implode(',', $request->service_id),
                        'diciplin' => $request->diciplin,
                        'language' => Common::getOrCreateLanguageId($empDetailsById->language),
                        'county' => $empDetailsById->country,
                        'alaycare_id' => $empDetailsById->emp_id,
                        'alaycare_name' =>  $empDetailsById->first_name .' '. $empDetailsById->last_name,
                        'email'=>$empDetailsById->email,
                        'patient_code'=>$empDetailsById->external_id,
                        'address1'=>$empDetailsById->address,
                        'state'=>$empDetailsById->state,
                        'city'=>$empDetailsById->city,
                        'zip_code'=>$empDetailsById->zip,
                        'referral_type'=>'Alayacare'
                    ];

                    $getPatientDetails = $this->patientService->checkExistingAlayacareEmp($empDetailsById->emp_id,$empDetailsById->agency_id);

                    if(isset($getPatientDetails->id)){
                         $logType = "Appointment Link Service Update";
                        $insert = $getPatientDetails->id;
                        $this->patientService->update(array('service_id'=>implode(',', $request->service_id)),array('id'=>$getPatientDetails->id));
                        $empDetailsById->update(['patient_id' =>$getPatientDetails->id]);
                    }else{
                        $checkExistingRecordOrNot = $this->patientService->checkForThirdPartyExistingDataApi($data,$empDetailsById->agency_id);
                        if(isset($checkExistingRecordOrNot->id)){
                            $logType = "Appointment Link Service Update";
                            $insert = $checkExistingRecordOrNot->id;
                            $this->patientService->update(array('service_id'=>implode(',', $request->service_id)),array('id'=>$insert));
                        }else{
                            $logType = "Add Appointment";
                            $insert = $this->patientService->save($data);
                        }
                        $empDetailsById->update(['patient_id' => $insert]);
                    }
                    
                    $patientServiceLastId = $this->patientServicesRequest->save([
                        'patient_id' => $insert,
                    ]);
                    $addServiceIds = $request->input('service_id');

                    if (is_array($addServiceIds)) {
                        foreach ($addServiceIds as $serviceId) {
                            if($serviceId !=""){
                                $patientWiseServiceRequest = [
                                    'patient_id' => $insert,
                                    'service_id'=> $serviceId,
                                    'patient_service_request_id' => $patientServiceLastId,
                                
                                ];
                                $this->patientWiseServicesRequests->save($patientWiseServiceRequest);
                            }
                            
                        }
                    }
                }
                if($insert){
                    $ipaddress = Utility::getIP();
                    $insertLog = [
                        'type' => $logType,
                        'link' => url('alayacare/alayacare-employee/employee-add-appointment'),
                        'module' => 'Patient Appointment',
                        'object_id' => $insert,
                        'message' => $user->first_name . ' ' . $user->last_name . ' has added an appointment via AlayaCare',
                        'new_response' => serialize($data),
                        'ip' => $ipaddress,
                    ];
                    LogsService::save($insertLog);
                    return response()->json(['error_msg' => "Appointment  successfully Added", 'status' => 1, 'data' => ""], 200);
                }else{
                    return response()->json(['error_msg' => "Some thing wrong", 'status' => 1, 'data' => ""], 500);
                }
            }
        }
    }

    public function alaycareEmployeeSkill(Request $request){
        $agencyDetails = Agency::getIdById($request->agency_id);
        //$query =$this->commonEmployeeDetails($request->id,$request->agency_id);
        
        $response = AlayacareHelper::getEmployeeSkill($request->page,$request->agency_id);
        
        $skillDetails = json_decode($response,true);

        $final = [];
     
        if(isset($skillDetails['items'])){
            foreach($skillDetails['items'] as $val){
                $getEmployeeSkillDetails = AlayacareHelper::getEmployeeSkillDetails($val['id'],$request->id,$request->agency_id);
                $employeeSkillDetails= json_decode($getEmployeeSkillDetails,true);
                $val['skillDetails'] = [];
                if(isset($employeeSkillDetails['code']) && $employeeSkillDetails['code'] ==404){
                    
                }else{
                    $val['skillDetails'] = $employeeSkillDetails;
                }
                $final[] = $val;
            }

            $skillDetails['items'] = $final;
        }

        return response()->json(['error_msg' => "", 'status' => 1, 'data' => ($skillDetails !=null)?$skillDetails:[]], 200);
    }

    public function getAlayacareSkillList(Request $request){
        $agencyDetails = $this->agencyService->getDetailsById($request->agency_id);
        if(empty($agencyDetails)){
            return response()->json(['error_msg' => "Invalid Agency", 'status' => 1, 'data' => []], 403);
        }
        $allSkills = [];
        $page = 1;

        do {
            $response = AlayacareHelper::getEmployeeSkill($page, $request->agency_id,1000);
            $skillDetails = json_decode($response, true);
       
            if(isset($skillDetails['items'])){
                foreach($skillDetails['items'] as $val){
                     if(count($val['fields']) >0){
                        $allSkills[] = [
                            'id' => $val['id'],
                            'name' => $val['name'],
                            'fields' => $val['fields']
                        ];
                     }
                }
            }

            $totalPages = $skillDetails['total_pages'] ?? 1;
            $page++;
        } while($page <= $totalPages);

        return response()->json(['error_msg' => "", 'status' => 1, 'data' => $allSkills], 200);
    }

    public function alaycareEmployeeSchedular(Request $request){

        
        $startDate = date('Y-m-d',strtotime($request->start));
        $endDate =date('Y-m-d',strtotime($request->end));

        $startDate = Utility::convertUSAToUTC($startDate);
        $startDate =$startDate.'08:00:00-05:00';

        $endDate = Utility::convertUSAToUTC($endDate);
        $endDate =$endDate.'08:00:00-05:00';

        $agencyDetails = Agency::getIdById($request->agency_id);
        if($request->record_type =='Caregiver'){
          
            //$query = $this->commonEmployeeDetails($request->id,$request->agency_id);
            $empId = $request->id;
        }else{
            
            $query = $this->alayacareService->getClientDetailsByAlayacreId($request->id);
         
            $empId = $query->client_id;
        }
        
        $response = AlayacareHelper::getEmployeeSchedular($request->page,$empId,$startDate,$endDate,$agencyDetails->alaycare_username,$agencyDetails->alaycare_password,$request->record_type);
        $response = json_decode($response,true);
 
        $final = [];
        $finalArrayResponse = [];
        if(isset($response['items'])){
            foreach($response['items'] as $val){

                $val['start_at'] = Utility::convertUTCToUSA($val['start_at']);
                $val['end_at'] = Utility::convertUTCToUSA($val['end_at']);

                $final[] = $val;
            }
            $response['items'] = $final;
            $finalArrayResponse = $response;
        }
        //echo "<pre>";print_r($finalArrayResponse);die();
        return response()->json(['error_msg' => "", 'status' => 1, 'data' => $finalArrayResponse], 200);

    }

    public function alaycareVisitDetails(Request $request){
        $query = $this->commonEmployeeDetails($request->id,$request->agency_id);;
        $response = AlayacareHelper::getVisitDetails($request->visit_id,$query->agencyDetails->alaycare_username,$query->agencyDetails->alaycare_password);
        $response = json_decode($response,true);

		$response['start_at'] =Utility::convertUTCToUSA($response['start_at']);
		$response['end_at'] =Utility::convertUTCToUSA($response['end_at']);

        return response()->json(['error_msg' => "", 'status' => 1, 'data' => $response], 200);
    }

    public function alaycareEmployeeNotes(Request $request){
        // $query = $this->commonEmployeeDetails($request->id,$request->agency_id);
        $agencyDetails = Agency::getIdById($request->agency_id);
        $response = AlayacareHelper::getEmployeeNotes($request->page,$request->id,$agencyDetails->alaycare_username,$agencyDetails->alaycare_password);
        $response = json_decode($response,true);
       
        $finalArrayResponse = [];
        if(isset($response['items'])){
            $final = [];
            foreach($response['items'] as $val){
                $notes_type = ucfirst(str_replace('employee_','',$val['note_type']));
                $val['note_type'] = $notes_type;
                $val['status'] = ucfirst($val['status']);
               
                $val['created_at'] = Utility::convertUTCToUSA($val['created_at']);
                $final[] = $val;
            }
            $response['items'] = $final;
            $finalArrayResponse = $response;
        }
        return response()->json(['error_msg' => "", 'status' => 1, 'data' => $finalArrayResponse], 200);
    }

    function alaycareEmployeeNotesType(Request $request){
        $query = $this->commonEmployeeDetails($request->id,$request->agency_id);
     
        $response = AlayacareHelper::getEmployeeNotesType($query->agencyDetails->alaycare_username,$query->agencyDetails->alaycare_password);
        $response = json_decode($response,true);
        $final =$response['items']??[];
        return response()->json(['error_msg' => "", 'status' => 1, 'data' => $final], 200);
    }

    public function createAlayaCareEmployeeNotes(Request $request){
        $validator = Validator::make($request->all(), [
			'note_type' => 'required',
			'content' => 'required',
			'id' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		}else{
            // $query = $this->commonEmployeeDetails($request->id,$request->agency_id);
            $agencyDetails= Agency::getIdById($request->agency_id);
            $response = AlayacareHelper::createEmployeeNotes($request->id,$agencyDetails->alaycare_username,$agencyDetails->alaycare_password,$request->all());
            $response = json_decode($response,true);
            if(isset($response['code']) && $response['code'] !=""){
                return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 1, 'data' => array()], 500);
            }else{
                return response()->json(['error_msg' => "Successfully added", 'status' => 1, 'data' => array()], 200);
            }
        }
    }

    public function alaycareEmployeeSkillUpdate(Request $request){
        
        $validators['skill_id'] ='required';
        $validators['id'] ='required';
        if($request->flag !='auto'){
            $validators['content'] ='required';
        }
        $validator = Validator::make($request->all(), $validators);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		}else{


            $query = $this->commonEmployeeDetails($request->id,$request->agency_id);
            if($request->method_type =='edit'){
                $response = AlayacareHelper::updateEmployeeSkill($request->id,$query->agencyDetails->id,$request->all());
            }else{
               
                $response = AlayacareHelper::addEmployeeSkill($request->id,$query->agencyDetails->id,$request->all());
            }
            
            $response = json_decode($response,true);
            if(isset($response['code']) && $response['code'] !=""){
                return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 1, 'data' => array()], 500);
            }else{
                return response()->json(['error_msg' => "Saved Successfully", 'status' => 1, 'data' => array()], 200);
            }
        }
    }

    public function alaycareUploadDocumentList(Request $request){
        // $query = $this->commonEmployeeDetails($request->id,$request->agency_id);

        $agencyDetails = Agency::getIdById($request->agency_id);
        $response = AlayacareHelper::getDocumentList($request->id,$agencyDetails->alaycare_username,$agencyDetails->alaycare_password,"nybest");
        $response = json_decode($response,true);
        $mainArray = [];
        if(isset($response['entries'])){
            $final = [];
            foreach($response['entries'] as $val){
               
                $val['last_modified'] = Utility::convertUTCToUSA($val['last_modified']);
                $final[] = $val;
            }
            $response['entries'] = $final;
            $mainArray = $response;
        }
        
        return response()->json(['error_msg' => "", 'status' => 1, 'data' => $mainArray], 200);
    }

    public function alaycareDocumentUpload(Request $request){

        $validator = Validator::make($request->all(), [
			'alaya_document' => 'required',
			'id' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		}else{
            $file =  $request->file('alaya_document');

            $data = $request->all();
            
            
            $data['type'] = $file->getClientMimeType();
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_path'] = $file->getPathname();
            $data['folder'] = 'nybest';
            
            $query = $this->commonEmployeeDetails($request->id,$request->agency_id);
            $response = AlayacareHelper::uploadDocument($query->emp_id,$query->agencyDetails->alaycare_username,$query->agencyDetails->alaycare_password,$data);
                $response = json_decode($response,true);
                $message = $response['message']??"";
                if(isset($response['code']) && $response['code'] ==201){
                    $message = "Successfully Uploaded";
                }
                return response()->json(['error_msg' => $message, 'data' => array()], $response['code']);
           
            
        }
        
    }

    public function skillCategory(Request $request){  
        $query = $this->commonEmployeeDetails($request->id,$request->agency_id);
        $response = AlayacareHelper::getSkillCategory($query->emp_id,$query->agencyDetails->alaycare_username,$query->agencyDetails->alaycare_password);
        $response = json_decode($response,true);
        return response()->json(['error_msg' =>"Success", 'data' => array($response)], 200);
    }

    public function commonEmployeeDetails($id,$agencyId=""){
        return $this->alayacareService->getAllDetailsByAlayacreId($id,$agencyId);
    }

    public function skillDelete(Request $request){
        $validator = Validator::make($request->all(), [
			'skill_id' => 'required',
			'id' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		}else{

 
            $query = $this->commonEmployeeDetails($request->id,$request->agency_id);
            $response = AlayacareHelper::deleteSkill($request->id,$query->agencyDetails->alaycare_username,$query->agencyDetails->alaycare_password,$request->all());
            $response = json_decode($response,true);
            if(isset($response['code']) && $response['code'] !=""){
                return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 1, 'data' => array()], 500);
            }else{
                return response()->json(['error_msg' => "Skill removed successfully", 'status' => 1, 'data' => array()], 200);
            }
        }
    }

    public function editSkill(Request $request){
        $validator = Validator::make($request->all(), [
			'skill_id' => 'required',
			'id' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		}else{


            // $query = $this->commonEmployeeDetails($request->id,$request->agency_id);
            $agencyDetails = Agency::getIdById($request->agency_id);
            $response = AlayacareHelper::editSkill($request->id,$agencyDetails->alaycare_username,$agencyDetails->alaycare_password,$request->skill_id);
            $response = json_decode($response,true);
            if(isset($response['code']) && $response['code'] !=""){
                return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 1, 'data' => array()], 500);
            }else{
                return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => $response], 200);
            }
        }
    }

    public function alaycareDocumentUploadNew(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
			'id' => 'required',
		]);

		if ($validator->fails()) {
			return $this->validationErrorResponse($validator);
		}

        try {
            $getDocumentDetails = $this->documentPatientService->getDocumentDetailsById($request->id);
            $getPatientDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($getDocumentDetails->patient_id);
            $responseData = json_decode($request->is_new_skill_relationship);

            $data = $this->prepareUploadData(
                $request,
                $getDocumentDetails,
                $getPatientDetails
            );

            $this->commonEmployeeDetails(
                $getPatientDetails->alaycare_id,
                $request->agency_id
            );

            // Upload document to Alayacare
            $response = $this->uploadDocumentToAlayacare(
                $getPatientDetails->alaycare_id,
                $request->agency_id,
                $data
            );

            // Handle failed upload response from Alayacare API
            if (($response['code'] ?? 0) != 201) {
                $errorMessage = $response['message'] ?? 'Document upload failed';

                // Save failed upload log to database
                $this->saveDocumentUploadCronLog(
                    $getPatientDetails->alaycare_id ?? null,
                    $request->agency_id,
                    $request->id,
                    $errorMessage,
                    "",
                    serialize($data),
                    serialize($response)
                );

                return response()->json([
                    'error_msg' => $errorMessage,
                    'data' => []
                ], $response['code'] ?? 500);
            }

            // Handle skill relationship if provided
            $skillResponse = $this->handleSkillRelationship(
                $request->agency_id,
                $responseData,
                $getPatientDetails->alaycare_id
            );

            if ($skillResponse) {
                return $skillResponse;
            }

            // Save success log
            $this->saveUploadLog(
                $user,
                $getDocumentDetails,
                $response,
                $responseData,
                $data
            );

            $this->documentPatientService->update(
                [
                    'send_alayacare_date' => now(),
                    'send_alayacare_by' => $user->id
                ],
                [
                    'id' => $request->id
                ]
            );

            return response()->json([
                'error_msg' => 'Successfully Uploaded',
                'data' => []
            ], 201);

        } catch (\Throwable $e) {
            // Log the exception details
            Log::error('Alayacare document upload exception', [
                'employee_id' => $getPatientDetails->alaycare_id ?? null,
                'document_id' => $request->id,
                'agency_id'   => $request->agency_id,
                'error'       => $e->getMessage(),
                'file'        => $e->getFile(),
                'line'        => $e->getLine(),
            ]);

            // Save exception details to alayacare_cron_log table
            $this->saveDocumentUploadCronLog(
                $getPatientDetails->alaycare_id ?? null,
                $request->agency_id ?? null,
                $request->id,
                $e->getMessage(),
                $e->getTraceAsString()
            );

            return response()->json([
                'error_msg' => 'Something went wrong while uploading the document. Please try again.',
                'data' => []
            ], 500);
        }
    }

    public function downloadAttachment(Request $request){
     
        $query = $this->commonEmployeeDetails($request->id,$request->agency_id);
        $data = $request->all();
        $data['folder'] = "nybest";
        $response = AlayacareHelper::downloadAttachmentFiles($query->emp_id,$query->agencyDetails->alaycare_username,$query->agencyDetails->alaycare_password,$data);
        $file_info = new \finfo(FILEINFO_MIME_TYPE);
        $mime_type = $file_info->buffer($response);
        $removeFiles = explode('/',$request->alaya_document);
      
        return response($response, 200)
        ->header('Content-Type', 'application/*')
        ->header('Content-Disposition', 'attachment; filename='.$removeFiles[2]);
    }

    public function searchAlayacareEmployee(Request $request){
        $agencyDetails  = Agency::getIdById($request->agency_id);
        $query = AlayacareHelper::searchEmployee($agencyDetails->alaycare_username,$agencyDetails->alaycare_password,$request->q);
        $finalArray = [];
        $response = json_decode($query,true);
       
        if(isset($response['count']) && $response['count'] !=0){
            $finalArray = $response['items'];
        }
       
        return response()->json(['error_msg'=>'asdassd','data'=>$finalArray]);
    }

    public function getAlayacareEmployeeDetail(Request $request){
        $agencyDetails  = Agency::getIdById($request->agency_id);
        $query = AlayacareHelper::getEmployeeById($request->agency_id,$request->id);
        $response  = json_decode($query,true);
        $final = [];
        if(isset($response['demographics']['first_name']) && $response['demographics']['first_name'] !=""){
            $final =$response['demographics'];
            $final['status'] = ucfirst($response['status']);
        }
       
        return response()->json(['error_msg'=>'asdassd','data'=>array($final)]);
    }

    public function fetchSkillDetails(Request $request){
        
        $getEmployeeSkillDetails = AlayacareHelper::getEmployeeSkillDetails($request->skill_id,$request->id,$request->agency_id);
        $employeeSkillDetails = json_decode($getEmployeeSkillDetails,true);
        $errorMgs = "";
        if(isset($employeeSkillDetails['code']) && $employeeSkillDetails['code'] ==404){
            $errorMgs = $employeeSkillDetails['message']??"";
        }else{
            $val['skillDetails'] = $employeeSkillDetails;
        }

         return response()->json(['error_msg'=>$errorMgs,'data'=>$employeeSkillDetails],200);
    }

    private function createNewDueSkill($agencyId,$requestData,$empId){
        $final = [];
     
        if (!empty($requestData)) {

            foreach ($requestData as $skill) {

               AlayacareHelper::updateEmployeeSkill($empId,$agencyId,['skill_id'=>$skill->skill_id,'fields'=>(array)$skill->fields]);
              
            }
        }
      
    }

    /**
     * Validation Error Response
     */
    private function validationErrorResponse($validator)
    {
        return response()->json([
            'error_msg' => $validator->errors()->first(),
            'data' => []
        ], 400);
    }
    /**
     * Prepare Upload Data
     */
    private function prepareUploadData($request, $documentDetails, $patientDetails)
    {
        $data = $request->except([
            '_token',
            'is_new_skill_relationship',
            'is_type'
        ]);

        $data['id'] = $patientDetails->id;
        $data['document_id'] = $request->id;
        $data['type'] = '';
        $data['file_name'] = $documentDetails->attachment;
        $data['file_path'] = $documentDetails->attachment;
        $data['folder'] = 'nybest';

        return $data;
    }

    /**
     * Upload Document To Alayacare
     */
    private function uploadDocumentToAlayacare($alaycareId, $agencyId, $data)
    {
        $response = AlayacareHelper::uploadDocumentSection(
            $alaycareId,
            $agencyId,
            $data
        );

        return json_decode($response, true);
    }

    /**
     * Handle Skill Relationship
     */
    private function handleSkillRelationship($agencyId, $responseData, $alaycareId)
    {
        if (empty($responseData)) {
            return null;
        }

        $skillDetails = $this->createNewDueSkill(
            $agencyId,
            $responseData,
            $alaycareId
        );

        if (empty($skillDetails[0])) {
            return null;
        }

        $message = $this->prepareSkillErrorMessage($skillDetails);

        if (empty($message)) {
            return null;
        }

        return response()->json([
            'error_msg' => $message,
            'data' => []
        ], $skillDetails[0]['response']['code']);
    }

    /**
     * Prepare Skill Error Message
     */
    private function prepareSkillErrorMessage($skillDetails)
    {
        $messages = [];
        $count = 1;

        foreach ($skillDetails as $skill) {
            if (!isset($skill['response']['code'])) {
                continue;
            }

            $messages[] = $count . '. ' . $skill['response']['message'];
            $count++;
        }

        return implode("\n", $messages);
    }
    /**
     * Save failed document upload details to alayacare_cron_log table
     */
    private function saveDocumentUploadCronLog($employeeId, $agencyId, $documentId, $errorMessage, $trace,$requestResponse="",$returnResponse="")
    {
        try {
            AlayacareCronLog::create([
                'type'        => 'DocumentUploadNew-DocId:' . $documentId,
                'employee_id' => $employeeId,
                'agency_id'   => $agencyId,
                'error_log'   => $errorMessage,
                'line'        => __LINE__,
                'trace'       => $trace,
                'created_at'  => date('Y-m-d H:i:s'),
                'cron_type'=>'Manual',
                'request_response'=>$requestResponse,
                'return_response'=>$returnResponse
            ]);
        } catch (\Throwable $logException) {
            Log::error('Failed to save AlayacareCronLog for document upload', [
                'original_error' => $errorMessage,
                'employee_id'    => $employeeId,
                'document_id'    => $documentId,
                'log_error'      => $logException->getMessage(),
            ]);
        }
    }

    /**
     * Save Upload Log
     */
    private function saveUploadLog(
        $user,
        $documentDetails,
        $response,
        $responseData,
        $data
    ) {
        $data['send_response'] = $responseData;
        $data['alayacare_return_url'] = $response['url'] ?? '';

        $insertLog = [
            'type' => 'Send To Alayacare Document',
            'link' => url('alayacare/alayacare-employee/alayacare-document-upload-new'),
            'module' => 'Patient Appointment',
            'object_id' => $documentDetails->patient_id,
            'message' => $user->first_name . ' ' . $user->last_name .
                ' has sent a document to the AlayaCare portal',
            'new_response' => serialize($data),
            'ip' => Utility::getIP(),
        ];

        LogsService::save($insertLog);
    }
}
