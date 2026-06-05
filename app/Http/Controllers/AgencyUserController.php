<?php

namespace App\Http\Controllers;

use App\Exmed;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Helpers\AgencyWiseDomainHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Input;
use App\Helpers\UserHelper;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\User;
use App\Master;
use App\Agency;
use App\UserAgency;
use App\Record;
use App\Helpers\UserAgencyHelper;
use URL;
use App\Helpers\AttachMailer;
use App\Services\LoginLogService;
use App\Services\LogsService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\EncryptDecryptCodeHelper;
use App\Helpers\Utility;
use App\Helpers\Common;
class AgencyUserController extends BaseController
{
    public function __construct()
    {
   
        // $this->middleware('permission:agency-add-user', ['only' => ['add_page','add']]);
        $this->middleware('auth', ['except' => ['AcceptInvivation', 'AcceptView']]);

    }


    public function add_page(Request $request)
    {
        $agenciesId = EncryptDecryptCodeHelper::decryptData($request['id']);
       
        $data['user'] = $user = auth()->user();
        if (empty($user)) {
            return redirect('/');
        }
        if ($user['user_type_fk'] == 4) {
            return abort(404);
        }
        /* Condition for agency user not access agency add user if it have no access */
        if ($user->user_type_fk != 184 && auth()->user()->agency_fk == "" && auth()->user()->role_access != 1) {
            return abort(404);
        }

        if ($user->user_type_fk == 184 && auth()->user()->agency_fk == "") {
            $data['user'] = $user = auth()->user();
            $permissions = [];

            foreach ($user->roles as $role) {
                $permissions = array_merge($permissions, $role->permissions->pluck('name')->toArray());
            }
            if(!in_array('agency-add-user',$permissions)){
                return abort(403);
            }
        }

        $data['menu'] = "Add user";
        // $data['agencyList'] = Exmed::ROLES;
        if ($user->user_type_fk == 184) {
            $data['loginType'] = Master::where('master_type_fk', '1')->where('del_flag', 'N')->orderBy('name', 'asc')->get();
        } else {
            $data['loginType'] = Master::where('id', '2')->where('del_flag', 'N')->orderBy('name', 'asc')->get();
        }

        $data['userType'] = Master::whereIn('id', array(184, 4, 5, 6))->where('del_flag', 'N')->orderBy('name', 'asc')->get();
        $data['agencyList'] = Agency::where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
        if (isset($request->id) || in_array($user['user_type_fk'], array(5, 6))) {

            // nayan
            $agencyId = isset($request->id) ? $request->id : sha1($user['agency_fk']);
            $agencyId = EncryptDecryptCodeHelper::decryptData($agencyId);
            $data['domainName'] = AgencyWiseDomainHelper::getDomainList($agencyId);

            $data['agencyId'] = Agency::getIdById($agenciesId);
           
        }
        if($agenciesId){
            $data['agencyName'] = Agency::where('id', $agenciesId)->where('delete_flag', 'N')->first();
        }
        if(auth()->user()->agency_fk != ""){
            return view("agency-user.agency_user_add", $data);
        }else{
            return view("agency_user.add", $data);
        }
    }
    public function add(Request $request)
    {
        if(!isset($request['uid'])){
            Session::flash('success', 'Please enter atleast one row for user');
            return redirect()->back(); 
        }
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'domain' => 'required',
        ]);
       
        if ($validator->fails()) {
            return redirect("/agency/adduser?id=" . $request->input('current_agency_id'))
                ->withErrors($validator, 'add_user')
                ->withInput();
        } else {
            $cnt = 0;
            $insert = 0;
            $emailArray = array();
            if (isset($request->input('first_name')[0]) && is_array($request->input('first_name')) && !empty($request->input('first_name')[0])) {
                foreach ($request->input('first_name') as $key => $val) {
                    $emails = '';
                    $emailAddress = '';
                    if (isset($request->input('email')[$key]) && $request->input('email')[$key] != '') {
                        $emails = $request->input('email')[$key];
                    }
                    if (isset($request->input('domain')[$key]) && $request->input('domain')[$key] != '' && $emails != '') {
                        $getDetails = AgencyWiseDomainHelper::getDetailsById($request->input('domain')[$key]);

                        $emailAddress = $emails . '@' . $getDetails->domain;
                    }
                    $emailArray[] = $emailAddress;
                    $userExit = User::where('email', $emailAddress)->where('delete_flag', 'N')->get();
                    if (count($userExit) == 0) {
                        $final_array = array(
                            'first_name' => $val,
                            'last_name' => $request->input('last_name')[$key],
                            'email' => strtolower($emailAddress),
                            'phone' => $request->input('phone')[$key],
                            'ext' => $request->input('ext')[$key],
                            'login_type_fk' => 2,
                            'user_type_fk' => 6,
                            'active' => 'active',
                            'agency_fk' => $request['uid'],
                            'record_access' =>$request->input('record_access')[$key],
                            'department' =>$request->input('department')[$key],
                            'role_access' => isset($request->input('role_access')[$key]) && $request->input('role_access')[$key] == 1 ? 1 : 0,
                        );
                        $insert = UserHelper::insert($final_array);

                        if ($insert) {

                            // $ipaddress = request()->getClientIp();
                            $ipaddress = Utility::getIP();
                            $insertLog = [
                                'type' => "Add Agency User",
                                'link' => url('/agency/add_user'),
                                'module' => 'Agency',
                                'object_id' => $insert,
                                'message' => $user->first_name . ' ' . $user->last_name . " has added Agency's User",
                                'new_response' => serialize($final_array),
                                'ip' => $ipaddress,
                            ];
                            LogsService::save($insertLog);

                            $from = 'notifications@nybestmedical.com';
                            $subject = 'Account Activation - NyBest Medicals';

                            // $message = $this->getHtml("", $insert);

                            // if (isset($request->input('user_type')[$cnt])) {
                                // if (isset($request->input('user_type')[$cnt]) && $request->input('user_type')[$cnt] == 5) {
                                //     $message = $this->getHtml($request->input('user_type')[$cnt], $insert);
                                // } else {
                                //     $message = $this->getHtml($request->input('user_type')[$cnt], $insert);
                                // }
                                $message = $this->getHtml(6, $insert);
                                $final_aray = array('to' => $emailAddress, 'from' => $from, 'subject' => $subject, 'message' => $message);
                               try {
                                    Mail::mailer('second')->send([], [], function ($m) use ($final_aray) {
                                        // $m->from(env('MAIL_USERNAME'), 'NyBest');
                                        $m->to(trim($final_aray['to']), "NyBest")->subject($final_aray['subject'])->html($final_aray['message']);
                                    });
                               } catch (\Throwable $th) {
                                //throw $th;
                               }
                            // }
                        }
                        $cnt++;
                    }else{
                        Session::flash('error', 'Sorry, Same users are already active in portal.');
                    }
                }
            }else{
                $emails = '';
                $emailAddress = '';
                $emails = $request->input('email')??'';
                if ($request->input('domain') != '' && $emails != '') {
                    $getDetails = AgencyWiseDomainHelper::getDetailsById($request->input('domain'));

                    $emailAddress = $emails . '@' . $getDetails->domain;
                }
                $emailArray[] = $emailAddress;
                $userExit = User::where('email', $emailAddress)->where('delete_flag', 'N')->get();
                if (count($userExit) == 0) {
                    $final_array = array(
                        'first_name' => $request->input('first_name'),
                        'last_name' => $request->input('last_name'),
                        'email' => strtolower($emailAddress),
                        'phone' => $request->input('phone'),
                        'ext' => $request->input('ext'),
                        'login_type_fk' => 2,
                        'user_type_fk' => 6,
                        'active' => 'active',
                        'agency_fk' => $request['uid'],
                        'record_access' =>$request->input('record_access'),
                        'department' =>$request->input('department'),
                        'role_access' => $request->input('role_access') == 1 ? 1 : 0,
                    );
                    $insert = UserHelper::insert($final_array);

                    if ($insert) {

                        // $ipaddress = request()->getClientIp();
                        $ipaddress = Utility::getIP();
                        $insertLog = [
                            'type' => "Add Agency User",
                            'link' => url('/agency/add_user'),
                            'module' => 'Agency',
                            'object_id' => $insert,
                            'message' => $user->first_name . ' ' . $user->last_name . " has added Agency's User",
                            'new_response' => serialize($final_array),
                            'ip' => $ipaddress,
                        ];
                        LogsService::save($insertLog);

                        $from = 'notifications@nybestmedical.com';
                        $subject = 'Account Activation - NyBest Medicals';

                        $message = $this->getHtml(6, $insert);
                        $final_aray = array('to' => $emailAddress, 'from' => $from, 'subject' => $subject, 'message' => $message);
                        try {
                            Mail::mailer('second')->send([], [], function ($m) use ($final_aray) {
                                $m->to(trim($final_aray['to']), "NyBest")->subject($final_aray['subject'])->html($final_aray['message']);
                            });
                        } catch (\Throwable $th) {
                        }
                    }
                    $cnt++;
                }else{
                    Session::flash('error', 'Sorry, Same users are already active in portal.');
                }
            }

            if ($insert) {
                try {
                    if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
                        $agencyNotifyData = array(
                            'agencyid' => auth()->user()->agency_fk,
                            'title' => 'Created new User',
                            'record_id' => $insert,
                            'record_type' => 'User',
                            'msg' => '',
                            'res_data' => serialize($final_array),
                        );
                        Common::insertAgencyNotificationsOfUser($agencyNotifyData);
                    }
                } catch (\Throwable $th) {}
                Session::flash('success', __('Successfully added. Activation mail has been sent.', array('name' => "User")));
                if (isset($request->uid)) {
                    if(auth()->user()->agency_fk != ''){
                        return redirect("/agency-setting");
                    }else{
                        return redirect("/agency-view" . '/' . $request->uid);
                    }
                } else {
                    if(auth()->user()->agency_fk != ''){
                        return redirect("/agency-setting");
                    }else{
                        return redirect('/user');
                    }
                }
            } else {
                if(auth()->user()->agency_fk != ''){
                    return redirect("/agency-setting");
                }else{
                    return redirect('/user');
                }
                Session::flash('error', 'Sorry, Users are not activated. Please check details.');
            }
        }
    }
    function getHtml($user_type, $id)
    {
        $query = User::where('id', $id)->where('delete_flag', 'N')->first();
        $url  = URL::to('/') . '/invitation-accept/' . sha1($query->id);
        $user = auth()->user();
        $fname = '';
        if ($user->first_name != '') {
            $fname  = $query->first_name;
        }
        $lname  = '';
        if ($user->last_name != '') {
            $lname  = $query->last_name;
        }
        $img = URL::to('/') . '/img/logo.png';
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
									  You have been invited by ' . $fname . " " . $lname . ' to sign up and start using the portal right away!
use the link here to Create your Password and sign in.
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
									  Dear ' . $query->first_name . " " . $query->last_name . ', you have been invited by ' . $fname . " " . $lname . ' to join the ENyBest Medicals Client Portal.
									 
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
			'user_first_name' => $user->first_name??'',
			'user_last_name' => $user->last_name??'',
			'user_type' => $user_type,
		);
		$message = Utility::getHtmlContent('email_template.user_invitation',$emailData);

        return $message;
    }
}
