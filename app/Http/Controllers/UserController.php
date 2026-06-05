<?php

namespace App\Http\Controllers;

use App\Exmed;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\UserPasswordData;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;
use App\Master;
use App\Agency;
use App\UserAgency;
use App\UserIpAddress;
use App\Helpers\UserAgencyHelper;
use App\Helpers\UserWiseDomainHelper;
use Illuminate\Support\Facades\URL;
use App\Helpers\AttachMailer;
use App\Helpers\UserHelper;
use App\Services\LogsService;
use App\Services\IpInfoService;
use App\Services\LoginLogService;
use App\Services\CommonLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Model\UserNotificationEmail;
use App\Model\UserWiseAgency;
use App\Model\UserWiseLocation;
use App\Services\UserWiseAgencyService;
use App\Services\UserWiseLocationService;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Mail;
use App\Services\PatientService;
use App\Services\LanguageService;
use App\Services\NurseLanguageService;
use App\Services\UserDocApprovalService;
use App\Services\UserService;
use App\Helpers\Common;
use App\Model\LocationMaster;
class UserController extends BaseController
{
	protected $commonLogService, $userWiseAgencyService, $userWiseLocationService, $patientService, $languageService, $nurseLanguageService,$userDocApprovalService,$userService= "";
	public function __construct(CommonLogService $commonLogService, UserWiseAgencyService $userWiseAgencyService, UserWiseLocationService $userWiseLocationService, PatientService $patientService, LanguageService $languageService, NurseLanguageService $nurseLanguageService, UserDocApprovalService $userDocApprovalService,UserService $userService)
	{
		$this->middleware('permission:user-list|user-create|user-edit|user-delete|user-view', ['only' => ['index', 'add', 'view']]);
		$this->middleware('permission:user-list', ['only' => ['index']]);
		$this->middleware('permission:user-create', ['only' => ['add_page', 'add']]);
		$this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
		$this->middleware('permission:user-delete', ['only' => ['delete']]);
		$this->middleware('permission:user-view', ['only' => ['view']]);

		$this->middleware('auth', ['except' => ['AcceptInvivation', 'AcceptView', 'activeView', 'activeAccountUpdate', 'makeSecurePassword', 'checkPasswords', 'verifyOtp', 'checkOtp', 'linkExpired','resendOTP','otpValid']]);
		$this->commonLogService = $commonLogService;
		$this->userWiseAgencyService = $userWiseAgencyService;
		$this->userWiseLocationService = $userWiseLocationService;
		$this->patientService = $patientService;
		$this->languageService = $languageService;
		$this->nurseLanguageService = $nurseLanguageService;
		$this->userDocApprovalService = $userDocApprovalService;
		$this->userService= $userService;
	}

	public function index(Request $request)
	{
		$data['menu'] = "user";
		$data['user'] = $user = auth()->user();
		if ($user['user_type_fk'] == 4 || $user['user_type_fk'] == 6) {
			return abort(404);
		}
		/*Serch*/
		$data['role_list'] = Role::orderBy('name', 'asc')->get();
		$first_name = $data['first_name'] = request('first_name');
		$last_name = $data['last_name'] = request('last_name');
		$email = $data['email'] = request('email');
		$login_type = $data['login_type'] = request('login_type');
		$user_type = $data['user_type'] = request('user_type');
		$agency = $data['agency_fk'] = request('agency_fk');
		$record_access = $data['record_access'] = request('record_access');
		$roles = $data['roles_name'] = $request->roles_name;
		/*end*/
		if (in_array($user->user_type_fk, array(184, 4))) {
			$usersDetails = User::getData($first_name, $last_name, $email, $login_type, $user_type, $agency, $record_access,$roles);
		} else {
			$usersDetails = User::getDataByAgency($user->agency_fk, $first_name, $last_name, $email);
		}

		$final = [];
		if (!empty($usersDetails)) {
			foreach ($usersDetails as $val) {
				$userRole = $val->roles->pluck('name')->all();
				$final[$val->id] = $userRole;
				$typeLog = 0;
				$val->userRole = $userRole;
				$val->totalAgencyCountRecord = $typeLog;
			}
		}

		$data['roles'] = $final;
		$data['query'] = $usersDetails;
		return view("user", $data);
	}

	public function add_page()
	{
		$data['menu'] = "Add user";
		$data['user'] = $user = auth()->user();
		$data['roles'] = Role::where('guard_name', 'web')->pluck('name', 'name')->all();
		if ($user['user_type_fk'] == 4 || $user['user_type_fk'] == 6) {
			return abort(404);
		}

		if ($user->user_type_fk == 184) {
			$data['loginType'] = Master::where('master_type_fk', '1')->where('del_flag', 'N')->orderBy('name', 'asc')->get();
		} else {
			$data['loginType'] = Master::where('id', '2')->where('del_flag', 'N')->first();
		}

		$data['userType'] = Master::whereIn('id', array(184, 4, 5, 6))->where('del_flag', 'N')->orderBy('name', 'asc')->get();
		$data['agencyList'] = Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
		$language = $this->languageService->getLanguageList();
		$data['language'] = $language;
		return view("user_add", $data);
	}

	public function add(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|email|unique:users,email,null,null,delete_flag,N',

		]);
		if ($validator->fails()) {
			return redirect("/adduser")
				->withErrors($validator, 'add_user')
				->withInput();
		} else {

			$first_name = request('first_name');
			$last_name = request('last_name');
			$email = request('email');
			$phone = request('phone');
			$login_type = 183;
			$user_type = 184;
			$ext = request('ext');
			$data = array(
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
				'phone' => $phone,
				'ext' => $ext,
				'active' => 'active',
				'created_at' => date('Y-m-d H:i:s'),
				'created_by' => $user->id,
				'login_type_fk' => $login_type,
				'user_type_fk' => $user_type,
				'record_access' => $request->record_access,
				'department' => $request->department,
				'is_nurse'      => $request->is_nurse ?? 0,
				'is_mdo'        => $request->is_mdo ?? 0,
				'is_telehealth' => $request->is_telehealth ?? 0,
			);

			$ins_test = new User($data);
			$ins_test->save();
			$insert = $ins_test->id;

			if ($insert) {
				// Language add 
				if ($request->is_nurse == 1 && isset($request->language_id)) {
					foreach ($request->language_id as $lang) {
						$language_data = array(
							'nurse_id' => $insert,
							'language_id' => $lang
						);
						$this->nurseLanguageService->save($language_data);
					}
				}
				$ins_test->assignRole($request['roles']);
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Add',
					'link' => url('/adduser'),
					'module' => 'User',
					'object_id' => $ins_test->id,
					'message' => $user->first_name . ' ' . $user->last_name . ' has added User',
					'new_response' => serialize($data),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);

				$from = 'notifications@nybestmedical.com';
				$subject = 'Account Activation - NY Best Medicals';

				if ($user_type == 5) {
					$messages = $this->getHtml($user_type, $insert);
				} else {
					$messages = $this->getHtml($user_type, $insert);
				}


				try {
					$mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages) {
						$message->to($email, "Ny Best Medicals")
							->subject($subject)->html($messages);
					});
				} catch (\Throwable $th) {
					//throw $th;
				}



				Session::flash('success', __('message.success.add', array('name' => "User")));

				return redirect('/user');
			} else {

				Session::flash('error', 'Sorry, something went wrong. Please try again.');

				return redirect('/user');
			}
		}
	}

	function getHtml($user_type, $id)
	{
		$query = User::where('id', $id)->where('delete_flag', 'N')->first();
		$url = URL::to('/') . '/invitation-accept/' . sha1($query->id);
		$user = auth()->user();
		$fname = '';
		if ($query->first_name != '') {
			$fname = $query->first_name;
		}
		$lname = '';
		if ($query->last_name != '') {
			$lname = $query->last_name;
		}
		$img = URL::to('/') . '/img/logo-ny.png';
		if ($user_type == 5) {
			$message = '<style>
						body {
							font-family: verdana;
						}        
						</style>

 
						<table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;background:#fff;">
					<tbody><tr>
								<td style="text-align: center;background: #eee;padding:20px 0">
									<a href=""><img src="' . $img . '" alt="" width="100"></a>
								</td>
							</tr>

						 <tr>
									<td style="padding:10px 40px;font-size: 14px;color:blue">
									  Welcome to the NyBest Medicals Client Portal!
									  </td>
								</tr>
								<tr>
									<td style="padding:10px 40px;font-size: 14px;color:blue">
									  You have been invited by ' . $fname . " " . $lname . ' to sign up and start using the portal right away! use the link here to Create your Password and sign in.
									  </td>
								</tr>
								<tr>
									<td style="padding: 20px 40px 0;">
										<center><a style="text-decoration:none;background: #2196F3;border: 1px solid #2196F3;color: #fff;padding: 7px 30px;border-radius: 5px;margin-bottom:20px;" href="' . $url . '">Open Invite</a></center><br><br> 
									</td>
								</tr>
				
								<tr>
									<td style="padding:10px 40px;font-size: 14px;color:blue">
									 If you experience any issues with the sign-up process, do not hesitate to reach out to us at (718)650-3540

									  </td>
								</tr>
								
					 <tr>
								<td style="padding: 10px 40px;background: #eee;font-size: 12px;color: #555">';
		} else {
			$message = '<style>
						body {
							font-family: verdana;
						}        
						</style>

 
						<table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;background:#fff;">
					<tbody><tr>
								<td style="text-align: center;background: #eee;padding:20px 0">
									<a href=""><img src="' . $img . '" alt="" width="100"></a>
								</td>
							</tr>

						 <tr>
						 

									<td style="padding:10px 40px;font-size: 14px;color:blue">
									  Welcome to the NyBest Medicals Client Portal!
									  </td>
								</tr>
								<tr>
									<td style="padding:10px 40px;font-size: 14px;color:blue">
									  Dear ' . $user->first_name . " " . $user->last_name . ', you have been invited by ' . $fname . " " . $lname . ' to join the NyBest Medicals Client Portal.
									 
									  </td>
								</tr>
								<tr>
									<td style="padding:10px 40px;font-size: 14px;color:blue">
									 Click below to create your password and sign in.
									  </td>
								</tr>
								
								<tr>
									<td style="padding: 20px 40px 0;">
										<a style="text-decoration:none;background: #2196F3;border: 1px solid #2196F3;color: #fff;padding: 7px 30px;border-radius: 5px;margin-bottom:20px;" href="' . $url . '">Open Invite</a><br><br>
									</td>
								</tr>
					 <tr>
								<td style="padding: 10px 40px;background: #eee;font-size: 12px;color: #555">';
		}

		$emailData = array(
			'img' => $img,
			'fname' => $fname,
			'lname' => $lname,
			'url' => $url,
			'user_first_name' => $user->first_name,
			'user_last_name' => $user->last_name,
			'user_type' => $user_type,
		);
		$message = Utility::getHtmlContent('email_template.user_invitation', $emailData);
		return $message;
	}


	public function edit()
	{

		$data['menu'] = "user";

		$data['user'] = $user = auth()->user();
		if ($user['user_type_fk'] == 4 || $user['user_type_fk'] == 6) {
			return abort(404);
		}

		$data['flag'] = request('flag');/*redurection mate used*/
		$data['id'] = $id = request("i");

		$data['userDetail'] = $userDetail = User::with('nurseLanguages:id,nurse_id,language_id')->where("id", $id)->first();
		$data['userDetail']->language = $userDetail->nurseLanguages->pluck('language_id')->toArray();
		if ($user['user_type_fk'] == 5 && $userDetail['user_type_fk'] !== 184 && $data['userDetail']->agency_fk != $user['agency_fk']) {
			return abort(404);
		}
		$data['agencyList'] = Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
		$data['roles'] = Role::where('guard_name', 'web')->pluck('name', 'name')->all();
		$data['userRole'] = $userDetail->roles->pluck('name', 'name')->all();
		$language = $this->languageService->getLanguageList();
		$data['language'] = $language;
		return view('user_edit', $data);
	}



	public function update(Request $request)
	{

		$user = auth()->user();
		$data['id'] = $id = request("i");
		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => [
				'required',
				Rule::unique('users')->where(function ($query) use ($id) {
					return $query->where('id', '!=', $id)
						->where('delete_flag', 'N');
				}),
			],
		]);
		if ($validator->fails()) {
			return redirect("/edituser?i=$id")
				->withErrors($validator, 'edit_user')
				->withInput();
		} else {
			$user = User::find($id);
			$first_name = request('first_name');
			$last_name = request('last_name');
			$email = request('email');
			$phone = request('phone');
			$login_type = 183;
			$user_type = 184;
			$ext = request('ext');
			$data = array(

				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
				'phone' => $phone,
				'ext' => $ext,
				'updated_at' => date('Y-m-d H:i:s'),
				'updated_by' => $user->id,
				'user_type_fk' => $user_type,
				'login_type_fk' => $login_type,
				'department' => $request->department,
				'is_nurse'      => $request->is_nurse ?? 0,
				'is_mdo'        => $request->is_mdo ?? 0,
				'is_telehealth' => $request->is_telehealth ?? 0,
			);
			$update = $user->update($data);
			if ($request->is_nurse == 1 && isset($request->language_id)) {
				// Delete old data
				$this->nurseLanguageService->SoftDelete(['del_flag' => 'Y'], ['nurse_id' => $id]);
				foreach ($request->language_id as $lang) {
					$language_data = array(
						'nurse_id' => $id,
						'language_id' => $lang
					);
					$this->nurseLanguageService->save($language_data);
				}
			} else {
				$this->nurseLanguageService->SoftDelete(['del_flag' => 'Y'], ['nurse_id' => $id]);
			}
			$flag = request("flag");

			if ($update) {
				DB::table('model_has_roles')->where('model_id', $id)->delete();
				$user->assignRole($request->input('roles'));
				// $ipaddress = request()->getClientIp();
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Update',
					'link' => url('/edituser?=' . $id . '&flag=uview'),
					'module' => 'User',
					'object_id' => $id,
					'message' => $user->first_name . ' ' . $user->last_name . ' has updated User',
					'new_response' => serialize($data),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);

				Session::flash('success', __('message.success.update', ['name' => "User"]));

				if ($flag == 'uview') {
					return redirect('/user-view/' . $id);
				} else {
					return redirect('/user');
				}
			} else {

				Session::flash('error', 'Sorry, something went wrong. Please try again.');
				if ($flag == 'uview') {
					return redirect('/user-view/' . $id);
				} else {
					return redirect('/user');
				}
			}
		}
	}



	public function delete()
	{

		$user = auth()->user();
		if ($user['user_type_fk'] == 4 || $user['user_type_fk'] == 6) {
			return abort(404);
		}
		$data['id'] = $id = request("i");
		$delArr = array('delete_flag' => 'Y', 'deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id);

		$update = User::where('id', $id)->update($delArr);

		if ($update) {

			// $ipaddress = request()->getClientIp();
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Delete',
				'link' => url('/delete_user?i=', $id),
				'module' => 'User',
				'object_id' => $id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has deleted User',
				'new_response' => serialize($delArr),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);

			Session::flash('success', 'User successfully deleted');

			return redirect('/user');
		} else {

			Session::flash('error', 'Sorry, something went wrong. Please try again.');

			return redirect('/user');
		}
	}



	public function editProfile()
	{

		$data['user'] = auth()->user();

		$data['id'] = $id = $data['user']['id'];

		$data['userDetail'] = User::where("id", $id)->first();

		return view('edit_profile', $data);
	}



	public function updateProfile(Request $request)
	{

		$user = auth()->user();

		$data['id'] = $id = request("i");

		$validator = Validator::make($request->all(), [

			'name' => 'required',

			'email' => 'required|unique:users,email,' . $id . ',id',

		]);

		if ($validator->fails()) {

			return redirect("/edit_profile")

				->withErrors($validator, 'edit_profile')

				->withInput();
		} else {

			$name = request('name');

			$email = request('email');



			$data = array(

				'name' => $name,

				'email' => $email,

				'updated_at' => date('Y-m-d H:i:s'),

				'updated_by' => $user->id

			);

			$update = User::where('id', $id)->update($data);

			if ($update) {

				Session::flash('success', __('success.update', array('name' => "Profile")));

				return redirect('/edit_profile');
			} else {

				Session::flash('error', 'Sorry, something went wrong. Please try again.');

				return redirect('/edit_profile');
			}
		}
	}
	public function view($id)
	{

		$data['menu'] = "User";
		$data['title'] = "User View";
		$data['user'] = $user = auth()->user();
		if ($user['user_type_fk'] == 4 || $user['user_type_fk'] == 6) {
			return abort(404);
		}

		$data['id'] = $id;

		//$data['query'] = User::getData();
		$data['userDetails'] = User::getDataById($id);
		if (empty($data['userDetails'])) {
			return abort(404);
		} else {
			if ($user['user_type_fk'] == 5 && $data['userDetails']->agency_fk != $user['agency_fk']) {
				return abort(404);
			}
		}
		if($data['userDetails']->agency_fk != ""){
			$checkForAgencyDeteleted = Agency::getDetailsByAgencyId($data['userDetails']->agency_fk);
			if (isset($checkForAgencyDeteleted->id)) {
			} else {
				return redirect('support_error');
			}
		}
		
		$nurselanguage = [];
		if (isset($data['userDetails']->nurseLanguages) && !empty($data['userDetails']->nurseLanguages)) {
			foreach ($data['userDetails']->nurseLanguages as $nLang) {
				foreach ($nLang->languages as $language) {
					$nurselanguage[] = $language->name;
				}
			}
		}
		$data['userDetails']->nurselanguage = implode(', ', $nurselanguage);
		$data['agency_list'] = Agency::select('id', 'agency_name')->where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
		$assignedLocationIds = UserWiseLocation::where('user_id', $id)->where('delete_flag', 'N')->pluck('location_id')->toArray();
		$data['location_list'] = LocationMaster::select('id', 'location_name')->where('delete_flag', 'N')->whereNotIn('id', $assignedLocationIds)->orderBy('location_name', 'asc')->get();
		$data['agency'] = $agency_id = request('agency_id');
		$data['userByAgencyList'] = UserAgency::getAgencyListByUserId($id, $agency_id);

		$data['UserNotificationEmail'] = Master::getAllDataByMasterTypeFk(array(24));

		$typeLog = 0;
		$EMCtypeLog = 0;
		$data['totalEmcCountRecord'] = $EMCtypeLog;
		$data['totalAgencyCountRecord'] = $typeLog;
		$data['allPermission'] = Utility::staticDateWiseAgencyAccess();
		return view('user_view', $data);
	}
	public function userTypeByLoginType(Request $request)
	{
		$id = request('id');
		if ($id == 1) {
			$idarray = array('3', '4');
		} else if ($id == 2) {
			$idarray = array('5', '6');
		} else if ($id == 183) {
			$idarray = array('184');
		}

		$query = Master::where('master_type_fk', '2')
			->whereIn('id', $idarray)
			->where('del_flag', 'N')
			->get();
		foreach ($query as $value) {
			echo '<option value="' . $value->id . '">' . $value->name . '</option>';
		}
	}
	public function change_password()
	{
		$data['menu'] = "User";
		$data['title'] = "User Change Password";
		$data['user'] = auth()->user();
		//$data['userDetails'] = User::getDataById($id);

		//$data['query'] = User::getData();

		return view('user_change_password', $data);
	}
	public function userExport(Request $request)
	{
		$user = auth()->user();
		$first_name = request('first_name');
		$last_name = request('last_name');
		$email = request('email');
		$login_type = request('login_type');
		$user_type = request('user_type');
		$agency = request('agency_fk');
		$record_access = request('record_access');
		$roles_name = request('roles_name');
		if ($user->user_type_fk == 184) {
			$users = User::getDataExport($first_name, $last_name, $email, $record_access,$roles_name);
		} else {
			$users = User::getDataByAgencyExport($user->agency_fk, $first_name, $last_name, $email,$roles_name);
		}

		$filename = 'User' . date("m-d-Y");
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $filename . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);
		$columns = array('ID','Record Type', 'Full Name', 'Email', 'Phone','EXT','Status','Role','Last Login');

		$callback = function () use ($users, $columns) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);
			foreach ($users as $list) {
				$userRole = $list->roles->pluck('name')->all();
				fputcsv($file, array($list->id, $list->record_access, $list->first_name . ' ' . $list->last_name, $list->email, $list->phone, $list->ext, ucfirst($list->active), implode(',', $userRole), $list->last_login_at));
			}

			fclose($file);
		};
		return response()->stream($callback, 200, $headers);
	}


	function addMultipleAgency()
	{
		$agency = request('agency_id');
		$user_id = request('id');
		$data['auth'] = $auth = auth()->user();

		$agencyAdded = array(
			'user_id' => $user_id,
			'agency_fk' => $agency,
			'created_date' => date('Y-m-d H:i:s'),
			'created_by' => $auth->id
		);
		$agencyAdd = UserAgencyHelper::insert($agencyAdded);
		if ($agencyAdd) {
			Session::flash('success', 'Agency successfully inserted');

			return redirect()->back();
		} else {
			Session::flash('error', 'Sorry, something went wrong. Please try again.');

			return redirect()->back();
		}
	}

	function AgencyRemove($id)
	{
		$agency = $id;

		$data['auth'] = $auth = auth()->user();
		$agencyAdded = array(
			'del_flag' => 'Y',
			'deleted_date' => date('Y-m-d H:i:s'),
			'deleted_by' => $auth->id
		);
		$agencyAdd = UserAgencyHelper::update($agencyAdded, array('id' => $id));

		if ($agencyAdd) {

			Session::flash('success', 'Agency successfully removed');

			return redirect()->back();
		} else {

			Session::flash('error', 'Sorry, something went wrong. Please try again.');

			return redirect()->back();
		}
	}
	public function update_password(Request $request)
	{

		$id = request('id');
		$user = User::findOrFail($id);

		if ($request->new_password == $request->confirm_password) {
			$user->fill([
				'password' => Hash::make($request->new_password)
			])->save();

			// $ipaddress = request()->getClientIp();
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'User Update Password',
				'link' => url('/user/update-password'),
				'module' => 'User',
				'object_id' => $id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has updated password',
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);

			$request->session()->flash('success', 'Password successfully changed');
			return redirect()->back();
		} else {
			$request->session()->flash('error', 'Password does not match');
			return redirect()->back();
		}
	}

	public function send_invitation($id)
	{
		$query = User::where('id', $id)->where('delete_flag', 'N')->first();
		$from = 'noreply@nybestmedicals.com';
		$subject = 'Account Activation - NY Best Medicals ';
		if ($query->user_type_fk == 5) {
			$message = $this->getHtml($query->user_type_fk, $query->id);
		} else {
			$message = $this->getHtml($query->user_type_fk, $query->id);
		}
		$insert = AttachMailer::sendEmail($from, $query->email, $subject, $message);
		if ($insert) {
			Session::flash('success', 'Invitation sent');
			return redirect()->back();
		} else {
			Session::flash('error', 'Sorry, something went wrong. Please try again.');
			return redirect()->back();
		}
	}
	function AcceptView($id)
	{
		$query = User::whereRaw('sha1(id) = "' . $id . '"')->where('delete_flag', 'N')->first();

		if ($query && $query->password == '') {
			$data['id'] = $query->id;

			return view('sendInvitation', $data);

		} else {
			return view('errorExpire');
		}
	}
	public function AcceptInvivation()
	{
		$id = request('id');
		$password = request('password');
		$update = array(
			'password' => Hash::make($password),
			'active' => 'active'
		);
		$update = User::where('id', $id)->update($update);
		if ($update) {
			Session::flash('success', 'Password successfully updated');
			return redirect('/');
		} else {
			Session::flash('error', 'Sorry, something went wrong. Please try again.');
			return redirect()->back();
		}
	}

	public function getUserListByAgencyId($id, $selfId)
	{

		$query = User::where('agency_fk', $id)->where('delete_flag', 'N')->orderBy('first_name', 'asc')->get();
		$agencyArray = '<option value="">Select Agency User</option>';
		if (count($query) > 0) {
			foreach ($query as $val) {
				if ($selfId != $val->id) {
					if ($val->user_type_fk == 5) {
						$type = 'Admin';
					}
					if ($val->user_type_fk == 6) {
						$type = 'User';
					}
					$agencyArray .= '<option value="' . $val->id . '">' . ucfirst($val->first_name . " " . $val->last_name) . ' ( ' . $type . ') </option>';
				}
			}
		}
		echo $agencyArray;
	}
	public function getUserListByEmcId($selfId)
	{

		$query = User::whereIn('user_type_fk', [184, 4])->where('delete_flag', 'N')->orderBy('first_name', 'asc')->get();
		$agencyArray = '<option value="">Select EMC User</option>';
		if (count($query) > 0) {
			foreach ($query as $val) {
				if ($selfId != $val->id) {
					if ($val->user_type_fk == 3) {
						$type = 'Super Admin';
					}
					if ($val->user_type_fk == 4) {
						$type = 'Customer Service Rep';
					}
					$agencyArray .= '<option value="' . $val->id . '">' . ucfirst($val->first_name . " " . $val->last_name) . ' ( ' . $type . ') </option>';
				}
			}
		}
		echo $agencyArray;
	}

	public function updateagencyRecord()
	{
		$user = auth()->user();
		$oldUserid = request('prev');
		$newUserId = request('newuser');

		if ($oldUserid != '' && $newUserId != '') {
			$userDelete = User::where('id', $oldUserid)->update(array('delete_flag' => 'Y', 'deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id));


			if ($userDelete) {

				Session::flash('success', 'User deleted successfully.');

				return redirect('/user');
			} else {

				Session::flash('error', 'Sorry, something went wrong. Please try again.');

				return redirect('/user');
			}
		}
	}
	public function updateEmcRecord()
	{
		$user = auth()->user();
		$oldUserid = request('emcprev');
		$newUserId = request('newemcuser');

		if ($oldUserid != '' && $newUserId != '') {
			$userDelete = User::where('id', $oldUserid)->update(array('delete_flag' => 'Y', 'deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id));


			if ($userDelete) {

				Session::flash('success', 'User deleted successfully.');

				return redirect('/user');
			} else {

				Session::flash('error', 'Sorry, something went wrong. Please try again.');

				return redirect('/user');
			}
		}
	}

	public function checkPasswords()
	{
		$password = request('password');
		$id = request('id');
		$checkUserOldPasswords = UserPasswordData::whereRaw('sha1(user_id) = "' . $id . '"')->get();
		if ($checkUserOldPasswords) {
			foreach ($checkUserOldPasswords as $value) {
				if (Hash::check($password, $value->password)) {
					echo '1';
				}
			}
		} else {
			echo '0';
		}
	}

	function expiredChangeUpdate(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'password' => 'required',
			'password_confirmation' => 'required',

		]);

		if ($validator->fails()) {
			return response()->json([
				'errors' => $validator->errors()
			], 422);
		}
		$userId = $request->input('id');
		$query = User::getDetailsById($userId);
		$updateData = array(
			'password' => Hash::make($request->input('password')),
			'password_expired_at' => date('Y-m-d H:i:s', strtotime("+30 days")),
		);
		$update = UserHelper::update($updateData, array('id' => $userId));

		// save password
		$passData = [
			'user_id' => $userId,
			'password' => Hash::make($request->password),
			'created_at' => date('Y-m-d H:i:s'),
			'created_by' => $userId,
		];
		$inserId = new UserPasswordData($passData);
		$inserId->save();
		// save password

		/**
		 * insert log in user Account update 
		 */

		$ipaddress = request()->getClientIp();
		
		$ipDetails = IpInfoService::ipInfo($ipaddress);


		$city = $ipDetails['city'] ?? "";
		$state = $ipDetails['state'] ?? "";
		$country = $ipDetails['country'] ?? "";
		$areaCode = $ipDetails['area_code'] ?? "";
		$insertLog = [
			'type' => 'Users Passowrd Update',
			'object_id' => $userId,
			'message' => "Passowrd has been Update by 30 Days",
			'old_response' => serialize($query),
			'new_response' => serialize($updateData),
			'ip' => $ipaddress,
			'address' => $city . ' ' . $state . ' ' . $country . ' ' . $areaCode,
			'created_by' => $userId,
		];
		LogsService::save($insertLog);
		if ($inserId) {
			return response()->json(
				[
					"timestamp" => Carbon::now('UTC')->toDateTimeString(),
					'message' => "Password changed successfully",
					'status' => 'success',
					'data' => array(),
				],
				200
			);
		}
		return response()->json(
			[
				"message" => "Sorry,something went wrong. Please try again",
				'status' => 'error',
				'data' => array(),
			],
			200
		);
	}

	public function userWiselogs(Request $request)
	{
		$id = request('id');
		$data['user'] = $authId = auth()->user();
		$data['logList'] = LogsService::getDatByUserID($id);

		return view("user_log_ajax_list", $data);
	}

	public function userWiseLoginLogs(Request $request)
	{
		$id = request('id');
		$data['user'] = $authId = auth()->user();
		$data['logList'] = LoginLogService::getDataByUserID($id);

		return view("user_login_log_ajax_list", $data);
	}
	function userChangeStatus(Request $request)
	{
		$user = auth()->user();
		$validator = Validator::make($request->all(), [

			'status' => 'required',

			'user_id' => 'required',

		]);

		if ($validator->fails()) {

			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		} else {
			$user = UserHelper::getUserDetails($request->input('user_id'));
			$update = UserHelper::update(array('active' => $request->input('status')), array('id' => $request->input('user_id')));
			if ($user->active == 'block' && $user->login_attemps == 0) {
				/* Also update login attempts */
				UserHelper::update(array('login_attemps' => 5), array('id' => $request->input('user_id')));
			}
			// $ipaddress = request()->getClientIp();
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'User Status',
				'link' => url('/user-change-status'),
				'module' => 'User',
				'object_id' => $request->input('user_id'),
				'message' => $user->first_name . ' ' . $user->last_name . ' has updated status',
				'new_response' => serialize(array('active' => $request->input('status'))),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			try {
                if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
                    $agencyNotifyData = array(
                        'agencyid' => auth()->user()->agency_fk,
                        'title' => 'Updated Agency User Status',
                        'record_id' => $request->input('user_id'),
                        'record_type' => 'User',
                        'msg' => '',
                        'res_data' => serialize(array('active' => $request->input('status'))),
                    );
                    Common::insertAgencyNotificationsOfUser($agencyNotifyData);
                }
            } catch (\Throwable $th) {}
			return response()->json(['error_msg' => "Status successfully updated", 'status' => 1, 'data' => array('status' => $request->input('status'))], 200);
		}
	}
	function changeStatus(Request $request)
	{
		// return $request->all();
		$user = auth()->user();
		$validator = Validator::make($request->all(), [

			'status' => 'required',

			'user_id' => 'required',

		]);

		if ($validator->fails()) {

			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		} else {
			$update = UserHelper::update(array('limit_access' => $request->input('status')), array('id' => $request->input('user_id')));

			return response()->json(['error_msg' => "Status successfully updated", 'status' => 1, 'data' => array('status' => $request->input('status'))], 200);
		}
	}
	public function exmedcChangeStatus(Request $request)
	{
		$validator = Validator::make($request->all(), [

			'status' => 'required',

			'user_id' => 'required',

		]);

		if ($validator->fails()) {

			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		} else {
			if ($request->status == 1) {
				$status = 1;
			}
			if ($request->status == 2) {
				$status = 0;
			}
			$update = UserHelper::update(array('exmedc_flag' => $status), array('id' => $request->input('user_id')));

			return response()->json(['error_msg' => "Status successfully updated", 'status' => $request->input('status'), 'data' => array('status' => $request->input('status'))], 200);
		}
	}
	public function hospitalChangeStatus(Request $request)
	{
		$validator = Validator::make($request->all(), [

			'status' => 'required',

			'user_id' => 'required',

		]);

		if ($validator->fails()) {

			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		} else {
			if ($request->status == 1) {
				$status = 1;
			}
			if ($request->status == 2) {
				$status = 0;
			}
			$update = UserHelper::update(array('hospital_flag' => $status), array('id' => $request->input('user_id')));

			return response()->json(['error_msg' => "Status successfully updated", 'status' => $request->input('status'), 'data' => array('status' => $request->input('status'))], 200);
		}
	}
	public function changePassword()
	{
		return view('user_change_password');
	}
	public function checkOldPassword(Request $request)
	{
		// return $request->all();
		$password = request('old_password');
		$id = request('id');
		$checkAdminlogin = User::where('id', $id)->where('delete_flag', 'N')->first();
		if ($checkAdminlogin) {
			if ($checkAdminlogin && Hash::check($password, $checkAdminlogin->password)) {
				echo '1';
			} else {
				echo '0';
			}
		} else {
			echo '0';
		}
	}
	public function updatePassword(Request $request)
	{
		$id = request('id');
		$auth = auth()->user();
		$userDetailOld = $user = User::findOrFail($id);
		if (Hash::check($request->oldpassword, $user->password)) {
			$validator = Validator::make($request->all(), [
				'newpassword' => 'required',
				'confirmpassword' => 'required'
			]);
			if ($validator->fails()) {
				return redirect()->back()
					->withErrors($validator, 'Password')
					->withInput();
			} else {
				$data_array = array(
					'password' => Hash::make($request->newpassword),
				);
				$user->password = Hash::make($request->newpassword);
				$user->save();
				if ($user) {
					// save password
					$passData = [
						'user_id' => $id,
						'password' => Hash::make($request->newpassword),
						'created_at' => date('Y-m-d H:i:s'),
						'created_by' => $auth->id,
					];
					$inser_id = new UserPasswordData($passData);
					$inser_id->save();
					// save password

					/**
					 * insert log in user password update 
					 */

					$ipaddress = IpInfoService::getUserIP();
					$ipDetails = IpInfoService::ipInfo($ipaddress);


					$city = $ipDetails['city'] ?? "";
					$state = $ipDetails['state'] ?? "";
					$country = $ipDetails['country'] ?? "";
					$areaCode = $ipDetails['area_code'] ?? "";
					$insertLog = [
						'type' => 'Users Change Password',
						'object_id' => $id,
						'message' => $auth->first_name . ' ' . $auth->last_name . ' has update User Passowrd',
						'old_response' => serialize($userDetailOld),
						'new_response' => serialize($passData),
						'ip' => $ipaddress,
						'address' => $city . ' ' . $state . ' ' . $country . ' ' . $areaCode,

					];
					LogsService::save($insertLog);

					Session::flash('success', 'Successfully changed password');
					return redirect()->back();
				} else {
					Session::flash('error', 'Sorry,something went wrong. Please try again');
					return redirect()->back();
				}
			}
		} else {
			Session::flash('error', 'Old password wrong');
			return redirect()->back();
		}
	}
	public function checkUserOldPasswords()
	{
		$password = request('password');
		$id = request('id');
		$checkUserOldPasswords = UserPasswordData::where('user_id', $id)->get();
		if ($checkUserOldPasswords) {
			foreach ($checkUserOldPasswords as $value) {
				if (Hash::check($password, $value->password)) {
					echo '1';
				}
			}
		} else {
			echo '0';
		}
	}
	// make secure password
	public function makeSecurePassword($id)
	{
		$query = User::whereRaw('sha1(id) = "' . $id . '"')->where('delete_flag', 'N')->first();
		$data['id'] = $query->id;
		return view('reset_password', $data);
	}

	// two fact auth
	public function verifyOtp($id)
	{
		$query = User::whereRaw('sha1(id) = "' . $id . '"')->where('delete_flag', 'N')->first();
		if ($query) {
			$data['id'] = $query->id;
			$data['otp_expired_time'] = ($query->otp_expired_time !="")?$query->otp_expired_time:"0000-00-00 00:00:00";
		
			return view('twofactauth', $data);
		} else {
			Session::flash('error', 'Sorry something went wrong please try again');
			return redirect()->back();
		}
	}

	public function checkOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'otp' => 'required',
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return redirect("/login")
				->withErrors($validator, 'two_factor_verify')
				->withInput();
		} else {
			$otp = $request->otp;
			$id = $request->id;
			$rememberDevice = $request->remember;
			$query = User::whereRaw('sha1(id) = "' . $id . '"')->where('rand_no', $otp)->where('delete_flag', 'N')->first();
			if ($query) {
				if($query->otp_expired_time < now()){
					Session::flash('error', 'Otp is Expired.');
					return redirect()->back();
				}
				try {
					User::whereRaw('sha1(id) = "' . $id . '"')
						->update(array('rand_no' => NULL,'otp_expired_time'=>NULL));

					Auth::login($query);
					$ipaddress = IpInfoService::getUserIP();
					$ipDetails = IpInfoService::ipInfo($ipaddress);
					
					$browserDetails = '';
					$ipDetails = IpInfoService::ipInfo($ipaddress);
					$country = ($ipDetails['country']) ?? "";
					$countryCode = ($ipDetails['country_code']) ?? "";
					//Store user & browser login log
					$log = array('browser_ip' => $ipaddress, 'browser_details' => $browserDetails, "ip_details" => $ipDetails);
					$logData = [
						'user_id' => $query->id,
						'logs' => serialize($log),
						'country' => isset($ipDetails['country']) ? $ipDetails['country'] : '',
						'ipaddress' => $ipaddress,
						'country_code' => $countryCode,
						'login_status' => 'success'
					];
					LoginLogService::insert($logData);
					User::where('id', $query->id)->update(array('login_attemps' => 5, 'last_login_ip' => $ipaddress, 'last_login_at' => date('Y-m-d H:i:s')));

					Session::flash('success', 'You have been successfully logged in');
					return redirect("/home");
				} catch (Exception $e) {
					return $e;
				}
			} else {
				Session::flash('error', 'Your verification code is incorrect/expired.');
				return redirect()->back();
			}
		}
	}

	public function checkOtp30072025(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'otp' => 'required',
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return redirect("/login")
				->withErrors($validator, 'two_factor_verify')
				->withInput();
		} else {
			$otp = $request->otp;
			$id = $request->id;
			$rememberDevice = $request->remember;
			$query = User::whereRaw('sha1(id) = "' . $id . '"')->where('rand_no', $otp)->where('delete_flag', 'N')->first();

			if ($query) {
				try {
					User::whereRaw('sha1(id) = "' . $id . '"')
						->update(array('rand_no' => NULL));


					Auth::login($query);

					if ($rememberDevice) {
						$randomString = Str::random(30);
						Cookie::queue('rememberdevice', $randomString, 60 * 24 * 30);
						DB::table('remeber_device')->insert(array('user_id' => $query->id, 'token' => $randomString, 'expiry' => date('Y-m-d', strtotime("+30 days"))));
					} else {
						Cookie::forget('rememberdevice');
					}


					$ipaddress = IpInfoService::getUserIP();
					$ipDetails = IpInfoService::ipInfo($ipaddress);
					//$browserDetails = get_browser(null, true);
					$browserDetails = '';
					$ipDetails = IpInfoService::ipInfo($ipaddress);
					$country = ($ipDetails['country']) ?? "";
					$countryCode = ($ipDetails['country_code']) ?? "";
					//Store user & browser login log
					$log = array('browser_ip' => $ipaddress, 'browser_details' => $browserDetails, "ip_details" => $ipDetails);
					$logData = [
						'user_id' => $query->id,
						'logs' => serialize($log),
						'country' => isset($ipDetails['country']) ? $ipDetails['country'] : '',
						'ipaddress' => $ipaddress,

						'country_code' => $countryCode,
						'login_status' => 'success'
					];
					LoginLogService::insert($logData);

					$uus = User::where('id', $query->id)->update(array('login_attemps' => 5, 'last_login_ip' => $ipaddress, 'last_login_at' => date('Y-m-d H:i:s')));



					Session::flash('success', 'You have been successfully logged in');
					return redirect("/home");
				} catch (Exception $e) {
					return $e;
				}
			} else {
				Session::flash('error', 'Your verification code is incorrect/expired.');
				return redirect()->back();
			}
		}
	}
	// two fact auth

	function activeAccountUpdate(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'password' => 'required',
			'password_confirmation' => 'required',

		]);

		if ($validator->fails()) {
			return redirect("/active-account/" . sha1($request->input('id')))->withErrors($validator, 'edit_user')->withInput();
		} else {
			$user_id = $request->input('id');
			$query = User::getDetailsById($user_id);

			$user = User::findOrFail($user_id);
			// if ($request->input('password') == $query->password) {
			$updateData = array('password' => Hash::make($request->input('password')), 'active' => 'active', 'password_expired_at' => date('Y-m-d H:i:s', strtotime("+30 days")));
			$update = UserHelper::update($updateData, array('id' => $user_id));

			// save password
			$passData = [
				'user_id' => $user_id,
				'password' => Hash::make($request->password),
				'created_at' => date('Y-m-d H:i:s'),
				'created_by' => $user_id,
			];
			$inser_id = new UserPasswordData($passData);
			$inser_id->save();
			// save password

			/**
			 * insert log in user Account update 
			 */

			$ipaddress = IpInfoService::getUserIP();
			$ipDetails = IpInfoService::ipInfo($ipaddress);


			$city = $ipDetails['city'] ?? "";
			$state = $ipDetails['state'] ?? "";
			$country = $ipDetails['country'] ?? "";
			$areaCode = $ipDetails['area_code'] ?? "";
			$insertLog = [
				'type' => 'Users Update',
				'object_id' => $user_id,
				'message' => 'Account has been active / Make secure password.',
				'old_response' => serialize($query),
				'new_response' => serialize($updateData),
				'ip' => $ipaddress,
				'address' => $city . ' ' . $state . ' ' . $country . ' ' . $areaCode,
				'id' => $user_id,
			];
			LogsService::save($insertLog);

			Session::flash('success', 'Password changed successfully');
			return redirect('/login');
			// } else {
			// 	Session::flash('error', 'Sorry, something went wrong. Please try again.');
			// 	return redirect()->back();
			// }
		}
	}

	function viewProfile(Request $request)
	{
		$auth = auth()->user();
		if (empty($auth)) {
			return redirect('login');
		}

		return view('user.view_profile');
	}

	function profileUpdate(Request $request)
	{

		$auth = auth()->user();
		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|email',
			'phone_no' => 'required|numeric|digits_between:10,15',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$checkEmail = User::where('email', $request->input('email'))->where('delete_flag', 'N')->where('id', '!=', $auth['id'])->count();
			if ($checkEmail > 0) {
				return response()->json(['error_msg' => "Email already exist", 'status' => 0, 'data' => array()], 500);
			}

			$final_array = array(
				'first_name' => $request->input('first_name'),
				'last_name' => $request->input('last_name'),
				'email' => $request->input('email'),
				'phone' => $request->input('phone_no'),
				'ext' => $request->input('ext'),
				'enable_disable' => $request->input('enable'),
			);
			$update = User::where('id', $auth['id'])->update($final_array);

			return response()->json(['error_msg' => "Profile successfully update", 'status' => 1, 'data' => array()], 200);
		}
	}

	function userAccess(Request $request)
	{
		$auth = auth()->user();
		$validator = Validator::make($request->all(), [
			'user_id' => 'required',


		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$getUserDetails = User::getDetailsById($request->input('user_id'));
			$status = 0;
			if (isset($getUserDetails->nybest_user_access) && $getUserDetails->nybest_user_access == 0) {
				$status = 1;
			}

			$update = User::where('id', $request->input('user_id'))->update(array('nybest_user_access' => $status));
			return response()->json(['error_msg' => "Successfully updated", 'status' => 1, 'data' => array('status' => $status)], 200);
		}
	}
	public function userWiseIpAddress(Request $request)
	{
		$data['query'] = UserIpAddress::userWiseIpAddress($request->input('user_id'));
		return view("user/ip_ajax_list", $data);
	}
	public function userIpAddressSave()
	{
		$user = Auth::user();
		$input['user_id'] = request('user_id');
		$input['type'] = request('type');
		$input['ip_address'] = request('ip_address');
		$input['created_at'] = now();
		$input['created_by'] = $user->id;

		$agencyIpAddress = UserIpAddress::create($input);

		// $ipaddress = request()->getClientIp();
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Add IP Address',
			'link' => url('/user-ip-address-save'),
			'module' => 'User',
			'object_id' => request('user_id'),
			'message' => $user->first_name . ' ' . $user->last_name . ' has added User Ip Address',
			'new_response' => serialize($input),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);

		if ($agencyIpAddress) {
			return response()->json(['error_msg' => 'IP Address successfully inserted', 'data' => array()], 200);
		} else {
			return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'data' => array()], 200);
		}
	}
	public function userIpAddressEdit(Request $request)
	{

		$edit = UserIpAddress::editIpData($request->id);

		if ($edit) {
			return response()->json(['error_msg' => 'Success', 'data' => $edit], 200);
		} else {
			return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'data' => array()], 200);
		}
	}
	public function userIpAddressUpdate(Request $request)
	{
		$user = Auth::user();
		$input['ip_address'] = $request->ip_address_edit;
		$input['type'] = $request->type_edit;
		$input['updated_at'] = now();
		$input['updated_by'] = $user->id;

		$updateIp = UserIpAddress::updateIpData($input, $request->id);

		// $ipaddress = request()->getClientIp();
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Update IP Address',
			'link' => url('/user-ip-update'),
			'module' => 'User',
			'object_id' => request('user_id'),
			'message' => $user->first_name . ' ' . $user->last_name . ' has updated User IP Address',
			'new_response' => serialize($input),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);

		if ($updateIp) {
			return response()->json(['error_msg' => 'IP Address successfully updated', 'data' => array()], 200);
		} else {
			return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'data' => array()], 200);
		}
	}
	public function userIpAddressDelete(Request $request)
	{
		$user = Auth::user();
		$update = UserIpAddress::SoftDelete(array('delflag' => 'Y'), array('id' => $request->input('id')));

		// $ipaddress = request()->getClientIp();
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Delete IP Address',
			'link' => url('/user-ip-delete'),
			'module' => 'User',
			'object_id' => $request->input('user_id'),
			'message' => $user->first_name . ' ' . $user->last_name . ' has deleted User IP Address',
			'new_response' => serialize(array('delflag' => 'Y')),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);

		if ($update) {
			return response()->json(['error_msg' => 'IP Address successfully deleted', 'data' => array()], 200);
		} else {
			return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'data' => array()], 200);
		}
	}

	function agencyUserUpdate(Request $request)
	{

		$auth = auth()->user();
		$id = $request->id;
		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required',
			'phone' => [
				'numeric',
				'digits_between:10,15'
			],
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {


			$checkExistingEmailOrNot = $this->userService->checkDuplicateEmail($request->email . '' . $request->domain,$request->id);
			if(count($checkExistingEmailOrNot) !=0){
				return response()->json(['error_msg' => "Email address already exist", 'status' => 0, 'data' => array()], 409);
			}
			$old_response = User::getDetailsById($request->id);
			$dataArray = array(
				'first_name' => $request->first_name,
				'last_name' => $request->last_name,
				'email' => $request->email . '' . $request->domain,
				'phone' => $request->phone,
				'ext' => $request->ext_no
			);
			if (isset($request->record_access) && !empty($request->record_access)) {
				$dataArray['record_access'] = ($request->record_access);
			}
			if (isset($request->role_access)) {
				$dataArray['role_access'] = ($request->role_access);
			}
			if (isset($request->show_hub)) {
				$dataArray['show_hub'] = ($request->show_hub);
			}
			$update = UserHelper::update($dataArray, array('id' => $request->id));
			$message = $type = '';
			if ($update) {
				if ($auth->agency_fk != "") {
					$message = 'Agency User ' . $auth->first_name . ' ' . $auth->last_name . ' has updated user';
					$type = "Update Agency User";
				} else {
					$message = $auth->first_name . ' ' . $auth->last_name . ' has updated user';
					$type = "User Update";
				}
				$ipaddress = Utility::getIP();
				$new_response = User::getDetailsById($request->id);
				$insertLog = [
					'type' => $type,
					'link' => url('agency-user-update'),
					'module' => 'User',
					'object_id' => $request->id,
					'message' => $message,
					'ip' => $ipaddress,
					'old_response' => serialize($old_response),
					'new_response' => serialize($new_response),
				];
				LogsService::save($insertLog);
				try {
                    if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
                        $agencyNotifyData = array(
                            'agencyid' => auth()->user()->agency_fk,
                            'title' => 'Updated User Details',
                            'record_id' => $request->id,
                            'record_type' => 'User',
                            'msg' => '',
                            'res_data' => serialize($new_response),
                        );
                        Common::insertAgencyNotificationsOfUser($agencyNotifyData);
                    }
                } catch (\Throwable $th) {}
				return response()->json(['error_msg' => 'User successfully updated', 'data' => $dataArray], 200);
			} else {
				return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'data' => array()], 500);
			}
		}
	}

	function linkExpired()
	{
		return view('link_appointment_error');
	}

	function changeRecordType(Request $request)
	{
		$auth = auth()->user();
		$validator = Validator::make($request->all(), [
			'record_type' => 'required',

		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
		} else {
			$userDetail = User::where("id", $request->id)->first();
			$dataArray = array(
				'record_access' => $request->record_type,

			);
			$update = UserHelper::update($dataArray, array('id' => $request->id));
			if ($update) {
				$newUserDetail = User::where("id", $request->id)->first();
				$this->commonLogService->save(array('type' => 'Users', 'common_fk' => $request->id, 'old_response' => serialize($userDetail), 'new_response' => serialize($newUserDetail), 'main_type' => 'Ny Best Medicare'));

				return response()->json(['error_msg' => 'User successfully updated', 'data' => $dataArray], 200);
			} else {
				return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'data' => array()], 500);
			}
		}
	}

	public function userNotificationEmailList(Request $request)
	{

		$data['page'] = $request->input('page');
		$data['query'] = UserNotificationEmail::notificationEmailByUserId($request->input('user_id'));

		$data['UserNotificationEmail'] = Master::getAllDataByMasterTypeFk(array(24));

		return view("user_notification_email_ajax_list", $data);
	}

	public function saveUSerNotifictionEmail(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'patient' => ['nullable', 'array'],
			'caregiver' => ['nullable', 'array'],
		]);
		$validator->sometimes(['patient', 'caregiver'], 'required', function ($input) {
			return empty($input->patient) && empty($input->caregiver);
		});
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		} else {
			$statusUpdatePatientId = '';
			$uploadDocPatientId = '';
			$sendNotesPatientId = '';
			$addNewRecordPatientId = '';
			$statusUpdateCaregiverId = '';
			$uploadDocCaregiverId = '';
			$sendNotesCaregiverId = '';
			$addNewRecordCaregiverId = '';

			if ($request->patient) {
				if (in_array('Status Update', $request->patient)) {
					$statusUpdatePatientId = 'Status Update';
				}
				if (in_array('Document Upload', $request->patient)) {
					$uploadDocPatientId = 'Document Upload';
				}
				if (in_array('Send Note', $request->patient)) {
					$sendNotesPatientId = 'Send Note';
				}
				if (in_array('Add New Record', $request->patient)) {
					$addNewRecordPatientId = 'Add New Record';
				}
			}
			if ($request->caregiver) {
				if (in_array('Status Update', $request->caregiver)) {
					$statusUpdateCaregiverId = 'Status Update';
				}
				if (in_array('Document Upload', $request->caregiver)) {
					$uploadDocCaregiverId = 'Document Upload';
				}
				if (in_array('Send Note', $request->caregiver)) {
					$sendNotesCaregiverId = 'Send Note';
				}
				if (in_array('Add New Record', $request->caregiver)) {
					$addNewRecordCaregiverId = 'Add New Record';
				}
			}


			$data = array(
				'user_id' => $request->user_id,
				'status_update_patient_id' => $statusUpdatePatientId ?? "",
				'status_update_caregiver_id' => $statusUpdateCaregiverId ?? "",
				'upload_doc_patient_id' => $uploadDocPatientId ?? "",
				'upload_doc_caregiver_id' => $uploadDocCaregiverId ?? "",
				'send_notes_patient_id' => $sendNotesPatientId ?? "",
				'send_notes_caregiver_id' => $sendNotesCaregiverId ?? "",
				'created_by' => Auth()->user()->id,
				'updated_by' => Auth()->user()->id,
				'add_new_record_caregiver_id' => $addNewRecordCaregiverId ?? "",
				'add_new_record_patient_id' => $addNewRecordPatientId ?? "",
			);

			$conditions = ['user_id' => $request->user_id];
			$save = UserNotificationEmail::updateOrCreate($conditions, $data);

			if ($save) {

				return response()->json(['error_msg' => 'User Notification Email successfully updated', 'data' => array()], 200);
			} else {
				return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
			}

		}
	}

	public function getUserAgencyList(Request $request)
	{
		$auth = auth()->user();

		$query = $this->userWiseAgencyService->getAgencyList($request->user_id, $request->page);
		return view('user_agency_list_ajax', compact('query'));
	}

	public function userAgencySave(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'user_id' => 'required',

			'user_agency_id' => 'required',

		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		} else {

			$data = array(
				'user_id' => $request->user_id,
				'agency_id' => $request->user_agency_id,
			);

			if ($request->user_agency_mid != "") {
				$msg = "User Agency successfully Updated";
				$data['updated_by'] = Auth()->user()->id;
			} else {
				$msg = "User Agency successfully Inserted";
				$data['created_by'] = Auth()->user()->id;
			}

			$conditions = ['id' => $request->user_agency_mid];
			$save = UserWiseAgency::updateOrCreate($conditions, $data);

			return response()->json(['error_msg' => $msg, 'data' => array()], 200);


		}


	}

	public function userAgencyEdit(Request $request)
	{

		$query = $this->userWiseAgencyService->getAgencyDetails($request->id);

		return response()->json(['error_msg' => "", 'data' => $query], 200);
	}

	public function userAgencyDelete(Request $request)
	{

		$deleted = $this->userWiseAgencyService->SoftDelete(array('delete_flag' => 'Y', 'deleted_at' => date('Y-m-d H:i:s')), array('id' => $request->id));
		if ($deleted) {
			return response()->json(['error_msg' => "Successfully deleted", 'data' => array()], 200);
		} else {
			return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
		}
	}

	// User Location Methods
	public function getUserLocationList(Request $request)
	{
		$auth = auth()->user();

		$query = $this->userWiseLocationService->getLocationList($request->user_id, $request->page);
		return view('user_location_list_ajax', compact('query'));
	}

	public function userLocationSave(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'user_id' => 'required',
			'user_location_id' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		} else {
			$data = array(
				'user_id' => $request->user_id,
				'location_id' => $request->user_location_id,
			);

			if ($request->user_location_mid != "") {
				$msg = "User Location successfully Updated";
				$data['updated_by'] = Auth()->user()->id;
			} else {
				$msg = "User Location successfully Inserted";
				$data['created_by'] = Auth()->user()->id;
			}

			$conditions = ['id' => $request->user_location_mid];
			$save = UserWiseLocation::updateOrCreate($conditions, $data);

			return response()->json(['error_msg' => $msg, 'data' => array()], 200);
		}
	}

	public function userLocationEdit(Request $request)
	{
		$query = $this->userWiseLocationService->getLocationDetails($request->id);
		return response()->json(['error_msg' => "", 'data' => $query], 200);
	}

	public function userLocationDelete(Request $request)
	{
		$deleted = $this->userWiseLocationService->SoftDelete(array('delete_flag' => 'Y', 'deleted_at' => date('Y-m-d H:i:s')), array('id' => $request->id));
		if ($deleted) {
			return response()->json(['error_msg' => "Successfully deleted", 'data' => array()], 200);
		} else {
			return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
		}
	}

	public function searchNyBestUser(Request $request)
	{
		$query = UserHelper::searchNybestUser($request->q);
		$final = [];
		foreach ($query as $val) {
			$temp = [];
			$temp['id'] = $val->id;
			$temp['name'] = $val->first_name . ' ' . $val->last_name . ' ' . $val->agency_fk;
			$final[] = $temp;
		}
		return json_encode($final);
	}

	public function searchUserData(Request $request)
	{
		$query = UserHelper::searchAllUsers($request->q);

		$final = [];
		foreach ($query as $val) {
			$temp = [];
			$userType = "Nybest User";
			if ($val->agency_fk != "") {
				$userType = "Agency User";
			}
			$temp['id'] = $val->id;
			$temp['name'] = $val->first_name . ' ' . $val->last_name . ' ( ' . $userType . ' ) ';
			$final[] = $temp;
		}
		return json_encode($final);
	}

	public function changePageViewStatus(Request $request)
	{
		$updatePageView = User::where('id', $request['user_id'])->update(['patient_page' => $request['patient_page']]);
		$message = '';
		$oldResponse = ['patient_page'=>$request['patient_page']];
		if (isset($request->patient_id) && !empty($request->patient_id)) {
			$link = url('/patient/view/') . $request->patient_id;
			$patient = $this->patientService->getDetailById($request->patient_id);
			$message = 'Patient ' . $patient->first_name . ' ' . $patient->last_name . ' has updated patient view page toggle button.';
			$module = 'Patient Appointment';
			$module_id = $request->patient_id;
			$newResponse = ['patient_page'=>$request['patient_page'],'patient_id'=>$request->patient_id,'user_id'=>$request->user_id];
		} else {
			$link = url('user-view/') . $request->user_id;
			$user = Auth::user();
			$message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has updated patient view page toggle button.';
			$module = 'User';
			$newResponse = ['patient_page'=>$request['patient_page']];
			$module_id = $request->user_id;
		}
		
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Patient view page toggle update',
			'link' => $link ?? '',
			'module' => $module,
			'object_id' => $module_id,
			'message' => $message,
			'old_response' => serialize($oldResponse),
			'new_response' => serialize($newResponse),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		return response()->json(['status' => true, 'error_msg' => 'Patient view page updated sucessfully'], 200);
	}

	public function changeDirectoryViewStatus(Request $request)
	{
		$updatePageView = User::where('id', $request['user_id'])->update(['show_in_directory' => $request['show_in_directory']]);
		$message = '';
		if (isset($request->patient_id) && !empty($request->patient_id)) {
			$link = url('/patient/view/') . $request->patient_id;
			$patient = $this->patientService->getDetailById($request->patient_id);
			$message = 'Patient ' . $patient->first_name . ' ' . $patient->last_name . ' has updated show in directory toggle button.';
			$module = 'Patient';
		} else {
			$link = url('user-view/') . $request->user_id;
			$user = Auth::user();
			$message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has updated show in directory toggle button.';
			$module = 'User';
		}
		
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type' => 'Show In Directory toggle update',
			'link' => $link ?? '',
			'module' => $module,
			'object_id' => $request->user_id,
			'message' => $message,
			'new_response' => serialize(array('show_in_directory' => $request['show_in_directory'])),
			'ip' => $ipaddress,
		];
		LogsService::save($insertLog);
		return response()->json(['status' => true, 'error_msg' => 'Show in Directory status updated successfully'], 200);
	}

	public function changeHubStatus(Request $request)
	{
		if(isset($request['show_hub'])){
			User::where('id', $request['user_id'])->update(['show_hub' => $request['show_hub']]);
			$message = '';
			$link = url('user-view/') . $request->user_id;
			$user = Auth::user();
			$message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has updated show in hub toggle button.';
			$module = 'User';
			// $ipaddress = request()->getClientIp();
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Show In Hub toggle update',
				'link' => $link ?? '',
				'module' => $module,
				'object_id' => $request->user_id,
				'message' => $message,
				'new_response' => serialize(array('show_hub' => $request['show_hub'])),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'Hub status updated successfully.'], 200);
		}else{
			return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 200);
		}
		
	}

	public function getPateintDocApprovedUser(Request $request)
	{
		$query = $this->userDocApprovalService->searchApprovalUser($request->q);
		$final = [];
		foreach ($query as $val) {
			$temp = [];
			$temp['id'] = $val->id;
			$temp['name'] = $val->first_name . ' ' . $val->last_name . ' ' . $val->agency_fk;
			$final[] = $temp;
		}
		return json_encode($final);
	}

	public function changeHubViewSSN(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'show_hub' => 'required',
			'user_id' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 422);
		} else {
			$getDetails = User::where('id', $request['user_id'])->first();
			$update = User::where('id', $request['user_id'])->update(['view_ssn_hub' => $request['show_hub']]);

			if($update){
				$message = '';
				$link = url('user-hub-view-ssn');
				$user = Auth::user();
				$message = $user->first_name . ' ' . $user->last_name . ' has updated the SSN status using the hub toggle button.';
				$module = 'User';
				// $ipaddress = request()->getClientIp();
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Show In View SSN toggle update',
					'link' => $link ?? '',
					'module' => $module,
					'object_id' => $request->user_id,
					'message' => $message,
					'old_response'=>serialize($getDetails->toArray()),
					'new_response' => serialize(array('view_ssn_hub' => $request['show_hub'])),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
				return response()->json(['status' => true, 'error_msg' => 'Hub SSN status updated successfully.'], 200);
			}else{
				return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 500);
			}
		}
	}

	public function twoFactorEnable(Request $request)
	{

		$userId= auth()->user()->id;
		if(isset($request['two_fact_auth'])){
			User::where('id', $userId)->update(['two_fact_auth' => $request['two_fact_auth']]);
			$message = '';
			$link = url('user-view/') . $userId;
			$user = Auth::user();
			$message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has updated show in hub toggle button.';
			$module = 'User';
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Two Factor Authentication',
				'link' => $link ?? '',
				'module' => $module,
				'object_id' => $userId,
				'message' => $message,
				'new_response' => serialize(array('two_fact_auth' => $request['two_fact_auth'])),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'User Two Factor Authentication successfully updated.'], 200);
		}else{
			return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 200);
		}
		
	}

		public function usertwoFactorEnable(Request $request)
	{

		$userId= !empty($request->user_id) ? $request->user_id :"";
		if(isset($request['two_fact_auth'])){
			User::where('id', $userId)->update(['two_fact_auth' => $request['two_fact_auth']]);
			$message = '';
			$link = url('user-view/') . $userId;
			$user = Auth::user();
			$message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has updated show in hub toggle button.';
			$module = 'User';
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Two Factor Authentication',
				'link' => $link ?? '',
				'module' => $module,
				'object_id' => $userId,
				'message' => $message,
				'new_response' => serialize(array('two_fact_auth' => $request['two_fact_auth'])),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'User Two Factor Authentication successfully updated.'], 200);
		}else{
			return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 200);
		}
		
	}

	public function resendOTP(Request $request){
		$validator = Validator::make($request->all(), [
			'id' => 'required'
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		}else{
			$getUserDetails = User::getDetailsById($request->id);
			$two_factor_code = rand(pow(10, 4 - 1), pow(10, 4) - 1);
			$data['rand_no'] =   $two_factor_code;
			$data['otp_expired_time'] = now()->addMinutes(10);
			User::where('id', $request->id)->update($data);
			
			$first_name = $getUserDetails->first_name.' '.$getUserDetails->last_name;
			$duration = "10 minutes";
			$_SUBJECT = "Two Factor Verification";
			$from = 'noreply@nybestmedicals.com';
			$emailData = array(
				'first_name' => $first_name,
				'two_factor_code' => $two_factor_code,
				'duration'=>$duration
			);
			$_CONTENT = Utility::getHtmlContent('email_template.sign_in_attempt_login_template',$emailData);
			AttachMailer::sendEmail($from, $getUserDetails->email, $_SUBJECT, $_CONTENT);
			return response()->json(['error_msg' =>'Mail has been sent to you with verification code.','data'=>array('otp_expired_time'=>date('Y-m-d H:i:s',strtotime($data['otp_expired_time'])))]);
		}
	}

	public function otpValid(Request $request){
		$validator = Validator::make($request->all(), [
			'id' =>'required',
			'otp'=>'required'
		]);
		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
		}else{
			$query = User::whereRaw('id = "' . $request->id . '"')->where('rand_no', $request->otp)->where('delete_flag', 'N')->first();
			if (!$query) {
				return response()->json([
					'error_msg' => "Invalid OTP",
					'status'    =>0,
					'data'      => []
				], 422);
			} elseif ($query->otp_expired_time < now()) {
				return response()->json([
					'error_msg' => "OTP is expired",
					'status'    =>0,
				], 422);
			}
			return response()->json([
				'error_msg' => "",
				'status'  =>0,
				'data'      => []
			], 200);
		}
	}

	public function creatorEmailNotiToggle(Request $request)
	{
		if(isset($request['creator_email_noti_toggle'])){
			$userDetails = User::select('id','creator_email_noti_toggle')->where('id',$request['user_id'])->first();

			$creator_email_noti_toggle = 1;
			if($userDetails->creator_email_noti_toggle == 1){
				$creator_email_noti_toggle = 0;
			}
			User::where('id', $request['user_id'])->update(['creator_email_noti_toggle' => $creator_email_noti_toggle]);
			$link = url('user-view/') . $request->user_id;
			$user = Auth::user();
			$message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has updated '.$creator_email_noti_toggle == 1 ? 'Enable' : 'Disable'.' Creator Email Notification.';
			$module = 'User';
			$ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Creator Email Notification',
				'link' => $link ?? '',
				'module' => $module,
				'object_id' => $request->user_id,
				'message' => $message,
				'new_response' => serialize(array('creator_email_noti_toggle' => $creator_email_noti_toggle)),
				'old_response' => serialize(array('creator_email_noti_toggle' => $userDetails->creator_email_noti_toggle)),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
			return response()->json(['status' => true, 'error_msg' => 'User Creator Email Notification successfully updated.'], 200);
		}else{
			return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 200);
		}
		
	}

	public function userTelehealthToggle(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'user_id'       => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['status' => false, 'error_msg' => $validator->errors()->all()[0]], 422);
		}

		$userDetails = User::select('id', 'is_telehealth')->where('id', $request->user_id)->first();
		if (!$userDetails) {
			return response()->json(['status' => false, 'error_msg' => 'User not found.'], 422);
		}
		$is_telehealth =  $userDetails->is_telehealth ? 0 : 1;
		User::where('id', $request->user_id)->update(['is_telehealth' => $is_telehealth]);

		$link = url('user-telehealth-toggle');
		$user = Auth::user();
		$message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has ' . ($is_telehealth == 1 ? 'enabled' : 'disabled') . ' Telehealth File Access.';
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type'         => 'Telehealth File Access',
			'link'         => $link ?? '',
			'module'       => 'User',
			'object_id'    => $request->user_id,
			'message'      => $message,
			'new_response' => serialize(['is_telehealth' => $is_telehealth]),
			'old_response' => serialize(['is_telehealth' => $userDetails->is_telehealth]),
			'ip'           => $ipaddress,
		];
		LogsService::save($insertLog);

		return response()->json(['status' => true, 'error_msg' => 'User Telehealth File Access successfully updated.'], 200);
	}

	public function userMdoToggle(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'user_id' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['status' => false, 'error_msg' => $validator->errors()->all()[0]], 422);
		}

		$userDetails = User::select('id', 'is_mdo')->where('id', $request->user_id)->first();
		if (!$userDetails) {
			return response()->json(['status' => false, 'error_msg' => 'User not found.'], 422);
		}
		$is_mdo =  $userDetails->is_mdo ? 0 : 1;
		User::where('id', $request->user_id)->update(['is_mdo' => $is_mdo]);

		$link = url('user-mdo-toggle');
		$user = Auth::user();
		$message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has ' . ($is_mdo == 1 ? 'enabled' : 'disabled') . ' MDO File Access.';
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type'         => 'MDO File Access',
			'link'         => $link ?? '',
			'module'       => 'User',
			'object_id'    => $request->user_id,
			'message'      => $message,
			'new_response' => serialize(['is_mdo' => $is_mdo]),
			'old_response' => serialize(['is_mdo' => $userDetails->is_mdo]),
			'ip'           => $ipaddress,
		];
		LogsService::save($insertLog);

		return response()->json(['status' => true, 'error_msg' => 'User MDO File Access successfully updated.'], 200);
	}

	public function userTemplateTypeUpdate(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'user_id' => 'required',
			'template_type' => 'required|in:All,Location,Telehealth',
		]);

		if ($validator->fails()) {
			return response()->json(['status' => false, 'error_msg' => $validator->errors()->all()[0]], 422);
		}

		$userDetails = $this->userService->getUserDetailsById($request->user_id);
		
		if (!$userDetails) {
			return response()->json(['status' => false, 'error_msg' => 'User not found.'], 422);
		}

		$oldTemplateType = $userDetails->template_type;

		$this->userService->update(['template_type' => $request->template_type],['id'=>$request->user_id]);

		$link = url('user-template-type-update');
		$user = Auth::user();
		$message = 'User ' . $user->first_name . ' ' . $user->last_name . ' has changed Template Type from ' . $oldTemplateType . ' to ' . $request->template_type . '.';
		$ipaddress = Utility::getIP();
		$insertLog = [
			'type'         => 'Template Type Update',
			'link'         => $link ?? '',
			'module'       => 'User',
			'object_id'    => $request->user_id,
			'message'      => $message,
			'new_response' => serialize(['id' => $request->user_id,'template_type' => $request->template_type]),
			'old_response' => serialize($userDetails->toArray()),
			'ip'           => $ipaddress,
		];
		LogsService::save($insertLog);

		return response()->json(['status' => true, 'error_msg' => 'Template Type successfully updated.'], 200);
	}

	public function userRestrict(Request $request){
		
		$validator = Validator::make($request->all(), [
			'status' => 'required',
			'user_id' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 422);
		} else {
			$getDetails = User::getDetailsById($request['user_id']);
			$update = User::where('id', $request['user_id'])->update(['restrict_user' => $request['status']]);
			if($update){
				$message = '';
				$link = url('user-restrict');
				$user = Auth::user();
				$message = $user->first_name . ' ' . $user->last_name . ' has updated the user restriction status using the toggle button.';
				$module = 'User';
			
				$ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Restrict User',
					'link' => $link ?? '',
					'module' => $module,
					'object_id' => $request->user_id,
					'message' => $message,
					'old_response'=>serialize($getDetails->toArray()),
					'new_response' => serialize(array('restrict_user' => $request['status'])),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
				return response()->json(['status' => true, 'error_msg' => 'User restriction status updated successfully.','data'=>array('restrict_user' => $request['status'])], 200);
			}else{
				return response()->json(['status' => false, 'error_msg' => 'Something went to wrong.'], 500);
			}
		}
	}

	public function searchAllUserData(Request $request)
	{
		$query = UserHelper::searchUsers($request->q);
		$final = [];
		foreach ($query as $val) {
			$temp = [];
			$userType = "Nybest User";
			if ($val->agency_fk != "") {
				$userType = "Agency User";
			}
			$temp['id'] = $val->id;
			$temp['name'] = $val->first_name . ' ' . $val->last_name . ' ( ' . $userType . ' ) ';
			$final[] = $temp;
		}
		return json_encode($final);
	}
}