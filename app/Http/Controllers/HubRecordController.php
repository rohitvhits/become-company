<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Helpers\Utility;
use App\Model\HubCompany;
use Illuminate\Support\Facades\Validator;
use App\Master;
use App\Model\Language;
use App\User;
use App\Services\HubLogsService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Services\HubRecordService;
use App\Services\LocationMasterService;
use App\Services\HubRecordNotesService;
use App\Services\HubRecordDocService;
use App\Services\HubRecordtextMessageService;
use App\Services\HubRecordImportLogService;
use App\Helpers\Common;
use App\Model\HubRecord;
use App\Services\HubRecordAgencyService;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use App\Services\HubRecordDependentService;
use App\Services\CreateAppointmentService;
use App\Services\SiteSettingServices;
use App\Services\HubCompanyService;
use Illuminate\Support\Facades\Mail;
use App\Agency;
use App\Model\HubRecordAgency;
use App\PDF;
use App\Jobs\ProcessHubRecordImport;

class HubRecordController extends BaseController
{

	protected $hubRecordService, $locationMasterService, $hubRecordNotesService, $hubRecordDocService, $hubRecordtextMessageService, $hubRecordImportLogService, $hubRecordAgencyService, $hubRecordDependentService, $createAppointmentService, $siteSettingService, $hubCompanyService;

	public function __construct(HubRecordService $hubRecordService, LocationMasterService $locationMasterService, HubRecordNotesService $hubRecordNotesService, HubRecordDocService $hubRecordDocService, HubRecordtextMessageService $hubRecordtextMessageService, HubRecordImportLogService $hubRecordImportLogService, HubRecordAgencyService $hubRecordAgencyService, HubRecordDependentService $hubRecordDependentService, CreateAppointmentService $createAppointmentService, SiteSettingServices $siteSettingService, HubCompanyService $hubCompanyService)
	{
		$this->middleware('permission:hub-list', ['only' => ['index', 'ajaxList', 'save']]);
		$this->hubRecordService = $hubRecordService;
		$this->locationMasterService = $locationMasterService;
		$this->hubRecordNotesService = $hubRecordNotesService;
		$this->hubRecordDocService = $hubRecordDocService;
		$this->hubRecordtextMessageService = $hubRecordtextMessageService;
		$this->hubRecordImportLogService = $hubRecordImportLogService;
		$this->hubRecordAgencyService = $hubRecordAgencyService;
		$this->hubRecordDependentService = $hubRecordDependentService;
		$this->createAppointmentService = $createAppointmentService;
		$this->siteSettingService = $siteSettingService;
		$this->hubCompanyService = $hubCompanyService;
	}

	public function index(Request $request)
	{
		if (auth()->user()->agency_fk == "" && auth()->user()->show_hub == 1) {
			$angecyList = Cache::get('patient_master_locations', function () {
				return HubCompany::getAgencyListHub();
			}, 10);
			$data['agencyList'] = $angecyList;
			$data['auth'] = auth()->user();
			return view("hubRecord/hub_list", $data);
		} else {
			abort(404);
		}
	}

	public function ajaxList(Request $request)
	{
		$search_data = $request->all();
		$search_data['agency_fk'] = request('agency_fk') ?? Auth()->user()->agency_fk;
		$search_data['is_dependent'] = "N";
		$data['query'] = $this->hubRecordService->getHubData($search_data);
		return view("hubRecord/hub_ajax_list", $data);
	}

	public function save(Request $request)
	{
		$user = auth()->user();
		if ($user->view_ssn_hub == 1) {
			$validator = Validator::make($request->all(), [
				'first_name' => 'required',
				'last_name' => 'required',
				'mobile' => 'required',
				'ssn' => 'required'
			]);
		} else {
			$validator = Validator::make($request->all(), [
				'first_name' => 'required',
				'last_name' => 'required',
				'mobile' => 'required',

			]);
		}

		if ($validator->fails()) {
			return response()->json(['status' => false, 'error_msg' =>  $validator->errors()->all()[0]], 422);
		} else {
			$finals = $request->all();
			$finals['ssn'] = str_replace('-', '', $request->ssn);
			$checkDuplicateSSN = $this->hubRecordService->checkDuplicateSSN($finals);
			if (isset($checkDuplicateSSN->id)) {
				return response()->json(['status' => false, 'error_msg' => 'The SSN already exists'], 409);
			}
			$first_name = request('first_name');
			$middle_name = request('middle_name');
			$last_name = request('last_name');
			$age = '';
			if (request('dob') != '') {
				$age = date('Y-m-d', strtotime(request('dob')));
			}
			$phone = request('phone');
			$gender = request('gender');
			$other_gender = '';
			if ($gender == 'other') {
				$other_gender = request('other_gender');
			}
			$data = array(
				'full_name' => $first_name . ' ' . $last_name,
				'first_name' => $first_name,
				'middle_name' => $middle_name,
				'last_name' => $last_name,
				'dob' => $age,
				'phone' => Common::normalizePhoneNumberdate($phone),
				'mobile' => Common::normalizePhoneNumberdate($request->input('mobile')),
				'gender' => $gender,
				'other_gender' => $other_gender,
				'language' => $request->input('language'),
				'address1' => $request->input('address1'),
				'address2' => $request->input('address2'),
				'state' => $request->input('state'),
				'city' => $request->input('city'),
				'zip_code' => $request->input('zip_code'),
				'county' => $request->input('county'),
				'location_branch' => $request->location_branch,
				'ssn' => str_replace('-', '', $request->ssn),
				'email' => $request->email,
				'relation_ship' => $request->spouse,

			);
			$getDuplicate = $this->hubRecordService->getDuplicateSearch($data);
			if (isset($getDuplicate->id) && !empty($getDuplicate->id)) {
				return response()->json(['status' => false, 'error_msg' => 'The record already exists'], 409);
			} else {
				$insert = $this->hubRecordService->save($data);
				if ($insert) {
					$agencyData = array(
						'hub_record_id' => $insert,
						'agency_id' => $request->input('agency_id') != null ? $request->input('agency_id') : NULL,
						'status' => 'active',
						'employee_code' => $request->input('employee_code') != null ? $request->input('employee_code') : NULL,
						"member_id" => $request->input('member_id') != null ? $request->input('member_id') : NULL,
					);
					if ($request->hire_date != "") {
						$agencyData['hire_date'] = date('Y-m-d', strtotime($request->hire_date));
					}
					if ($request->work_contact != "") {
						$agencyData['work_contact'] = Common::normalizePhoneNumberdate($request->work_contact);
					}
					if ($request->work_email != "") {
						$agencyData['work_email'] = $request->work_email;
					}
					if ($request->last_worked_date != "") {
						$agencyData['last_worked_date'] = date('Y-m-d', strtotime($request->last_worked_date));
					}
					$this->hubRecordAgencyService->save($agencyData);
					$ipaddress = Utility::getIP();
					$insertLog = [
						'type' => 'Hub Record created',
						'link' => url('/hub-record/add'),
						'module' => 'Hub Record',
						'object_id' => $insert,
						'message' => $user->first_name . ' ' . $user->last_name . ' has added Hub Record',
						'new_response' => serialize($data),
						'ip' => $ipaddress,
					];
					HubLogsService::save($insertLog);
				}
			}
			if ($insert) {
				return response()->json(['status' => true, 'error_msg' => 'New Hub Created successfully.'], 200);
			} else {
				return response()->json(['status' => false, 'error_msg' => 'Sorry, something went wrong. Please try again.'], 500);
			}
		}
	}

	public function delete($id)
	{
		$user = auth()->user();

		$data['id'] = $id;
		$update = $this->hubRecordService->SoftDelete(array('deleted_flag' => 'Y'), array('id' => $id));

		if ($update) {
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Delete Hub Record',
				'link' => url('/hub-record/delete'),
				'module' => 'Hub Record',
				'object_id' => $id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Hub Record',
				'new_response' => serialize($data),
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);
			Session::flash('success', 'Hub Record successfully deleted.');
			return redirect('/hub-record');
		} else {
			Session::flash('error', 'Sorry, something went wrong. Please try again.');
			return redirect('/hub-record');
		}
	}

	public function view($id)
	{
		$data['menu'] = "Hub Record Details";
		$data['auth'] = $data['user'] = $auth = auth()->user();
		if (!$auth || $auth == null) {
			return redirect('login');
		}

		$record = $this->hubRecordService->getDetailById($id);
		$angecyList = Cache::get('patient_master_locations', function () {
			return HubCompany::getAgencyListHub();
		}, 10);
		$data['agencyList'] = $angecyList;
		if (isset($record->id) && $record->id != '' && auth()->user()->agency_fk == "" && auth()->user()->show_hub == 1) {
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


			foreach ($data['location_list'] as $val) {
				if (isset($record->location_id) && $record->location_id != '') {
					if ($record->location_id == $val->id) {
						$address1 = '';
						$address2 = '';
						$city = '';

						if ($val->address1 != '') {
							$address1 = $val->address1;
						}
						if ($val->address1 != '') {
							$address2 = $val->address2;
						}
						if ($val->city != '') {
							$city = $val->city;
						}
						$localdetails = $address1 . ' ' . $address2 . ' ' . $city . ' ' . $val->state . ' ' . $val->zip_code;
					}
				}
			}

			$new_location_list = $this->locationMasterService->getDetailbyId($record->location_id);
			$localdetails = "";
			if (isset($new_location_list->address1) && $new_location_list->address1 != '') {
				$localdetails = $new_location_list->address1;
			}
			$record->location = $localdetails;
			$data['record'] = $record;
			$data['assign_user_list'] = User::getNYBestUserData();
			$data['nyBestUserList'] = User::getNyBestUsersList();
			$data['locations'] = $this->locationMasterService->AllList();
			$data['masterSubjectData'] = Master::getAllDataByMasterTypeFk(array(30));
			$data['language_list'] =  Cache::get('language_list', function ()  use ($auth) {
				return Language::getLanguageList();
			}, 10 * 60);

			return view('hubRecord/view', $data);
		} else {
			abort(404);
		}
	}

	public function saveBasicDetails(Request $request)
	{
		$user = auth()->user();
		$data['id'] = $id = $request->id;
		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
		]);
		if ($validator->fails()) {
			return redirect("")
				->withErrors($validator, 'add_agency')
				->withInput();
		} else {
			$first_name = $request->input('first_name');
			$middle_name = $request->input('middle_name');
			$last_name = $request->input('last_name');

			$ssn = $request->input('ssn');
			$age = '';
			if ($request->input('dob') != '') {
				$age = date('Y-m-d', strtotime($request->input('dob')));
			}

			$gender = $request->input('gender');
			$other_gender = '';
			if ($gender == 'other') {
				$other_gender = $request->input('other_gender');
			}

			$data = array(
				'full_name' => $first_name . ' ' . $last_name,
				'first_name' => $first_name,
				'middle_name' => $middle_name,
				'last_name' => $last_name,
				'dob' => $age,
				'gender' => $gender,
				'other_gender' => $other_gender,
			);

			if ($user->view_ssn_hub == 1) {
				$data['ssn'] = str_replace('-', '', $ssn);
				$checkDuplicateSSN = $this->hubRecordService->checkDuplicateSSN($data, $id);

				if (isset($checkDuplicateSSN->id) && $checkDuplicateSSN->id != "") {
					return response()->json(['status' => false, 'error_msg' => 'The SSN already exists'], 409);
				}
			}
			$getExistingData = $this->hubRecordService->getDetailById($id);
			$this->hubRecordService->update($data, array('id' => $id));
			$getNewData = $this->hubRecordService->getDetailById($id);
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Basic details updated',
				'link' => url('/hub-record/view/') . '/' . $id,
				'module' => 'Hub Record',
				'object_id' => $id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Hub Record basic details',
				'new_response' => serialize($getNewData->toArray()),
				'old_response' => serialize($getExistingData->toArray()),
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'Basic detail saved successfully.', 'data' => $getNewData], 200);
		}
	}

	public function saveAddressDetails(Request $request)
	{
		$user = auth()->user();
		$data['id'] = $id = $request->id;
		$data = array(
			'address1' => $request->input('address1'),
			'address2' => $request->input('address2'),
			'state' => $request->input('state'),
			'city' => $request->input('city'),
			'zip_code' => $request->input('zip_code'),
			'county' => $request->input('county') == "County not found" ? '' : $request->input('county'),
			'email' => $request->email,
		);

		$getExistingData = $this->hubRecordService->getDetailById($id);
		$this->hubRecordService->update($data, array('id' => $id));
		$getNewData = $this->hubRecordService->getDetailById($id);
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Address details updated.',
			'link' => url('/hub-record/view/') . '/' . $id,
			'module' => 'Hub Record',
			'object_id' => $id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Hub Record Address details',
			'new_response' => serialize($getNewData->toArray()),
			'old_response' => serialize($getExistingData->toArray()),
			'ip' => $ipaddress,
		];
		HubLogsService::save($insertLog);
		return response()->json(['status' => true, 'error_msg' => 'Address detail saved successfully.', 'data' => $getNewData], 200);
	}

	public function updateMobileNumber(Request $request)
	{
		$user = auth()->user();
		$data['id'] = $id = $request->hub_id;
		$data = array(
			'mobile' => Common::normalizePhoneNumberdate($request->input('mobile')),
		);

		$getExistingData = $this->hubRecordService->getDetailById($id);
		$this->hubRecordService->update($data, array('id' => $id));
		$getNewData = $this->hubRecordService->getDetailById($id);
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Mobile number updated',
			'link' => url('/hub-record/view/') . '/' . $id,
			'module' => 'Hub Record',
			'object_id' => $id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Hub Record Mobile Number.',
			'new_response' => serialize($getNewData->toArray()),
			'old_response' => serialize($getExistingData->toArray()),
			'ip' => $ipaddress,
		];
		HubLogsService::save($insertLog);
		return response()->json(['status' => true, 'error_msg' => 'Hub Record Mobile Number updated successfully.', 'data' => $getNewData], 200);
	}

	public function updatePhoneNumber(Request $request)
	{
		$user = auth()->user();
		$data['id'] = $id = $request->hub_id;
		$data = array(
			'phone' => Common::normalizePhoneNumberdate($request->input('phone')),
		);

		$getExistingData = $this->hubRecordService->getDetailById($id);
		$this->hubRecordService->update($data, array('id' => $id));
		$getNewData = $this->hubRecordService->getDetailById($id);
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Phone number updated',
			'link' => url('/hub-record/view/') . $id,
			'module' => 'Hub Record',
			'object_id' => $id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Hub Record Phone Number.',
			'new_response' => serialize($getNewData->toArray()),
			'old_response' => serialize($getExistingData->toArray()),
			'ip' => $ipaddress,
		];
		HubLogsService::save($insertLog);
		return response()->json(['status' => true, 'error_msg' => 'Hub Record Phone Number updated successfully.', 'data' => $getNewData], 200);
	}

	public function updateLanguage(Request $request)
	{
		$user = auth()->user();
		$data['id'] = $id = $request->hub_id;
		$data = array(
			'language' => $request->input('language_id'),
		);

		$getExistingData = $this->hubRecordService->getDetailById($id);
		$this->hubRecordService->update($data, array('id' => $id));
		$getNewData = $this->hubRecordService->getDetailById($id);
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Update Hub Record Language',
			'link' => url('/hub-record/view/') . '/' . $id,
			'module' => 'Hub Record',
			'object_id' => $id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has Updated Hub Record Language.',
			'new_response' => serialize($getNewData->toArray()),
			'old_response' => serialize($getExistingData->toArray()),
			'ip' => $ipaddress,
		];
		HubLogsService::save($insertLog);
		return response()->json(['status' => true, 'error_msg' => 'Hub Record Language updated successfully.', 'data' => $getNewData], 200);
	}


	public function hubRecordNotesData(Request $request, $id)
	{
		$data = $this->hubRecordNotesService->getHubNotesData($id, $request->hub_record_agency_id, $request->hub_agency_id);
		return response()->json(['status' => true, 'data' => $data], 200);
	}

	public function saveHubNotes(Request $request, $id)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'msg-box' => 'required',
			'subject' => 'required',
		]);
		if ($validator->fails()) {
			return redirect("")
				->withErrors($validator, 'add_agency')
				->withInput();
		} else {
			$subject = $request->input('subject');
			$notes = $request->input('msg-box');

			$data = array(
				'subject' => $subject,
				'message' => $notes,
				'hub_record_id' => $id,
				'hub_record_agency_id' => $request->hub_record_agency_id,
				'hub_agency_id' => $request->hub_agency_id
			);

			$getExistingData = $this->hubRecordNotesService->getHubNotesData($id);
			$this->hubRecordNotesService->save($data);
			$getNewData = $this->hubRecordNotesService->getHubNotesData($id);
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Notes added',
				'link' => url('/hub-record/view/') . '/' . $id,
				'module' => 'Hub Record',
				'object_id' => $id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has added Hub Record notes',
				'new_response' => serialize($getNewData->toArray()),
				'old_response' => serialize($getExistingData->toArray()),
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'Notes saved successfully.'], 200);
		}
	}

	public function hubRecordDocData(Request $request, $id)
	{
		$data['document_list'] = $this->hubRecordDocService->getAllDocumentByPatientId($id, $request->hub_record_agency_id, $request->hub_agency_id);
		return view("hubRecord/doc_ajax_list", $data);
	}

	public function saveHubRecordDocData(Request $request, $id)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'document_id' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json([
				'error_msg' => $validator->errors()->all()[0],
				'status' => false,
			], 422);
		} else {
			$image = '';
			if ($request->file('images') != '') {
				$priceImage = $request->file('images');
				$name = uniqid() . time() . '.' . $priceImage->getClientOriginalExtension();
				//$destination = public_path('hubdocument');
				$destination1 = public_path('hubdocument') . '/' . $request->input('id');

				if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
					$priceImage->move($destination1, $name);


					$image = $name;
				} else {
					//$image = $filepath = Storage::disk('s3')->putFileAs('hubdocument', $priceImage, $name);
					$path = 'hubdocument/' . $request->input('id');

					Storage::disk('s3')->putFileAs($path, $priceImage, $name);

					$image = $name;
				}
			}
			$getExistingRecord  = '';
			if ($request->input('did') != '') {
				$getExistingRecord  = $this->hubRecordDocService->getDetailsById($request->input('did'));
				$data = array(
					'document_name' => $request->input('document_id'),
				);
				$type = "Document updated";
				$insert = $this->hubRecordDocService->update($data, array('id' => $request->input('did')));
			} else {
				$data = array(
					'document_name' => $request->input('document_id'),
					'attachment' => $image,
					'hub_record_id' => $request->input('id'),
					'hub_record_agency_id' => $request->hub_record_agency_id,
					'hub_agency_id' => $request->hub_agency_id
				);
				$insert = $this->hubRecordDocService->save($data);
				$type = "Document added";
			}

			if ($insert) {
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => $type,
					'link' =>  url('/hub-record/') . '/view/' . $request->input('id'),
					'module' => 'Hub Record',
					'object_id' => $request->input('id'),
					'message' => $user->first_name . ' ' . $user->last_name . ' has added Document',
					'new_response' => serialize($data),
					'ip' => $ipaddress,
				];
				if (isset($getExistingRecord) && $getExistingRecord != "") {
					$insertLog['old_response'] = serialize($getExistingRecord->toArray());
				}
				HubLogsService::save($insertLog);
				if ($request->input('did') != '') {
					$msg = 'Document successfully updated';
				} else {
					$msg = 'Document successfully uploaded';
				}
				return response()->json(['status' => true, 'error_msg' => $msg], 200);
			} else {
				return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again'], 500);
			}
		}
	}

	function hubDocumentDelete($recordId, $id)
	{
		$insert = $this->hubRecordDocService->SoftDelete(array('deleted_flag' => 'Y'), array('id' => $id));
		if ($insert) {
			$ipaddress = Utility::getIP();
			$user = auth()->user();
			$insertLog = [
				'type' => 'Document deleted',
				'link' => url('/hub-record/view/') . '/' . $recordId,
				'module' => 'Hub Record',
				'object_id' => $recordId,
				'message' => $user->first_name . ' ' . $user->last_name . ' has Delete Document Hub Record',
				'new_response' => serialize(array('deleted_flag' => 'Y')),
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'Document successfully deleted'], 200);
		} else {
			return response()->json(['status' => true, 'error_msg' => 'Sorry, something went wrong. Please try again.'], 200);
		}
		return redirect()->back();
	}

	public function exportToCsv(Request $request)
	{
		$auth = auth()->user();
		$search_data = $request->all();
		$search_data['is_dependent'] = "N";
		$response = $this->hubRecordService->getHubData($search_data, 'export');
		if (count($response) > 0) {
			$filename = 'Hub_record' . date("m-d-Y");
			$headers = array(
				"Content-type" => "text/csv",
				"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
				"Pragma" => "no-cache",
				"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
				"Expires" => "0",
			);
			if ($auth->view_ssn_hub == 1) {
				$columns = array('ID', 'First Name', 'Middle Name', 'Last Name', 'Birth Date', 'Gender', 'Email', 'Address 1', 'Address 2', 'City', 'Zip Code', 'Phone', 'Mobile', 'SSN', 'Company', 'Employee Code', 'Member Id', 'Work Contact', 'Work Email', 'Hire Date', 'Last Work Date', 'Created Date', 'Created By', 'Updated Date', 'Updated By');
			} else {
				$columns = array('ID', 'First Name', 'Middle Name', 'Last Name', 'Birth Date', 'Gender', 'Email', 'Address 1', 'Address 2', 'City', 'Zip Code', 'Phone', 'Mobile', 'Company', 'Employee Code', 'Member Id', 'Work Contact', 'Work Email', 'Hire Date', 'Last Work Date', 'Created Date', 'Created By', 'Updated Date', 'Updated By');
			}

			$callback = function () use ($response, $columns, $auth) {
				$file = fopen('php://output', 'w');
				fputcsv($file, $columns);
				foreach ($response as $list) {
					$usersUpdateFullName = $usersName = '';
					if (isset($list->usersUpdate->first_name) && isset($list->usersUpdate->last_name)) {
						$usersUpdateFullName = $list->usersUpdate->first_name . ' ' . $list->usersUpdate->last_name;
					}
					if (isset($list->users->first_name) && isset($list->users->last_name)) {
						$usersName = $list->users->first_name . ' ' . $list->users->last_name;
					}

					$hire_date = $list->hire_date != '' ? date('m/d/Y', strtotime($list->hire_date)) : '';
					$last_worked_date = $list->last_worked_date != '' ? date('m/d/Y', strtotime($list->last_worked_date)) : '';
					$updated_date = $list->updated_date != '' && !empty($list->updated_date) ? date('m/d/Y', strtotime($list->updated_date)) : '';
					if ($auth->view_ssn_hub == 1) {
						fputcsv($file, array($list->id, $list->first_name, $list->middle_name, $list->last_name, date('m/d/Y', strtotime($list->dob)), ucfirst($list->gender), $list->email, $list->address1, $list->address2, $list->city, $list->zip_code, $list->phone, $list->mobile, Utility::formatSSN($list->ssn), $list->agency_name, $list->member_id, $list->work_contact, $list->work_email, $hire_date, $last_worked_date, date('m/d/Y', strtotime($list->created_date)), $usersName, $updated_date, $usersUpdateFullName));
					} else {
						fputcsv($file, array($list->id, $list->first_name, $list->middle_name, $list->last_name, date('m/d/Y', strtotime($list->dob)), ucfirst($list->gender), $list->email, $list->address1, $list->address2, $list->city, $list->zip_code, $list->phone, $list->mobile, $list->agency_name, $list->employee_code, $list->member_id, $list->work_contact, $list->work_email, $hire_date, $last_worked_date, date('m/d/Y', strtotime($list->created_date)), $usersName, $updated_date, $usersUpdateFullName));
					}
				}
				fclose($file);
			};
			return response()->stream($callback, 200, $headers);
		} else {
			return null;
		}
	}

	public function getMessageList(Request $request)
	{
		$ids = [$request->hub_record_id];
		$query = $this->hubRecordtextMessageService->getMessageListWithMultipleIds($ids, $request->hub_record_agency_id, $request->hub_agency_id);
		return response()->json(['error_msg' => "Success", 'status' => 1, 'data' => $query], 200);
	}

	public function smsTextMessage(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'hub_record_id' => 'required',
			'mobile' => 'required',
			'message' => 'required'
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 500);
		} else {
			$data = array(
				'mobile' => $request->mobile,
				'hub_record_id' => $request->hub_record_id,
				'message' => $request->message,
				'message_type' => 'text',
				'hub_record_agency_id' => $request->hub_record_agency_id,
				'hub_agency_id' => $request->hub_agency_id
			);
			$save = $this->hubRecordtextMessageService->save($data);
			if ($save) {
				$getDetails =    $this->hubRecordtextMessageService->getDetailsId($save);
				$getPatientDetails = $this->hubRecordService->getDetailById($request->hub_record_id);
				$mobileArray = [];

				if ($request->phone != "") {
					$mobileArray[] = str_replace(['(', ')', '-', ' '], '', $getPatientDetails->phone);
				}
				if ($request->mobile != "") {
					$mobileArray[] = str_replace(['(', ')', '-', ' '], '', $getPatientDetails->mobile);
				}
				if (count($mobileArray) > 0) {
					foreach ($mobileArray as $mb) {
						$sms = Common::sendTwillioSms($mb, $request->message);
					}
				}
				$ipaddress = Utility::getIP();
				$user = auth()->user();
				$id = $request->hub_record_id;
				$insertLog = [
					'type' => 'Text message added',
					'link' => url('/hub-record/view/') . '/' . $id,
					'module' => 'Hub Record',
					'object_id' => $id,
					'message' => $user->first_name . ' ' . $user->last_name . ' has added Text messages on Hub Record',
					'new_response' => serialize($getDetails),
					'ip' => $ipaddress,
				];
				HubLogsService::save($insertLog);
				return response()->json(['error_msg' => '', 'status' => 1, 'data' => $getDetails], 200);
			} else {
				return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array(), 'status' => 0], 500);
			}
		}
	}


	public function showImage($id)
	{
		$auth = auth()->user();
		$getDetails = $this->hubRecordDocService->getDetailsById($id);
		if (isset($getDetails->hub_record_id)) {
			$getHubDetails = $this->hubRecordService->getDetailById($getDetails->hub_record_id);

			if (isset($getHubDetails->id)) {
				$file = public_path('/') . "/hubdocument/" . $getDetails->hub_record_id . '/' . $getDetails->attachment;
				$headers = [];
				if (str_contains($getDetails->attachment, 'hubdocument')) {
					if (file_exists($file)) {
						$headers = [];
						return response()->download($file, $getDetails->attachment, $headers);
					} else {
						return   Storage::disk('s3')->download($getDetails->attachment);
					}
					die();
				} else {
					if (file_exists($file)) {
						$headers = [];
						return response()->download($file, $getDetails->attachment, $headers);
					} else {
						return   Storage::disk('s3')->download('hubdocument/' . $getDetails->hub_record_id . '/' . $getDetails->attachment);
					}
					die();
				}
				return response()->download($file, $getDetails->attachment, $headers);
			} else {

				abort(404);
			}
		} else {

			abort(404);
		}
	}

	public function viewPdfDoc(Request $request)
	{

		$data['url'] = "";
		$getDetails = $this->hubRecordDocService->getDetailsById($request->id);

		if (isset($getDetails->hub_record_id)) {
			$getPatientDetails = $this->hubRecordService->getDetailById($getDetails->hub_record_id);

			if (isset($getPatientDetails->id)) {

				if (str_contains($getDetails->attachment, 'hubdocument')) {
					if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
						$data['url'] = url('hubdocument/' . $getDetails->hub_record_id . '/' . $getDetails->attachment);
					} else {
						$data['url'] = Storage::disk('s3')->temporaryUrl($getDetails->attachment, now()->addMinutes(60));
					}
				} else {
					if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
						$data['url'] = url('hubdocument/' . $getDetails->hub_record_id . '/' . $getDetails->attachment);
					} else {
						$data['url'] = Storage::disk('s3')->temporaryUrl('hubdocument/' . $getDetails->hub_record_id . '/' . $getDetails->attachment, now()->addMinutes(60));
					}
				}
			}
		}
		return view('patient._partial.view_pdf_iframe', $data);
	}


	public function HubImportsV2(Request $request)
	{
		ini_set('max_execution_time', 600); // 300 seconds = 5 minutes
		$auth = auth()->user();
		$imported = 0;
		$updated = 0;
		$skipped = 0;
		$deactivated = 0;
		$errors = [];
		$header = [];
		$importedRecords = [];

		if (!$request->hasFile('images')) {
			return response()->json(['status' => false, 'message' => 'No file uploaded.']);
		}

		$file = $request->file('images');
		$extension = $file->getClientOriginalExtension();
		$path = $file->getRealPath();
		$fileName = $file->getClientOriginalName();
		$name = time() . '_' . $file->getClientOriginalName();
		$destinationPath = public_path('hubupload');

		$file = $request->file('images');
		$newName = uniqid() . '.' . $file->getClientOriginalExtension();

		if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
			$file->move(public_path() . '/hubupload', $newName);
			$path = public_path() . '/hubupload/' . $newName;
			$url = URL::to('/') . '/hubupload/' . $newName;
		} else {
			$s3Path = 'hubupload/' . $newName;
			Storage::disk('s3')->putFileAs('hubupload', $file, $newName);
		}
		// Start import logging
		$logId = $this->hubRecordImportLogService->logImportStart(
			$fileName,
			$request->agency_id ?? $auth->agency_fk,
			0 // Will update total records after reading file
		);

		try {
			// Read file
			$rows = [];
			if (strtolower($extension) === 'csv') {
				$rows = array_map('str_getcsv', file($path));
			}

			if (empty($rows) || count($rows) < 2) {
				$this->hubRecordImportLogService->failImport($logId, 'File is empty or missing data.');
				return response()->json(['status' => false, 'message' => 'File is empty or missing data.']);
			}

			// Update total records count
			$this->hubRecordImportLogService->updateImportProgress($logId, [
				'total_records' => count($rows) - 1 // Exclude header row
			]);

			$header = array_map('trim', $rows[0]);
			// Map columns
			$map = [
				'Last Name' => 'last_name',
				'First Name' => 'first_name',
				'Middle Initial' => 'middle_name',
				'Birth Date' => 'dob',
				'Gender' => 'gender',
				'Email Address' => 'email',
				'Primary Address 1' => 'address1',
				'Primary Address 2' => 'address2',
				'Primary City' => 'city',
				'Primary State' => 'state',
				'Primary Zip Code' => 'zip_code',
				'Home Phone' => 'phone',
				'Mobile Phone' => 'mobile',
				'SSN' => 'ssn',
				'Hire Date' => 'hire_date',
				'Work Contact' => 'work_contact',
				'Work Email' => 'work_email',
				'Employee Code' => 'employee_code',
				'Member Id' => 'member_id',
				'Last Worked Date' => 'last_worked_date',
			];
			$colIndex = [];
			foreach ($map as $col => $field) {
				$idx = array_search($col, $header);
				if ($idx !== false) {
					$colIndex[$field] = $idx;
				}
			}
			$header = array_map('trim', $header);

			$expected = array_keys($map);
			$missing = array_diff($expected, $header);
			if (!empty($missing)) {
				$errors[] = "Missing required columns: " . implode(', ', $missing);
			} else {
				$flag = 0;
				// Status Update to deactivate all fields
				for ($i = 1; $i < count($rows); $i++) {

					$row = $rows[$i];
					if (count(array_filter($row)) == 0) {
						$flag++;
						$errors[] = "For Row $i: The following required columns are missing from your file: [First name, Last name, Mobile, Date Of Birth and Gender]. Please upload a valid file.";
						$skipped++;
					} // skip empty rows
					else {
						$record = [];
						foreach ($colIndex as $field => $idx) {
							$record[$field] = isset($row[$idx]) ? trim($row[$idx]) : null;
						}

						if (empty($record['first_name']) || empty($record['last_name'] || empty($record['mobile']) || empty($record['dob']) || empty($record['gender']))) {
							$errors[] = "For Row $i: The following required columns are missing from your file: [First name, Last name, Mobile, Date Of Birth, SSN and Gender]. Please upload a valid file.";
							$skipped++;
							continue;
						}

						// if (empty($record['ssn'])) {
						// 	$errors[] = "For Row $i: SSN is required.";
						// 	$skipped++;
						// 	continue;
						// }
						$existing = null;
						// Format DOB
						if (!empty($record['dob'])) {
							//$record['dob'] = date('Y-m-d', strtotime($record['dob']));

							$dateStr = $record['dob'];
							$parts = explode('-', $dateStr);

							$exPort = "";
							$dateFlag = 0;
							if (isset($parts[2])) {
								$exPort = $parts[2];
							} else {

								$parts = explode('/', $dateStr);
								$dateFlag = 1;
								$exPort = $parts[2];
							}

							$yearPart = trim($exPort);

							if ($yearPart != "") {

								if (strlen($yearPart) == 2) {

									if ($dateFlag != 1) {
										$date = Carbon::createFromFormat('m-d-y', $record['dob']);
									} else {

										$date = Carbon::createFromFormat('m/d/y', $record['dob']);
									}


									if ($date->year > date('Y') + 10) {
										$date = $date->subCentury();
									}
								} elseif (strlen($yearPart) == 4) {
									$date  = $record['dob'];
								}
							}
							$record['dob'] = date('Y-m-d', strtotime($date));
							if ($record['dob'] == '1969-12-31') {
								$record['dob'] = Utility::parseFlexibleDate($date);
							}
						} else {
							$record['dob'] = NULL;
						}
						if (!empty($record['mobile'])) {
							$record['mobile'] = Common::normalizePhoneNumberdate($record['mobile']);
						}
						if (!empty($record['phone'])) {
							$record['phone'] = Common::normalizePhoneNumberdate($record['phone']);
						}
						$record['ssn'] = str_replace('-', '', $record['ssn']);
						$record['full_name'] = $record['first_name'] . ' ' . $record['last_name'];
						//check if entry founded in table
						$existing = $this->hubRecordService->getImportDuplicateSSN($record);
						// Check duplicate ssn
						$record['import_flag'] = 1;
						$agencyData = [];
						if ($existing) {
							// Update
							// $this->hubRecordService->update($record, ['id' => $existing->id]);
							$checkagencyData = $this->hubRecordAgencyService->getAgencyData($existing->id, $request->agency_id);
							if (empty($checkagencyData)) {
								$agencyData = array(
									'hub_record_id' => $existing->id,
									'agency_id' => $request->agency_id,
									'status' => 'active',
									'hire_date' => !empty($record['hire_date']) ? date('Y-m-d H:i:s', strtotime($record['hire_date'])) : NULL,
									'work_contact' => $record['work_contact'] ?? '',
									'work_email' => $record['work_email'] ?? '',
									'employee_code' => $record['employee_code'] ?? '',
									'member_id' => $record['member_id'] ?? '',
									'last_worked_date' => !empty($record['last_worked_date']) ? date('Y-m-d H:i:s', strtotime($record['last_worked_date'])) : NULL,
								);
								$this->hubRecordAgencyService->save($agencyData);
							} else {
								$agencyData = array(
									'hub_record_id' => $existing->id,
									'agency_id' => $request->agency_id,
									'status' => 'active',
									'hire_date' => !empty($record['hire_date']) ? date('Y-m-d H:i:s', strtotime($record['hire_date'])) : NULL,
									'work_contact' => $record['work_contact'] ?? '',
									'work_email' => $record['work_email'] ?? '',
									'employee_code' => $record['employee_code'] ?? '',
									'member_id' => $record['member_id'] ?? '',
									'last_worked_date' => !empty($record['last_worked_date']) ? date('Y-m-d H:i:s', strtotime($record['last_worked_date'])) : NULL,
								);
								$this->hubRecordAgencyService->update($agencyData, array('hub_record_id' => $existing->id, 'agency_id' => $request->agency_id));
							}
							$updated++;
							$ipaddress = Utility::getIP();
							$insertLog = [
								'type' => 'Hub record activated',
								'link' => url('/hub-record/'),
								'module' => 'Hub Record',
								'object_id' => $existing->id,
								'message' => $auth->first_name . ' ' . $auth->last_name . ' has activate Hub Record',
								'ip' => $ipaddress,
							];
							HubLogsService::save($insertLog);
						} else {
							$checkSSN = $this->hubRecordService->checkDuplicateSSN($record);
							if ($checkSSN) {
								$errors[] = "For Row $i: SSN is duplicate.";
								$skipped++;
								continue;
							}
							// Insert
							$recordInsId = $this->hubRecordService->save($record);
							// Insert log into agency
							$agencyData = array(
								'hub_record_id' => $recordInsId,
								'agency_id' => $request->agency_id,
								'status' => 'active',
								'hire_date' => date('Y-m-d H:i:s', strtotime($record['hire_date'])),
								'work_contact' => $record['work_contact'] ?? '',
								'work_email' => $record['work_email'] ?? '',
								'employee_code' => $record['employee_code'] ?? '',
								'member_id' => $record['member_id'] ?? '',
								'last_worked_date' => !empty($record['last_worked_date']) ? date('Y-m-d H:i:s', strtotime($record['last_worked_date'])) : NULL,
							);
							$this->hubRecordAgencyService->save($agencyData);
							$imported++;
							$ipaddress = Utility::getIP();
							$insertLog = [
								'type' => 'Hub record created',
								'link' => url('/hub-record/'),
								'module' => 'Hub Record',
								'object_id' => $recordInsId,
								'message' => $auth->first_name . ' ' . $auth->last_name . ' has created Hub Record',
								'ip' => $ipaddress,
							];
							HubLogsService::save($insertLog);
						}

						// Update progress every 10 records
						if ($i % 10 === 0) {
							$this->hubRecordImportLogService->updateImportProgress($logId, [
								'successful_records' => $imported,
								'failed_records' => $skipped,
								'updated_records' => $updated
							]);
						}
						$importedRecords[] = $agencyData;
					}
				}
				if ($flag != (count($rows) - 1)) {
					// Get all existing active records
					$existingRecords = $this->hubRecordAgencyService->getAllRecord($request->agency_id);
					// Compare and update status
					foreach ($existingRecords as $existingRecord) {
						$found = false;
						foreach ($importedRecords as $importedRecord) {
							if (
								$existingRecord->hub_record_id == $importedRecord['hub_record_id'] &&
								$existingRecord->agency_id == $importedRecord['agency_id']
							) {
								$found = true;
								break;
							}
						}

						// If record not found in import, update status to deactivate
						if (!$found) {
							$existingRecord->status = 'deactivated';
							$existingRecord->deactivated_date = date('Y-m-d H:i:s');
							$existingRecord->deactivated_by = auth()->user()->id;
							$existingRecord->save();
							$deactivated++;

							$ipaddress = Utility::getIP();
							$insertLog = [
								'type' => 'Hub record agency deactivated',
								'link' => url('/hub-record/'),
								'module' => 'Hub Record',
								'object_id' => $existingRecord->id,
								'message' => $auth->first_name . ' ' . $auth->last_name . ' has deactivate Hub Record',
								'ip' => $ipaddress,
							];
							HubLogsService::save($insertLog);
						}
					}
				}


				// Complete import logging
				$this->hubRecordImportLogService->completeImport($logId, [
					'successful_records' => $imported,
					'failed_records' => $skipped,
					'updated_records' => $updated,
					'deactivate_records' => $deactivated
				], !empty($errors) ? json_encode($errors) : null);
			}
			$summary = [
				'imported' => $imported,
				'skipped' => $skipped,
				'updated' => $updated,
				'deactivated' => $deactivated,
				'errors' => $errors,
			];

			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Hub Record data imported',
				'link' => url('/hub-record/'),
				'module' => 'Hub Record Import',
				'message' => '"Import completed. "' . $imported . '" records were imported, " .
           "' . $updated . '" updated, "' . $skipped . '" skipped, " .
           "' . $deactivated . '" deactivated.',
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);
			if ($imported == 0 && $skipped == 0 && $updated == 0) {
				return response()->json(['status' => false, 'summary' => $summary, 'message' => $errors]);
			} else {
				return response()->json(['status' => true, 'summary' => $summary]);
			}
		} catch (\Exception $e) {
			// Log import failure
			return response()->json(['status' => false, 'message' => 'Error during import: ' . $e->getMessage()]);
		}
	}

	public function HubImports(Request $request)
	{

		$auth = auth()->user();

		$validator = Validator::make($request->all(), [
			'images'          => 'required|file|mimetypes:text/plain,text/csv,text/comma-separated-values|max:102400',
			'add_remove'      => 'required',
			'unique_fields'   => 'required|array|min:1',
			'unique_fields.*' => 'in:last_name,first_name,middle_name,dob,gender,email,address1,address2,city,state,zip_code,phone,company,ssn,hire_date,work_contact,work_email,last_worked_date,member_id,employee_code',
		]);

		if ($validator->fails()) {
			return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()]);
		}

		if (!$request->hasFile('images')) {
			return response()->json(['status' => false, 'message' => 'No file uploaded.']);
		}

		$file      = $request->file('images');
		$extension = strtolower($file->getClientOriginalExtension());
		$fileName  = $file->getClientOriginalName();
		$newName   = uniqid() . '.' . $extension;
		$date      = date('mdy');

		if (env('FILE_UPLOAD_PERMISSION') == 'development') {
			$destination = public_path('hubupload') . '/' . $date;
			if (!is_dir($destination)) {
				mkdir($destination, 0755, true);
			}
			$file->move($destination, $newName);
			$path = $destination . '/' . $newName;
		} else {
			Storage::disk('s3')->putFileAs('hubupload/' . $date, $file, $newName);
			$path = 'hubupload/' . $date . '/' . $newName;
		}

		$agencyId  = $request->agency_id ?? $auth->agency_fk;
		$addRemove = $request->input('add_remove');
		$filetype  = $request->input('filetype');
		$uniqueFields = $request->input('unique_fields', []);

		if ($extension !== 'csv') {
			return response()->json(['status' => false, 'message' => 'Only CSV files are supported.']);
		}

		if (env('FILE_UPLOAD_PERMISSION') == 'development') {
			$handle = fopen($path, 'r');
			if ($handle === false) {
				return response()->json(['status' => false, 'message' => 'Unable to open uploaded file.']);
			}
			$headerRow = fgetcsv($handle);
			fclose($handle);
		} else {
			$csvLines  = explode("\n", str_replace("\r", '', Storage::disk('s3')->get($path)));
			$headerRow = str_getcsv(array_shift($csvLines) ?? '');
		}

		if (empty($headerRow)) {
			return response()->json(['status' => false, 'message' => 'File is empty or missing data.']);
		}
		$header = array_map('trim', $headerRow);

		$map = [
			'Last Name'         => 'last_name',
			'First Name'        => 'first_name',
			'Middle Initial'    => 'middle_name',
			'Birth Date'        => 'dob',
			'Gender'            => 'gender',
			'Email Address'     => 'email',
			'Primary Address 1' => 'address1',
			'Primary Address 2' => 'address2',
			'Primary City'      => 'city',
			'Primary State'     => 'state',
			'Primary Zip Code'  => 'zip_code',
			'Home Phone'        => 'phone',
			'Mobile Phone'      => 'mobile',
			'SSN'               => 'ssn',
			'Hire Date'         => 'hire_date',
			'Work Contact'      => 'work_contact',
			'Work Email'        => 'work_email',
			'Employee Code'     => 'employee_code',
			'Member Id'         => 'member_id',
			'Last Worked Date'  => 'last_worked_date',
		];
		if ($filetype === 'master_file') {
			$map['Company Name'] = 'company_name';
		}

		$missing = array_diff(array_keys($map), $header);
		if (!empty($missing)) {
			return response()->json(['status' => false, 'message' => 'Missing required columns: ' . implode(', ', $missing)]);
		}

		$colIndex = [];
		foreach ($map as $col => $field) {
			$idx = array_search($col, $header);
			if ($idx !== false) {
				$colIndex[$field] = $idx;
			}
		}

		// Count rows for log
		if (env('FILE_UPLOAD_PERMISSION') == 'development') {
			$countHandle = fopen($path, 'r');
			$totalRows   = 0;
			while (fgetcsv($countHandle) !== false) { $totalRows++; }
			fclose($countHandle);
			$totalRows = max(0, $totalRows - 1);
		} else {
			$totalRows = max(0, count(array_filter($csvLines, fn($l) => trim($l) !== '')));
		}

		$logId = $this->hubRecordImportLogService->logImportStart($fileName, $agencyId, $totalRows);

		ProcessHubRecordImport::dispatch([
			'log_id'        => $logId,
			'path'          => $path,
			'add_remove'    => $addRemove,
			'agency_id'     => $agencyId,
			'filetype'      => $filetype,
			'unique_fields' => $uniqueFields,
			'auth_id'       => $auth->id,
			'auth_name'     => $auth->first_name . ' ' . $auth->last_name,
			'auth_email'    => $auth->email,
			'file_name'     => $fileName,
			'total_rows'    => $totalRows,
			'col_index'     => $colIndex,
			'ip'            => Utility::getIP(),
		]);

		return response()->json([
			'status'  => true,
			'queued'  => true,
			'log_id'  => $logId,
			'total'   => $totalRows,
			'message' => 'Import started. ' . $totalRows . ' records are being processed in the background, you will be notified by email once the process is complete.',
		]);
	}

	private function parseDobField(array $record): array
	{
		if (empty($record['dob'])) {
			$record['dob'] = null;
			return $record;
		}

		$dateStr  = $record['dob'];
		$parts    = explode('-', $dateStr);
		$dateFlag = 0;

		if (isset($parts[2])) {
			$exPort = $parts[2];
		} else {
			$parts    = explode('/', $dateStr);
			$dateFlag = 1;
			$exPort   = $parts[2] ?? null;
		}

		if (empty($exPort)) {
			$record['dob'] = null;
			return $record;
		}

		$yearPart = trim($exPort);
		$date     = $dateStr;

		if ($yearPart !== '') {
			if (strlen($yearPart) === 2) {
				$date = $dateFlag === 0
					? Carbon::createFromFormat('m-d-y', $dateStr)
					: Carbon::createFromFormat('m/d/y', $dateStr);
				if ($date->year > (int) date('Y') + 10) {
					$date = $date->subCentury();
				}
			} elseif (strlen($yearPart) === 4) {
				$date = $dateStr;
			}
		}

		$parsed = date('Y-m-d', strtotime((string) $date));
		if ($parsed === '1969-12-31') {
			try {
				$parsed = Utility::parseFlexibleDate($date);
			} catch (\Exception $e) {
				$parsed = null;
			}
		}
		$record['dob'] = $parsed;
		return $record;
	}

	private function HubImportsLegacyUnusedDELETEME(Request $request)
	{
		$auth = auth()->user();
		$imported = 0;
		$updated = 0;
		$skipped = 0;
		$deactivated = 0;
		$errors = [];
		$header = [];
		$importedRecords = [];
		$dublicateRecords = 0;

		if (!$request->hasFile('images')) {
			return response()->json(['status' => false, 'message' => 'No file uploaded.']);
		}

		$file = $request->file('images');
		$extension = $file->getClientOriginalExtension();
		$path = $file->getRealPath();
		$fileName = $file->getClientOriginalName();
		$name = time() . '_' . $file->getClientOriginalName();
		$destinationPath = public_path('hubupload');

		$file = $request->file('images');
		$newName = uniqid() . '.' . $file->getClientOriginalExtension();

		if (env('FILE_UPLOAD_PERMISSION')  == 'development') {
			$file->move(public_path() . '/hubupload', $newName);
			$path = public_path() . '/hubupload/' . $newName;
			$url = URL::to('/') . '/hubupload/' . $newName;
		} else {
			$s3Path = 'hubupload/' . $newName;
			Storage::disk('s3')->putFileAs('hubupload', $file, $newName);
		}
		$logId = $this->hubRecordImportLogService->logImportStart(
			$fileName,
			$request->agency_id ?? $auth->agency_fk,
			0
		);

		try {
			// Read file
			$rows = [];
			if (strtolower($extension) === 'csv') {
				$rows = array_map('str_getcsv', file($path));
			}

			if (empty($rows) || count($rows) < 2) {
				$this->hubRecordImportLogService->failImport($logId, 'File is empty or missing data.');
				return response()->json(['status' => false, 'message' => 'File is empty or missing data.']);
			}

			// Update total records count
			$this->hubRecordImportLogService->updateImportProgress($logId, [
				'total_records' => count($rows) - 1 // Exclude header row
			]);

			$header = array_map('trim', $rows[0]);
			// Map columns
			$map = [
				'Last Name' => 'last_name',
				'First Name' => 'first_name',
				'Middle Initial' => 'middle_name',
				'Birth Date' => 'dob',
				'Gender' => 'gender',
				'Email Address' => 'email',
				'Primary Address 1' => 'address1',
				'Primary Address 2' => 'address2',
				'Primary City' => 'city',
				'Primary State' => 'state',
				'Primary Zip Code' => 'zip_code',
				'Home Phone' => 'phone',
				'Mobile Phone' => 'mobile',
				'SSN' => 'ssn',
				'Hire Date' => 'hire_date',
				'Work Contact' => 'work_contact',
				'Work Email' => 'work_email',
				'Employee Code' => 'employee_code',
				'Member Id' => 'member_id',
				'Last Worked Date' => 'last_worked_date',
			];
			if ($request->filetype == "master_file") {
				$map['Company Name'] = 'company_name';
			}
			$colIndex = [];
			foreach ($map as $col => $field) {
				$idx = array_search($col, $header);
				if ($idx !== false) {
					$colIndex[$field] = $idx;
				}
			}
			$header = array_map('trim', $header);

			$expected = array_keys($map);
			$missing = array_diff($expected, $header);
			if (!empty($missing)) {
				$errors[] = "Missing required columns: " . implode(', ', $missing);
			} else {
				$flag = 0;
				if ($request->input('add_remove') == 'add_remove') {
					// Status Update to deactivate all fields
					for ($i = 1; $i < count($rows); $i++) {

						$row = $rows[$i];
						if (count(array_filter($row)) == 0) {
							$flag++;
							$errors[] = "For Row $i: The following required columns are missing from your file: [First name, Last name, Mobile, Date Of Birth and Gender]. Please upload a valid file.";
							$skipped++;
						} // skip empty rows
						else {
							$record = [];
							foreach ($colIndex as $field => $idx) {
								$record[$field] = isset($row[$idx]) ? trim($row[$idx]) : null;
							}

							if (empty($record['first_name']) || empty($record['last_name'] || empty($record['mobile']) || empty($record['dob']) || empty($record['gender'])) || ($request->filetype == "master_file" && empty($record['company_name']))) {
								if (($request->filetype == "master_file" && empty($record['company_name']))) {

									$errors[] = "For Row $i: The following required columns are missing from your file: [First name, Last name, Mobile, Date Of Birth, SSN,Gender and Company name]. Please upload a valid file.";
								} else {

									$errors[] = "For Row $i: The following required columns are missing from your file: [First name, Last name, Mobile, Date Of Birth, SSN and Gender]. Please upload a valid file.";
								}

								$skipped++;
								continue;
							}
							if ($request->filetype == "master_file" && !empty($record['company_name'])) {
								$agencyDetails = $this->hubCompanyService->getAllAgencyPluck($record['company_name']);
								if (array_key_first($agencyDetails->toArray())) {
									$request->agency_id = array_key_first($agencyDetails->toArray());
								} else {
									$errors[] = "For Row $i: Company name '" . $record['company_name'] . "' not found. Please upload a valid file.";
									$skipped++;
									continue;
								}
							}
							// if (empty($record['ssn'])) {
							// 	$errors[] = "For Row $i: SSN is required.";
							// 	$skipped++;
							// 	continue;
							// }
							$existing = null;
							// Format DOB
							if (!empty($record['dob'])) {
								//$record['dob'] = date('Y-m-d', strtotime($record['dob']));

								$dateStr = $record['dob'];
								$parts = explode('-', $dateStr);

								$exPort = "";
								$dateFlag = 0;
								if (isset($parts[2])) {
									$exPort = $parts[2];
								} else {

									$parts = explode('/', $dateStr);
									$dateFlag = 1;
									$exPort = $parts[2];
								}

								$yearPart = trim($exPort);

								if ($yearPart != "") {

									if (strlen($yearPart) == 2) {

										if ($dateFlag != 1) {
											$date = Carbon::createFromFormat('m-d-y', $record['dob']);
										} else {

											$date = Carbon::createFromFormat('m/d/y', $record['dob']);
										}


										if ($date->year > date('Y') + 10) {
											$date = $date->subCentury();
										}
									} elseif (strlen($yearPart) == 4) {
										$date  = $record['dob'];
									}
								}
								$record['dob'] = date('Y-m-d', strtotime($date));
								if ($record['dob'] == '1969-12-31') {
									try {
										$record['dob'] = Utility::parseFlexibleDate($date);
									} catch (\Exception $e) {
										return response()->json(['status' => false, 'message' => 'Error during import: Invalid date']);
									}
								}
							} else {
								$record['dob'] = NULL;
							}
							if (!empty($record['mobile'])) {
								$record['mobile'] = Common::normalizePhoneNumberdate($record['mobile']);
							}
							if (!empty($record['phone'])) {
								$record['phone'] = Common::normalizePhoneNumberdate($record['phone']);
							}
							$record['ssn'] = str_replace('-', '', $record['ssn']);

							// Check for duplicates using selected unique fields
							$uniqueFields = $request->input('unique_fields', []);
							$existingQuery = $this->findExistingRecordByUniqueFields($record, $request->agency_id, $uniqueFields, 'first');
							$existing = $existingQuery ? $existingQuery : null;

							// Check duplicate ssn
							$record['import_flag'] = 1;
							$agencyData = [];
							if ($existing) {
								// Update
								// $this->hubRecordService->update($record, ['id' => $existing->id]);
								$checkagencyData = $this->hubRecordAgencyService->getAgencyData($existing->id, $request->agency_id);
								if (empty($checkagencyData)) {
									$agencyData = array(
										'hub_record_id' => $existing->id,
										'agency_id' => $request->agency_id,
										'status' => 'active',
										'hire_date' => !empty($record['hire_date']) ? date('Y-m-d H:i:s', strtotime($record['hire_date'])) : NULL,
										'work_contact' => $record['work_contact'] ?? '',
										'work_email' => $record['work_email'] ?? '',
										'employee_code' => $record['employee_code'] ?? '',
										'member_id' => $record['member_id'] ?? '',
										'last_worked_date' => !empty($record['last_worked_date']) ? date('Y-m-d H:i:s', strtotime($record['last_worked_date'])) : NULL,
									);
									$this->hubRecordAgencyService->save($agencyData);
								} else {
									$agencyData = array(
										'hub_record_id' => $existing->id,
										'agency_id' => $request->agency_id,
										'status' => 'active',
										'hire_date' => !empty($record['hire_date']) ? date('Y-m-d H:i:s', strtotime($record['hire_date'])) : NULL,
										'work_contact' => $record['work_contact'] ?? '',
										'work_email' => $record['work_email'] ?? '',
										'employee_code' => $record['employee_code'] ?? '',
										'member_id' => $record['member_id'] ?? '',
										'last_worked_date' => !empty($record['last_worked_date']) ? date('Y-m-d H:i:s', strtotime($record['last_worked_date'])) : NULL,
									);
									$this->hubRecordAgencyService->update($agencyData, array('hub_record_id' => $existing->id, 'agency_id' => $request->agency_id));
								}
								$updated++;
								$ipaddress = Utility::getIP();
								$insertLog = [
									'type' => 'Hub record activated',
									'link' => url('/hub-record/'),
									'module' => 'Hub Record',
									'object_id' => $existing->id,
									'message' => $auth->first_name . ' ' . $auth->last_name . ' has activate Hub Record',
									'ip' => $ipaddress,
								];
								HubLogsService::save($insertLog);
							} else {
								$record['full_name'] = $record['first_name'] . ' ' . $record['last_name'];
								$checkSSN = $this->hubRecordService->checkDuplicateSSN($record);
								if ($checkSSN) {
									if ($record['ssn'] != '') {
										$dublicateRecords++;
									}
									$errors[] = "For Row $i: SSN is duplicate.";
									// $skipped++;
									// continue;
								}
								// Insert
								$recordInsId = $this->hubRecordService->save($record);
								// Insert log into agency
								$agencyData = array(
									'hub_record_id' => $recordInsId,
									'agency_id' => $request->agency_id,
									'status' => 'active',
									'hire_date' => date('Y-m-d H:i:s', strtotime($record['hire_date'])),
									'work_contact' => $record['work_contact'] ?? '',
									'work_email' => $record['work_email'] ?? '',
									'employee_code' => $record['employee_code'] ?? '',
									'member_id' => $record['member_id'] ?? '',
									'last_worked_date' => !empty($record['last_worked_date']) ? date('Y-m-d H:i:s', strtotime($record['last_worked_date'])) : NULL,
								);
								$this->hubRecordAgencyService->save($agencyData);
								$imported++;
								$ipaddress = Utility::getIP();
								$insertLog = [
									'type' => 'Hub record created',
									'link' => url('/hub-record/'),
									'module' => 'Hub Record',
									'object_id' => $recordInsId,
									'message' => $auth->first_name . ' ' . $auth->last_name . ' has created Hub Record',
									'ip' => $ipaddress,
								];
								HubLogsService::save($insertLog);
							}

							// Update progress every 10 records
							if ($i % 10 === 0) {
								$this->hubRecordImportLogService->updateImportProgress($logId, [
									'successful_records' => $imported,
									'failed_records' => $skipped,
									'updated_records' => $updated
								]);
							}
							$importedRecords[] = $agencyData;
						}
					}
					if ($flag != (count($rows) - 1)) {
						// Get all existing active records
						$existingRecords = $this->hubRecordAgencyService->getAllRecord($request->agency_id);
						// Create a fast lookup map: hub_record_id + agency_id
						$importedMap = [];
						foreach ($importedRecords as $row) {
							$importedMap[$row['hub_record_id'] . '-' . $row['agency_id']] = true;
						}
						// Compare and update status
						foreach ($existingRecords as $existingRecord) {

							$key = $existingRecord->hub_record_id . '-' . $existingRecord->agency_id;

							if (!isset($importedMap[$key])) {
								// If record not found in import, update status to deactivate
								$existingRecord->status = 'deactivated';
								$existingRecord->deactivated_date = date('Y-m-d H:i:s');
								$existingRecord->deactivated_by = auth()->user()->id;
								$existingRecord->save();
								$deactivated++;

								$ipaddress = Utility::getIP();
								$insertLog = [
									'type' => 'Hub record agency deactivated',
									'link' => url('/hub-record/'),
									'module' => 'Hub Record',
									'object_id' => $existingRecord->hub_record_id,
									'message' => $auth->first_name . ' ' . $auth->last_name . ' has deactivate Hub Record',
									'ip' => $ipaddress,
								];
								HubLogsService::save($insertLog);
							}
						}
					}
				} elseif ($request->input('add_remove') == 'add') {
					for ($i = 1; $i < count($rows); $i++) {

						$row = $rows[$i];
						if (count(array_filter($row)) == 0) {
							$flag++;
							$errors[] = "For Row $i: The following required columns are missing from your file: [First name, Last name, Mobile, Date Of Birth and Gender]. Please upload a valid file.";
							$skipped++;
						} // skip empty rows
						else {
							$record = [];
							foreach ($colIndex as $field => $idx) {
								$record[$field] = isset($row[$idx]) ? trim($row[$idx]) : null;
							}

							if (empty($record['first_name']) || empty($record['last_name'] || empty($record['mobile']) || empty($record['dob']) || empty($record['gender'])) || ($request->filetype == "master_file" && empty($record['company_name']))) {
								if (($request->filetype == "master_file" && empty($record['company_name']))) {

									$errors[] = "For Row $i: The following required columns are missing from your file: [First name, Last name, Mobile, Date Of Birth, SSN,Gender and Company name]. Please upload a valid file.";
								} else {

									$errors[] = "For Row $i: The following required columns are missing from your file: [First name, Last name, Mobile, Date Of Birth, SSN and Gender]. Please upload a valid file.";
								}

								$skipped++;
								continue;
							}
							if ($request->filetype == "master_file" && !empty($record['company_name'])) {
								$agencyDetails = $this->hubCompanyService->getAllAgencyPluck($record['company_name']);
								if (array_key_first($agencyDetails->toArray())) {
									$request->agency_id = array_key_first($agencyDetails->toArray());
								} else {
									$errors[] = "For Row $i: Company name '" . $record['company_name'] . "' not found. Please upload a valid file.";
									$skipped++;
									continue;
								}
							}

							$existing = null;
							// Format DOB
							if (!empty($record['dob'])) {

								$dateStr = $record['dob'];
								$parts = explode('-', $dateStr);

								$exPort = "";
								$dateFlag = 0;
								if (isset($parts[2])) {
									$exPort = $parts[2];
								} else {

									$parts = explode('/', $dateStr);
									$dateFlag = 1;
									$exPort = $parts[2];
								}

								$yearPart = trim($exPort);

								if ($yearPart != "") {

									if (strlen($yearPart) == 2) {

										if ($dateFlag != 1) {
											$date = Carbon::createFromFormat('m-d-y', $record['dob']);
										} else {

											$date = Carbon::createFromFormat('m/d/y', $record['dob']);
										}


										if ($date->year > date('Y') + 10) {
											$date = $date->subCentury();
										}
									} elseif (strlen($yearPart) == 4) {
										$date  = $record['dob'];
									}
								}
								$record['dob'] = date('Y-m-d', strtotime($date));
								if ($record['dob'] == '1969-12-31') {
									$record['dob'] = Utility::parseFlexibleDate($date);
								}
							} else {
								$record['dob'] = NULL;
							}
							if (!empty($record['mobile'])) {
								$record['mobile'] = Common::normalizePhoneNumberdate($record['mobile']);
							}
							if (!empty($record['phone'])) {
								$record['phone'] = Common::normalizePhoneNumberdate($record['phone']);
							}
							$record['ssn'] = str_replace('-', '', $record['ssn']);

							// Check for duplicates using selected unique fields
							$uniqueFields = $request->input('unique_fields', []);
							$existingQuery = $this->findExistingRecordByUniqueFields($record, $request->agency_id, $uniqueFields, 'first');
							$existing = $existingQuery ? $existingQuery : null;

							// Check duplicate ssn
							$record['import_flag'] = 1;
							$agencyData = [];
							if ($existing) {
								// Update
								// $this->hubRecordService->update($record, ['id' => $existing->id]);
								$checkagencyData = $this->hubRecordAgencyService->getAgencyData($existing->id, $request->agency_id);
								if (empty($checkagencyData)) {
									$agencyData = array(
										'hub_record_id' => $existing->id,
										'agency_id' => $request->agency_id,
										'status' => 'active',
										'hire_date' => !empty($record['hire_date']) ? date('Y-m-d H:i:s', strtotime($record['hire_date'])) : NULL,
										'work_contact' => $record['work_contact'] ?? '',
										'work_email' => $record['work_email'] ?? '',
										'employee_code' => $record['employee_code'] ?? '',
										'member_id' => $record['member_id'] ?? '',
										'last_worked_date' => !empty($record['last_worked_date']) ? date('Y-m-d H:i:s', strtotime($record['last_worked_date'])) : NULL,
									);
									$this->hubRecordAgencyService->save($agencyData);
								} else {
									$agencyData = array(
										'hub_record_id' => $existing->id,
										'agency_id' => $request->agency_id,
										'status' => 'active',
										'hire_date' => !empty($record['hire_date']) ? date('Y-m-d H:i:s', strtotime($record['hire_date'])) : NULL,
										'work_contact' => $record['work_contact'] ?? '',
										'work_email' => $record['work_email'] ?? '',
										'employee_code' => $record['employee_code'] ?? '',
										'member_id' => $record['member_id'] ?? '',
										'last_worked_date' => !empty($record['last_worked_date']) ? date('Y-m-d H:i:s', strtotime($record['last_worked_date'])) : NULL,
									);

									$this->hubRecordAgencyService->update($agencyData, array('hub_record_id' => $existing->id, 'agency_id' => $request->agency_id));
								}
								$updated++;
								$ipaddress = Utility::getIP();
								$insertLog = [
									'type' => 'Hub record activated',
									'link' => url('/hub-record/'),
									'module' => 'Hub Record',
									'object_id' => $existing->id,
									'message' => $auth->first_name . ' ' . $auth->last_name . ' has activate Hub Record',
									'ip' => $ipaddress,
								];
								HubLogsService::save($insertLog);
							} else {
								$checkSSN = $this->hubRecordService->checkDuplicateSSN($record);
								if ($checkSSN) {
									if ($record['ssn'] != '') {
										$dublicateRecords++;
									}
									$errors[] = "For Row $i: SSN is duplicate.";
									// $skipped++;
									// continue;
								}
								$record['full_name'] = $record['first_name'] . ' ' . $record['last_name'];
								// Insert
								$recordInsId = $this->hubRecordService->save($record);
								// Insert log into agency
								$agencyData = array(
									'hub_record_id' => $recordInsId,
									'agency_id' => $request->agency_id,
									'status' => 'active',
									'hire_date' => date('Y-m-d H:i:s', strtotime($record['hire_date'])),
									'work_contact' => $record['work_contact'] ?? '',
									'work_email' => $record['work_email'] ?? '',
									'employee_code' => $record['employee_code'] ?? '',
									'member_id' => $record['member_id'] ?? '',
									'last_worked_date' => !empty($record['last_worked_date']) ? date('Y-m-d H:i:s', strtotime($record['last_worked_date'])) : NULL,
								);
								$this->hubRecordAgencyService->save($agencyData);
								$imported++;
								$ipaddress = Utility::getIP();
								$insertLog = [
									'type' => 'Hub record created',
									'link' => url('/hub-record/'),
									'module' => 'Hub Record',
									'object_id' => $recordInsId,
									'message' => $auth->first_name . ' ' . $auth->last_name . ' has created Hub Record',
									'ip' => $ipaddress,
								];
								HubLogsService::save($insertLog);
							}

							// Update progress every 10 records
							if ($i % 10 === 0) {
								$this->hubRecordImportLogService->updateImportProgress($logId, [
									'successful_records' => $imported,
									'failed_records' => $skipped,
									'updated_records' => $updated
								]);
							}
							$importedRecords[] = $agencyData;
						}
					}
				} elseif ($request->input('add_remove') == 'remove') {
					for ($i = 1; $i < count($rows); $i++) {
						$row = $rows[$i];
						if (count(array_filter($row)) == 0) {
							$flag++;
							$errors[] = "For Row $i: The following required columns are missing from your file: [First name, Last name, Mobile, Date Of Birth and Gender]. Please upload a valid file.";
							$skipped++;
						} // skip empty rows
						$record = [];
						foreach ($colIndex as $field => $idx) {
							$record[$field] = isset($row[$idx]) ? trim($row[$idx]) : null;
						}
						if ($request->filetype == "master_file" && !empty($record['company_name'])) {
							$agencyDetails = $this->hubCompanyService->getAllAgencyPluck($record['company_name']);

							if (array_key_first($agencyDetails->toArray())) {
								$request->agency_id = array_key_first($agencyDetails->toArray());
							} else {
								$errors[] = "For Row $i: Company name '" . $record['company_name'] . "' not found. Please upload a valid file.";
								$skipped++;
								continue;
							}
						}
						//else {
						$record = [];
						foreach ($colIndex as $field => $idx) {
							$record[$field] = isset($row[$idx]) ? trim($row[$idx]) : null;
						}
						if (!empty($record['dob'])) {
							//$record['dob'] = date('Y-m-d', strtotime($record['dob']));

							$dateStr = $record['dob'];
							$parts = explode('-', $dateStr);

							$exPort = "";
							$dateFlag = 0;
							if (isset($parts[2])) {
								$exPort = $parts[2];
							} else {

								$parts = explode('/', $dateStr);
								$dateFlag = 1;
								$exPort = $parts[2];
							}

							$yearPart = trim($exPort);

							if ($yearPart != "") {

								if (strlen($yearPart) == 2) {

									if ($dateFlag != 1) {
										$date = Carbon::createFromFormat('m-d-y', $record['dob']);
									} else {

										$date = Carbon::createFromFormat('m/d/y', $record['dob']);
									}


									if ($date->year > date('Y') + 10) {
										$date = $date->subCentury();
									}
								} elseif (strlen($yearPart) == 4) {
									$date  = $record['dob'];
								}
							}
							$record['dob'] = date('Y-m-d', strtotime($date));
							if ($record['dob'] == '1969-12-31') {
								$record['dob'] = Utility::parseFlexibleDate($date);
							}
						} else {
							$record['dob'] = NULL;
						}
						if (!empty($record['mobile'])) {
							$record['mobile'] = Common::normalizePhoneNumberdate($record['mobile']);
						}
						if (!empty($record['phone'])) {
							$record['phone'] = Common::normalizePhoneNumberdate($record['phone']);
						}
						$record['ssn'] = str_replace('-', '', $record['ssn']);

						// Check for duplicates using selected unique fields
						$uniqueFields = $request->input('unique_fields', []);

						$existingQuery = $this->findExistingRecordByUniqueFields($record, $request->agency_id, $uniqueFields, 'get');
						$existingRecords = $existingQuery ? $existingQuery : collect();
						foreach ($existingRecords as $existingR) {

							$existingRecord = HubRecordAgency::where('hub_record_id', $existingR->id)->where('agency_id', $request->agency_id)->first();

							if (isset($existingRecord->id)) {

								HubRecordAgency::where('id', $existingRecord->id)->update(['status' => 'deactivated', 'deactivated_date' => date('Y-m-d H:i:s'), 'deactivated_by' => auth()->user()->id]);

								$ipaddress = Utility::getIP();
								$insertLog = [
									'type' => 'Hub record agency deactivated',
									'link' => url('/hub-record/'),
									'module' => 'Hub Record',
									'object_id' => $existingR->id,
									'message' => $auth->first_name . ' ' . $auth->last_name . ' has deactivate Hub Record',
									'ip' => $ipaddress,
								];
								HubLogsService::save($insertLog);

								$deactivated++;
							}
						}
						//}
					}
				}


				// Complete import logging
				$this->hubRecordImportLogService->completeImport($logId, [
					'successful_records' => $imported,
					'failed_records' => $skipped,
					'updated_records' => $updated,
					'deactivate_records' => $deactivated,
					'duplicate_ssn_records' => $dublicateRecords
				], !empty($errors) ? json_encode($errors) : null);
			}
			$summary = [
				'imported' => $imported,
				'skipped' => $skipped,
				'updated' => $updated,
				'deactivated' => $deactivated,
				'errors' => $errors,
				'dublicate SSN Records' => $dublicateRecords
			];

			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Hub Record data imported',
				'link' => url('/hub-record/'),
				'module' => 'Hub Record Import',
				'message' => '"Import completed. "' . $imported . '" records were imported, " .
           "' . $updated . '" updated, "' . $skipped . '" skipped, " .
           "' . $deactivated . '" deactivated.',
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);
			if ($imported == 0 && $skipped == 0 && $updated == 0 && $deactivated == 0) {
				return response()->json(['status' => false, 'summary' => $summary, 'message' => $errors]);
			} else {
				return response()->json(['status' => true, 'summary' => $summary]);
			}
		} catch (\Exception $e) {
			// Log import failure
			return response()->json(['status' => false, 'message' => 'Error during import: ' . $e->getMessage()]);
		}
	}

	// ... existing code ...

	public function getImportLogs(Request $request)
	{
		$search_data = [
			'file_name' => $request->input('file_name'),
			'status' => $request->input('status'),
			'created_date' => $request->input('date_range')
		];
		$data['query'] = $this->hubRecordImportLogService->getImportLogs($search_data);
		return view("hubRecord/hub_record_import", $data);
	}

	public function getBasicDeatils(Request $request)
	{
		$data = $this->hubRecordService->getDetailById($request->id);
		return response()->json(['status' => true, 'data' => $data]);
	}

	public function hubRecordWiselogs(Request $request)
	{
		$id = request('hub_record_id');
		$data['user'] = auth()->user();
		$data['logList'] = HubLogsService::getAllHubLogs($id, 'Hub Record');
		return view("hubRecord.hub_log_ajax_list", $data);
	}

	public function checkDuplicateRecord(Request $request)
	{
		$response = $this->hubRecordAgencyService->getDetailsByIdWhitoutAgency($request->hub_record_id);
		return response()->json(['success' => true, 'data' => $response]);
	}

	public function getDependentData(Request $request)
	{
		$hub_id = $request->hub_record_id;
		$data['query'] = $this->hubRecordDependentService->getDependentData($hub_id);
		return view("hubRecord/hub_dependent_data", $data);
	}

	public function saveHubDependentData(Request $request)
	{
		$user = auth()->user();

		if ($user->view_ssn_hub == 1) {
			$validator = Validator::make($request->all(), [
				'first_name' => 'required',
				'last_name' => 'required',
				'mobile' => 'required',
				'dob' => 'required',
				'ssn' => 'required',
				'employee_code' => 'required'
			]);
		} else {
			$validator = Validator::make($request->all(), [
				'first_name' => 'required',
				'last_name' => 'required',
				'mobile' => 'required',
				'dob' => 'required',
				'employee_code' => 'required'
			]);
		}

		if ($validator->fails()) {
			return response()->json(['status' => false, 'error_msg' =>  $validator->errors()->all()[0]], 422);
		} else {
			$oldData = [];
			$dob = NULL;
			if ($request->dob != "") {
				$dob = Utility::convertMdyToYmdUsingCarbonbySlash($request->dob);
			}

			$hire_date = NULL;
			$work_contact = NULL;
			$work_email = NULL;
			$last_worked_date = NULL;
			if ($request->hire_date != "") {
				$hire_date = Utility::convertMdyToYmdUsingCarbonbySlash($request->hire_date);
			}
			if ($request->work_contact != "") {
				$work_contact = Common::normalizePhoneNumberdate($request->work_contact);
			}
			if ($request->work_email != "") {
				$work_email = $request->work_email;
			}
			if ($request->last_worked_date != "") {
				$last_worked_date = Utility::convertMdyToYmdUsingCarbonbySlash($request->last_worked_date);
			}

			$data = [
				'first_name' => $request->first_name,
				'middle_name' => $request->middle_name,
				'last_name' => $request->last_name,
				'email' => $request->email,
				'dob' => $dob,
				'phone' => Common::normalizePhoneNumberdate($request->phone),
				'mobile' => Common::normalizePhoneNumberdate($request->input('mobile')),
				'gender' => $request->gender,
				'ssn' => str_replace('-', '', $request->ssn),
				'address1' => $request->address1,
				'address2' => $request->address2,
				'state' => $request->state,
				'city' => $request->city,
				'zip_code' => $request->zip_code,
				'county' => $request->county,
				'relation_ship' => $request->spouse,
				'is_dependent' => 'Y'
			];

			$getDuplicate = $this->hubRecordService->getDuplicateSearch($data);
			if (isset($getDuplicate->id) && !empty($getDuplicate->id)) {

				$this->hubRecordService->update(array('address1' => $request->address1, 'address2' => $request->address2, 'state' => $request->state, 'city' => $request->city, 'zip_code' => $request->zip_code, 'county' => $request->county, 'relation_ship' => $request->relation_ship), array('id' => $getDuplicate->id));
				$oldDatas = $this->hubRecordService->getDetailById($getDuplicate->id);
				$oldData = $oldDatas->toArray();
				$insert = $getDuplicate->id;
				$checkAgencyDuplicate = $this->hubRecordAgencyService->getAgencyData($request->hub_record_id, $request->agency_id);

				$agencyData = array(
					'hub_record_id' => $request->hub_record_id,
					'agency_id' => $request->agency_id,
					'status' => 'active',
					'employee_code' => $request->employee_code,
					'member_id' => $request->member_id
				);
				$agencyData['hire_date'] = $hire_date;
				$agencyData['work_contact'] = $work_contact;
				$agencyData['work_email'] = $work_email;
				$agencyData['last_worked_date'] = $last_worked_date;
				if (isset($checkAgencyDuplicate->id)) {
					$saveHubAgencyId =  $this->hubRecordAgencyService->update($agencyData, array('id' => $checkAgencyDuplicate->id));
					$saveHubAgencyId = $checkAgencyDuplicate->id;
				} else {
					$saveHubAgencyId =  $this->hubRecordAgencyService->save($agencyData);
				}
				$checkForDepentAdded = $this->hubRecordDependentService->checkDependentAddOrNot($request->hub_record_id, $request->agency_id, $saveHubAgencyId, $getDuplicate->id);
				if (isset($checkForDepentAdded->id)) {
					return response()->json(['status' => false, 'error_msg' => 'User already exist.'], 409);
				} else {
					$this->hubRecordDependentService->save(array('hub_record_id' => $request->hub_record_id, 'agency_id' => $request->agency_id, 'dependent_id' => $insert, 'hub_agency_id' => $saveHubAgencyId));
				}
			} else {
				$checkDuplicateSSN = $this->hubRecordService->checkDuplicateSSN($data);
				if (isset($checkDuplicateSSN->id) && $checkDuplicateSSN->id != "") {
					return response()->json(['status' => false, 'error_msg' => 'The SSN already exists'], 409);
				}
				$insert = $this->hubRecordService->save($data);

				$agencyData = array(
					'hub_record_id' => $insert,
					'agency_id' => $request->agency_id,
					'status' => 'active',
					'employee_code' => $request->employee_code,
					'member_id' => $request->member_id
				);
				$agencyData['hire_date'] = $hire_date;
				$agencyData['work_contact'] = $work_contact;
				$agencyData['work_email'] = $work_email;
				$agencyData['last_worked_date'] = $last_worked_date;

				$saveHubAgencyId =  $this->hubRecordAgencyService->save($agencyData);

				$this->hubRecordDependentService->save(array('hub_record_id' => $request->hub_record_id, 'agency_id' => $request->agency_id, 'dependent_id' => $insert, 'hub_agency_id' => $saveHubAgencyId));
			}
			if ($insert) {
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Hub Record dependent created',
					'link' => url('/hub-record/add'),
					'module' => 'Hub Record',
					'object_id' => $insert,
					'message' => $user->first_name . ' ' . $user->last_name . ' has added Hub Record dependent',
					'old_response' => serialize($oldData),
					'new_response' => serialize($data),
					'ip' => $ipaddress,
				];
				HubLogsService::save($insertLog);
				return response()->json(['status' => true, 'error_msg' => 'New Hub Created successfully.'], 200);
			} else {
				return response()->json(['status' => false, 'error_msg' => 'Sorry, something went wrong. Please try again.'], 500);
			}
		}
	}

	public function getAgencyOtherData(Request $request)
	{
		$agency_id = $request->agency_id;
		$hub_record_id = $request->hub_record_id;
		// $query = $this->hubRecordDependentService->getAllAgency($hub_record_id);
		// if(count($query) >0){
		// 	$response = $query;
		// }else{

		// }
		$response = $this->hubRecordAgencyService->getAgencyDetails($hub_record_id, $agency_id);

		return response()->json(['success' => true, 'data' => $response]);
	}

	public function createHubRecord(Request $request)
	{
		$data['user'] = $auth = auth()->user();
		if (auth()->user()->agency_fk == "" && $auth->show_hub == 1) {
			$angecyList = Cache::get('patient_master_locations', function () {
				return HubCompany::getAgencyListHub();
			}, 10);
			$data['agencyList'] = $angecyList;

			return view("hubRecord/hub_create", $data);
		} else {
			abort(404);
		}
	}

	public  function searchHubRecord(Request $request)
	{
		$query = $this->hubRecordService->searchUserData($request->all());

		$final = [];
		if (count($query) > 0) {
			foreach ($query as $val) {
				$temp = [];
				$temp['id'] = $val->id;
				$date = "";
				if ($val->dob != NULL) {
					$date = date('m/d/Y', strtotime($val->dob));
				}
				$temp['name'] = $val->first_name . ' ' . $val->last_name . ' - ' . $val->agency_name . ' - ' . ucfirst($val->gender) . ' - ' . $date;
				$final[] = $temp;
			}
		}
		return json_encode($final);
	}

	public function createHubDetails(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'search_patient' => 'required',
			'create_hire_date' => 'required',
			'create_work_contact' => 'required',
			'create_work_email' => 'required|email'
		], [
			'search_patient.required'      => 'Please select a employee.',
			'create_hire_date.required'    => 'The hire date is required.',
			'create_work_contact.required' => 'The work contact is required.',
			'create_work_email.required'   => 'The work email is required.',
			'create_work_email.email'      => 'Please enter a valid email address.',
		]);

		if ($validator->fails()) {
			return redirect("/create-hub-record")
				->withErrors($validator, 'add_agency')
				->withInput();
		} else {
			$agencyData = array(
				'hub_record_id' => $request->search_patient,
				'agency_id' => $request->agency_id,
				'status' => 'active'

			);
			if ($request->create_hire_date != "") {
				$agencyData['hire_date'] = date('Y-m-d', strtotime($request->create_hire_date));
			}
			if ($request->create_work_contact != "") {
				$agencyData['work_contact'] = Common::normalizePhoneNumberdate($request->create_work_contact);
			}
			if ($request->create_work_email != "") {
				$agencyData['work_email'] = $request->create_work_email;
			}
			if ($request->create_last_worked_date != "") {
				$agencyData['last_worked_date'] = date('Y-m-d', strtotime($request->create_last_worked_date));
			}
			$query = $this->hubRecordAgencyService->getAgencyData($request->search_patient, $request->agency_id);
			if (isset($query->id)) {
				$save =	$this->hubRecordAgencyService->update(array('hire_date' => date('Y-m-d', strtotime($request->create_hire_date)), 'work_contact' => Common::normalizePhoneNumberdate($request->create_work_contact), 'work_email' => $request->create_work_email, 'last_worked_date' => date('Y-m-d', strtotime($request->create_last_worked_date))), array('id' => $query->id));
			} else {
				$save =	$this->hubRecordAgencyService->save($agencyData);
			}

			if ($save) {
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Hub Record created',
					'link' => url('/update-remaining-hub-details'),
					'module' => 'Hub Record',
					'object_id' => $request->search_patient,
					'message' => $user->first_name . ' ' . $user->last_name . ' has added Hub Record',
					'new_response' => serialize($agencyData),
					'ip' => $ipaddress,
				];
				HubLogsService::save($insertLog);
				Session::flash('success', 'Hub record has been added successfully');
				return redirect('/hub-record');
			} else {
				Session::flash('error', 'Sorry, something went wrong. Please try again.');
				return redirect('/create-hub-record');
			}
		}
	}

	public function updateAgencyWiseOtherDetails(Request $request)
	{
		$user = auth()->user();

		$validator = Validator::make($request->all(), [
			'employee_code' => 'required',
			'work_email' => 'required',
			'hire_date' => 'required',
			'work_contact' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['status' => false, 'error_msg' =>  $validator->errors()->all()[0]], 422);
		} else {

			$query = $this->hubRecordAgencyService->getAgencyData($request->hub_record_id, $request->agency_id);

			$last_worked_date = NULL;
			if ($request->last_worked_date != "") {
				$last_worked_date = date('Y-m-d', strtotime($request->last_worked_date));
			}
			$data = array(
				'hire_date' => date('Y-m-d', strtotime($request->hire_date)),
				'work_contact' => Common::normalizePhoneNumberdate($request->work_contact),
				'work_email' => $request->work_email,
				'employee_code' => $request->employee_code,
				'last_worked_date' => $last_worked_date,
				'member_id' => $request->member_id

			);
			$update = $this->hubRecordAgencyService->update($data, array('hub_record_id' => $request->hub_record_id, 'agency_id' => $request->agency_id));

			$postData = $request->all();
			unset($postData['_token']);

			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Other Details Update',
				'link' => url('/hub-record/view/') . '/' . $request->hub_record_id,
				'module' => 'Hub Record',
				'object_id' => $request->hub_record_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has updated additional details of the Hub Record.',
				'new_response' => serialize($postData),
				'old_response' => serialize($query->toArray()),
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'Other details successfully updated.', 'data' => $postData], 200);
		}
	}

	public function nyBestAgency()
	{

		return	$nybestAgency = $this->createAppointmentService->getAllAgencyList();
	}

	public function saveHubNybestData(Request $request, $id)
	{

		$auth = auth()->user();

		$validator = Validator::make($request->all(), [
			'agency' => 'required',
			'service' => 'required',
			'type' => 'required'
		]);
		if ($validator->fails()) {
			return response()->json(['status' => false, 'error_msg' =>  $validator->errors()->all()[0]], 422);
		}

		$hubRecord =	$this->hubRecordService->getDetailById($id);


		if ($hubRecord) {
			$hubRecordPhone = $hubRecord->phone;
			if (empty($hubRecord->phone)) {
				$hubRecordPhone = $hubRecord->mobile;
			}
			$data = [
				'first_name' => $hubRecord->first_name,
				'middle_name' => $hubRecord->middle_name,
				'last_name' => $hubRecord->last_name,
				'type' => $request->type,
				'dob' => $hubRecord->dob,
				'phone' => $hubRecord->mobile,
				'mobile' => $hubRecordPhone,
				'agency_id' => $request->agency,
				'gender' => $hubRecord->gender,
				'address1' => $hubRecord->address1,
				'address2' => $hubRecord->address2,
				'state' => $hubRecord->state,
				'city' => $hubRecord->city,
				'zip_code' => $hubRecord->zip_code,
				'county' => $hubRecord->county,
				'ssn' => $hubRecord->ssn,
				'email' => $hubRecord->email,
				'referral_type' => "Hub",
				'other_gender' => $hubRecord->other_gender,
				'service_id' => implode(',', $request->service),
				'platform_type' => "Hub",
				'hub_id' => $id,
				'company_id' => $request->hub_agency_id,
				'created_by' => $auth['id'],
				'remarks' => $request->remarks,
				'booking_date' => $request->booking_date ? date('Y-m-d', strtotime($request->booking_date)) : null,
			];

			$storeData = $this->createAppointmentService->saveAppointment($data);

			if ($storeData->getData()) {
				$storeData = $storeData->getData();
				$patientId = '';
				$hubId = '';
				if ($storeData->status) {

					if (isset($storeData->data->patient_id)) {
						$patientId = $storeData->data->patient_id;
					}
					if (isset($storeData->data->patient_id)) {
						$hubId = $storeData->data->hub_id;
					}
					$recordAgencyData = ['nybest_id' => $patientId, 'nybest_created_at' => date('Y-m-d H:i:s'), 'nybest_created_by' => $auth['id']];

					$this->hubRecordAgencyService->update($recordAgencyData, ['hub_record_id' => $id, 'agency_id' => $request->hub_agency_id]);

					$getAgencyData = Agency::getAllDetailsbyAgencyId($request->agency);
					$serviceName = Master::getRecordById($request->service);
					$serviceName = array_column($serviceName->toArray(), 'name');

					$emailData = [
						'first_name' => $hubRecord->first_name,
						'last_name' => $hubRecord->last_name,
						'agencyname' => $getAgencyData->agency_name ?? '',
						'insert' => $hubId,
						'type' => $request->type,
						'service' => implode(',', $serviceName),
					];
					$this->sendHubApomentMail($emailData);
					$ipaddress = Utility::getIP();
					$insertLog = [
						'type' => 'Hub record NyBest Medical Request',
						'link' => url('/hub-record/'),
						'module' => 'Hub Record',
						'object_id' => $id,
						'message' => $auth->first_name . ' ' . $auth->last_name . ' has book NyBest Medical Request for Hub Record',
						'ip' => $ipaddress,
					];
					HubLogsService::save($insertLog);
					return  response()->json(['status' => true, 'error_msg' => 'Record Created successfully.'], 200);
				}
				return  response()->json(['status' => false, 'error_msg' => 'Something happened. Try again'], 500);
			}
			return  response()->json(['status' => false, 'error_msg' => 'Something happened. Try again'], 500);
		}
		return  response()->json(['status' => false, 'error_msg' => 'Something happened. Try again'], 500);
	}

	public function nyBestList(Request $request)
	{
		$hubId = $request->hub_record_id;
		$companyId = $request->hub_agency_id;
		$data['user'] = auth()->user();
		$data['list'] = $this->createAppointmentService->fetchAppointmentsByHubAndCompanyId($hubId, $companyId);

		return view("hubRecord.hub_nybest_ajax_list", $data);
	}
	public function smsSendToDependent($id)
	{

		$getRecord = $this->hubRecordService->getDetailById($id);
		$data['token'] =   $this->random_string(20);
		$data['token_expired_time'] = now()->addHours(24);

		$message = "Hello {$getRecord->first_name} {$getRecord->last_name}, ";
		$message .= "This message is from Nybest Medical. Please click the link below to add your dependent. ";
		$message .= "Your link: " . url('/hub-record/add-dependent?token=' . $data['token'] . '&id=' . sha1($getRecord->id)) . ". ";
		$message .= "This link will be valid for 24 hours.";

		$sms = Common::sendTwillioSms($getRecord->mobile, $message);
		if ($sms || true) {
			HubRecord::where('id', $getRecord->id)->update($data);
			Session::flash('success', 'SMS sent successfully.');
			return redirect()->back();
		}
	}

	public function addDependent()
	{
		$token = request('token');
		$id = request('id');
		if (!$token || !$id) {
			Session::flash('error', 'Invalid link.');
			return view('errorExpire');
			// return redirect('/');
		}

		$record = HubRecord::where('id', '=', $id)->where('token', $token)->first();
		if (!$record || $record->token_expired_time < now()) {
			Session::flash('error', 'Link expired or invalid.');
			return view('errorExpire');
			// return redirect('/');
		}
		Session::put('hub_record_id', $record->id);
		$data['user'] = auth()->user();
		$data['hub_record_id'] = $record->id;
		$data['record'] = $record;
		return view("hubRecord.hub_add_dependent", $data);
	}

	function random_string($length)
	{
		$key = '';
		$keys = array_merge(range(0, 9), range('a', 'z'));

		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}

		return $key;
	}

	public function updateStatus(Request $request)
	{
		$user = auth()->user();
		$data['id'] = $id = $request->hub_id;
		$hubAgencyId = $request->hub_agency_id;
		$data = array(
			'status' => $request->status,
			'deactivated_date' => ($request->status == 'deactivated') ? date('Y-m-d H:i:s') : NULL,
			'deactivated_by' => ($request->status == 'deactivated') ? $user->id : NULL,
		);
		// $this->hubRecordService->update($data, array('id' => $id));
		$this->hubRecordAgencyService->update($data, array('hub_record_id' => $id, 'agency_id' => $hubAgencyId));
		$this->hubRecordService->update([], ['id' => $id]);
		$getNewData = $this->hubRecordService->getDetailById($id);
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Hub record agency ' . $request->status,
			'link' => url('/hub-record/'),
			'module' => 'Hub Record',
			'object_id' => $request->hub_id,
			'message' => $user->first_name . ' ' . $user->last_name . ' has ' . $request->status . ' Hub Record',
			'ip' => $ipaddress,
		];
		HubLogsService::save($insertLog);
		return response()->json(['status' => true, 'error_msg' => 'Hub Record ' . $request->status . ' successfully.', 'data' => $getNewData], 200);
	}

	public function updateBulkStatus(Request $request)
	{
		$user = auth()->user();
		$data['id'] = $id = $request->hub_record_ids;

		// Convert comma-separated IDs into an array
		$ids = explode(',', $request->hub_record_ids);

		$data = array(
			'status' => $request->status,
			'deactivated_date' => ($request->status == 'deactivated') ? date('Y-m-d H:i:s') : NULL,
			'deactivated_by' => ($request->status == 'deactivated') ? $user->id : NULL,
		);
		$insertLog = [];
		foreach ($ids as $id) {
			$this->hubRecordAgencyService->update($data, ['hub_record_id' => $id]);
			$this->hubRecordService->update([], ['id' => $id]);
			$ipaddress = Utility::getIP();
			$insertLog[] = [
				'type' => 'Hub record agency ' . $request->status,
				'link' => url('/hub-record/'),
				'module' => 'Hub Record',
				'object_id' => $id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has ' . $request->status . ' Hub Record',
				'ip' => $ipaddress,
				'created_at' => date('Y-m-d H:i:s'),
				'created_by' => $user->id,

			];
		}

		HubLogsService::insert($insertLog);

		return response()->json(['status' => true, 'error_msg' => 'Hub Record ' . $request->status . ' successfully.'], 200);
	}

	public function sendHubApomentMail($emailData)
	{
		$messages = Utility::getHtmlContent('email_template.appointment_booking_email', $emailData);

		$getSiteSettingDetails = $this->siteSettingService->getDetails();
		$subject = "Appointment has been Booked";
		try {
			$explode = explode(',', $getSiteSettingDetails->hub_nybest_email);
			if (!empty($explode[0])) {
				foreach ($explode as $email) {
					$mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages) {
						$message->to($email, "")
							->subject($subject)->html($messages);
					});
				}
			}
		} catch (\Throwable $th) {
			//throw $th;
		}
	}

	/**
	 * Find existing record by unique fields for import
	 */
	protected function findExistingRecordByUniqueFields($record, $agencyId, $uniqueFields, $method = 'first')
	{
		// First check hub_record table for duplicates
		$hubRecordQuery = HubRecord::select('hub_record.*', 'hub_record_agency.agency_id', 'hub_record_agency.hire_date', 'hub_record_agency.work_contact', 'hub_record_agency.work_email', 'hub_record_agency.employee_code', 'hub_record_agency.member_id', 'hub_record_agency.id as hub_record_agency_id')->where('deleted_flag', 'N')->join('hub_record_agency', 'hub_record.id', '=', 'hub_record_agency.hub_record_id');
		// ->where('hub_record_agency.agency_id', $agencyId);
		$hubConditions = [];
		foreach ($uniqueFields as $field) {
			if (!empty($record[$field])) {
				if ($field === 'email') {
					$hubConditions[] = ['email', '=', $record[$field]];
				}
				if ($field === 'ssn') {
					$hubConditions[] = ['ssn', '=', $record[$field]];
				}
				if ($field === 'dob') {
					$hubConditions[] = ['dob', '=', $record['dob']];
				}
				if ($field === 'phone' || $field === 'mobile') {
					$hubConditions[] = [$field, '=', Common::normalizePhoneNumberdate($record[$field])];
				}
				if ($field === 'first_name') {
					$hubConditions[] = ['first_name', '=', $record['first_name']];
				}
				if ($field === 'last_name') {
					$hubConditions[] = ['last_name', '=', $record['last_name']];
				}
				if ($field === 'gender') {
					$hubConditions[] = ['gender', '=', $record['gender']];
				} else {
					if (!in_array($field, ['member_id', 'employee_code', 'work_email', 'work_contact'])) {
						// Add other fields as exact match conditions
						$hubConditions[] = [$field, '=', $record[$field]];
					}
				}
			}
		}

		// Check hub_record_agency table for employee-specific unique fields
		$agencyConditions = [];
		foreach ($uniqueFields as $field) {
			if (!empty($record[$field])) {
				if (in_array($field, ['member_id', 'employee_code', 'work_email', 'work_contact'])) {
					$agencyConditions[] = [$field, '=', $record[$field]];
				}
			}
		}

		$existingRecord = null;

		// Check hub_record conditions
		if (!empty($hubConditions)) {

			$hubRecordQuery->where(function ($query) use ($hubConditions) {
				foreach ($hubConditions as $condition) {
					$query->where($condition[0], $condition[1], $condition[2]);
				}
			});

			// ->first();
		}
		if (!empty($agencyConditions)) {
			$hubRecordQuery = $hubRecordQuery->where(function ($query) use ($agencyConditions) {
				foreach ($agencyConditions as $condition) {
					$query->where('hub_record_agency.' . $condition[0], $condition[1], $condition[2]);
				}
			});
		}
		if ($method === 'first') {
			$hubRecordQuery = $hubRecordQuery->first();
		} else {
			$hubRecordQuery = $hubRecordQuery->where('hub_record_agency.agency_id', $agencyId)->where('hub_record_agency.status', 'active')->get();
		}
		return $hubRecordQuery;
	}

	// Clinical methods
	public function getClinicalHtml($type, Request $request)
	{
		try {
			$data['data'] =	$this->hubRecordService->getDetailById($request->id);
			$htmlContent = '';

			if ($type === 'medical_visit') {
				$htmlContent = view('Hubclinical.HubMedicalHistory', $data)->render();
			} elseif ($type === 'medical_note') {
				$htmlContent = view('Hubclinical.HubMedicalNote', $data)->render();
			}

			return response($htmlContent, 200)
				->header('Content-Type', 'text/html');
		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'message' => 'Error loading HTML content: ' . $e->getMessage()
			], 500);
		}
	}

	public function saveClinicalPdf(Request $request)
	{
		ini_set('max_execution_time', 300); // 5 minutes
		try {
			$validator = Validator::make($request->all(), [
				'record_id' => 'required|exists:hub_record,id',
				'pdf_type' => 'required|in:medical_visit,medical_note',
				'name' => 'required|string|max:255'
			]);

			if ($validator->fails()) {
				return response()->json([
					'status' => false,
					'message' => $validator->errors()->first()
				], 422);
			}
			$pdfContent = $pdfName = null;
			// Generate PDF content based on type
			$pdfName = $this->generatePdfContent($request->pdf_type, $request->all());

			if (!$pdfName || !str_ends_with($pdfName, '.pdf')) {
				return response()->json([
					'status' => false,
					'message' => 'Error generating PDF. Please try again.'
				], 500);
			}

			$clinicalRecord = \App\HubClinicalRecord::create([
				'hub_record_id' => $request->record_id,
				'name' => $request->name,
				'pdf_type' => $request->pdf_type,
				'notes' => $request->notes,
				'visit_date' => Utility::convertYMD($request->visit_date),
				'doctor_name' => $request->doctor_name,
				'excuse_from' => Utility::convertYMD($request->excuse_from),
				'excuse_to' => Utility::convertYMD($request->excuse_to),
				'pdf_content' => $pdfContent,
				'created_by' => auth()->id(),
				// Patient Information Fields
				'patient_name' => $request->patient_name,
				'patient_dob' => Utility::convertYMD($request->patient_dob),
				'patient_gender' => $request->patient_gender,
				'patient_address' => $request->patient_address,
				// Medical Form Fields
				'chief_complaint' => $request->chief_complaint,
				'reason_for_visit' => $request->reason_for_visit,
				'history_of_present_illness' => $request->history_of_present_illness,
				'medical_history' => $request->medical_history,
				'current_medications' => $request->current_medications,
				'past_surgical_history' => $request->past_surgical_history,
				'social_history' => $request->social_history,
				// Review of Systems
				'cardiovascular' => $request->cardiovascular,
				'constitutional' => $request->constitutional,
				'ent' => $request->ent,
				'endocrine' => $request->endocrine,
				'gastrointestinal' => $request->gastrointestinal,
				'genitourinary' => $request->genitourinary,
				'musculoskeletal' => $request->musculoskeletal,
				'neurologic' => $request->neurologic,
				'ophthalmologic' => $request->ophthalmologic,
				'psychiatric' => $request->psychiatric,
				'respiratory' => $request->respiratory,
				'skin' => $request->skin,
				// Vitals
				'bp' => $request->bp,
				'pulse' => $request->pulse,
				'allergies' => $request->allergies,
				'resp' => $request->resp,
				'temp' => $request->temp,
				'weight' => $request->weight,
				'height' => $request->height,
				'bmi' => $request->bmi,
				// Physical Exam
				'appearance' => $request->appearance,
				'heent' => $request->heent,
				'neck' => $request->neck,
				'cardiovascular_exam' => $request->cardiovascular_exam,
				'lungs' => $request->lungs,
				'abdomen' => $request->abdomen,
				'extremities' => $request->extremities,
				'neuro' => $request->neuro,
				// Diagnosis, Assessment, Instructions, Medications
				'diagnosis' => $request->diagnosis,
				'assessment_plan' => $request->assessment_plan,
				'instructions' => $request->instructions,
				'medications' => $request->medications,
				// Medical Note Specific Fields
				'excuse' => $request->excuse,
				'work' => $request->work,
				'school' => $request->school,
				'other' => $request->other,
				'injury' => $request->injury,
				'illness' => $request->illness,
				'due_to_other' => $request->due_to_other,
				'doc_comment' => $request->doc_comment,
				'pdf_path' => $pdfName,
			]);
			$user = auth()->user();
			$ipaddress = Utility::getIP();

			$insertLog = [
				'type' => 'Clinical Record created',
				'link' => url('hub-record/view/') . '/' . $request->record_id,
				'module' => 'Hub Record',
				'object_id' =>  $request->record_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has added clinical record',
				'new_response' => serialize($clinicalRecord->toArray()),
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);
			return response()->json([
				'status' => true,
				'message' => 'Clinical record saved successfully',
				'record' => $clinicalRecord
			]);
		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'message' => 'Error saving clinical record: ' . $e->getMessage()
			], 500);
		}
	}

	public function getClinicalRecords($id)
	{
		try {
			$records = \App\HubClinicalRecord::where('hub_record_id', $id)
				->orderBy('created_at', 'desc')
				->get();

			return response()->json([
				'status' => true,
				'records' => $records
			]);
		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'message' => 'Error loading clinical records: ' . $e->getMessage()
			], 500);
		}
	}

	public function downloadClinicalPdf($id)
	{
		try {
			// Increase execution time limit for PDF generation
			set_time_limit(300); // 5 minutes
			// ini_set('memory_limit', '512M');

			$record = \App\HubClinicalRecord::findOrFail($id);

			// Prepare data array from the record
			$data = $record->toArray();

			if ($record->pdf_type == 'medical_visit') {
				$htmlContent = view('Hubclinical.HubMedicalHistoryPDF', compact('data'));
			} else {
				$htmlContent = view('Hubclinical.HubMedicalNotePDF', compact('data'));
			}
			return $htmlContent;
			// Generate PDF content using the PDF template with data
			if ($record->pdf_type == 'medical_visit') {
				$htmlContent = view('Hubclinical.HubMedicalHistoryPDF', compact('data'))->render();
			} else {
				$htmlContent = view('Hubclinical.HubMedicalNotePDF', compact('data'))->render();
			}

			// Use your preferred PDF generation pattern
			$pdf = new PDF(null, 'px');
			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);
			$pdf->AddPage();

			// Write HTML content to PDF
			$pdf->writeHTML($htmlContent);

			$filename = $record->name . '_' . $record->pdf_type . '_' . date('Y-m-d') . '.pdf';

			// Set headers for download
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment; filename="' . $filename . '"');

			// Output PDF for download
			$pdf->output($filename, 'D');
		} catch (\Exception $e) {
			echo 'Error generating PDF: ' . $e->getMessage();
		}
	}

	public function deleteClinicalRecord($id)
	{
		try {
			$user = auth()->user();
			$ipaddress = Utility::getIP();
			$record = \App\HubClinicalRecord::findOrFail($id);
			$record->delete();
			$insertLog = [
				'type' => 'Clinical Record deleted',
				'link' => url('hub-record/view/') . '/' . $record->hub_record_id,
				'module' => 'Hub Record',
				'object_id' => $record->hub_record_id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has deleted a clinical record',
				'new_response' => serialize($record->toArray()),
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);
			return response()->json([
				'status' => true,
				'message' => 'Clinical record deleted successfully'
			]);
		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'message' => 'Error deleting clinical record: ' . $e->getMessage()
			], 500);
		}
	}

	private function generatePdfContent($pdfType, $data)
	{
		try {
			$data['data'] = $data;
			$pdfContent = $pdfName = '';
			if ($pdfType === 'medical_visit') {
				$pdfContent = view('Hubclinical.HubMedicalHistoryPDFNew', $data)->render();
			} elseif ($pdfType === 'medical_note') {
				$pdfContent =  view('Hubclinical.HubMedicalNotePDFNew', $data)->render();
			}
			$pdf = new PDF();
			$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
			$pdf->SetFont('helvetica', '', 9);
			$pdf->AddPage();
			$pdf->writeHTML($pdfContent, true, false, true, false, '');
			$pdfName = uniqid() . '-' . time() . '.pdf';
			$public_path = public_path('/hubEsign') . '/' . $pdfName;

			if (env('FILE_UPLOAD_PERMISSION') != "development") {
				$pdfContain = $pdf->Output("", 'S');
				Storage::disk('s3')->put('/hubEsign/' . $pdfName, $pdfContain);
			} else {
				$pdf->Output($public_path, 'F');
			}

			return $pdfName;
		} catch (\Exception $e) {
			return '<p>Error generating PDF content: ' . $e->getMessage() . '</p>';
		}
	}

	public function generatePdfDownload($id)
	{
		$auth = auth()->user();
		$getDetails = \App\HubClinicalRecord::findOrFail($id);

		if (isset($getDetails->pdf_path)) {
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Clinical Record downloaded',
				'link' => url('hub-record/view/') . '/' . $getDetails->hub_record_id,
				'module' => 'Hub Record',
				'object_id' => $getDetails->hub_record_id,
				'message' => $auth->first_name . ' ' . $auth->last_name . ' has downloaded  ' . $getDetails->name . ' clinical record',
				'new_response' => serialize($getDetails->toArray()),
				'ip' => $ipaddress,
			];
			HubLogsService::save($insertLog);

			// Normalize: get just the filename without any directory prefix
			$pdfFileName = basename($getDetails->pdf_path);
			$s3Path = 'hubEsign/' . $pdfFileName;
			$localFile = public_path('hubEsign/' . $pdfFileName);

			$headers = [
				'Content-Type' => 'application/pdf',
			];

			if (file_exists($localFile)) {
				return response()->download($localFile, $pdfFileName, $headers);
			} else {
				return Storage::disk('s3')->download($s3Path, $pdfFileName, $headers);
			}
		} else {
			abort(404);
		}
	}
}
