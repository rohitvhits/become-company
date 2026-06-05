<?php

namespace App\Services;

use App\Model\Patient;
use App\Model\ThirdPartyPatientMaster;
use Illuminate\Support\Facades\DB;
use App\Helpers\Utility;
use App\Agency;
use App\Services\AgencyWiseVistingClientService;
class ThirdPartyPatientMasterService
{

	protected const STATIC_START_TIME ="00:00:00";
	protected const STATIC_END_TIME ="23:59:59";
	protected const COMMON_DATE_FORMAT_YYYYMMDD_HIS="Y-m-d H:i:s";
	protected const AUTH_KEY = 'AuthKey';
	protected const AUTH_PWD = 'AuthPWD';

	public  function save($data)
	{
		$auth = auth()->user();
		$data['created_date'] = date(self::COMMON_DATE_FORMAT_YYYYMMDD_HIS);
		$data['created_by'] = $auth['id'];
		$data['deleted_flag'] = "N";

		$insert = new ThirdPartyPatientMaster($data);
		$insert->save();
		return $insert->id;
	}

	public static function hhasave($data)
	{
		$data['created_date'] = date(self::COMMON_DATE_FORMAT_YYYYMMDD_HIS);
		$data['deleted_flag'] = "N";
		$insert = new ThirdPartyPatientMaster($data);
		$insert->save();
		return $insert->id;
	}

	public  function update($data, $where)
	{
		$auth = auth()->user();
		$data['updated_date'] = date(self::COMMON_DATE_FORMAT_YYYYMMDD_HIS);
		if ($auth) {
			$data['updated_by'] = $auth['id'];
		}

		return ThirdPartyPatientMaster::where($where)->update($data);
	}

	public  function SoftDelete($data, $where)
	{
		$auth = auth()->user();
		$data['deleted_date'] = date(self::COMMON_DATE_FORMAT_YYYYMMDD_HIS);
		$data['deleted_by'] = $auth['id'];

		return ThirdPartyPatientMaster::where($where)->update($data);
	}

	public function getPatientList($data, $paginate = "")
	{

		$query = ThirdPartyPatientMaster::with(['agencyDetails', 'agencyGenerateDetails', 'patientDetails', 'serviceDetails:id,status'])->where('deleted_flag', 'N');

		if (!empty($data['patient_status'])) {
			if ($data['patient_status'] !== 'na') {
				$query->whereHas('serviceDetails', function ($ptQuery) use ($data) {
					$ptQuery->whereRaw('LOWER(status)= "'.strtolower($data['patient_status']).'"');
				});
			} else {
				$query->whereDoesntHave('serviceDetails');
			}
		}

		if (isset($data['module_type']) && $data['module_type'] != "") {
			$query->where('platform_type', $data['module_type']);
		} else {
			$query->whereRaw('(platform_type != "arla" or platform_type IS NULL)');
		}
		if (isset($data['full_name']) && $data['full_name'] != "") {
			$query->whereRaw('CONCAT(first_name," ",last_name) LIKE "%' . $data['full_name'] . '%"');
		}
		if (isset($data['dob']) && $data['dob'] != "") {
			$query->whereDate('dob', '=', date('Y-m-d', strtotime($data['dob'])));
		}
		if (isset($data['gender']) && $data['gender'] != "") {
			$query->where('gender', $data['gender']);
		}

		if (isset($data['mobile']) && $data['mobile'] != "") {
			$query->where('mobile', $data['mobile']);
		}

		if (isset($data['agency_id']) && $data['agency_id'] != "") {

			$query->where('agency_id', $data['agency_id']);
		}
		if (isset($data['portal_id']) && $data['portal_id'] != "") {
			$query->where('id', $data['portal_id']);
		}

		if (isset($data['type_id']) && $data['type_id'] != "") {

			$query->where('type', $data['type_id']);
		}

		if (isset($data['status']) && $data['status'] != "") {
			if ($data['status'] == 'Booked') {
				$query->whereNotNull('patient_id');
			} else {
				$query->whereNull('patient_id');
			}
		}
		if (isset($data['created_date']) && $data['created_date'] != "") {
			$explode = explode('-', $data['created_date']);
			$query->whereDate('created_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('created_date', '<=', date('Y-m-d', strtotime($explode[1])));
		}

		if (isset($data['due_date']) && $data['due_date'] != "") {
			$explode = explode('-', $data['due_date']);
			$query->whereDate('due_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('due_date', '<=', date('Y-m-d', strtotime($explode[1])));
		}

		if ($paginate != "") {
			$query = $query->orderBy('created_date', 'desc')->get();
		} else {
			$query = $query->orderBy('created_date', 'desc')->paginate(50);
		}

		return $query;
	}

	public function getPatientDetails($id, $agency_fk)
	{
		return ThirdPartyPatientMaster::with(['agencyDetails'])->where('deleted_flag', 'N')->where('agency_id', $agency_fk)->where('id', $id)->first();
	}

	public function patientListForUsignThirdPartyApi($agency_fk, $firstName, $lastName, $patient_code, $offset)
	{
		$query =  ThirdPartyPatientMaster::where('agency_id', $agency_fk)->where('deleted_flag', 'N');
		if ($firstName != "") {
			$query->where('first_name', 'LIKE', '%' . $firstName . '%');
		}
		if ($lastName != "") {
			$query->where('last_name', 'LIKE', '%' . $lastName . '%');
		}

		if ($patient_code != "") {
			$query->where('patient_code', 'LIKE', '%' . $patient_code . '%');
		}
		return $query = $query->orderBy('id', 'desc')->offset($offset)->limit(50)->get();
	}

	public function searchData($data)
	{

		return ThirdPartyPatientMaster::selectRaw('id,CONCAT(first_name," ",last_name) as name')->where('agency_id', $data['agency_id'])->whereRaw('(CONCAT(first_name," ",last_name) LIKE "%' . $data['q'] . '%")')->get();
	}

	public function advancedSearchThirdParty($data)
	{
		$details = $this->getAgencyWiseVisitingCredential($data['agency_id']);
	
		$finalArray = [];
		if(isset($data['first_name']) && $data['first_name'] !=""){
			$finalArray['FirstName'] = $data['first_name'];
		}
		if(isset($data['last_name']) && $data['last_name'] !=""){
			$finalArray['LastName'] = $data['last_name'];
		}
		if(isset($data['employee_code']) && $data['employee_code'] !=""){
			$finalArray['EmployeeCode'] = $data['employee_code'];
		}
		if(isset($data['dob']) && $data['dob'] !=""){
			$finalArray['DOB'] = $data['dob'];
		}
		if(isset($data['phone']) && $data['phone'] !=""){
			$finalArray['Phone'] = $data['dob'];
		}

		$headers = [
			self::AUTH_KEY.': '.$details->app_user_key,
			self::AUTH_PWD.': '.$details->app_user_password
		];
		
		foreach ($finalArray as $key => $value) {
			$headers[] = $key . ': ' . $value;
		}
	
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => env('VISITING_AIDS').'/Search',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_SSL_VERIFYPEER=>false,
		CURLOPT_SSL_VERIFYHOST=>false,

		CURLOPT_HTTPHEADER => $headers,
		));

		$response = curl_exec($curl);

		curl_close($curl);

		return json_decode($response,true);
		
	}

	public function getPatientDetailsWithoutAgencyId($id)
	{
		return ThirdPartyPatientMaster::selectRaw('*,concat(first_name," ",last_name) as full_name, "Visiting Aid" as referral_type')->where('deleted_flag', 'N')->where('id', $id)->first();
	}

	public function totalVisitingCounts()
	{
		return ThirdPartyPatientMaster::where('deleted_flag', 'N')->count();
	}

	public function totalVisitingCountsDateWise($from_date, $to_date)
	{
		$query = ThirdPartyPatientMaster::where('deleted_flag', 'N');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_date', [$from_date . ' '.self::STATIC_START_TIME, $to_date . ' '.self::STATIC_END_TIME]);
		}
		return $query->count();
	}

	public function getThirdPartyPatientReportList($data, $paginate = "")
	{

		$query = ThirdPartyPatientMaster::with(['agencyDetails', 'agencyGenerateDetails', 'patientDetails', 'serviceDetails:id,status'])->where('deleted_flag', 'N');
		if (!empty($data['patient_status'])) {
			if ($data['patient_status'] !== 'na') {
				$query->whereHas('serviceDetails', function ($ptQuery) use ($data) {
					$ptQuery->whereRaw('LOWER(status) ="' . strtolower($data['patient_status']) . '"');
				});
			} else {
				$query->whereDoesntHave('serviceDetails');
			}
		}
		
		if (isset($data['full_name']) && $data['full_name'] != "") {
			$query->whereRaw('CONCAT(first_name," ",last_name) LIKE "%' . $data['full_name'] . '%"');
		}
		if (isset($data['dob']) && $data['dob'] != "") {
			$query->whereDate('dob', '=', date('Y-m-d', strtotime($data['dob'])));
		}
		if (isset($data['gender']) && $data['gender'] != "") {
			$query->where('gender', $data['gender']);
		}

		if (isset($data['mobile']) && $data['mobile'] != "") {
			$query->where('mobile', $data['mobile']);
		}

		if (isset($data['agency_id']) && $data['agency_id'] != "") {

			$query->where('agency_id', $data['agency_id']);
		}
		if (isset($data['status']) && $data['status'] != "") {
			if ($data['status'] == 'Booked') {
				$query->whereNotNull('patient_id');
			} else {
				$query->whereNull('patient_id');
			}
		}
		if (isset($data['created_date']) && $data['created_date'] != "") {
			$explode = explode('-', $data['created_date']);
			$query->whereDate('created_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('created_date', '<=', date('Y-m-d', strtotime($explode[1])));
		}

		if (isset($data['due_date']) && $data['due_date'] != "") {
			$explode = explode('-', $data['due_date']);
			$query->whereDate('due_date', '>=', date('Y-m-d', strtotime($explode[0])))->whereDate('due_date', '<=', date('Y-m-d', strtotime($explode[1])));
		}

		if (isset($data['service_linked_status']) && $data['service_linked_status'] != "") {
			if ($data['service_linked_status'] == 1) {
				$query->whereNull('requested_service_id');
			} elseif ($data['service_linked_status'] == 2) {
				$query->whereNotNull('requested_service_id');
			}
		}

		if (isset($data['patient_linked_status']) && $data['patient_linked_status'] != "") {
			if ($data['patient_linked_status'] == 1) {
				$query->whereNull('patient_id');
			} elseif ($data['patient_linked_status'] == 2) {
				$query->whereNotNull('patient_id');
			}
		}

		if ($paginate != "") {
			$query = $query->orderBy('created_date', 'desc')->get();
		} else {
			$query = $query->orderBy('created_date', 'desc')->paginate(50);
		}

		return $query;
	}


	public function getPatientListDashboard($from_date, $to_date)
	{

		$query = ThirdPartyPatientMaster::with(['agencyDetails:id,agency_name', 'agencyGenerateDetails', 'patientDetails', 'serviceDetails:id,status'])->where('deleted_flag', 'N');

		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('third_party_patient_master.created_date', [$from_date . ' '.self::STATIC_START_TIME, $to_date . ' '.self::STATIC_END_TIME]);
		}
		$query->where('platform_type', '!=', 'arla');
		$query = $query->orderBy('created_date', 'desc')->simplepaginate(5);
		return $query;
	}

	public function getAgencyWiseChartData($from_date, $to_date)
	{
		$query = ThirdPartyPatientMaster::select(DB::raw("COUNT(id) as count"), "agency_id")->whereNotNull('agency_id')->where('deleted_flag', 'N');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('third_party_patient_master.created_date', [$from_date . ' '.self::STATIC_START_TIME, $to_date . ' '.self::STATIC_END_TIME]);
		}
		$query->where('platform_type', '!=', 'arla');
		$query = $query->groupBy('agency_id')->get();
		return $query->toArray();
	}

	public function getServiceStatusWiseChartData($from_date, $to_date)
	{
		$query = ThirdPartyPatientMaster::join('patient_service_requests', 'patient_service_requests.id', '=', 'third_party_patient_master.requested_service_id')
			->select(DB::raw("COUNT(third_party_patient_master.id) as count"), "patient_service_requests.status", "third_party_patient_master.requested_service_id")
			->where('third_party_patient_master.deleted_flag', 'N')
			->where('third_party_patient_master.platform_type', '!=', 'arla')
			->whereNotNull('patient_service_requests.status');

		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('third_party_patient_master.created_date', [$from_date . ' '.self::STATIC_START_TIME, $to_date . ' '.self::STATIC_END_TIME]);
		}
		$query = $query->groupBy("patient_service_requests.status")->get();
		return $query->toArray();
	}

	public function getTypeWiseChartData($from_date, $to_date)
	{
		$query = ThirdPartyPatientMaster::select(DB::raw("COUNT(id) as count"), "type")->whereNotNull('agency_id')->where('deleted_flag', 'N');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('third_party_patient_master.created_date', [$from_date . ' '.self::STATIC_START_TIME, $to_date . ' '.self::STATIC_END_TIME]);
		}
		$query->where('platform_type', '!=', 'arla');
		$query = $query->groupBy('type')->get();
		return $query->toArray();
	}

	public function getVisitingCountData($from_date, $to_date)
	{
		$query = ThirdPartyPatientMaster::select('id')->where('deleted_flag', 'N');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('third_party_patient_master.created_date', [$from_date . ' '.self::STATIC_START_TIME, $to_date . ' '.self::STATIC_END_TIME]);
		}
		$query->where('platform_type', '!=', 'arla');
		$query = $query->get();
		return $query->toArray();
	}

	public function getStatusWiseData($from_date, $to_date, $status)
	{
		$query = ThirdPartyPatientMaster::with(['serviceDetails'])->select('id', 'requested_service_id')->where('deleted_flag', 'N');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('third_party_patient_master.created_date', [$from_date . ' '.self::STATIC_START_TIME, $to_date  . ' '.self::STATIC_END_TIME]);
		}
		$query->where('platform_type', '!=', 'arla');
		if ($status != '' && $status != 'na') {
			$query->whereHas('serviceDetails', function ($ptQuery) use ($status) {
				$ptQuery->whereRaw('LOWER(status) ="' . $status . '"');
			});
		} else {
			$query->whereDoesntHave('serviceDetails');
		}
		$query = $query->get();
		return $query->toArray();
	}

	public function getPatientData($from_date, $to_date)
	{
		$query = ThirdPartyPatientMaster::select('id')->where('deleted_flag', 'N');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('third_party_patient_master.created_date', [$from_date . ' '.self::STATIC_START_TIME, $to_date  . ' '.self::STATIC_END_TIME]);
		}
		$query->where('platform_type', '!=', 'arla');
		$query->where('patient_id', '!=', '');
		$query = $query->get();
		return $query->toArray();
	}

	public function getOverdueData($from_date, $to_date)
	{
		$query = ThirdPartyPatientMaster::select('id')->where('deleted_flag', 'N');
		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('third_party_patient_master.created_date', [$from_date . ' '.self::STATIC_START_TIME, $to_date  . ' '.self::STATIC_END_TIME]);
		}
		$query->where('platform_type', '!=', 'arla');
		$query->where('due_date', '<=', date(self::COMMON_DATE_FORMAT_YYYYMMDD_HIS));
		$query = $query->get();
		return $query->toArray();
	}

	public function getDetailsByServiceRequestedId($serviceRequestedId, $agency_fk)
	{
		return ThirdPartyPatientMaster::with(['agencyDetails'])->where('deleted_flag', 'N')->where('agency_id', $agency_fk)->where('requested_service_id', $serviceRequestedId)->get();
	}

	public function sendArlaCurl($data)
	{
		$url = 'https://nybest.api.arla.ai/app/getAppointmentFileNyBest/' . $data['client_name'] . '?appointmentId=' . $data['appointment_id'] . '&documentId=' . $data['document_id'] . '&date=' . $data['date'];


		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer ' . env('ARLA_SECRET_KEY'),
				'Accept: application/json'
			),
		));
		$response = curl_exec($curl);
		info($response);
		if (curl_errno($curl)) {
			$error_msg = curl_error($curl);
			echo response()->json(['status' => false, 'error_msg' => $error_msg], 500);
			die();
		}

		curl_close($curl);
		return json_decode($response, true);
	}


	public function sendArlaNotesCurl($data)
	{
		$record = ThirdPartyPatientMaster::where('id', $data['appointment_id'])->first();
		 $url = env('SEND_ARLA_NOTES') . '/' . $data['client_name'] . '?platformId=' . $record->platform_id;


		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'PUT',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer ' . env('ARLA_SECRET_KEY'),
				'Content-Type: application/json'

			),
			CURLOPT_POSTFIELDS => json_encode(array('notes' => $data['notes'], 'date' => $data['created_date']))

		));
		
		 $response = curl_exec($curl);
		if (curl_errno($curl)) {
			$error_msg = curl_error($curl);
			echo response()->json(['status' => false, 'error_msg' => $error_msg], 500);
			die();
		}
		curl_close($curl);

		return json_decode($response, true);
	}

	public function getTPUrlByAgencyAndPortal($patientId,$agencyId){
		return ThirdPartyPatientMaster::select('third_party_patient_master.id','third_party_patient_master.service_id','third_party_patient_master.requested_service_id','patient_service_requests.status as patientServiceRequestStatus','third_party_patient_master.created_date')
			->leftjoin('patient_service_requests',function($join){
				$join->on('patient_service_requests.id','=','third_party_patient_master.requested_service_id');
			})->where('patient_service_requests.del_flag','N')->where('third_party_patient_master.deleted_flag','N')->whereNotNull('third_party_patient_master.third_party_callback_url')->where('third_party_patient_master.patient_id',$patientId)->where('third_party_patient_master.agency_id',$agencyId)->get();
	}

	public function getDetailsByIdAndPatientId($id,$patientId){
		return ThirdPartyPatientMaster::where('deleted_flag','N')->whereNotNull('third_party_callback_url')->where('patient_id',$patientId)->where('id',$id)->first();
	}

	public function getThirdPartyPatientList($patient_id)
	{
		$query = ThirdPartyPatientMaster::with(['agencyDetails', 'patientDetails', 'serviceDetails:id,status'])->where('deleted_flag', 'N');
		if (!empty($data['patient_status'])) {
			if ($data['patient_status'] !== 'na') {
				$query->whereHas('serviceDetails', function ($ptQuery) use ($data) {
					$ptQuery->whereRaw('LOWER(status)= "'.strtolower($data['patient_status']).'"');
				});
			} else {
				$query->whereDoesntHave('serviceDetails');
			}
		}
		$query->whereRaw('(platform_type != "arla" or platform_type IS NULL)');
		$query = $query->where('patient_id',$patient_id)->orderBy('created_date', 'desc')->get();
		return $query;
	}

	protected function getAgencyWiseVisitingCredential($agencyId){
		$agencyClientService = new AgencyWiseVistingClientService();
		return $agencyClientService->getDetailsByAgencyIdEnabledStatus($agencyId);
	}

	public function getAllPendingMedicalList($agency_id,$empCode)
	{
		$details = $this->getAgencyWiseVisitingCredential($agency_id);
	
		$headers = [
			self::AUTH_KEY.': '.$details->app_user_key,
			self::AUTH_PWD.': '.$details->app_user_password,
			'EmployeeCode:'.$empCode
		];
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => env('VISITING_AIDS').'/GetPendingMedicals',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_SSL_VERIFYPEER=>false,
			CURLOPT_SSL_VERIFYHOST=>false,

			CURLOPT_HTTPHEADER => $headers,
		));

		$response = curl_exec($curl);

		curl_close($curl);

		return json_decode($response,true);
		
	}

	public function getMedicalResultByMedicalId($data)
	{
	
		$details = $this->getAgencyWiseVisitingCredential($data['agencyId']);
	
		$headers = [
			self::AUTH_KEY.': '.$details->app_user_key,
			self::AUTH_PWD.': '.$details->app_user_password,
			'MedicalID:'.$data['medicaid_id']
		];
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => env('VISITING_AIDS').'/GetMedicalLookup',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_SSL_VERIFYPEER=>false,
			CURLOPT_SSL_VERIFYHOST=>false,
			CURLOPT_HTTPHEADER => $headers,
		));

		$response = curl_exec($curl);

		curl_close($curl);

		return json_decode($response,true);
		
	}
	
	public function updateMedical($data)
	{
	
		$details = $this->getAgencyWiseVisitingCredential($data['agencyId']);
		
		$payload = [
			"medical_ref_id"    => $data['medical_ref_id'],
			"medical_date"      => $data['medical_date'],
			"medical_result_id" => $data['medical_result_id'],
			"notes"             => $data['notes'],
			"medical_id"        => $data['medical_id'],
		];
		
		$headers = [
			self::AUTH_KEY.': '.$details->app_user_key,
			self::AUTH_PWD.': '.$details->app_user_password,
			'EmployeeCode: ' . $data['emp_code'],
			'Content-Type: application/json',
			'Accept: application/json'
		];
		
		$curl = curl_init();
		
		curl_setopt_array($curl, [
			CURLOPT_URL => env('VISITING_AIDS') . '/UpdateMedical',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_POSTFIELDS => json_encode($payload),
			CURLOPT_HTTPHEADER => $headers,
		]);
		
		$response = curl_exec($curl);
		
		if (curl_errno($curl)) {
			$error = curl_error($curl);
			curl_close($curl);
			return ['error' => $error];
		}
		
		$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		curl_close($curl);
		
		return [
			'status' => $statusCode,
			'data'   => json_decode($response, true)
		];
		
	}

	public function updateMedicalDocument($data)
	{
	
		$details = $this->getAgencyWiseVisitingCredential($data['agencyId']);
	
		$headers = [
			self::AUTH_KEY.': '.$details->app_user_key,
			self::AUTH_PWD.': '.$details->app_user_password,
			'EmployeeCode: ' . $data['emp_code'],
			'MedicalRefID: ' . $data['MedicalRefID'],
		];

		$postFields = [
			'file' => new \CURLFile($data['file'])
		];

		$curl = curl_init();
		
		curl_setopt_array($curl, [
			CURLOPT_URL => env('VISITING_AIDS') . '/UpdateMedicalDocument',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_POSTFIELDS =>$postFields,
			CURLOPT_HTTPHEADER => $headers,
		]);
		
		$response = curl_exec($curl);
		
		if (curl_errno($curl)) {
			$error = curl_error($curl);
			curl_close($curl);
			return ['error' => $error];
		}
		
		$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		curl_close($curl);
		
		return [
			'status' => $statusCode,
			'data'   => json_decode($response, true)
		];
		
	}

	public function getPendingMedicalList($agency_id,$dueMedical){
		$details = $this->getAgencyWiseVisitingCredential($agency_id);
		$headers = [
			self::AUTH_KEY.': '.$details->app_user_key,
			self::AUTH_PWD.': '.$details->app_user_password
		];

		if(!empty($dueMedical)){
			$explode = explode('-',$dueMedical);
			$headers[] = 'MedicalDueFrom: ' . $explode[0];
			$headers[] = 'MedicalDueTo: ' . $explode[1];
		}
		
		$apiUrl = env('VISITING_AIDS') . '/GetPendingMedicalsByAgency';

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $apiUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_SSL_VERIFYPEER=>false,
			CURLOPT_SSL_VERIFYHOST=>false,

			CURLOPT_HTTPHEADER => $headers,
		));

		$response = curl_exec($curl);

		curl_close($curl);

		return json_decode($response,true);
	}
}
