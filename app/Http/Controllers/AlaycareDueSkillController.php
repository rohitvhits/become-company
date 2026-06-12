<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Agency;
use App\Services\AlayacareEmployeeDueSkillService;
use App\Services\PatientService;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Helpers\Utility;
use App\Helpers\Common;
use App\Services\LogsService;
use App\Services\AgencyService;
use Illuminate\Support\Facades\Cache;
use App\Services\MasterService;

class AlaycareDueSkillController extends Controller
{

    protected $alayacareEmployeeDueSkillService;
    protected $patientService;
    protected $patientServicesRequest;
    protected $patientWiseServicesRequests="";
    protected $agencyService;
    protected $masterService;
    public function __construct(AlayacareEmployeeDueSkillService $alayacareEmployeeDueSkillService,PatientService $patientService,PatientServicesRequest $patientServicesRequest,PatientWiseServicesRequests $patientWiseServicesRequests,AgencyService $agencyService,MasterService $masterService)
    {
        $this->middleware('permission:alayacare-due-skill', ['only' => ['index', 'ajaxList', 'exportCSV', 'addAlayacarePatientAppointment']]);
        $this->middleware('permission:add-appointment-alayacare-due-skill', ['only' => ['addAlayacarePatientAppointment']]);
        $this->middleware('permission:alayacare-due-skill-export', ['only' => ['exportCSV']]);
        $this->middleware('auth');
        $this->alayacareEmployeeDueSkillService = $alayacareEmployeeDueSkillService;
        $this->patientService = $patientService;
        $this->patientServicesRequest = $patientServicesRequest;
        $this->patientWiseServicesRequests = $patientWiseServicesRequests;
        $this->agencyService = $agencyService;
        $this->masterService = $masterService;
    }

    public function index(){
        $data['menu'] = "user";
        $data['auth'] = $data['user'] = $user = auth()->user();

        if (empty($user)) {
            return redirect('/login');
        }

        if (in_array($user['user_type_fk'], array(3, 4, 5, 6))) {
            abort(404);
        }
        $data['agencyList'] = $this->agencyService->getAlayacareAgencyList();
        $data['masterData'] = Cache::get('alayacare-emp-due-skil', function () {
            return $this->masterService->getAllDataByMasterTypeFk([26]);
        }, 10 * 60);
        return view("alayaDueSkill.index", $data);
    }

    public function ajaxList(Request    $request){
        
        $data['list'] = $this->alayacareEmployeeDueSkillService->getList($request->all());
        return view("alayaDueSkill.ajax_list", $data);
    }

    public function exportCSV(Request $request)
    {
        $user = auth()->user();

        if (empty($user)) {
            return redirect('/login');
        }

        // Get data without pagination
        $list = $this->alayacareEmployeeDueSkillService->getList($request->all(), 'export');

        // Set filename
        $filename = 'alayacare_due_skill_' . date('Y-m-d_His') . '.csv';

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Create file pointer
        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Add CSV headers
        fputcsv($output, [
            'Agency Name',
            'Employee Name',
            'Employee Code',
            'Date of Birth',
            'Employee Phone',
            'Skill Name',
            'Due Date',
            'Appointment Status',
            'Created Date'
        ]);

        // Add data rows
        foreach ($list as $row) {
            $employeeName = trim($row->first_name . ' ' . $row->last_name);
            $status = !empty($row->patient_id) ? 'Added' : 'Pending';
            $dob = !empty($row->birthday) ? Utility::convertMDY($row->birthday): '';
            $dueDate = !empty($row->due_date) ?Utility::convertMDYTime($row->due_date) : '';
            $createdDate = !empty($row->created_date) ?Utility::convertMDYTime($row->created_date) : '';

            fputcsv($output, [
                $row->agency_name ?? '',
                $employeeName,
                $row->employee_id ?? '',
                $dob,
                $row->phone ?? '',
                $row->skill_name ?? '',
                $dueDate,
                $status,
                $createdDate
            ]);
        }

        fclose($output);
        exit();
    }

    public function addAlayacarePatientAppointment(Request $request)
    {
      $user=   $auth = auth()->user();
        $validator = Validator::make($request->all(),[
            'ids'=>'required',
        ],['name.required' => 'Please select Appointment Ids']);

        if($validator->fails()){
            return response()->json(['status'=>false, 'error'=>$validator->errors()->toArray()]);
        } else {
            $ids = explode(',',$request->ids);
            
            $update = 0;
            if (!empty($ids[0])) {
                foreach ($ids as $val) {
                    $final_array = $this->prepareData($val,$request->all());

                    $getLinkPatientOrNot = $this->alayacareEmployeeDueSkillService->checkForLinkPatient($final_array['alaycare_id'],$final_array['agency_id']);
                 
                    if(count($getLinkPatientOrNot) ==0){
                        $update = $this->patientService->save($final_array);
                        if($update){
                            $this->saveCaregiverRequest($update,$final_array,$request->all());
                            $this->alayacareEmployeeDueSkillService->update(array('patient_id' => $update,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$auth->id), array('id' => $val));
                            $patientDetailsNew = $this->patientService->getPatientId($update);

                            $insertLog = [
                                'type' => 'Add Alayacare Due Skill Appointment',
                                'link' => url('alayacare/alayacare-skill/add-alayacare-patient-appointment'),
                                'module' => 'Patient Appointment',
                                'object_id' => $update,
                                'message' => $user->first_name . ' ' . $user->last_name . ' has added Appointment',
                                'new_response' => serialize($patientDetailsNew->toArray()),
                            ];
                            $this->logAction($insertLog);
                        }
                        
                    }else{
                        $update = $getLinkPatientOrNot[0]->patient_id??"";
                        if($update !=""){
                            $patientOldDetailsNew = $this->patientService->getPatientId($getLinkPatientOrNot[0]->patient_id);
                            $this->patientService->update(array('service_id'=>implode(',', $request->service_id),'status'=>'Pending'),array('id'=>$getLinkPatientOrNot[0]->patient_id));
                            $this->saveCaregiverRequest($update,$final_array,$request->all());
                            
                            $this->alayacareEmployeeDueSkillService->update(array('patient_id' => $getLinkPatientOrNot[0]->patient_id,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>$auth->id), array('id' => $val));
                            $patientDetailsNew = $this->patientService->getPatientId($getLinkPatientOrNot[0]->patient_id);
                            $insertLog = [
                                'type' => 'Update Alayacare Due Skill Appointment For Existing Patient',
                                'link' => url('alayacare/alayacare-skill/add-alayacare-patient-appointment'),
                                'module' => 'Patient Appointment',
                                'object_id' => $getLinkPatientOrNot[0]->patient_id,
                                'message' => $user->first_name . ' ' . $user->last_name . ' has added Appointment',
                                'old_response'=>serialize($patientOldDetailsNew->toArray()),
                                'new_response' => serialize($patientDetailsNew->toArray())
                            ];
                            $this->logAction($insertLog);
                        }
                    }
                }
                return response()->json(['error_msg' => "Appointment successfully added", 'data' => array()], 200);
            } else {
                return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
            }
        }
    }

    private function logAction($data){
        $insertLog = [
            'type' =>$data['type'],
            'link' =>$data['link'],
            'module' =>$data['module'],
            'object_id' =>$data['object_id'],
            'message' =>$data['message'],
            'new_response' =>$data['new_response'],
            'ip' =>Utility::getIP(),
        ];

        if(isset($data['old_response'])){
            $insertLog['old_response'] = $data['old_response'];
        }
        LogsService::save($insertLog);
    }

    private function prepareData($id,$data){
        $getDetails = $this->alayacareEmployeeDueSkillService->getDetailsById($id);
        $agency_Id   = ($getDetails->agency_id  !="")?$getDetails->agency_id:$getDetails->employeeDetails[0]->agency_id;
        
        return [
            'first_name' => $getDetails->employeeDetails[0]->first_name,
            'last_name' => $getDetails->employeeDetails[0]->last_name,
            'full_name' => $getDetails->employeeDetails[0]->first_name.' '.$getDetails->employeeDetails[0]->last_name,
            'patient_code' => $getDetails->employeeDetails[0]->ac_id,
            'agency_id' => $agency_Id,
            'phone' => $getDetails->employeeDetails[0]->phone,
            'mobile' => $getDetails->employeeDetails[0]->phone,
            'type'=>'Caregiver',
            'alaycare_id' => $getDetails->employeeDetails[0]->emp_id,
            'service_id' => implode(',',$data['service_id']),
            'diciplin' => $data['diciplin'],
            'language' => Common::getOrCreateLanguageId($getDetails->employeeDetails[0]->language),
            'county' => $getDetails->employeeDetails[0]->country,
            'dob' => $getDetails->employeeDetails[0]->birthday,
            'gender' =>$getDetails->employeeDetails[0]->gender,
            'due_date'=>Utility::convertYMD($getDetails->due_date),
            'email'=>$getDetails->employeeDetails[0]->email,
            'patient_code'=>$getDetails->employeeDetails[0]->external_id,
            'address1'=>$getDetails->employeeDetails[0]->address,
            'state'=>$getDetails->employeeDetails[0]->state,
            'city'=>$getDetails->employeeDetails[0]->city,
            'zip_code'=>$getDetails->employeeDetails[0]->zip,
            'referral_type'=>'Alayacare'
        ];
    }

    private function saveCaregiverRequest($update,$final_array,$data){
        $patientServiceLastId = $this->patientServicesRequest->save([
            'patient_id' => $update,
            'due_date'=>$final_array['due_date']
        ]);
        $addServiceIds = $data['service_id'];
        if (is_array($addServiceIds)) {
            foreach ($addServiceIds as $serviceId) {
                if($serviceId !=""){
                    $patientWiseServiceRequest = [
                        'patient_id' => $update,
                        'service_id'=> $serviceId,
                        'patient_service_request_id' => $patientServiceLastId,
                    ];
                    $this->patientWiseServicesRequests->save($patientWiseServiceRequest);
                }
            }
        }
    }
}
