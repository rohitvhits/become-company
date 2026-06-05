<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;

use App\Helpers\UserHelper;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Master;
use App\Agency;
use URL;
use App\Services\LogsService;
use Illuminate\Support\Facades\Mail;
use App\Helpers\EncryptDecryptCodeHelper;
use App\Helpers\Utility;
use App\Services\AgencyWiseNotificationEmailService;
use App\Helpers\AgencyNotificationEmailHelper;
use App\Helpers\Common;
class AgencySettingController extends BaseController
{
    protected $agencyWiseNotificationEmailService = "";
    public function __construct(AgencyWiseNotificationEmailService $agencyWiseNotificationEmailService)
    {
        $this->middleware('auth', ['except' => ['AcceptInvivation', 'AcceptView']]);
        $this->agencyWiseNotificationEmailService = $agencyWiseNotificationEmailService;
    }

    public function agencySetting(Request $request){
		$data['menu'] = "User";
		$data['title'] = "User View";
		$data['user'] = $user = auth()->user();

        if($user->role_access != 1  ||  $user->agency_fk == ""){
            abort(404);
        }
		$data['agency'] = request('agency_id');
        
        $agencyWiseNotificationEmail =  Master::getAllDataByMasterTypeFk(array(24,28));
        $statusUpdateArray = [];
        $generateUpdateArray = [];
        if(!empty($agencyWiseNotificationEmail[0])){
            foreach($agencyWiseNotificationEmail as $val){
                if($val->master_type_fk !='28'){
                    if($val->name !='Add New Record'){
                        $statusUpdateArray[] = $val;
                    }
                }else{
                    $generateUpdateArray[] =$val;
                }
                
            }
        }
        $data['agencyWiseNotificationEmail'] = $statusUpdateArray;
        $data['loginType'] = Master::where('id', '2')->where('del_flag', 'N')->first();
		$final = [];
		$data['roles'] = $final;
		// $data['query'] = $usersDetails;
        $data['encryptedId'] = EncryptDecryptCodeHelper::encryptData($user->agency_fk);
		return view('agency_setting/agency_setting_view', $data);
    }

    public function assignUserRole(Request $request){
        if(isset($request->role_access)){
            $query = User::getDetailsById($request->userId);
            UserHelper::update(array('role_access' => $request->role_access), array('id' => $request->userId));
            $updateData = User::getDetailsById($request->userId);
            // $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Users Role Access Update',
                'object_id' => $request->userId,
                'message' => "Role access has been Updated",
                'old_response' => serialize($query),
                'new_response' => serialize($updateData),
                'ip' => $ipaddress,
                'created_by' => auth()->user()->id,
            ];
            LogsService::save($insertLog);
            return response()->json(['error_msg' => "Role Access successfully updated", 'status' => $request->input('status'), 'data' => array('status' => $request->input('status'))], 200);
        }else{
            return response()->json(["message"   => "Sorry,something went wrong. Please try again",'status' => 'error','data' => array(),],200);
        }
    }

    public function agencyWiseUserList(Request $request)
	{
		$data['menu'] = "user";

		$data['user'] = $user = auth()->user();
        if($user->role_access != 1  ||  $user->agency_fk == ""){
            abort(404);
        }
		/*Serch*/
		$data['loginType'] = Master::where('id', '2')->where('del_flag', 'N')->first();
		$data['first_name']  = request('first_name');
		$data['email'] = request('email');
		$data['phone'] = request('phone');
		$data['status'] = request('status');
		$data['created_by'] = request('created_by');
		$data['created_date'] = request('created_date');
		$data['full_name'] = request('full_name');
        
        if($request->created_date != ''){
            $created_date = explode('-',$request->created_date);
            if(count($created_date) > 0){
                $data['start_date'] = isset($created_date[0]) ? date('Y-m-d',strtotime(trim($created_date[0]))):'';
                $data['end_date'] = isset($created_date[1]) ? date('Y-m-d',strtotime(trim($created_date[1]))):'';
            }
        }
        // $agency = Agency::getAgencyList()->toArray();
        // $agency_ids = array_column($agency,'id'); // uncomment this code in case of access agency user data
        $agency_ids = array($user->agency_fk);
		$usersDetails = User::getUserDataAgency($data,$agency_ids);
		$final = [];
		$data['roles'] = $final;
		$data['query'] = $usersDetails;
        $data['encryptedId'] = EncryptDecryptCodeHelper::encryptData($user->agency_fk);
		return view("agency-user/agency_user", $data);
	}

    public function agencyWiseUserExport(Request $request)
	{
		$user = auth()->user();
        if($user->role_access != 1  ||  $user->agency_fk == ""){
            abort(404);
        }
		$data['email'] = request('email');
		$data['phone'] = request('phone');
		$data['status'] = request('status');
		$data['created_by'] = request('created_by');
		$data['created_date'] = request('created_date');
		$data['full_name'] = request('full_name');
        // $agency = Agency::getAgencyList()->toArray();
        // $agency_ids = array_column($agency,'id'); // uncomment this code in case of access agency user data
        $agency_ids = array($user->agency_fk);
        
        $users = User::getUserDataAgency($data,$agency_ids);
        if(count($users) > 0 ){
            $filename = 'User' . date("m-d-Y");
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0",
            );
	            $columns = array('# NO','Full Name', 'Email', 'Phone','EXT','Status','Is Admin','Last Login Date','Last Login IP Address','Created Date','Created By');

            $callback = function () use ($users, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach ($users as $key => $list) {
                    fputcsv($file, array($key+1, $list->first_name.' '.$list->last_name, strtolower($list->email), $list->phone,$list->ext, ucfirst($list->active),$list->role_access== 1 ? 'Yes':'No', $list->last_login_at,$list->last_login_ip,date('m-d-Y h:i:s', strtotime($list->created_at)),$list->created_by_fname .' '.$list->created_by_lname));
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }else{
            return null;
        }
	}
    
    public function agencyUserView($id)
	{
		
		$data['menu'] = "User";
		$data['title'] = "User View";
		$data['user'] = $user = auth()->user();
		$data['id'] = $id;

		$data['userDetails'] = User::getDataById($id);
		if (($data['userDetails']) == "" || ($user['user_type_fk'] == 5 || $data['userDetails']->agency_fk !=  $user['agency_fk'])) {
			return abort(404);
		}
        if($user->role_access != 1  ||  $user->agency_fk == ""){
            abort(404);
        }
		$data['agency_list'] = Agency::select('id', 'agency_name')->where('delete_flag', 'N')->orderBy('agency_name', 'asc')->get();
		$data['agency'] = $agency_id = request('agency_id');
        $typeLog = 0;
		$EMCtypeLog = 0;
		$data['totalEmcCountRecord'] = $EMCtypeLog;
		$data['totalAgencyCountRecord'] = $typeLog;
        $agencyWiseNotificationEmail =  Master::getAllDataByMasterTypeFk(array(24,28));
        $statusUpdateArray = [];
        $generateUpdateArray = [];
        if(!empty($agencyWiseNotificationEmail[0])){
            foreach($agencyWiseNotificationEmail as $val){
                if($val->master_type_fk !='28'){
                    if($val->name !='Add New Record'){
                        $statusUpdateArray[] = $val;
                    }
                }else{
                    $generateUpdateArray[] =$val;
                }
                
            }
        }
        $data['agencyWiseNotificationEmail'] = $statusUpdateArray;
		return view('agency-user/agency_user_view', $data);
	}

    public function agencyUserDelete()
	{
		$user = auth()->user();
		if ($user['agency_fk'] == "" && $user['role_access'] != 1) {
			return abort(404);
		}
		$data['id'] = $id = request("i");
		$delArr = array('delete_flag' => 'Y', 'deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id);

		$update = User::where('id', $id)->update($delArr);

		if ($update) {
			// $ipaddress = request()->getClientIp();
            $ipaddress = Utility::getIP();
			$insertLog = [
				'type' => 'Delete Agency User',
				'link' => url('/agency-user-delete?i=', $id),
				'module' => 'User',
				'object_id' => $id,
				'message' => $user->first_name . ' ' . $user->last_name . ' has deleted Agency User',
				'new_response' => serialize($delArr),
				'ip' => $ipaddress,
			];
			LogsService::save($insertLog);
            try {
                if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
                    $agencyNotifyData = array(
                        'agencyid' => auth()->user()->agency_fk,
                        'title' => 'Deleted Agency User',
                        'record_id' => $id,
                        'record_type' => 'User',
                        'msg' => '',
                        'res_data' => serialize($delArr),
                    );
                    Common::insertAgencyNotificationsOfUser($agencyNotifyData);
                }
            } catch (\Throwable $th) {}
			Session::flash('success', 'User successfully deleted');

			return redirect('/agency-setting');
		} else {

			Session::flash('error', 'Sorry, something went wrong. Please try again.');

			return redirect('/agency-setting');
		}
	}

    public function getAgencyUserDetail(Request $request)
	{
		$data['userDetails'] = User::getDataById($request->id);
        if($data['userDetails']->email !=""){
            $explode = explode('@',$data['userDetails']->email);
            $data['userDetails']->email = $explode[0]??''; 
        }
		return response()->json(['error_msg' => "", 'status' => '', 'data' => $data], 200);
	}

    public function saveEmailNotification(Request $request){
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'email' =>  'required|email',
            
        ]);
        
        if ($validator->fails()) {
        return response()->json(['error_msg' => $validator->errors()->all()[0],  'data' => array()], 400);
        }
        else {
           
            $patients= '';
           
            if(!empty($request->patient[0])){
                $patients= implode(',',$request->patient);
            }

            $caregivers= '';
            if(!empty($request->caregiver[0])){
                $caregivers= implode(',',$request->caregiver);
            }
           
            $data = array(
                'email'=>$request->email,
                'agency_id'=>$request->agency_id,
                'patients'=>$patients,
                'caregivers'=>$caregivers,
                'patients_id'=>$request->patient_id,
                'caregivers_id'=>$request->caregivers_id,
            );
            $data['service_id'] = "";
            if($request->service_id !=""){
                $data['service_id']=implode(',',$request->service_id);
            }
            $discipline_id = "";
            if($request->discipline_id !=""){
                $discipline_id=implode(',',$request->discipline_id);
            }
            $data['discipline_id']=$discipline_id;
            $new_response = $old_response = "";
            if($request->id !=""){
                $old_response = $this->agencyWiseNotificationEmailService->getDetailById($request->id);
                $save = $this->agencyWiseNotificationEmailService->update($data,['id'=>$request->id]);
                $new_response = $this->agencyWiseNotificationEmailService->getDetailById($request->id);  
            }else{
                $save = $this->agencyWiseNotificationEmailService->save($data);  
                $new_response = $this->agencyWiseNotificationEmailService->getDetailById($save);
            }
           
            if($request->id !=""){
                $msg = 'Notification Email successfully updated';
                $type = "Update Agency User Notification";
                // $ipaddress = request()->getClientIp();
                $ipaddress = Utility::getIP();
				$insertLog = [
					'type' => $type,
					'link' => url('agency-email-notification-email-save'),
					'module' => 'User',
					'object_id' => $request->id,
					'message' => $msg.' by '.auth()->user()->first_name.' '.auth()->user()->last_name ,
                    'old_response' => serialize($old_response),
					'new_response' => serialize($new_response),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);
                try {
                    if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
                        $agencyNotifyData = array(
                            'agencyid' => $request->agency_id,
                            'title' => 'Updated Notification Email',
                            'record_id' => $request->id,
                            'record_type' => 'Notification Email',
                            'msg' => '',
                            'res_data' => serialize(array($request->all())),
                        );
                        Common::insertAgencyNotificationsOfUser($agencyNotifyData);
                    }
                } catch (\Throwable $th) {}
                return response()->json(['error_msg' => $msg,  'data' => array()], 200);
            }else{
                if($save){
                    $msg = 'Notification Email successfully added';
                    $type = "Add Agency User Notification";
                    $ipaddress = Utility::getIP();
                    $insertLog = [
                        'type' => $type,
                        'link' => url('agency-email-notification-email-save'),
                        'module' => 'User',
                        'object_id' => $save??0,
                        'message' => $msg.' by '.auth()->user()->first_name.' '.auth()->user()->last_name,
                        'new_response' => serialize($new_response),
                        'ip' => $ipaddress,
                    ];
                    LogsService::save($insertLog);
                     try {
                        if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
                            $agencyNotifyData = array(
                                'agencyid' => $request->agency_id,
                                'title' => 'Added Notification Email',
                                'record_id' => $save??0,
                                'record_type' => 'Notification Email',
                                'msg' => '',
                                'res_data' => serialize(array($request->all())),
                            );
                            Common::insertAgencyNotificationsOfUser($agencyNotifyData);
                        }
                    } catch (\Throwable $th) {}
                    return response()->json(['error_msg' => $msg,  'data' => array()], 200);
                }else{
                    return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.",  'data' => array()], 500);
                }
            }  
        }
    }

    function agencyUserWiseNotificationList(Request $request){
        // dd($request);
        $data['page'] = $request->input('page');
        $query = AgencyNotificationEmailHelper::notificationEmailByAgencyId($request->input('agency_id'));
        foreach($query as $val){
            $finals = [];
            if($val->service_id !=""){
                $getDetails = Master::geServiceName($val->service_id);
                foreach($getDetails->toArray() as $names){
                    $finals[] = $names['name'];
                }
            }
            $val->service_name =$finals;
            $val->patients_exp = explode(',',$val->patients);
            $val->caregivers_exp = explode(',',$val->caregivers);
            $val->discipline_id = explode(',',$val->discipline_id);
        }
        $data['query'] = $query;
        $data['color'] = array('success','info','danger','warning','primary');
        return view("agency-user._partial.list_notification", $data);
    }

    function deleteNotificationEmailAgency(Request $request){
        $deleted = AgencyNotificationEmailHelper::SoftDelete(array('delete_flag'=>'Y'),array('id'=>$request->id));
        if($deleted){
            $msg = 'Notification Email successfully added';
            $type = "Delete Agency User Notification";
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => $type,
                'link' => url('agency-email-notification-email-delete'),
                'module' => 'User',
                'object_id' => $request->id??0,
                'message' => $msg.' by '.auth()->user()->first_name.' '.auth()->user()->last_name,
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            try {
                if(isset(auth()->user()->agency_fk) && !empty(auth()->user()->agency_fk) ){
                    $agencyNotifyData = array(
                        'agencyid' => auth()->user()->agency_fk,
                        'title' => 'Deleted Notification Email',
                        'record_id' => $request->id??0,
                        'record_type' => 'Notification Email',
                        'msg' => '',
                        'res_data' => serialize(array($request->all())),
                    );
                    Common::insertAgencyNotificationsOfUser($agencyNotifyData);
                }
            } catch (\Throwable $th) {}
            return response()->json(['error_msg' => "Notification Email Successfully deleted",  'data' => array()], 200);
        }else{
            return response()->json(['error_msg' => "Sorry, something went wrong. Please try again.",  'data' => array()], 500);
        }
    
    }
}
