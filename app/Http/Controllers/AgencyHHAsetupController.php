<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Agency;
use App\Helpers\UserHelper;
use App\Helpers\AgencyWiseDomainHelper;
use App\Helpers\AttachMailer;
use App\User;
use Illuminate\Support\Facades\URL;

class AgencyHHAsetupController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $data['menu'] = "Agency Setup";
        $data['user'] = $user = auth()->user();
        
        if ($user['user_type_fk'] != 184 || $user['nybest_user_access']==0) {
            return abort(403);
        }
        $agency_name = $data['agency_name'] = request('agency_name');
        $email = $data['email'] = request('email');
        $phone = $data['phone'] = request('phone');
        $city = $data['city'] = request('city');

        $data['query'] = Agency::nyBestAgencyList($agency_name, $email, $phone, $city);
      
        return view("agency-hha-setup/index", $data);
    }

    function view(Request $request,$id){
        $data['menu'] = "Agency";
        $data['title'] = "Agency View";
        $data['user'] = $user = auth()->user();
        if ($user['user_type_fk'] != 184 || $user['nybest_user_access']==0) {
            return abort(403);
        }
        $data['id'] = $id;
        $data['agencyDetails'] = Agency::where('delete_flag', 'N')->where('id', $id)->where('service_md_appointment',1)->first();

        if ($data['agencyDetails'] != '') {
           
          
            $data['domainName'] = AgencyWiseDomainHelper::getDomainByAgencyId(sha1($id));
    
            return view('agency-hha-setup/view_agency', $data);
        } else {
            abort(404);
        }
    }
    public function userSave(Request $request)
    {
        // return $request->all();
        $user = auth()->user();


        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'domain_email' => 'required',
            'domain_id' => 'required',
            'phone' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
        } else {
            $getDetails = AgencyWiseDomainHelper::getDetailsById($request->input('domain_id'));
            $emailAddress = $request->input('domain_email') . '@' . $getDetails->domain;

            $checkExistingEmail = UserHelper::checkUserBlockOrNotByEmail($emailAddress);
            if(isset($checkExistingEmail->id) && $checkExistingEmail->id !=''){
                return response()->json(['error_msg' => "Email already exist", 'status' => 0, 'data' => array()], 400);
            }else{
                $final_array = array(
                    'login_type_fk'=>2,
                    'user_type_fk'=>6,
                    'agency_fk'=>$request->input('agency_id'),
                    'first_name'=>$request->input('first_name'),
                    'last_name'=>$request->input('last_name'),
                    'email'=>$emailAddress,
                    'ext'=>$request->input('ext'),
                    'phone'=>$request->input('phone'),
                    'hospital_flag'=>1
                );
                $insert = UserHelper::insert($final_array);
                if($insert){
                    $from = 'noreply@nybestmedicals.com';
                    $subject = 'Account Activation - NY Best Medicals';
                    $message = $this->getHtml(6, $insert);
                    $insert = AttachMailer::sendEmail($from, $emailAddress, $subject, $message);
                    
                    return response()->json(['error_msg' => "Successfully Inserted", 'status' => 0, 'data' => array()], 200);
                }else{
                    return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 0, 'data' => array()], 500);
                    
                }
            }
 
        }
    }

    function usertList(Request $request){
        $data['page'] = $page = $request->input('page');
        $data['UsersList'] = UserHelper::getAgencyWiseUserList($request->input('agency_id'));
        return view('agency-hha-setup/agency_user_list',$data);
    }
    public function updateHHASetup(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'app_name' => 'required',
            'app_key' => 'required',
            'app_token' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
        }

        $data = array(
            'app_name' => $request->input('app_name'),
            'app_key' => $request->input('app_key'),
            'app_token' => $request->input('app_token'),
        );
        $update = Agency::where('id', $request->input('id'))->update($data);
        if ($update) {
            return response()->json(['error_msg' => "HHA setup successfully updated", 'status' => 1, 'data' => array()], 200);
        }
        return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 0, 'data' => array()], 500);
    }

    public function getHHASetup(Request $request)
    {
        $id = $request->input('id');
        $data = Agency::where("id", $id)->first();
        return response()->json(['status' => "1", 'error_msg' => "Success.", 'data' => $data]);
    }

    public function enableDisableHHASetup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 400);
        }
        
        $data = array(
            'enable_disable' => $request->input('status'),
        );
        $update = Agency::where('id', $request->input('id'))->update($data);
        if ($update) {
            return response()->json(['error_msg' => "HHA setup successfully updated", 'status' => 1, 'data' => array()], 200);
        }
        return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.", 'status' => 0, 'data' => array()], 500);
    }
    function getHtml($user_type, $id)
	{
		$query = User::where('id', $id)->where('delete_flag', 'N')->first();
		$url  = URL::to('/') . '/invitation-accept/' . sha1($query->id);
		$user = auth()->user();
		$fname = '';
		if ($user->first_name != '') {
			$fname  = $user->first_name;
		}
		$lname  = '';
		if ($user->last_name != '') {
			$lname  = $user->last_name;
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

		return $message;
	}
}
