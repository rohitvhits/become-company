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

use App\Agency;
use App\Rates;
use App\User;

use App\Record;
use App\RecordNotes;
use App\Master;
use App\GenerateAgencyToken;
use App\Invoice;
use Excel;
use App\Helpers\UserHelper;
use App\Services\PatientService;
use App\Services\DoctorService;
use App\Services\LocationMasterService;

class PendingAppointmentController extends BaseController
{

    public function __construct(PatientService $PatientService, DoctorService $DoctorService, LocationMasterService $LocationMasterService)
    {
        $this->middleware('permission:appointments-pending', ['only' => ['index']]);
        
        $this->middleware('auth');
        $this->PatientService = $PatientService;
        $this->DoctorService = $DoctorService;
        $this->LocationMasterService = $LocationMasterService;
    }

    public function index(Request $request)
    {
        $data['user'] = $user = auth()->user();
        $data['listHeadingName'] = "Pending Appointments";
        $data['appointmentUrl'] = "pending-appoinment";
        
        $data['agencyList'] = Agency::where('delete_flag', 'N')->where('service_md_appointment', 1)->orderBy('agency_name', 'asc')->get();
        $agency_fk = $data['agency_fk'] = request('agency_fk');
		$full_name = $data['full_name'] = request('first_name');
		$mobile = $data['mobile'] = request('mobile');
		// $age = $data['age'] = request('age');
		$status = $data['status'] = request('status');
		//$doctor_id = $data['doctor_id'] = request('doctor_id');
		$appointment_date = $data['appointment_date'] = request('appointment_date');
		$location_id = $data['location_id'] = request('locationId');
		$service_id = $data['service_id'] = request('service_id');
		$type = $data['type'] = request('type');
		$created_date = $data['created_date'] = request('created_date');
		$due_date = $data['due_date'] = request('due_date');
		$sms_status = $data['sms_status'] = request('sms_status');
		
		$record_form = $data['record_form'] = request('record_form');
		$assign_user_id = $data['assign_user_id'] = request('assign_user_id');
		
		//get
		$data['selected_sms_status'] = request('sms_status')!=null ? explode(',', request('sms_status')) : [];
		$data['selected_status'] = explode(',', request('status'));
		$data['selected_agency_fk'] = explode(',', request('agency_fk'));
		$data['selected_service_id'] = explode(',', request('service_id'));
		$data['selected_assign_user_id'] = explode(',', request('assign_user_id'));
		$data['selected_location_id'] = explode(',', request('locationId'));

        $open_record_list = $this->PatientService->AllPatientListWithPaginateSearch("", $full_name, "", $mobile, $status, "", $appointment_date, $agency_fk, $location_id, $service_id, $type, $created_date, $sms_status, $record_form, $due_date, $assign_user_id);

        foreach ($open_record_list as $vsl) {
            $newss = "" . $vsl->service_id;
            if ($newss != '') {
                $sins = Master::select('name')->whereRaw('id  IN (' . $newss . ')')->where('del_flag', 'N')->get();

                $nrens = array();
                foreach ($sins as $names) {
                    $nrens[$vsl->id][] = $names->name;
                }
            }
            $vsl->name = '';
            if (isset($nrens[$vsl->id]) && $nrens[$vsl->id] != '') {
                $vsl->name = implode(',', $nrens[$vsl->id]);
            }
        }
        $data['open_record_list'] = $open_record_list;

        $data['location_list'] = $this->LocationMasterService->AllListWithoutPaginate();
        $data['serviceList'] = Master::getServiceRequest();
        $data['assign_user_list'] = User::getNYBestUserData();
        return view('PendingAppointment.pending_appoinment_list', $data);


    }

    public function ajaxList_old(Request $request)
    {
        $data['user'] = $user = auth()->user();
        $data['agency_list'] = Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
        $data['doctor_list'] = $this->DoctorService->getDoctorList();
        $data['location_list'] = $this->LocationMasterService->AllListWithoutPaginate();
        $data['serviceList'] = Master::getServiceRequest();

        $data['agency_name'] = $agency_name = $request->input('agency_name');
        $data['doctor_id'] = $doctor_id = $request->input('doctor_id');
        $data['type'] = $type = $request->input('type');
        $data['full_name'] = $full_name = $request->input('full_name');
        $data['phone_no'] = $phone_no = $request->input('phone_no');
        $data['dob'] = $dob = $request->input('dob');
        $data['appoinment_date'] = $appoinment_date = $request->input('appoinment_date');
        $data['status_id'] = $status_id = $request->input('status_id');
        $data['datepickernn'] = $datepickernn = $request->input('datepickernn');

        $data['service_id'] = $service_id = $request->input('service_id');
        $data['locationId'] = $locationId = $request->input('locationId');
        $assign_user_id = $data['assign_user_id'] = request('assign_user_id');
        $sms_status = $data['sms_status'] = request('sms_status');
        $status = $data['status'] = request('status');
        $agency_fk = $data['agency_fk'] = request('agency_fk');
        $mobile = $data['mobile'] = request('mobile');
        $due_date = $data['due_date'] = request('due_date');
        $appointment_date = $data['appointment_date'] = request('appointment_date');
        $created_date = $data['created_date'] = request('created_date');
        $record_form = $data['record_form'] = request('record_form');

        $data['selected_sms_status'] = request('sms_status')!=null ? explode(',', request('sms_status')) : [];
		$data['selected_status'] = explode(',', request('status'));
		$data['selected_agency_fk'] = explode(',', request('agency_fk'));
		$data['selected_service_id'] = explode(',', request('service_id'));
		$data['selected_assign_user_id'] = explode(',', request('assign_user_id'));
		$data['selected_location_id'] = explode(',', request('locationId'));

        $open_record_list = $this->PatientService->AllPatientListWithPaginateSearch("", $full_name, "", $mobile, $status, "", $appointment_date, $agency_fk, $locationId, $service_id, $type, $created_date, $sms_status, $record_form, $due_date, $assign_user_id);

        foreach ($open_record_list as $vsl) {
            $newss = "" . $vsl->service_id;
            if ($newss != '') {
                $sins = Master::select('name')->whereRaw('id  IN (' . $newss . ')')->where('del_flag', 'N')->get();

                $nrens = array();
                foreach ($sins as $names) {
                    $nrens[$vsl->id][] = $names->name;
                }
            }
            $vsl->name = '';
            if (isset($nrens[$vsl->id]) && $nrens[$vsl->id] != '') {
                $vsl->name = implode(',', $nrens[$vsl->id]);
            }
        }
        $data['open_record_list'] = $open_record_list;
        $data['agencyList'] = Agency::where('delete_flag', 'N')->where('service_md_appointment', 1)->orderBy('agency_name', 'asc')->get();
        $data['assign_user_list'] = User::getNYBestUserData();
        return view('PendingAppointment.pending_appoinment_ajax_list', $data);
    }
}
