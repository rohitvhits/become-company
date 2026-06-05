<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroupNotificationService;
use App\Services\GroupWiseUserNotificationService;
use App\Services\GroupWiseServiceNotificationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use App\Services\DynamicFormLogService;
use App\Services\NotificationTypeService;
use App\Agency;
use App\User;
use App\Master;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Helpers\UserHelper;
class GroupNotificationController extends BaseController
{
	protected $groupNotificationService, $dynamicFormLogService, $groupWiseServiceNotificationService, $groupWiseUserNotificationService,$notificationTypeService ='';

	public function __construct(GroupNotificationService $groupNotificationService, DynamicFormLogService $dynamicFormLogService, GroupWiseUserNotificationService $groupWiseUserNotificationService, GroupWiseServiceNotificationService $groupWiseServiceNotificationService, NotificationTypeService $notificationTypeService)
	{
		$this->middleware('auth');
		$this->middleware('permission:group-notification|group-notification-add|group-notification-edit|group-notification-delete', ['only' => ['index', 'groupNotificationList','getServiceData']]);
        $this->middleware('permission:group-notification-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:group-notification-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:group-notification-delete', ['only' => ['destroy']]);

		$this->groupNotificationService = $groupNotificationService;
		$this->dynamicFormLogService = $dynamicFormLogService;
		$this->groupWiseServiceNotificationService = $groupWiseServiceNotificationService;
		$this->groupWiseUserNotificationService = $groupWiseUserNotificationService;
		$this->notificationTypeService = $notificationTypeService;
	}

    public function index(){
        $data['menu'] = "";
        $data['user'] = $auth = auth()->user();		
		if (!$auth || $auth == null) {
			return redirect('login');
		} 
        return view('groupNotification/group_notification_list', $data);        
    }

    public function create(){
        $data['user'] = auth()->user();	
        $data['notificationList'] = $this->notificationTypeService->getAllNotificationTypeData();        
        $angecyList = Cache::get('patient_master_locations', function () {
			return Agency::getAgencyList();
		}, 10);
		$data['agencyList'] = $angecyList;        
      
        
        return view('groupNotification/group_notification_add', $data);        
    }

	public function store(Request $request)
	{        
		$validator = Validator::make($request->all(), [
			'name' => 'required',
            'user_id' => 'required',
		]);
		if ($validator->fails()) {
            return redirect("/group-notification/create")
            ->withErrors($validator, 'add_agency')
            ->withInput();
		} else {
          
			$data = array(
				'name' => $request->input('name'),
				'patient_flag' => $request->input('patient_flag') == "Patient" || !empty($request->input('patients_notification')) ? 1 : NULL,
				'caregiver_flag' => $request->input('caregiver_flag') == "Caregiver" || !empty($request->input('caregiver_notification')) ? 1 : NULL,
				'patients_notification' => !empty($request->input('patients_notification')) ? implode(',',$request->input('patients_notification')): NULL,
				'caregiver_notification' => !empty($request->input('caregiver_notification')) ? implode(',',$request->input('caregiver_notification')) : NULL,
                
			);
            
            if(!empty($request->input('agency_fk')[0])){
                $data['agency_id'] = implode(',',$request->input('agency_fk'));
            }
			$insert = $this->groupNotificationService->save($data);
            
            // Insert Services data
            if(!empty($request->input('service_id'))){
                foreach($request->input('service_id') as $service_id){
                    $servicesData = array(
                        'group_id' => $insert,
                        'service_id' => $service_id,
                    );
                    $this->groupWiseServiceNotificationService->save($servicesData);
                }
            }
            
            // Insert User data
            if($request->input('user_id') !=""){
                $userIds = array_unique(explode(',',$request->input('user_id')));
          
                foreach($userIds as $user_id){
                    $userData = array(
                        'group_id' => $insert,
                        'user_id' => $user_id,
                    );
                    $this->groupWiseUserNotificationService->save($userData);
                }
            }
            
			if ($insert) {
				$getNewData = $this->groupNotificationService->getDetailById($insert);				
				$insertLog = [
					'type' => 'Add',
					'link' => url('/group-notification'),
					'module' => 'Group Notification',
					'module_id' => $getNewData->id,
					'new_response' => serialize($getNewData->toArray())
				];
				$this->dynamicFormLogService->storeFormLog($insertLog);
                Session::flash('success', 'Group Notification created successfully');
                return redirect('/group-notification');
              
			} else {
                Session::flash('error', 'Sorry, something went wrong. Please try again');
                return redirect('/group-notification/create');
			}
		}
	}

    public function edit($id){
        $data['user'] = $auth = auth()->user();
        $data['groupNotificationData']	= $this->groupNotificationService->getDetailById($id);
        $agencyRow = [];
        
        if($data['groupNotificationData']->agency_id !=""){
            $agencyRow = explode(',',$data['groupNotificationData']->agency_id);
        }
        $data['agencyRow'] =$agencyRow;
         
        foreach($data['groupNotificationData']->services as $row){
            if(isset($row->service_id) && !empty($row->service_id)){
                    $data['servicesData'][] =  $row->service_id;
            }        
        }

        $userIds = [];
        foreach($data['groupNotificationData']->users as $row){
            if(isset($row->user_id) && !empty($row->user_id)){
                $userIds[] =  $row->user_id;
            }
        }   

        $userDetails = [];
        $getUserDetails = UserHelper::getDetailsByUserids($userIds);
        if(!empty($getUserDetails[0])){
            foreach($getUserDetails as $udetail){
                $tempUserDetail = [];
                $userType = "Nybest User";

                if($udetail->agency_fk !=""){
                    $userType = "Agency User";
                }

                $tempUserDetail['id'] = $udetail->id;
                $tempUserDetail['name'] = $udetail->first_name.' '.$udetail->last_name.' ( '.$userType.' ) ';
                $userDetails[] = $tempUserDetail;
            }
        }
        $data['userData'] =  $userDetails;     
        $data['notificationList'] = $this->notificationTypeService->getAllNotificationTypeData();
        $angecyList = Cache::get('patient_master_locations', function () {
			return Agency::getAgencyList();
		}, 10);
		$data['agencyList'] = $angecyList;
		$data['id'] = $id;
        $type = [];
       
        if($data['groupNotificationData']->patient_flag){
            $type[] = 'Patient';
        } 
        if($data['groupNotificationData']->caregiver_flag){
            $type[] = 'Caregiver';
        }        
		$serviceData = Master::getServiceTypeBaseWithArray($type,'');	
        foreach($serviceData as $service){
            $data['servicesArray'][] = array(
                'name' => $service->name,
                'id' => $service->id,
            );
        } 
      
        return view('groupNotification/group_notification_edit', $data);        
    }

    public function update(Request $request, $id)
	{
		$validator = Validator::make($request->all(), [
			'name' => 'required',
            'user_id' => 'required',
		]);
		if ($validator->fails()) {
			return redirect("/group-notification/".$id."/edit")
            ->withErrors($validator, 'add_agency')
            ->withInput();
		} else {
            $getExistingData = $this->groupNotificationService->getDetailById($id);	
			$data = array(
				'name' => $request->input('name'),
				'patient_flag' => $request->input('patient_flag') == "Patient" || !empty($request->input('patients_notification')) ? 1 : NULL,
				'caregiver_flag' => $request->input('caregiver_flag') == "Caregiver" || !empty($request->input('caregiver_notification')) ? 1 : NULL,
				'patients_notification' => !empty($request->input('patients_notification')) ? implode(',',$request->input('patients_notification')): NULL,
				'caregiver_notification' => !empty($request->input('caregiver_notification')) ? implode(',',$request->input('caregiver_notification')) : NULL,
               
			);
            if(!empty($request->input('agency_fk')[0])){
                $data['agency_id'] = implode(',',$request->input('agency_fk'));
            }
			$update = $this->groupNotificationService->update($data,$id);   
            // Delete services and user data
            $this->groupWiseServiceNotificationService->SoftDelete(array('group_id' => $id));
            $this->groupWiseUserNotificationService->SoftDelete(array('group_id' => $id));
            
            // Insert Services data
            if(!empty($request->input('service_id'))){
                foreach($request->input('service_id') as $service_id){
                    $servicesData = array(
                        'group_id' => $id,
                        'service_id' => $service_id,
                    );
                    $this->groupWiseServiceNotificationService->save($servicesData);
                }
            }
            
            if($request->input('user_id') !=""){
                $userIds = array_unique(explode(',',$request->input('user_id')));
                foreach($userIds as $user_id){
                    $userData = array(
                        'group_id' => $id,
                        'user_id' => $user_id,
                    );
                    $this->groupWiseUserNotificationService->save($userData);
                }
            }
            $getNewData = $this->groupNotificationService->getDetailById($id);				
            
            $insertLog = [
                'type' => 'Update',
                'link' => url('/group-notification'),
                'module' => 'Group Notification',
                'module_id' => $getNewData->id,
                'old_response' => serialize($getExistingData->toArray()),
                'new_response' => serialize($getNewData->toArray())
            ];
            $this->dynamicFormLogService->storeFormLog($insertLog);    
            Session::flash('success', 'Group Notification updated successfully');
            return redirect('/group-notification');     
            
		}
	}

	public function destroy($id)
	{
		$update = $this->groupNotificationService->SoftDelete(array('id' => $id));
        $this->groupWiseServiceNotificationService->SoftDelete(array('group_id' => $id));
        $this->groupWiseUserNotificationService->SoftDelete(array('group_id' => $id));
		if ($update) {
			// Insert form Log into Dynamic form log table
			$insertLog = [
				'type' => 'Delete',
				'link' => url('/group-notification'),
				'module' => 'Group Notification',
				'module_id' => $id
			];
			$this->dynamicFormLogService->storeFormLog($insertLog);

			return response()->json(['status' => "1", 'error_msg' => "Group Notification successfully deleted.", 'data' => array()], 200);
		} else {
			return response()->json(['status' => "0", 'error_msg' => "Sorry, something went wrong. Please try again.", 'data' => array()], 500);
		}
	}

	public function groupNotificationList(Request $request)
	{
		$query = $this->groupNotificationService->groupNotificationList();
        foreach($query as $row){
            $servicesData = $userData = $agency = array();    
            if(isset($row->services) && !empty($row->services)){
                foreach($row->services as $service){
                    $servicesData[] =  $service->servicesDeatils->name;
                }
            }        
            $row->servicesRow = implode(', ',$servicesData);

            if(isset($row->users) && !empty($row->users)){
                foreach($row->users as $user){
                    $userData[] =  $user->userDeatils->first_name.' '.$user->userDeatils->last_name;
                }
            }
            $row->userRow = implode(', ',$userData);
            //get Agency Data
            $agencyIds = explode(',',$row->agency_id); 
            $agencyData = Agency::getAgencyListWithIds($agencyIds);
            foreach($agencyData as $ag){
                $agency[] = $ag->agency_name;
            }
            $row->agency = trim(implode(', ',$agency));
        }        
        $data['query'] = $query;                
		return view("groupNotification.group_notification_ajax_list", $data);
	}

	public function getServiceData(Request $request)
	{
        $type = [];
        $data = [];

        if($request['patient_flag']){
            $type[] = 'Patient';
        }
        if($request['caregiver_flag']){
            $type[] ='Caregiver';
        }
           
		$serviceData = Master::getServiceTypeBaseWithArray($type,'');	
        foreach($serviceData as $service){
            $data[] = array(
                'name' => $service->name,
                'id' => $service->id,
            );
        }        			
		return response()->json(['status' => "1", 'error_msg' => "", 'data' => $data], 200);
	}
}