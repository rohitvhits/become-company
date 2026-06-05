<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;

use App\Agency;
use App\Helpers\HHAAppointmentHelper;
use App\Helpers\HHACaregiversHelper;
use App\Model\HHAOffice;

use App\Services\PatientService;
use App\Master;
use App\Services\DocumentPatientService;
use Illuminate\Support\Facades\Cache;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use DB;
use Carbon\Carbon;
use App\Services\HHAOfficeService;
use App\Model\HHACaregivers;
use App\Model\HhaAppointment;

class HHAAppointmentController extends BaseController
{

    protected $patientServicesRequest;
    protected $patientWiseServicesRequests;
    protected $documentPatientService;
    protected $hhaOfficeService;
    protected $patientService;
    public function __construct(PatientService $patientService,DocumentPatientService $documentPatientService,PatientServicesRequest $patientServicesRequest, PatientWiseServicesRequests $patientWiseServicesRequests,HHAOfficeService $hhaOfficeService)
    {
        $this->middleware('auth');
        $this->middleware('permission:hha-medical|add-appointment-hha-medical', ['only' => ['index','hhaAppoitmentAjax','addAppoinmentPatient']]);
        $this->middleware('permission:hha-medical-export', ['only' => ['exportCsv','hhaAppoitmentAjax','addAppoinmentPatient']]);
        $this->middleware('permission:add-appointment-hha-medical', ['only' => ['addAppoinmentPatient']]);
        $this->patientService = $patientService;
        $this->documentPatientService = $documentPatientService;
        $this->patientServicesRequest = $patientServicesRequest;
		$this->patientWiseServicesRequests = $patientWiseServicesRequests;
        $this->hhaOfficeService = $hhaOfficeService;
    }

    public function index(Request $request)
    {
    
        $data['menu'] = "user";
        $data['auth'] = $data['user'] = $user = auth()->user();

        if (empty($user)) {
            return redirect('/login');
        }

        if (in_array($user['user_type_fk'], array(3, 4, 5, 6))) {
            abort(404);
        }

        $data['status_list'] =  Cache::get('hha_appointment_status_list', function () {
			return  HHACaregiversHelper::getHHACaregiverStatus();
		}, 10 * 60);
  
        $data['office_table_list'] =  Cache::get('hha_office_table_list', function () {
			return  $this->hhaOfficeService->getALLOfficeList();
		}, 10 * 60);

        $data['agency_list'] =  Cache::get('hha_agency_table_list', function () {
            return  Agency::getHHAAgencyList();
        }, 10 * 60);
        $data['startDate'] = Carbon::now()->subMonths(2)->startOfMonth()->format('m/d/Y');
        $data['endDate'] = Carbon::now()->format('m/d/Y');
        return view("hha_appointment.hha_appointment_list", $data);
    }


    public function hhaAppoitmentAjax(Request $request)
    {
        
        $data['agency_fk'] = $agency_fk = $request->agency_fk;
        $data['fname'] = $fname = $request->fname;
        $data['code'] = $code = $request->code;

        $data['medical_name'] = $medical_name = $request->medical_name;
        $data['due_date'] = $due_date = $request->due_date;
        $data['status'] = $status = $request->status;
        $data['caregiver_status'] = $caregiver_status = $request->caregiver_status;
        $data['date_perform'] = $date_perform = $request->date_perform;
        $hire_date = $request->hire_date;
        $employment_type = $request->employment_type;
       
        $data['query'] = HHAAppointmentHelper::hhaAppoitnmentList($agency_fk, $fname, $code,$request->office_id, $medical_name, $due_date, $status,$caregiver_status,$date_perform,$hire_date,$employment_type);
   
        $data['agencyListDetails'] =  Cache::get('hha_agency_list', function () {
            return Agency::where('delete_flag','N')->whereNotNull('app_name')->where('enable_hha',1)->pluck('agency_name','id');
        });

        $data['office_list'] = Cache::remember('hha_office_list', 10 * 60, function () {
            return $this->hhaOfficeService->getPluckOfficeList();
        });
        return view("hha_appointment.hha_appointment_ajax", $data);
    }

    public function addAppoinmentPatient(Request $request)
    {
        $auth = auth()->user();
        $ids = $request->input('final_array');
        $update = 0;
       
        if (!empty($ids[0])) {
            foreach ($ids as $val) {

                $getDetails = HHAAppointmentHelper::getDetailsById($val);
             
                $medicalId = '';
                if (isset($getDetails->medical_name) && $getDetails->medical_name != '') {
                    $getService = Master::getServiceName($getDetails->medical_name);
                    if (isset($getService->id) && $getService->id != '') {
                        $medicalId =  $getService->id;
                    } else {
                        $masters = array('name' => $getDetails->medical_name, 'master_type_fk' => 11, "types" => "Caregiver", 'del_flag' => 'N', 'user_id' => $auth['id'], 'created_at' => date('Y-m-d H:i:s'));
                        $inserty = new Master($masters);
                        $inserty->save();
                        $medicalId = $inserty->id;
                    }
                }

                
                $final_array = array(
                    'first_name' => $getDetails->caregiver_first_name,
                    'middle_name' => $getDetails->caregiver_middle_name,
                    'last_name' => $getDetails->caregiver_last_name,
                    'full_name' => $getDetails->caregiver_first_name.' '.$getDetails->caregiver_last_name,
                    'patient_code' => $getDetails->caregiver_code,
                    'agency_id' => $getDetails->agency_id,
                    'phone' => $getDetails->caregiver_phone,
                    'mobile' => $getDetails->caregiver_phone,
                    'type'=>'Caregiver',
                    'hha_id' => $getDetails->id,
                    'service_id' => $medicalId,
                    'dob' => $getDetails->dob,
                    'gender' => $getDetails->gender,
                    'link_hha_caregiver' => $getDetails->caregiver_id,
                   
                    'address1'=>$getDetails->address1,
                    'address2'=>$getDetails->address2,
                    'state'=>$getDetails->State,
                    'city'=>$getDetails->City,
                    'zip_code'=>$getDetails->Zip5,
                    'referral_type'=>'HHA Exchange'

                );
                $update = $this->patientService->save($final_array);
                if($update){
                    $patientServiceCount = $this->patientServicesRequest->getServiceCountPatientId($update);
                    if (count($patientServiceCount) == 0) {

                        if($medicalId !=""){
                            $patientServiceLastId = $this->patientServicesRequest->save([
                                'patient_id' => $update,
                                'follow_up_date' => null,
                                'due_date' => null,
                                'status' => "Pending",
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_by' => auth()->user()->id,
                                'completed_date' => null,
                                'completed_by' => null,
                                'flag'=>1
                            ]);
                            $patientWiseServiceRequest = [
                                'patient_id' => $update,
                                'service_id' => $medicalId,
                                'patient_service_request_id' => $patientServiceLastId,
                                
                            ];
                            $this->patientWiseServicesRequests->save($patientWiseServiceRequest);
                        }
                    }
                    $final_array = array(
                        'patient_id'=>$update,
                        'document_name'=>$getDetails->medical_name,
                        'hha_medical_doc_id'=>$getDetails->medical_id
                    );
                    $this->documentPatientService->save($final_array);
            }
                HHAAppointmentHelper::update(array('patient_id' => $update), array('id' => $val));
            }
            return response()->json(['error_msg' => "Appointment successfully added", 'data' => array()], 200);
        } else {
            return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
        }
    }

    public function exportCsv(Request $request){
        // ini_set('memory_limit', '-1');
        $data['agency_fk'] = $agency_fk = $request->agency_fk;
        $data['fname'] = $fname = $request->fname;
        $data['code'] = $code = $request->code;
       
        $data['medical_name'] = $medical_name = $request->medical_name;
        $data['due_date'] = $due_date = $request->due_date;
        $data['status'] = $status = $request->status;
        $data['caregiver_status'] = $caregiver_status = explode(',',$request->caregiver_status);
        $date_perform = $request->date_perform;
        $hire_date = $request->hire_date;
        $employment_type = $request->employment_type;
        $query = HHAAppointmentHelper::hhaAppoitnmentList($agency_fk, $fname, $code,$request->office_id, $medical_name, $due_date, $status,$caregiver_status,$date_perform,$hire_date,$employment_type,"export");

        $filename = 'hha_medical' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

        $columns = array( 'Agency Name','Office Name', 'Caregiver Full Name', 'Caregiver Code', 'Caregiver Phone', 'DOB', 'Caregiver Status','Hire Date','Language', 'Discipline','Employeement Type','Medical Name', 'Due Date','Medical Status', 'Appointment Status', 'First Work Date', 'Last Work Date', 'Last SYNC Date');
		
        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
			fputcsv($file, $columns);
            
            foreach ($query as $row) {
                $agencyName = "";
                if(isset($row->agencyDetails->agency_name) && $row->agencyDetails->agency_name !=""){
                    $agencyName = $row->agencyDetails->agency_name;
                }
                $caregiverFName = "";
                if(isset($row->first_name) && $row->first_name !=""){
                    $caregiverFName = $row->first_name;
                }

                $caregiverLName = "";
                if(isset($row->last_name) && $row->last_name !=""){
                    $caregiverLName = $row->last_name;
                }

                $caregiverCode = "";
                if(isset($row->caregiver_code) && $row->caregiver_code !=""){
                    $caregiverCode = $row->caregiver_code;
                }

                $mobile_or_sms = "";
                if(isset($row->mobile_or_sms) && $row->mobile_or_sms !=""){
                    $mobile_or_sms = $row->mobile_or_sms;
                }

                $dob = "";
                if(isset($row->dob) && $row->dob !=""){
                    $dob =date('m/d/Y',strtotime($row->dob));
                }

                $cStatus = "";
                if(isset($row->caregiverStatus) && $row->caregiverStatus !=""){
                    $cStatus =$row->caregiverStatus;
                }

                $clanguage = "";
                if(isset($row->language) && $row->language !=""){
                    $clanguage =$row->language;
                }

                $employmentTypesDiscipline = "";
                if(isset($row->EmploymentTypesDiscipline) && $row->EmploymentTypesDiscipline !=""){
                    $employmentTypesDiscipline =$row->EmploymentTypesDiscipline;
                }

                $dueDate = "";
                if($row->due_date !="" && $row->due_date !="0000-00-00 00:00:00"){
                    $dueDate =  date('m/d/Y',strtotime($row->due_date));
                }
                $appointmentStatus ="Pending";
                if($row->patient_id !=''){
                    $appointmentStatus ="Added";
                }

                $first_work_date = "";
                if($row->first_work_date !="" && $row->first_work_date !="0000-00-00" && $row->first_work_date !="1969-12-31"){
                    $first_work_date =date('m/d/Y',strtotime($row->first_work_date));
                }

                $last_work_date = "";
                if($row->last_work_date !="" && $row->last_work_date !="0000-00-00"  && $row->last_work_date !="1969-12-31"){
                    $last_work_date =date('m/d/Y',strtotime($row->last_work_date));
                }
                $last_updated_date = "";
                if(isset($row->updated_date) && $row->updated_date !="" && $row->updated_date !="1969-12-31 00:00:00" && $row->updated_date !="0000-00-00 00:00:00"){
                    $last_updated_date =date('m/d/Y',strtotime($row->updated_date));
                }
                $office_name = "";
                $office_code = "";
                if(isset($row->hhaOffices->office_name) && $row->hhaOffices->office_name !=""){
                    $office_name = $row->hhaOffices->office_name;
                    $office_code = $row->hhaOffices->office_code;
                }

                $employment_type = "";
                if(isset($row->employment_type) && $row->employment_type !=""){
                    $employment_type =$row->employment_type;
                }

                $hire_date = "";
                if($row->hire_date !="" && $row->hire_date !="0000-00-00" && $row->hire_date !="1969-12-31"){
                    $hire_date =  date('m/d/Y',strtotime($row->hire_date));
                }

                fputcsv($file, array($agencyName,$office_name, $caregiverFName.' '.$caregiverLName, $office_code.' - '.$caregiverCode, $mobile_or_sms, $dob, $cStatus,$hire_date, $clanguage, $employmentTypesDiscipline, $employment_type,$row->medical_name, $dueDate,$row->status, $appointmentStatus,  $first_work_date, $last_work_date,$last_updated_date));
              
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function caregiverWiseSYNCMedical(Request $request){
        $id = $request->caregiver_id;
        $getCaregiverIds =  HhaAppointment::where('del_flag','N')->where('id',$id)->get();

        if(count($getCaregiverIds) >0){
            foreach($getCaregiverIds as  $caregiver){
                $query = Agency::where('id',$caregiver->agency_id)->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->first();
                $getHHAMedicalDetails = HHACaregiversHelper::autoSYNCCaregiverMedicals($query,$caregiver->caregiver_id);

                if(count($getHHAMedicalDetails) >0){
                    foreach($getHHAMedicalDetails as $medical){
                        if($medical['caregiver_medical_id'] ==$caregiver->caregiver_medical_id){
                            $datas = [
                                'due_date' =>$medical['due_date'],
                                'status' => $medical['status'],
                                'del_flag' => 'N',
                                'updated_date' => date('Y-m-d H:i:s'),
                                'date_perform' => $medical['date_perform'],
                                'sync_status' => 1,
                                'updated_by' => auth()->user()->id,
                            ];
                            HhaAppointment::where('id',$caregiver->id)->update($datas);
                      
                        }
                    }
                }

                return response()->json(['error_msg' => "Medical records have been synced successfully.", 'data' => array()], 200);
            }
        }
        return response()->json(['error_msg' => "No records found", 'data' => array()], 404);
    }
}
