<?php

namespace App\Http\Controllers;

use App\Services\HHAPatientService;
use App\Model\HHAPatient;
use App\Model\HHACaregivers;
use App\Services\PatientService;
use App\Services\HHAPatientVisitService;
use App\Helpers\HHAPatientHelper;
use App\Services\HHACaregiverService;
use Illuminate\Http\Request;
use App\Agency;
use App\Helpers\Utility;
use App\Master;
use App\Services\LogsService;
use App\Services\LocationScheduleService;
use App\Model\Appointment;
use PDO;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Model\HHAOffice;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Cache;
use App\Services\HHAOfficeService;
use App\Services\HHAPOCTaskService;
use App\Services\AgencyService;
use App\Services\AgencyPocDocumentTypeService;
use App\Services\HHALogService;
use App\Services\HHAMDOService;
class HHAPatientController extends Controller
{

  protected $hhaPatientService;
  protected $patientService;
  protected $hhaPatientVisitService;
  protected $hhaCaregiverService;
  protected $locationScheduleService;
  protected $patientServicesRequest;
  protected $patientWiseServicesRequests = "";
  protected $hhaOffice;
  protected $hhaPOCTaskService;
  protected $agencyService;
  protected $agencyPOCDocumentTypeService;
  protected const COMMON_DATE_TIME = "Y-m-d H:i:s";
  protected $hhaLogService;
  protected $hhaMDOService;

  public function __construct(HHAPatientService $hhaPatientService, PatientService $patientService, HHAPatientVisitService $hhaPatientVisitService, HHACaregiverService $hhaCaregiverService, LocationScheduleService $locationScheduleService,PatientServicesRequest $patientServicesRequest, PatientWiseServicesRequests $patientWiseServicesRequests,HHAOfficeService $hhaOffice,HHAPOCTaskService $hhaPOCTaskService,AgencyService $agencyService,AgencyPocDocumentTypeService $agencyPOCDocumentTypeService,HHALogService $hhaLogService,HHAMDOService $hhaMDOService)
  {
    $this->hhaPatientService = $hhaPatientService;
    $this->patientService = $patientService;
    $this->hhaPatientVisitService = $hhaPatientVisitService;
    $this->hhaCaregiverService = $hhaCaregiverService;
    $this->locationScheduleService = $locationScheduleService;
    $this->patientServicesRequest = $patientServicesRequest;
		$this->patientWiseServicesRequests = $patientWiseServicesRequests;
    $this->hhaOffice = $hhaOffice;
    $this->hhaPOCTaskService = $hhaPOCTaskService;
    $this->agencyService = $agencyService;
    $this->agencyPOCDocumentTypeService = $agencyPOCDocumentTypeService;
    $this->hhaLogService = $hhaLogService;
    $this->hhaMDOService = $hhaMDOService;
  }

  public function index()
  {
    $data['user'] = $auth = auth()->user();
    if ($auth['user_type_fk'] != 184) {
      return abort(404);
    }
    $data['serviceList'] =  Cache::get('hha_appointment_status_list', function () {
      return Master::getServiceRequestNew('Patient');

    }, 10 * 60);
    $data['agency_list'] =  Cache::get('hha_appointment_agency_list', function () {
      return Agency::getHHAAgencyList();
    }, 10 * 60);

    return  view('hha_patient.hha_patient_list', $data);
  }

  public function ajaxList(Request $request)
  {
    $data['user'] = $auth = auth()->user();
    if ($auth['user_type_fk'] != 184) {
      return abort(404);
    }
    $data['agency_fk'] = $agency_fk = $request->agency_fk;
    $data['full_name'] = $full_name = $request->full_name;
    $data['admission_id'] = $admission_id = $request->admission_id;
    $data['home_phone'] = $home_phone = $request->home_phone;
    $data['coordinator_name'] = $coordinator_name = $request->coordinator_name;
    $data['service_start_date'] = $service_start_date = $request->service_start_date;
    $data['dob'] = $dob = $request->dob;
    $data['status'] = $status = $request->status;
    $data['sorting_column'] = $sorting_column = $request->sorting_column;
    $data['sorting_order'] = $sorting_order = $request->sorting_order;
    $data['hhasyncdatetime'] = $hhasyncdatetime = $request->hhasyncdatetime;

    $data['query'] = $this->hhaPatientService->getList($agency_fk, $full_name, $admission_id, $home_phone, $coordinator_name, $service_start_date, $dob, $status, $sorting_column, $sorting_order,$hhasyncdatetime);
    return  view('hha_patient.hha_patient_ajax_list', $data);
  }

  public function addAppoinmentPatient(Request $request)
  {
    $appointmentId = $request->input('appointmentId');
    $ids = explode(',', $appointmentId);

    $update = 0;

    if (!empty($ids[0])) {
      foreach ($ids as $val) {

        $getDetails = $this->hhaPatientService->getDetailsByPkId($val);

        $final_array = array(
          'first_name' => $getDetails->first_name,
          'middle_name' => $getDetails->middle_name,
          'last_name' => $getDetails->last_name,
          'full_name' => $getDetails->first_name.' '.$getDetails->last_name,
          'patient_code' => $getDetails->admission_id,
          'agency_id' => $getDetails->agency_fk,
          'phone' => $getDetails->home_phone,
          'mobile' => $getDetails->home_phone,
          'type' => 'Patient',
          'hha_id' => $getDetails->id,
          'address1' => $getDetails->address1,
          'address2' => $getDetails->address2,
          'state' => $getDetails->state,
          'city' => $getDetails->city,
          'zip_code' => $getDetails->zip5,
          'county' => $getDetails->county,

          'dob' => $getDetails->dob,
          'gender' => $getDetails->gender,
          'link_hha_patient' => $getDetails->patient_id,
          'cin' => $getDetails->medicaid_number,
          'referral_type'=>'HHA Exchange'
        );

        if (!empty($request->input('service_id')[0])) {
          $final_array['service_id'] = implode(',', $request->input('service_id'));
          $final_array['status'] = Utility::getStatusFromServiceId($request->input('service_id'));
        }
        $update = $this->patientService->save($final_array);
        if ($update) {
          $patientServiceCount = $this->patientServicesRequest->getServiceCountPatientId($update);
          $serviceRequestStatus = $final_array['status'];
          if (count($patientServiceCount) == 0) {
            $services = $request->input('service_id');
            if(!empty($services[0])){
              $patientServiceLastId = $this->patientServicesRequest->save([
                'patient_id' => $update,
                'follow_up_date' => null,
                'due_date' => null,
                'status' => $serviceRequestStatus,
                'created_at' => date(self::COMMON_DATE_TIME),
                'created_by' => auth()->user()->id,
                'completed_date' => null,
                'completed_by' => null,
                'flag'=>1
              ]);
              foreach ($services as $serviceId) {
                $patientWiseServiceRequest = [
                  'patient_id' => $update,
                  'service_id' => $serviceId,
                  'patient_service_request_id' => $patientServiceLastId,
                ];
                $this->patientWiseServicesRequests->save($patientWiseServiceRequest);
              }
            }
          }
          $this->hhaPatientService->update(array('patient_record_id' => $update), array('id' => $val));
          $ipaddress = Utility::getIP();
          $insertLog = [
            'type' => 'HHA Patient Appointment',
            'link' => url('/add-hha-appointment-patient'),
            'module' => 'Patient Appointment',
            'object_id' => $update,
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has added Appointment',
            'new_response' => serialize($final_array),
            'ip' => $ipaddress,
          ];
          LogsService::save($insertLog);
          try{
              Utility::saveResolutionLogForms($serviceRequestStatus,$patientServiceLastId,$update);
          }catch(Exception $e){}
        }
      }
      return response()->json(['error_msg' => "Appointment successfully added", 'data' => array()], 200);
    } else {
      return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
    }
  }


  public function syncPatientVisit(Request $request)
  {
    $query = $this->hhaPatientService->getDetailsWithAgencyRelationShipUsingAgency($request->patientId,$request->agency_id);
    $final_array = [];
    if (isset($query->id)) {

      $hhaDetails = HHAPatientHelper::getVisitDetails($query, $request->start, $request->end);

      if (!empty($hhaDetails[0])) {
        foreach ($hhaDetails as $val) {
          $tempArray = array();
          $fname =$val['caregiver_first_name'] ??"";
          $lname =$val['caregiver_last_name'] ??"";

          $visitDate = 'V:';
          if(isset($val['visit_date'])){
            $visitDate = 'V:' . date('m/d/Y', strtotime($val['visit_date']));
          }

          $schedule_start_time ="";

          if(isset($val['schedule_start_time']) && $val['schedule_start_time'] !=""){
            $schedule_start_time =date('h:i A', strtotime($val['schedule_start_time']));
          }

          $schedule_end_time ="";

          if(isset($val['schedule_end_time']) && $val['schedule_end_time'] !=""){
            $schedule_end_time =date('h:i A', strtotime($val['schedule_end_time']));
          }

          $sdate = "S : " . $schedule_start_time . ' - ' .$schedule_end_time;
          $pfname = isset($val['first_name'])?$val['first_name']:"";
          $plname = isset($val['last_name'])?$val['last_name']:"";

          $tempArray['title'] = $visitDate . '<br />' . $sdate;
          $patientname = 'P:' . $pfname . ' ' . $plname . '<br />';
          $caregivername ='C:' . $fname.' '.$lname;
          $tempArray['label'] = $caregivername .'<br>'. $patientname;
          $tempArray['start'] = $val['schedule_start_time']??"";
          $tempArray['caregiver_id'] = $val['caregiver_id'];
          $tempArray['agency_id'] =$request->agency_id;
          $tempArray['caregiver_full_name'] = $fname.' '.$lname;
          $final_array[] = $tempArray;
        }
      }
    }

    echo json_encode($final_array);
  }

  public function getAppointmentList(Request $request)
  {
    $query = $this->hhaPatientService->getDetailsWithAgencyRelationShip($request->id);
    $final_array = [];
    if (isset($query->id)) {


      $query = $this->hhaPatientVisitService->getHHAVisitList($query->patient_id, $request->start, $request->end);

      $tempArray = array();
      if (!empty($query)) {
        foreach ($query as $val) {

          $fname ="";
          $lname ="";
          if(isset($val->caregiverDetails) && $val->caregiverDetails !=""){
            $fname = isset($val->caregiverDetails->first_name) ? $val->caregiverDetails->first_name : "";
            $lname = isset($val->caregiverDetails->last_name) ? $val->caregiverDetails->last_name : "";
          }
          $visitDate = 'V:' . date('m/d/Y', strtotime($val->visit_date));
          $sdate = "S : " . date('h:i A', strtotime($val->schedule_start_time)) . ' - ' . date('h:i A', strtotime($val->schedule_end_time));

          $tempArray['title'] = $visitDate . '<br>' . $sdate;
          $patientname = 'P:' . $val->first_name . ' ' . $val->last_name . '<br>';
          $caregivername ='C:' . $fname.' '.$lname;
          $tempArray['label'] = $caregivername . $patientname;
          $tempArray['start'] = $val->schedule_start_time;

          $final_array[] = $tempArray;
        }
      }
    }
    echo json_encode($final_array);
  }

  public function getHHADocumentType(Request $request)
  {
    $agencyId = $request->input('agencyId');

    $query = HHAPatientHelper::getPatientDocumentType($agencyId);
    return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $query]);
  }

  public function HHAPatientCoordinator(Request $request)
  {

    $getPatientDetails =  $this->hhaPatientService->getDetailsById($request->patient_id);

    if (isset($getPatientDetails)) {
      $exploderd = explode('-', $request->serchDate);
      $startDate = $exploderd[0];
      $endDate = $exploderd[1];
      $query = HHAPatientHelper::getVisitsId($getPatientDetails->patient_id, $getPatientDetails->agency_fk, $startDate, $endDate);
    }
  }

  public function fetchPatient(Request $request)
  {
    $fetchCaregiver = HHAPatientHelper::getPatientIDListByAgencyIdNew($request->agency_id);
    $query = HHAPatient::fetchPatientCount($request->agency_id);
    return response()->json(['error_msg' => 'Patient successfully refresh',  'data' => array('total' => count($query))], 200);
  }

  public function syncPatient(Request $request, $id)
  {
    echo $offset = $request->offset;
    if ($request->$offset) {
      $offset = $request->$offset;
    }
    $query = HHAPatient::getSyncPatientList($id, $offset);
    if (!empty($query[0])) {
      foreach ($query as $val) {
        $subQuery = HHAPatientHelper::GetPatientDetailByPatientidID($val->patient_id, $val->agency_fk, 'manual');
      }
      $offset = $offset + 50;

      echo "<script>window.location.replace('/sync-agency-patient/" . $id . "?offset=" . $offset . "');</script>";
    } else {
      return response()->json(['error_msg' => 'HHA Patient successfully sync', 'data' => array()], 200);
    }
  }

  public function patientDemographicDetails(Request $request)
  {

    $getPatientDetails =  $this->hhaPatientService->getDetailsById($request->patient_id);
    $query = '';
    if (isset($getPatientDetails)) {
      $query = HHAPatientHelper::patientDemographicDetails($getPatientDetails->patient_id, $getPatientDetails->agency_fk);
      $getDiagnosisDetails = $this->hhaMDOService->fetchPatientDetails($getPatientDetails->agency_fk,$getPatientDetails->patient_id);
      $query['diagnosis'] = $getDiagnosisDetails;
    }
    
    return response()->json(['error_msg' => '', 'data' => array($query)], 200);
  }

  public function GetPatientAuthorizationInfo(Request $request)
  {
    $getPatientDetails =  $this->hhaPatientService->getDetailsById($request->patient_id);
    $response = [];
    if (isset($getPatientDetails)) {
      $response = HHAPatientHelper::GetPatientAuthorizationInfoDetails($getPatientDetails->patient_id, $getPatientDetails->agency_fk);
    }
    return response()->json(['error_msg' => '', 'data' => $response], 200);
  }


  public function linkHHAPatientList(Request $request)
  {
    $query = HHAPatientHelper::searchPatientWithAgencyId($request->agency_id, $request->q);
    $data = [];
    foreach ($query as $val) {
      $temp = [];
      $temp['id'] = $val->id;
      $temp['name'] = $val->name . '(' . $val->admission_id . ')';

      $data[] = $temp;
    }
    return json_encode($data);
  }

  public function syncHHAPatientNotes(Request $request)
  {

    $query = $this->hhaPatientService->getDetailsByOnlyPatientId($request->id);

    $startDate = date("Y-m-d", strtotime("-1 year"));
    $endDate = date("Y-m-d");
    $response = [];

    if (isset($query)) {

      $getAgencyDetails = Agency::where('id', $query->agency_fk)->first();

      $query->agencyDetails = $getAgencyDetails;
      $final = $query;


      if (isset($query->id)) {
        $final = $query;
      }
      $response = HHAPatientHelper::getHHAPatientNotes($final, $startDate, $endDate);
    }
    $response = [
      'message' => "success",
      'status' => 1,
      'data'    => $response,
    ];
    return response()->json($response, 200);
  }

  public function syncHHAPatientClinics(Request $request)
  {
    $query =$this->hhaPatientService->getDetailsByOnlyPatientId($request->id);


    $startDate = date("Y-m-d", strtotime("-1 year"));
    $endDate = date("Y-m-d");
    $response = [];

    if (isset($query)) {

      $getAgencyDetails = Agency::where('id', $query->agency_fk)->first();

      $query->agencyDetails = $getAgencyDetails;

      if (isset($query->id)) {
        $final = $query;
      }

      $response = HHAPatientHelper::getHHAPatientClinics($final);
    }
    $response = [
      'message' => "success",
      'status' => 1,
      'data'    => $response,
    ];
    return response()->json($response, 200);
  }

  public function getSyncHHAPatient()
  {
    $query = HHAPatientHelper::getSyncPatientList();
    foreach ($query as $patient) {
      HHAPatientHelper::GetPatientDetailByPatientidID($patient->patient_id, $patient->agency_fk);
    }
  }

  public function getSyncHHAPatientRemove()
  {
    $query = HHAPatient::selectRaw('COUNT(id) as total,patient_id')->whereNotNull('patient_id')->where('agency_fk', 246)->whereNull('patient_record_id')->whereNull('deleted_at')->groupBy('patient_id')->orderBy('total', 'desc')->get();
    foreach ($query as $val) {
      if ($val->total != 1) {
        $subQuery = HHAPatient::where('patient_id', $val->patient_id)->where('hha_delete_flag', 'N')->whereNull('patient_record_id')->whereNull('deleted_at')->orderBy('id', 'asc')->get();
        foreach ($subQuery as $key => $vals) {
          if ($key != 0) {
            HHAPatient::where('id', $vals->id)->update(array('deleted_at' => date(self::COMMON_DATE_TIME), 'hha_delete_flag' => 'Y'));
          }
        }
      }
    }
  }

  public function getSearchPatientPOC(Request $request)
  {
    $details = $this->commonPatientDetails($request->id);

    $getSearchPatientPoc = HHAPatientHelper::getSearchPatientPOCList($details);

    return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $getSearchPatientPoc]);
  }

  public function getPatientPOCInfo(Request $request)
  {
    $details = $this->commonPatientDetails($request->id);
    $getPatientPOCInfo = HHAPatientHelper::getPatientPOCInfoList($details,$request->poc_id);

    return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $getPatientPOCInfo]);
  }

  public function getSearchPatientAuthorizations()
  {
    $getSearchPatientAuthorizations = HHAPatientHelper::getSearchPatientAuthorizationsList();

    return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $getSearchPatientAuthorizations]);
  }

  public function commonPatientDetails($id){
    return $this->hhaPatientService->getDetailsWithAgencyRelationShip($id);
  }

  public function searchPatientCode(Request $request)
  {
    $query = HHAPatientHelper::searchPatientCodeWithAgencyId($request->agency_id, $request->all());
    return response()->json(['status' => true, 'data' => $query], 200);
  }

  public function checkExistingPatientRecord(Request $request)
  {
    $getDetails = $this->hhaPatientService->getDetailsById($request->id);
    $getExistingData = [];
    if (!empty($recordDetails)) {
      $getExistingData = $this->patientService->getDetailsByHHAPatient($recordDetails->toArray());
    }

    return response()->json(['error_msg' => "Appointment successfully added", 'data' => $getExistingData], 200);
  }

  public function linkForHHAPatient(Request $request)
  {
    $this->hhaPatientService->update(array('patient_record_id' => $request->id), array('id' => $request->patient_id));

    $oldData = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->id);
    if ($oldData->type == 'Caregiver') {
      $getAppointSchedule = $this->locationScheduleService->getDetailbyId($oldData->appoinment_time_id);

      $time = ($getAppointSchedule->start_time) ? $getAppointSchedule->start_time : "00:00:00";
    } else {
      $time = date('H:i:s', strtotime($oldData->appointment_date));
    }

    if ($oldData->status == 'completed') {

      $patientData = [
        'location_id' => "",
        'appointment_date' => null,
        'appointment_added_by' => null,
        'status' => 'Pending',
        'appointment_added_created_date' => null,
        'appoinment_time_id' => null,
        'appointment_mode' => '',

      ];
      if (!empty($request->service_id[0])) {
        $patientData['service_id'] = implode(',', $request->service_id);
      }
      $update = $this->patientService->update($patientData, array('id' => $request->id));
      $addAppintment = ["patient_id" => $request->id, "location_id" => $oldData->location_id, "service_id" => $oldData->service_id, "appointment_date" => $oldData->appointment_date, "appointment_time" => $time, "created_at" => date(self::COMMON_DATE_TIME)];
      Appointment::create($addAppintment);
    } else {
      $patientData['status'] = 'Pending';
      if (!empty($request->service_id[0])) {
        $patientData['service_id'] = implode(',', $request->service_id);
      }
      $update = $this->patientService->update($patientData, array('id' => $request->id));
    }


    $data = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->id);

    $ipaddress = Utility::getIP();
    $insertLog = [
      'type' => 'HHA Patient Appointment',
      'link' => url('/link-hha-patient-appointment'),
      'module' => 'Patient Appointment',
      'object_id' => $request->id,
      'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has added Appointment',
      'old_response' => serialize(array($oldData)),
      'new_response' => serialize(array($data)),
      'ip' => $ipaddress,
    ];
    LogsService::save($insertLog);
    return response()->json(['error_msg' => "Appointment successfully linked", 'data' => array()], 200);
  }

  public function syncHHAPatientSubject(Request $request)
  {
    $getPatientDetails =  $this->hhaPatientService->getDetailsById($request->id);
    $response = [];
    if (isset($getPatientDetails)) {
      $response = HHAPatientHelper::getHHASubject($getPatientDetails->patient_id, $getPatientDetails->agency_fk);
    }

    return response()->json(['error_msg' => "Success", 'data' => $response], 200);
  }

  public function getHHAPatientChangesV2(Request $request){
    $response = HHAPatientHelper::getHHAPatientChangesV2(trim($request->id));
  }
  public function getHHAPatientAuthorizationChangesV2(Request $request){
    $response = HHAPatientHelper::getHHAPatientAuthorizationChangesV2(trim($request->id));
  }
  public function syncHHAPatientOffice(Request $request)
  {
    $response = [];
    $officeData = HHAPatientHelper::getHHAPOCOffice($request->id);
    $getPatientDetails =  $this->hhaPatientService->getDetailsByOnlyPatientId($request->id);
    if(!empty($officeData[0])){
      foreach($officeData as $office){
        if($office['id'] ==$getPatientDetails->officeId){
          $response[] = $office;
        }
      }
    }
    return response()->json(['error_msg' => "Success", 'data' => $response], 200);
  }
  public function syncHHAPatientTask(Request $request){

    $taskData = HHAPatientHelper::getHHAPOCTask($request->id,$request->officeId,$request->patient_id);

    return response()->json(['error_msg' => "Success", 'data' => $taskData], 200);

  }
  public function addPatientPOCDetails(Request $request){
      $data = $request->all();
      $response = HHAPatientHelper::createPatientPOCDetails($request->id , $data);
      if(isset($response['status']) && $response['status'] ==1){
        $ipaddress = Utility::getIP();
        $insertLog = [
            'type' => 'POC create',
            'link' => url('/hha-add-patient-poc-deatils'),
            'module' => 'Patient Appointment',
            'object_id' => $request->portal_id??'',
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' has created POC Details',
            'new_response' => serialize($response),
            'ip' => $ipaddress,
          ];
          LogsService::save($insertLog);

          $ipaddress = Utility::getIP();
          $hhaLogData = [
            'patient_id'=>$request->portal_id??'',
            'hha_patient_id'=>$request->id,
            'type'=>"Patient",
            'hha_module_type'=>'HHX Exchange',
            'send_response'=>serialize($request->except('_token')),
            'ip_address' => $ipaddress,
            'action'=>'Create POC',
          ];
          $this->hhaLogService->save($hhaLogData);
          
        return response()->json(['error_msg' => "POC details added successfully.",'status' => 1, 'data' => $response['data']], 200);
      }else{
        return response()->json(['error_msg' => $response['message'], 'status' => 0], 500);
      }
  }
  public function searchPatientDocument(Request $request)
    {

        $docData = HHAPatientHelper::getDocumentData($request->id);
        usort($docData, array($this, "sortingDateWise"));
        return response()->json(['error_msg' => "Success", 'data' => $docData], 200);
    }
    public function getPatientDocumentType(Request $request)
    {

        $docTypeData = HHAPatientHelper::getDocumentTypeData($request->id);
        return response()->json(['error_msg' => "Success", 'data' => $docTypeData], 200);
    }
    public function savePatientDocument(Request $request)
    {
        $response = [];
        $data = $request->all();
        $response = HHAPatientHelper::saveDocumentData($data);
        if($response){
            return response()->json(['error_msg' => "Document details added successfully.",'status' => 1, 'data' => $response], 200);
        }else{
            return response()->json(['error_msg' => "Something went to wrong.", 'status' => 0, 'data' => $response], 422);
        }
    }

    public function sortingDateWise($a, $b){
      $a = date(self::COMMON_DATE_TIME, strtotime($a['CreatedOn']));
      $b = date(self::COMMON_DATE_TIME, strtotime($b['CreatedOn']));
      if ($a == $b) {
          return 0;
      }
      return ($b < $a) ? -1 : 1;
    }
    public function getPatientAppointmentData(Request $request){
      $query = HHAPatientHelper::commonDetails($request->id);
      $final_array = [];
      if (isset($query->id)) {
        $query = HHAPatientHelper::getVisitCalenderdata($query,$request->start,$request->end);
        $tempArray = array();
        if (!empty($query)) {
          foreach ($query as $val) {
            $pName=$cName="";
            if(isset($val['patient_first_name']) && $val['patient_last_name'] !=""){
                $pName = "P: ".$val['patient_first_name'].' '.$val['patient_last_name'];
            }
            $tempArray['title'] = "V : " . date('h:i A', strtotime($val['schedule_start_time'])) . ' - ' . date('h:i A', strtotime($val['schedule_end_time']));
            $tempArray['label'] = $cName.'</br>'.$pName;
            $tempArray['start'] = $val['schedule_start_time'];
            $final_array[] = $tempArray;
          }
        }
      }
      echo json_encode($final_array);
    }

    public function getPatientDiscipline(Request $request)
    {

        $disciplineData = HHAPatientHelper::getDisciplineData($request->id);
        return response()->json(['error_msg' => "Success", 'data' => $disciplineData], 200);
    }

    public function getPatientContract(Request $request)
    {
        $contractData = HHAPatientHelper::getContractData($request->id);
        return response()->json(['error_msg' => "Success", 'data' => $contractData], 200);
    }

    public function getPatientPrefrences(Request $request)
    {
        $docTypeData = HHAPatientHelper::getPrefrencesData($request->id);
        return response()->json(['error_msg' => "Success", 'data' => $docTypeData], 200);
    }

    public function getDownloadDocument(Request $request)
    {
        $docData = HHAPatientHelper::getDownloadDocumentData($request->id,$request->docid);
        return response()->json(['error_msg' => "Success", 'data' => $docData], 200);
    }

    public function exportCsv(Request $request)
    {
      $agency_fk = $request->agency_fk;
      $full_name = $request->full_name;
      $admission_id = $request->admission_id;
      $home_phone = $request->home_phone;
      $coordinator_name = $request->coordinator_name;
      $service_start_date = $request->service_start_date;
      $dob = $request->dob;
      $status = $request->status;
      $sorting_column = $request->sorting_column;
      $sorting_order = $request->sorting_order;
      $hhasyncdatetime = $request->hhasyncdatetime;

      $query = $this->hhaPatientService->getList($agency_fk, $full_name, $admission_id, $home_phone, $coordinator_name, $service_start_date, $dob, $status, $sorting_column, $sorting_order,$hhasyncdatetime,'export');
      $filename = 'Patient' . date("m-d-Y");
      $headers = array(
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0",
      );

      $columns = array('No','Office Name', 'Agency Name', 'Patient Full Name', 'Admission ID','Gender','Address1','Address2','Cross Street','City','State','County','Zip5','Home Phone', 'Coordinator Name', 'Service Start Date', 'DOB', 'Discipline', 'Medicaid Number', 'Medicare Number', 'HHA Status', "Last Sync Date", "Status", "Created Date");

      $callback = function () use ($query, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);
        foreach ($query as $list) {
          $service_start_date ="";
          if($list->service_start_date !=""){
            $service_start_date =date('m/d/Y',strtotime($list->service_start_date));
          }
          $hhasyncdatetime ="";
          if($list->hhasyncdatetime !=""){
            $hhasyncdatetime =date('m/d/Y h:i A',strtotime($list->hhasyncdatetime));
          }

          $created_date ="";
          if($list->created_at !=""){
            $created_date =date('m/d/Y h:i A',strtotime($list->created_at));
          }
          $statusAppointment ="Pending";
          if($list->patient_record_id !=""){
            $statusAppointment ="Added";
          }
          fputcsv($file, array($list->id, $list->office_name.' - '.$list->office_code,  $list->agencyDetail->agency_name, $list->first_name.' '.$list->last_name, $list->admission_id, $list->gender, $list->address1, $list->address2,$list->cross_street, $list->city, $list->state,$list->county,  $list->zip5, $list->home_phone,  $list->coordinator_name, $service_start_date, date('m/d/Y',strtotime($list->dob)), $list->EmploymentTypesDiscipline, $list->medicaid_number,$list->medicare_number, $list->status,$hhasyncdatetime,$statusAppointment, $created_date));
        }
        fclose($file);
      };
      return response()->stream($callback, 200, $headers);
    }

    public function agencyWiseSYNCPatient(){
      $query = Agency::whereNull('last_sync_patient')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->first();
        if(isset($query->id)){

            $getActivePatient = HHAPatient::where('agency_fk',$query->id)->where('hha_delete_flag','N')->whereRaw('(status ="Active" OR status IS NULL)')->pluck('patient_id');
            $getHoldPatient = HHAPatient::where('agency_fk',$query->id)->where('hha_delete_flag','N')->whereRaw('(status ="Hold" OR status IS NULL)')->pluck('patient_id');
            $getHospitalizedPatient = HHAPatient::where('agency_fk',$query->id)->where('hha_delete_flag','N')->whereRaw('(status ="Hospitalized" OR status IS NULL)')->pluck('patient_id');
            $getDischargedPatient = HHAPatient::where('agency_fk',$query->id)->where('hha_delete_flag','N')->whereRaw('(status ="Discharged" OR status IS NULL)')->pluck('patient_id');
            $hhaActivePatientResponse = HHAPatientHelper::autoSYNCPatientWithStatus($query,'Active');

            $final = array_merge($getActivePatient->toArray(),$getHoldPatient->toArray(),$getHospitalizedPatient->toArray(),$getHospitalizedPatient->toArray(),$getDischargedPatient->toArray());
            $activePatient= array_diff($hhaActivePatientResponse,$final);
            if(count($activePatient) >0){
                foreach($activePatient as $act){
                  HHAPatient::updateOrCreate([
                        "agency_fk"      => $query->id,
                        "patient_id"        => $act,
                    ], [
                      'hha_delete_flag'=>'N',
                      'created_at'=>date(self::COMMON_DATE_TIME),
                    ]);
                }
            }

            $hhaHoldPatientResponse = HHAPatientHelper::autoSYNCPatientWithStatus($query,'Hold');
            $hhaHoldPatientResponse =  array_intersect($hhaHoldPatientResponse,$getHoldPatient->toArray());

            if(count($hhaHoldPatientResponse) >0){
              HHAPatient::where('agency_fk', $query->id)
              ->whereIn('patient_id', $hhaHoldPatientResponse)
              ->update([
                  'status' => 'Hold',
                  'hhasyncdatetime' => date(self::COMMON_DATE_TIME),
              ]);
            }

            $hhaHospitalizedPatientResponse = HHAPatientHelper::autoSYNCPatientWithStatus($query,'Hospitalized');
            $hhaHospitalizedPatientResponse =  array_intersect($hhaHospitalizedPatientResponse,$getHospitalizedPatient->toArray());
            if(count($hhaHospitalizedPatientResponse) >0){
              HHAPatient::where('agency_fk', $query->id)
              ->whereIn('patient_id', $hhaHospitalizedPatientResponse)
              ->update([
                  'status' => 'Hospitalized',
                  'hhasyncdatetime' => date(self::COMMON_DATE_TIME),
              ]);
            }

            $hhaDischargedPatientResponse = HHAPatientHelper::autoSYNCPatientWithStatus($query,'Discharged');
            $hhaDischargedPatientResponse =  array_intersect($hhaDischargedPatientResponse,$getDischargedPatient->toArray());
            if(count($hhaDischargedPatientResponse) >0){
              HHAPatient::where('agency_fk', $query->id)
              ->whereIn('patient_id', $hhaDischargedPatientResponse)
              ->update([
                  'status' => 'Discharged',
                  'hhasyncdatetime' => date(self::COMMON_DATE_TIME),
              ]);
            }
            Agency::where('id',$query->id)->update(array('last_sync_patient'=>date(self::COMMON_DATE_TIME)));
        }
    }

    public function agencyWisePatientDemographicDetails(){
      $query = HHAPatient::where('hha_delete_flag','N')->where('hha_sync','N')->whereNull('first_name')->inRandomOrder()->limit(500)->get();

      if(count($query) >0){
        foreach($query as $val){
          $agencyDetails = Agency::where('id',$val->agency_fk)->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->first();
          if(isset($agencyDetails->id)){
            $subquery =  HHAPatientHelper::allDataSyncPatients($val->patient_id,$agencyDetails);

            if(isset($subquery['firstName'])){
              $updateArray = array(
                  "first_name" => $subquery['firstName'],
                  "last_name" => $subquery['lastName'],
                  'middle_name' => $subquery['middleName'],
                  'gender' => $subquery['gender'],
                  'dob' => date('Y-m-d',strtotime($subquery['dob'])),
                  'admission_id' => $subquery['admission_id'],
                  'coordinator_id' => $subquery['coordinator_id'],
                  'coordinator_name' => $subquery['coordinator_name'],
                  'service_start_date' =>date('Y-m-d',strtotime( $subquery['service_start_date'])),
                  'medicaid_number' => $subquery['medicaid_number'],
                  'medicare_number' => $subquery['medicare_number'],
                  'address1' => $subquery['address1'],
                  'address2' => $subquery['address2'],
                  'cross_street' => $subquery['cross_street'],
                  'city' => $subquery['city'],
                  'zip5' => $subquery['zip5'],
                  'state' => $subquery['state'],
                  'county' => $subquery['county'],
                  'home_phone' => $subquery['home_phone'],
                  'phone2' => $subquery['phone2'],
                  'officeId' =>$subquery['officeId'],
                  "hha_sync" => "Y",
                  "hhasyncdatetime" => date(self::COMMON_DATE_TIME),
                  "EmploymentTypesDiscipline" => $subquery['discipline'],
                  "status" => $subquery['patientStatusName'],
                  "TeamName" => $subquery['teamName'],
                  "nurseId" => $subquery['nurseId'],
                  "nurseName" => $subquery['nurseName'],
              );

              HHAPatient::where('id',$val->id)->update($updateArray);
            }
          }
        }
      }
    }

    public function patientModifiedPatientIds(){
      $query = Agency::whereNull('last_patient_sync_modifled_date')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->first();
      if(isset($query->id)){
        $modifiedCaregiverIds = HHAPatientHelper::getModifieldPatientIds($query);
        if(count($modifiedCaregiverIds) >0){
          foreach($modifiedCaregiverIds as $val){
            HHAPatient::where('patient_id',$val)->update(array('hha_sync'=>'N'));
          }
        }
      }
    }

    public function updateModifiedPatientIds(){
      $query = HHAPatient::select('id','patient_id','agency_fk')->whereNotNull('first_name')->where('hha_delete_flag','N')->where('hha_sync','N')->inRandomOrder()->limit(500)->get();

      if(count($query) >0){
        foreach($query as $val){
          $agencyDetails = Agency::where('id',$val->agency_fk)->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->first();
          if(isset($agencyDetails->id)){
            $subquery =  HHAPatientHelper::allDataSyncPatients($val->patient_id,$agencyDetails);
            if(isset($subquery['firstName'])){
              $updateArray = array(
                  "first_name" => $subquery['firstName'],
                  "last_name" => $subquery['lastName'],
                  'middle_name' => $subquery['middleName'],
                  'gender' => $subquery['gender'],
                  'dob' => date('Y-m-d',strtotime($subquery['dob'])),
                  'admission_id' => $subquery['admission_id'],
                  'coordinator_id' => $subquery['coordinator_id'],
                  'coordinator_name' => $subquery['coordinator_name'],
                  'service_start_date' =>date('Y-m-d',strtotime( $subquery['service_start_date'])),
                  'medicaid_number' => $subquery['medicaid_number'],
                  'medicare_number' => $subquery['medicare_number'],
                  'address1' => $subquery['address1'],
                  'address2' => $subquery['address2'],
                  'cross_street' => $subquery['cross_street'],
                  'city' => $subquery['city'],
                  'zip5' => $subquery['zip5'],
                  'state' => $subquery['state'],
                  'county' => $subquery['county'],
                  'home_phone' => $subquery['home_phone'],
                  'phone2' => $subquery['phone2'],
                  'officeId' =>$subquery['officeId'],
                  "hha_sync" => "Y",
                  "hhasyncdatetime" => date(self::COMMON_DATE_TIME),
                  "EmploymentTypesDiscipline" => $subquery['discipline'],
                  "status" => $subquery['patientStatusName'],
                  "TeamName" => $subquery['teamName'],
                  "nurseId" => $subquery['nurseId'],
                  "nurseName" => $subquery['nurseName'],

              );

              HHAPatient::where('id',$val->id)->update($updateArray);
            }
          }

        }
      }
    }

    public function getPatientTabDetails(Request $request,$tabName){
      try {
          $patientId = $request->patient_id;

          if (!$patientId) {

              return response()->json([
                  'error_msg' => 'Patient ID is required',
                  'data' => array()
              ], 422);
          }

          // Get caregiver details
          $query = $this->hhaPatientService->getDetailsByPatientID($patientId,$request->agency_id);
          if (!isset($query->id)) {
              return response()->json([
                  'error_msg' => 'Patient not found',
                  'data' => []
              ], 404);
          }

          $request->agency_fk = $request->agency_id;
          $data = [];

          // Route to appropriate method based on tab name
          switch ($tabName) {
              case 'demographic':
                  $data = $this->getDemographicData($request);
                  break;

              case 'pcalendar':
                  $data = $this->getPatientCalendarData($query, $request);
                  break;

              case 'authorization':
                  $data = $this->getAuthorizationInfoDetails($query, $request);
                  break;

              case 'pnotes':
                  $data = $this->getNotesData($query, $request);
                  break;

              case 'clinical':
                  $data = $this->getClinicalData($query, $request);
                  break;

              case 'pocInfo':
                  $data = $this->getPocInfoData($query,$request);
                  break;

              case 'contract':
                  $data = $this->getContractData($query, $request);
                  break;

              case 'pdocument':
                  $data = $this->getPatientDocumentData($query);
                  break;

              case 'discipline':
                  $data = $this->getPatientDisciplineData($query);
                  break;
              case 'ppreferences':
                  $data = $this->getPreferencesData($query);
                  break;

                case 'mdorder':
                  $data = $this->getPreferencesData($query);
                  break;
              default:
                  return response()->json([
                      'status' => 0,
                      'error_msg' => 'Invalid tab name',
                      'data' => []
                  ], 400);
          }

          return response()->json([
              'error_msg' => 'Success',
              'data' => $data
          ], 200);

      } catch (\Exception $e) {
          return response()->json([
              'error_msg' => 'Error loading data: ' . $e->getMessage(),
              'data' => []
          ], 500);
      }
  }

  /**
     * Get demographic data for caregiver
     */
    private function getDemographicData($data)
    {
      return HHAPatientHelper::patientDemographicDetails($data->patient_id, $data->agency_id);
    }

    private function getPatientCalendarData($query, $request){
      $agencyDetails = $this->agencyDetails($request->agency_id);
      $query->agencyDetails = $agencyDetails;
      return HHAPatientHelper::getVisitCalenderdata($query,$request->start,$request->end);
    }

    private function agencyDetails($agencyId){
      return Agency::where('id',$agencyId)->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->first();
    }

    private function getAuthorizationInfoDetails($query,$data){
      return HHAPatientHelper::GetPatientAuthorizationInfoDetails($query->patient_id, $data->agency_id);
    }

    private function getNotesData($query,$data){
      $agencyDetails = $this->agencyDetails($data->agency_id);
      $query->agencyDetails = $agencyDetails;
      $startDate = date("Y-m-d", strtotime("-1 year"));
      $endDate = date("Y-m-d");
      return HHAPatientHelper::getHHAPatientNotes($query, $startDate, $endDate);
    }

    private function getClinicalData($query,$data){
      $agencyDetails = $this->agencyDetails($data->agency_id);
      $query->agencyDetails = $agencyDetails;
      return HHAPatientHelper::getHHAPatientClinics($query);
    }

    private function getPocInfoData($query,$data){
      $agencyDetails = $this->agencyDetails($data->agency_id);
      $query->agencyDetails = $agencyDetails;
      return HHAPatientHelper::getSearchPatientPOCList($query);
    }

    private function getPatientDocumentData($query){
      return HHAPatientHelper::getDocumentData($query->patient_id);
    }

    private function getContractData($query){
      return HHAPatientHelper::getContractData($query->patient_id);
    }

    private function getPatientDisciplineData($query){
      return HHAPatientHelper::getDisciplineData($query->patient_id);
    }

    private function getPreferencesData($query){
      return HHAPatientHelper::getPrefrencesData($query->patient_id);
    }

    public function getPatientCalendarVisits(Request $request){
      try {
        $patient_id = $request->patient_id;
        $agencyId = $request->agency_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if (!$patient_id || !$agencyId) {
            return response()->json([
                'status' => 0,
                'error_msg' => 'Patient ID and Agency ID are required',
                'data' => []
            ], 400);
        }

        // If dates not provided, default to current month
        if (!$startDate || !$endDate) {
            $startDate = date('Y-m-01'); // First day of current month
            $endDate = date('Y-m-t');    // Last day of current month
        }
        $query = $this->hhaPatientService->getDetailsByPatientID($patient_id,$agencyId);
        if (!isset($query->id)) {
            return response()->json([
                'status' => 0,
                'message' => 'Patient not found',
                'data' => []
            ], 404);
        }

        $agency = Agency::getAllDetailsbyAgencyId($agencyId);
        $query->agencyDetails = $agency;
        // Get caregiver visits for calendar with date range
        $visits = HHAPatientHelper::getVisitCalenderdata($query, $startDate, $endDate);
        // Format visits for FullCalendar
        $formattedVisits = [];
        if (!empty($visits)) {
            foreach ($visits as $visit) {
                $visitDate = $visit['visit_date'] ?? $visit['VisitDate'] ?? $visit['start_date'] ?? null;
                // Filter visits by date range (in case helper doesn't do it)

                $pName="P: ".$visit['patient_first_name'].' '.$visit['patient_last_name'];

                $formattedVisits[] = [
                    'visit_id' => $visit['visit_id'] ?? $visit['id'] ?? null,
                    'title'=>"V :" . Utility::convertTwelveHourTime($visit['schedule_start_time']) . ' - ' .Utility::convertTwelveHourTime($visit['schedule_end_time']),
                    'patient_name' => $pName ?? 'N/A',
                    'label' =>"C: ". $visit['caregiver_first_name'] . ' ' . $visit['caregiver_last_name'].' <br>'.$pName,
                    'patient_id' => $visit['patient_id'] ?? null,
                    'visit_date' => $visitDate,
                    'start' =>Utility::convertMDYTime($visit['schedule_start_time']) ?? null,
                    'status' => $visit['status'] ?? $visit['Status'] ?? 'Scheduled',
                    'end' => Utility::convertMDYTime($visit['schedule_end_time']) ?? null,
                    'notes' => $visit['notes'] ?? $visit['Notes'] ?? null,
                    'start_time' => $visit['start_time'] ?? $visit['StartTime'] ?? null,
                    'end_time' => $visit['end_time'] ?? $visit['EndTime'] ?? null,
                ];

            }
        }

        return response()->json([
            'status' => 1,
            'error_msg' => 'Success',
            'data' => [
                'caregiver_id' => $query->patient_id,
                'visits' => $formattedVisits,
                'total_visits' => count($formattedVisits),
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 0,
            'error_msg' => 'Error loading calendar data: ' . $e->getMessage(),
            'data' => []
        ], 500);
    }
  }

  public function configurationPOCTask($id){
    $getOffice = $this->hhaOffice->getOfficeDetailsBySha1AgencyId($id);
    if(!empty($getOffice[0])){
      foreach($getOffice as $val){
        $getOffices = $this->hhaPatientService->getPatientDetailsWithWithAgencyId($id,$val->office_id);
        if(isset($getOffices->patient_id)){
          $getPOCTask = HHAPatientHelper::getHHAPOCTask($getOffices->patient_id,$getOffices->officeId,"");
          if (empty($getPOCTask)) {
              continue;
          }
          if(!empty($getPOCTask[0])){
            foreach($getPOCTask as $poc){
              $final = [
                'task_id'   => $poc['id'],
                'task_name' => $poc['task_name'],
                'task_code' => $poc['code'],
                'category'  => $poc['task_category'],
                'agency_id' =>$getOffices->agency_fk
              ];
              $this->hhaPOCTaskService->save($final);
            }
            return response()->json(['status'=>true,'error_msg'=>'POC successfully sync'],200);
          }
        }
      }
    }
    return response()->json(['status'=>false,'error_msg'=>'Office not found'],500);
  }

  public function syncDocumentType(Request $request){
      $getAgencyDetails = $this->agencyService->getAgencyDetailsBySha1Id($request->id);
      if(isset($getAgencyDetails->id)){
        $getDocumentType = HHAPatientHelper::GetPatientDocumentType($getAgencyDetails->id);
        if(!empty($getDocumentType[0])){
          foreach($getDocumentType as $doc){
            $finalResponse = [
              'agency_id' =>$getAgencyDetails->id,
              'document_id' =>$doc['id'],
              'document_name' =>$doc['name'],
              'status' =>$doc['Status']
            ];
            $this->agencyPOCDocumentTypeService->saveOrUpdate($finalResponse);
          }
        }
        $documentList = $this->agencyPOCDocumentTypeService->getDocumentbyAgencyId($getAgencyDetails->id);
        return response()->json([
          'status'     => true,
          'error_msg'  => 'Document successfully sync',
          'data'       => $documentList,
          'selected'   => $getAgencyDetails->poc_document_type_id,
        ], 200);
      }
      return response()->json(['status'=>false,'error_msg'=>'Agency not found'],500);
  }


}