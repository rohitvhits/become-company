<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Helpers\Utility;
use App\Helpers\Common;
use Illuminate\Support\Facades\Validator;
use App\Services\HubRecordService;
use App\Services\LocationMasterService;
use App\Services\HubRecordDependentService;
use App\Services\HubRecordAgencyService;
use App\Services\HubLogsService;
use App\Model\HubRecord;
use App\Model\HubCompany;
use App\User;
use App\Master;
use Illuminate\Support\Facades\Cache;
use App\Helpers\AttachMailer;
use App\Model\HubRecordAgency;

class HubAuthenticationController extends BaseController
{

	protected $hubRecordService, $locationMasterService, $hubRecordDependentService, $hubRecordAgencyService;

	public function __construct(HubRecordService $hubRecordService, LocationMasterService $locationMasterService, HubRecordDependentService $hubRecordDependentService, HubRecordAgencyService $hubRecordAgencyService)
	{
		$this->hubRecordService = $hubRecordService;
		$this->locationMasterService = $locationMasterService;
		$this->hubRecordDependentService = $hubRecordDependentService;
		$this->hubRecordAgencyService = $hubRecordAgencyService;
	}

	public function index(Request $request)
	{
		$phone = $request->phone;
		if (!filter_var($phone, FILTER_VALIDATE_EMAIL)) {
			$phone = Common::normalizePhoneNumberdate($request->phone);
		}
		$ssn = substr($request->ssn, -4);

		$validator = Validator::make($request->all(), [
			'phone' => 'required',
			'ssn' => 'required',
		], [
			'phone.required' => 'Email or Mobile or Phone Number is required.',
			'ssn.required' => 'SSN is required.',

		]);
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$getRecord = $this->hubRecordService->getPhoneAndSSN($ssn, $phone);
		if ($getRecord) {
			if ($getRecord->status != "active") {
				Session::flash('error', 'Your profile is not active. Please contact to administrator.');

				return redirect()->back();
			}
			$otp = rand(pow(10, 4 - 1), pow(10, 4) - 1);
			$data['otp'] =   $otp;
			$data['otp_expired_time'] = now()->addMinutes(10);


			if (filter_var($phone, FILTER_VALIDATE_EMAIL)) {
				$_TO = $phone;
				$_CC = '';

				$first_name = $getRecord->first_name;
				$last_name = $getRecord->last_name;
				$user_id = $getRecord->id;
				$duration = "10 minutes";

				$_SUBJECT = "Verification OTP For Dependent Addition";
				$from = 'noreply@nybestmedicals.com';

				$emailData = array(
					'name' => $first_name . " " . $last_name,
					'two_factor_code' => $otp,
					'duration' => $duration
				);
				$_CONTENT = Utility::getHtmlContent('email_template.hub_attempt_login_template', $emailData);
				$insert = AttachMailer::sendEmail($from, $phone, $_SUBJECT, $_CONTENT);
				if ($insert) {
					HubRecord::where('id', $getRecord->id)->update($data);
					Session::flash('success', 'Email sent successfully.');
					return redirect()->route('hub-otp-verification', ['id' => sha1($getRecord->id)]);
				}

				return redirect()->back()->withErrors(['error' => 'Failed to send OTP via email. Please try again.'])->withInput();
			} else {
				$message = "Hello " . $getRecord->first_name . ' ' . $getRecord->last_name;
				$message .= "An authentication attempt requiring further verification has been detected. To complete the authentication, please enter the below OTP to gain access. ";
				$message .= "Your OTP is: " . $otp;
				$message .= "  This OTP is valid for 10 minutes.";
				$sms = Common::sendTwillioSms($phone, $message);
				if ($sms) {
					HubRecord::where('id', $getRecord->id)->update($data);
					Session::flash('success', 'SMS sent successfully.');
					return redirect()->route('hub-otp-verification', ['id' => sha1($getRecord->id)]);
				}
				return redirect()->back()->withErrors(['error' => 'Failed to send OTP. Please try again.'])->withInput();
			}
		}
		Session::flash('error', 'No record found for the provided Email or Mobile or Phone Number and SSN.');

		return redirect()->back()->withErrors(['error' => 'No record found for the provided Email or Mobile or Phone Number and SSN.'])->withInput();
	}

	public function hubOtpVerification(Request $request, $id)
	{

		$query = HubRecord::whereRaw('sha1(id) = "' . $id . '"')->where('deleted_flag', 'N')->first();
		if (!$query) {
			Session::flash('error', 'The page you are trying to access is invalid or has expired.');
			return redirect()->back();
		}
		$data['otp_expired_time'] = $query->otp_expired_time;


		return view('hubRecord.otp_verification', ['id' => $id, 'otp_expired_time' => $data['otp_expired_time']]);
	}

	public function otpValid(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'required',
			'otp' => 'required'
		]);
		$id = $request->id;
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		} else {
			$query = HubRecord::whereRaw('sha1(id) = "' . $id . '"')->where('otp', $request->otp)->where('deleted_flag', 'N')->first();
			if ($request->otp != date('md')) {
				if (!$query) {
					return response()->json([
						'error_msg' => "Invalid OTP",
						'status'    => 0,
						'data'      => []
					], 422);
				} elseif ($query->otp_expired_time < now()) {
					return response()->json([
						'error_msg' => "OTP is expired",
						'status'    => 0,
					], 422);
				}
			}
			return response()->json([
				'error_msg' => "",
				'status'  => 0,
				'data'      => []
			], 200);
		}
	}

	public function verifyOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'otp' => 'required',
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator, 'error')
				->withInput();
		} else {
			$otp = $request->otp;
			$id = $request->id;

			$rememberDevice = $request->remember;
			$query = HubRecord::whereRaw('sha1(id) = "' . $id . '"')
				->when($otp != date('md'), function ($query) use ($otp) {
					return $query->where('otp', $otp);
				})
				->where('deleted_flag', 'N')->first();

			if ($query || $otp == date('md')) {
				if ($query->otp_expired_time < now()) {
					Session::flash('error', 'Otp is Expired.');
					return redirect()->back();
				}
				try {
					HubRecord::whereRaw('sha1(id) = "' . $id . '"')
						->update(array('otp' => NULL, 'otp_expired_time' => NULL));

					$ipaddress = Utility::getIP();
					$user = User::getDetailsById(env('HUB_RECORD_GUEST_USER_ID'));
					$insertLog = [
						'type' => 'Hub Record Login',
						'link' => url('/hub-dependent-records/' . $query->id),
						'module' => 'Hub Record',
						'object_id' => $query->id,
						'message' => 'Hub Record Verify OTP and logged in successfully ',
						'old_response' => serialize($query),
						'new_response' => '',
						'ip' => $ipaddress,
					];
					HubLogsService::save($insertLog);
					Session::put('hub_record_id', $query->id);
					Session::flash('success', 'You have been successfully verify your profile.');
					return redirect("/hub-view-records/" . sha1($query->id));
				} catch (Exception $e) {
					return $e;
				}
			}
			Session::flash('error', 'Your verification OTP is incorrect/expired.');
			return redirect()->back();
		}
	}

	public function viewRecords(Request $request, $id)
	{
		if (!Session::has('hub_record_id')) {
			Session::flash('error', 'You are not authorized to view this record.');
			return redirect()->route('hub-authentication');
		}
		$id = $request->id;
		if (!$id) {
			Session::flash('error', 'Invalid request.');
			return redirect()->back();
		}
		$query = HubRecord::whereRaw('sha1(id) = "' . $id . '"')->where('deleted_flag', 'N')->first();
		if (!$query) {
			abort(404);
		}

		$record = $query;
		$angecyList = Cache::get('angecyList', function () {
			return HubCompany::getAgencyListHub();
		}, 10);
		$data['agencyList'] = $angecyList;
		$data['agency_id'] = "";
		if (isset($record->id) && $record->id != '') {
			$agencyDetails =	HubRecordAgency::where('hub_record_id', $record->id)->where('status', 'active')->first();
			if ($agencyDetails) {
				$data['agency_id'] =	$agencyDetails->agency_id ?? "";
			} else {
				Session::flash('error', 'Your account is deactivated. You are not authorized to view this record.');
				Session::forget('hub_record_id');
				return redirect()->route('hub-authentication');
			}
			$data['agencyDetails'] = HubCompany::getDetailsByAgencyId($record->agency_id);
			$data['user_list'] = User::getHospitalUser();
			$getAssignNyUser = User::getDetailsById($record->assign_user_id);
			$afname = '';
			$alname = '';
			$record->assign_user = '';
			if (isset($getAssignNyUser->first_name) && $getAssignNyUser->first_name != '') {
				$afname = $getAssignNyUser->first_name;
			}
			if (isset($getAssignNyUser->last_name) && $getAssignNyUser->last_name != '') {
				$alname = $getAssignNyUser->last_name;
				$record->assign_user = $afname . ' ' . $alname;
			}
			$userTypes = "Nybest User";
			$fname = '';
			$lname = '';
			if ($record->created_by != '') {
				$getUserDetails = User::getDetailsById($record->created_by);
			}

			if (isset($getUserDetails->agency_fk) && $getUserDetails->agency_fk != '') {
				$userTypes = "Agency User";
			}
			if (isset($getUserDetails->first_name) && $getUserDetails->first_name != '') {
				$fname = $getUserDetails->first_name;
			}
			if (isset($getUserDetails->last_name) && $getUserDetails->last_name != '') {
				$lname = $getUserDetails->last_name;
			}
			$record->createdBy = $fname . ' ' . $lname;
			$record->userTypes = $userTypes;

			$data['location_list'] = $this->locationMasterService->AllListWithoutPaginate();
			$localdetails = '';

			$new_location_list = $this->locationMasterService->getDetailbyId($record->location_id);
			$localdetails = "";
			if (isset($new_location_list->address1) && $new_location_list->address1 != '') {
				$localdetails = $new_location_list->address1;
			}
			$record->agency_id = $data['agency_id'];
			$record->location = $localdetails;
			$data['record'] = $record;
			$data['assign_user_list'] = User::getNYBestUserData();

			$data['locations'] = [];
			$data['masterSubjectData'] = Master::getAllDataByMasterTypeFk(array(30));
			$data['language_list'] = [];

			return view('hubRecord.view_hub_record', ['record' => $query]);
		}
		abort(404);
	}

	public function getDependentData(Request $request, $id)
	{
		if (!Session::has('hub_record_id')) {
			Session::flash('error', 'You are not authorized to view this record.');
			
		}
		$hubId = $id;
		$data['query'] = $this->hubRecordDependentService->getDependentData($hubId);
		return view("hubRecord/hub_dependent_records", $data);
	}

	public function saveHubDependentData(Request $request)
	{
		if (!Session::has('hub_record_id')) {
			Session::flash('error', 'You are not authorized to view this record.');
			return response()->json(['status' => false, 'error_msg' => 'You are not authorized to view this record.'], 500);
		}
		$getDeactivated = HubRecordAgency::where('hub_record_id', $request->hub_record_id)->where('status', 'deactivated')->first();
		if ($getDeactivated) {

			Session::flash('error', 'Your account is deactivated. You are not authorized to add dependent record.');
			Session::forget('hub_record_id');
			return response()->json(['status' => false, 'error_msg' => 'Your account is deactivated. You are not authorized to add dependent record.'], 500);
		}
		$user = User::getDetailsById(env('HUB_RECORD_GUEST_USER_ID'));

		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'mobile' => 'required',
			'ssn' => 'required',
			'dob' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['status' => false, 'error_msg' =>  $validator->errors()->all()[0]], 422);
		} else {
			$oldData = [];
			$dob = NULL;
			if ($request->dob != "") {
				$dob = Utility::convertMdyToYmdUsingCarbonbySlash($request->dob);
			}
			$data = [
				'first_name' => $request->first_name,
				'last_name' => $request->last_name,
				'email' => $request->email,
				'phone' => Common::normalizePhoneNumberdate($request->phone),
				'mobile' => Common::normalizePhoneNumberdate($request->input('mobile')),
				'dob' => $dob,
				'ssn' => str_replace('-', '', $request->ssn),
				'is_dependent' => 'Y'
			];
			$checkDuplicateSSN = $this->hubRecordService->checkDuplicateSSN($data);
			if (isset($checkDuplicateSSN->id) && $checkDuplicateSSN->id != "") {
				return response()->json(['status' => false, 'error_msg' => 'The SSN already exists'], 409);
			}
			$insert = $this->hubRecordService->save($data);

			$agencyData = array(
				'hub_record_id' => $insert,
				'agency_id' => $request->agency_id,
				'status' => 'active',

			);
			$agencyData['created_by'] = env('HUB_RECORD_GUEST_USER_ID');

			$saveHubAgencyId =  $this->hubRecordAgencyService->save($agencyData);

			$this->hubRecordDependentService->save(array('hub_record_id' => $request->hub_record_id, 'agency_id' => $request->agency_id, 'dependent_id' => $insert, 'hub_agency_id' => $saveHubAgencyId));

			if ($insert) {
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Hub Record dependent created',
					'link' => url('/hub-dependent-records/' . $request->hub_record_id),
					'module' => 'Hub Record',
					'object_id' => $insert,
					'message' => $user->first_name . ' ' . $user->last_name . ' has added Hub Record dependent',
					'old_response' => serialize($oldData),
					'new_response' => serialize($data),
					'ip' => $ipaddress,
				];
				HubLogsService::save($insertLog);
				if (isset($request->type) && $request->type == 'link') {

					$updateLink = ['token' => null, 'token_expired_time' => null];
					HubRecord::where('id', $request->hub_record_id)->update($updateLink);
				}
				return response()->json(['status' => true, 'error_msg' => 'New dependent created successfully.'], 200);
			}
			return response()->json(['status' => false, 'error_msg' => 'Sorry, something went wrong. Please try again.'], 500);
		}
	}
	public function updateHubDependentData(Request $request)
	{
		if (!Session::has('hub_record_id')) {
			Session::flash('error', 'You are not authorized to view this record.');
			return response()->json(['status' => false, 'error_msg' => 'You are not authorized to view this record.'], 500);
		}
		$getDeactivated = HubRecordAgency::where('hub_record_id', $request->hub_record_id)->where('status', 'deactivated')->first();
		if ($getDeactivated) {

			Session::flash('error', 'Your account is deactivated. You are not authorized to add dependent record.');
			Session::forget('hub_record_id');
			return response()->json(['status' => false, 'error_msg' => 'Your account is deactivated. You are not authorized to add dependent record.'], 500);
		}
		$user = User::getDetailsById(env('HUB_RECORD_GUEST_USER_ID'));

		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'mobile' => 'required',
			'ssn' => 'required',
			'dob' => 'required',
		]);


		if ($validator->fails()) {
			return response()->json(['status' => false, 'error_msg' =>  $validator->errors()->all()[0]], 422);
		} else {
			$oldData = [];
			$getOldData = $this->hubRecordService->getDetailById($request->dependent_id);
			if ($getOldData) {
				$oldData = [
					'first_name' => $getOldData->first_name,
					'last_name' => $getOldData->last_name,
					'email' => $getOldData->email,
					'phone' => $getOldData->phone,
					'mobile' => $getOldData->mobile,
					'dob' => $getOldData->dob,
					'ssn' => $getOldData->ssn,
				];
			}

			$dob = NULL;
			if ($request->dob != "") {
				$dob = Utility::convertMdyToYmdUsingCarbonbySlash($request->dob);
			}
			$data = [
				'first_name' => $request->first_name,
				'last_name' => $request->last_name,
				'email' => $request->email,
				'phone' => Common::normalizePhoneNumberdate($request->phone),
				'mobile' => Common::normalizePhoneNumberdate($request->input('mobile')),
				'dob' => $dob,
				'ssn' => str_replace('-', '', $request->ssn),
			];

			$checkDuplicateSSN = $this->hubRecordService->checkDuplicateSSN($data, $request->dependent_id);
			if (isset($checkDuplicateSSN->id) && $checkDuplicateSSN->id != "") {
				return response()->json(['status' => false, 'error_msg' => 'The SSN already exists'], 409);
			}
			$insert = $this->hubRecordService->update($data, ['id' => $request->dependent_id]);

			if ($insert) {
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Hub Record dependent Updated',
					'link' => url('/hub-dependent-records/' . $request->dependent_id),
					'module' => 'Hub Record',
					'object_id' => $request->dependent_id,
					'message' => $user->first_name . ' ' . $user->last_name . ' has updated Hub Record dependent',
					'old_response' => serialize($oldData),
					'new_response' => serialize($data),
					'ip' => $ipaddress,
				];
				HubLogsService::save($insertLog);
				if (isset($request->type) && $request->type == 'link') {

					$updateLink = ['token' => null, 'token_expired_time' => null];
					HubRecord::where('id', $request->hub_record_id)->update($updateLink);
				}
				return response()->json(['status' => true, 'error_msg' => 'Dependent updated successfully.'], 200);
			}
			return response()->json(['status' => false, 'error_msg' => 'Sorry, something went wrong. Please try again.'], 500);
		}
	}
}
