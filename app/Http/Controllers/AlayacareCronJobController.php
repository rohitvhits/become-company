<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Agency;
use App\Helpers\AlayacareHelper;
use App\Model\AlayacareClient;
use App\Model\AlayacareEmployee;
use App\Model\BranchMaster;
use App\Model\GroupMaster;
use App\Model\HhaAppointment;
use App\Helpers\HHACaregiversHelper;
use App\Helpers\Utility;
use App\Model\AlayacareEmployeeSkill;
use App\Services\AlayacareService;
use App\Services\AlayacareClientService;
use Illuminate\Support\Facades\DB;

class AlayacareCronJobController extends Controller
{
   
    public function commonAgencyList($skip = 0){
        return  Agency::where('alaycare_status',1)->whereNotNull('alaycare_username')->whereNotNull('alaycare_password')->whereNull('deleted_at')->get();
       
    }
/*************************Start Get All Branches **********************************/
    public function getAllBranches($skip){
       
        $query = $this->commonAgencyList($skip);
        $return  =0;
        if(empty($query[0])){
            $skip = 0;
        }else{
            foreach($query as $val){
              $return =   $this->branchPaginate(1,$val->id,$val->alaycare_username,$val->alaycare_password);
            }
            $skip = $skip+10;
            $this->getAllBranches($skip);
        }
    }

    public function branchPaginate($page,$agencyId,$userName,$password){
        $getBranch = AlayacareHelper::getBranches($page,$userName,$password);
        if(!empty($getBranch['items'])){
            foreach($getBranch['items'] as $key=> $val){
                    $final = array(
                        'agency_id'=>$agencyId,
                        'branch_id'=>$val['id'],
                        'branch_name'=>$val['name'],
                        'profile'=>serialize($val['profile']),
                        
                    );
                    BranchMaster::updateOrCreate([
                        'branch_id'   => $val['id'],
                        'agency_id'=>$agencyId,
                    ],$final);
            }
            if($getBranch['total_pages'] !=$page){
                $page = $page+1;
                $this->branchPaginate($page,$agencyId,$userName,$password);
            }
            return 1;
        }else{
            return 0;
        }

    }
/*************************Start Get All Group List By agency and Branch **********************************/
    public function getGroupList($skip=0){
       
        $query = $this->commonAgencyList($skip);
        $return = 0;
        if(empty($query[0])){
            $skip = 0;
        }else{
            foreach($query as $val){
                $subQuery = BranchMaster::where('agency_id',$val->id)->get();
                foreach($subQuery as $key){
                    $return =  $this->getGroupDetails(1,$key->branch_id,$val->alaycare_username,$val->alaycare_password);
                }
            }
            $skip = $skip+10;
        }
        if($return !=0){
            $this->getGroupList($skip);
        }
        
    }

    function getGroupDetails($page,$branchId,$userName,$password){
        $getGroupDetails = AlayacareHelper::getGroups($page,$branchId,$userName,$password);

        if(!empty($getGroupDetails['items'])){
            foreach($getGroupDetails['items'] as $key=> $val){
                    $final = [
                            "branch_id" => $branchId ?? "",
                            "group_id" => $val['id'] ?? "",
                            "group_name" => $val['name'] ?? "",
                            "name" => $val['name'] ?? "",
                            "description" => $val['description'] ?? "",
                        
                        ];
                        GroupMaster::updateOrCreate([
                        'branch_id'   => $branchId,
                        'group_id'   => $val['id'],
                    ],$final);
            }
            if($getGroupDetails['total_pages'] !=$page){
                $page = $page+1;
                $this->getGroupDetails($page,$branchId,$userName,$password);
            }
            return 1;
        }else{
            return 0;
        }
    }

    /*************************Start Get All Employee List By agency **********************************/
    public function getEmployeeList($skip=0){
        $agencyList = $this->commonAgencyList($skip);
        if(empty($agencyList[0])){
            $this->getEmployeeList(0);
        }else{
            foreach($agencyList as $agency){
                $this->index(1,$agency->id,$agency->alaycare_username,$agency->alaycare_password,0);
            }
            $skip = $skip+10;
            $this->getEmployeeList($skip);
        }
    }

    
    public function index($page,$agencyId,$userName,$password,$skip){
        
        $subQuery = BranchMaster::where('agency_id',$agencyId)->skip($skip)->limit(10)->get();
        $responseFlag = 0;
        foreach($subQuery as $val){
            $responseFlag =   $this->getBranchWiseEmployee(1,$agencyId,$val->branch_id,$userName,$password);
        }

        if($responseFlag ==1){
            $skip = $skip+10;
            $this->index($page,$agencyId,$userName,$password,$skip);
        }
       
    }

    function getBranchWiseEmployee($page,$agencyId,$branch_id,$userName,$password){
        info($page.'====page=='.$agencyId.'-===ag sample');
        $alayacareEmpDetails = AlayacareHelper::getEmployeeRecord($userName,$password,$page,$branch_id);
        $alayacareEmpDetails = json_decode($alayacareEmpDetails,true);

        if(!empty($alayacareEmpDetails['items'][0])){
            foreach($alayacareEmpDetails['items'] as $employee){
                $empDataStore = [
                    "emp_id" => $employee['id'] ?? "",
                    "email" => $employee['email'] ?? "",
                    "first_name" => $employee['first_name'] ?? "",
                    "last_name" => $employee['last_name'] ?? "",
                    "job_title" => $employee['job_title'] ?? "",
                    "designation" => $employee['designation'] ?? "",
                    "external_id" => $employee['external_id'] ?? "",
                    "phone" => $employee['phone'] ?? "",
                    "phone_other" => $employee['phone_other'] ?? "",
                    "profile_id" => $employee['profile_id'] ?? "",
                    "status" => $employee['status'] ?? "",
                    "agency_id" => $agencyId ?? "",
                    "branch_id" => $employee['branch']['id'] ?? "",
                    "branch_name" => $employee['branch']['name'] ?? "",
                ];
                $existingData = AlayacareEmployee::where('emp_id',$employee['id'])->where('agency_id',$agencyId)->first();
                if(!isset($existingData->id)){
                    AlayacareEmployee::updateOrCreate([
                        'emp_id'   => $employee['id'],
                        "agency_id" => $agencyId
                    ],$empDataStore);
                }
            }
        }

        $totalPages = isset($alayacareEmpDetails['total_pages']) ? $alayacareEmpDetails['total_pages'] : 1;

        return [
            'current_page' => (int)$page,
            'total_pages' => $totalPages,
        ];
    }

    /*************************Start Get All Client List By agency **********************************/
    public function testtt(){
        
        $this->getEmployeeDetails();
    }
    public function getClientList($skip=0){
       die("Rere");
        $agencyList = $this->commonAgencyList($skip);
        
        $returnResponseFlag = 0;
        if(empty($agencyList[0])){
           // $this->getClientList(0);
        }else{
            foreach($agencyList as $agency){

                $returnResponseFlag =  $this->clientIndex(1,$agency->id,$agency->alaycare_username,$agency->alaycare_password,0,'client');
            }
            if($returnResponseFlag ==1){
                $skip = $skip+10;
                $this->getClientList($skip);
            }
            
        }
        
    }

    public function clientIndex($page,$agencyId,$userName,$password,$skip){
        
        $subQuery = BranchMaster::where('agency_id',$agencyId)->skip($skip)->limit(10)->get();
        $responseFlag = 0;
        foreach($subQuery as $val){
           $responseFlag= $this->getBranchWiseClient(1,$agencyId,$val->branch_id,$userName,$password);
        }
        if($responseFlag ==1){
            $skip = $skip+10;
            $this->clientIndex($page,$agencyId,$userName,$password,$skip);
            return 1;
        }
        
        return 0;
    }

    function getBranchWiseClient($page,$agencyId,$branchId,$userName,$password){
   
        $getClient = AlayacareHelper::getClientData($userName,$password,$page,$branchId);
        $getClientData = json_decode($getClient,true);
        
       $clientDataStore = [];

        if(!empty($getClientData['items'][0])){
            foreach($getClientData['items'] as $client){
                $clientDataStore[] = [
                    "client_id" => $client['id'] ?? "",
                    "branch_id" => $branchId,
                    "branch_name" =>$client['branch']['name'] ?? "",
                    "ac_id" =>$client['ac_id'] ?? "",
                    "first_name" =>$client['first_name']  ?? "",
                    "last_name" => $client['last_name'] ?? "",
                    "status" => $client['status'] ?? "",
                    "profile_id" => $client['profile_id'] ?? "",
                    "agency_id" => $agencyId ?? "",
                    "created_at" => date('Y-m-d H:i:s'),
                ];
                
                // $existingData = AlayacareClient::where('client_id',$client['id'])->where('agency_id',$agencyId)->first();
                // if(isset($existingData->id)){

                // }else{
                //     AlayacareClient::updateOrCreate([
                //         'client_id'   => $client['id'],
                //         'agency_id'=>$agencyId
                //     ],$clientDataStore);
                // }
                
                
                // $existingData = AlayacareClient::where('client_id',$client['id'])->where('agency_id',$agencyId)->first();
                // if(isset($existingData->id)){

                // }else{
                //     AlayacareClient::updateOrCreate([
                //         'client_id'   => $client['id'],
                //         'agency_id'=>$agencyId
                //     ],$clientDataStore);
                // }
                
            }

           DB::table('alayacare_client_master')->insert($clientDataStore);
        }
        if(isset($getClientData['total_pages'])){
            if($getClientData['total_pages'] !=$page){
                $page = $page+1;
                $this->getBranchWiseClient($page,$agencyId,$branchId,$userName,$password);
                return 1;
            }
        }
        return 0;
        
    }
/*************************Start Get All Employee Details **********************************/

    public function getEmployeeDetails(){
       
        $query = AlayacareEmployee::where('demographic_updated_flag','N')->inRandomOrder()->limit(100)->get();
        if(!empty($query[0])){

        
            foreach($query as $emp){
                $getAgencyDetails = Agency::where('id',$emp->agency_id)->where('alaycare_status',1)->first();
                if(isset($getAgencyDetails->alaycare_username)){
                    if($getAgencyDetails->alaycare_username !="" && $getAgencyDetails->alaycare_password !=""){
                        $empDatils = AlayacareHelper::getEmployeeById($getAgencyDetails->id,$emp->emp_id);
                        $alaycareEmployeeDetails = json_decode($empDatils);
                        
                        if(isset($alaycareEmployeeDetails->demographics)){
                            $gender = '';
                            if(isset($alaycareEmployeeDetails->demographics->gender) && $alaycareEmployeeDetails->demographics->gender !=""){
                                if($alaycareEmployeeDetails->demographics->gender =='M'){
                                    $gender = 'Male';
                                }else if($alaycareEmployeeDetails->demographics->gender =='F'){
                                    $gender = 'Female';
                                }else{
                                    $gender = 'Other';
                                }
                            }
                            $filteredString = '';
                            if(isset($alaycareEmployeeDetails->demographics->phone_main)){
                                $charactersToRemove = ['+', '-', '(', ')', '[', ']'];
                                $filteredString = str_replace($charactersToRemove, '', $alaycareEmployeeDetails->demographics->phone_main);
                                
                            }
                            $empDataStore = [
                            
                                "ac_id" => isset($alaycareEmployeeDetails->ac_id) ? $alaycareEmployeeDetails->ac_id : null,
                                "external_id" => isset($alaycareEmployeeDetails->external_id) ? $alaycareEmployeeDetails->external_id : null,
                                "address" => isset($alaycareEmployeeDetails->demographics->address) ? $alaycareEmployeeDetails->demographics->address : null,
                                "city" => isset($alaycareEmployeeDetails->demographics->city) ? $alaycareEmployeeDetails->demographics->city : null,
                                "state" => isset($alaycareEmployeeDetails->demographics->state) ? $alaycareEmployeeDetails->demographics->state : null,
                                "zip" => isset($alaycareEmployeeDetails->demographics->zip) ? $alaycareEmployeeDetails->demographics->zip : null,
                                "birthday" => isset($alaycareEmployeeDetails->demographics->birthday) ? $alaycareEmployeeDetails->demographics->birthday : null,
                                "country" => isset($alaycareEmployeeDetails->demographics->country) ? $alaycareEmployeeDetails->demographics->country : null,
                                
                                "language" => isset($alaycareEmployeeDetails->language) ? $alaycareEmployeeDetails->language : null,
                                "status" =>$alaycareEmployeeDetails->status,
                                "gender" =>$gender,
                                "username" =>$alaycareEmployeeDetails->username,
                                "demographic_updated_flag" =>'Y',
                            ];
                            if($filteredString !=""){
                                $empDataStore['phone'] =$filteredString;
                            }
                            AlayacareEmployee::updateOrCreate([
                                'emp_id'   => $emp->emp_id,
                                'agency_id'=>$emp->agency_id
                            ],$empDataStore);
                        }
                        
                    }
                } 
            }

        }
    }

    /*************************Start Get All Client Details **********************************/
    public function getClientDetails(){
        $query = AlayacareClient::where('demographic_update_flag','N')->inRandomOrder()->limit(100)->get();
        foreach($query as $client){
            $getAgencyDetails = Agency::where('id',$client->agency_id)->where('alaycare_status',1)->first();
            if(isset($getAgencyDetails->alaycare_username)){
                if($getAgencyDetails->alaycare_username !="" && $getAgencyDetails->alaycare_password !=""){
                    $getClientDetailsById = AlayacareHelper::getClientDetailsById($getAgencyDetails->alaycare_username,$getAgencyDetails->alaycare_password,$client->id);
                    $getClientDetails = json_decode($getClientDetailsById);
                    
                    if(isset($getClientDetails->demographics)){
                        $empDataStore = [
                            "birthday" => $getClientDetails->demographics->birthday ?? "",
                            "city" => $getClientDetails->demographics->city ?? "",
                            "state" => $getClientDetails->demographics->state ?? "",
                            "gender" => $getClientDetails->demographics->gender ?? "",
                            "phone_main" => $getClientDetails->demographics->phone_main ?? "",
                            "address" => $getClientDetails->demographics->address ?? "",
                            "country" => $getClientDetails->demographics->country ?? "",
                            "group_name" => $getClientDetails->groups[0]->name ?? "",
                            "group_id" => $getClientDetails->groups[0]->id ?? "",
                            "status" => $getClientDetails->status ?? "",
                            "demographic_update_flag" => "Y",
                        ];
                        AlayacareClient::updateOrCreate([
                            'client_id'   => $client->client_id,
                            'agency_id'=>$client->agency_id
                        ],$empDataStore);
                    }
                    
                }
            }
        }
    }

    function refreshEmployee(Request $request){
        ini_set('max_execution_time', -1);
        $query = Agency::where('id',$request->agency_id)->where('alaycare_status',1)->first();
        $page = $request->page ? $request->page : 1;
        $result = $this->getBranchWiseEmployee($page,$request->agency_id,"",$query->alaycare_username,$query->alaycare_password);

        $alayaEmp = new AlayacareService();
        $totalEmployees = $alayaEmp->totalSyncEmployeeDetails($request->agency_id);
        return response()->json([
            'error_msg' => 'Employee successfully refresh',
            'data' => [
                'total' => count($totalEmployees),
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
            ]
        ], 200);
    }

   

    function getAllEmployeeDetails(Request $request,$id){
        ini_set('max_execution_time', -1);
        $query = Agency::whereRaw('sha1(id) = "'.$id.'"')->where('alaycare_status',1)->first();

        $skip = 0;
        if(isset($request->offset) && $request->offset !=""){
            $skip = $request->offset;
        }
        $getDetails = AlayacareEmployee::where('agency_id',$query->id)->where('demographic_updated_flag','N')->inRandomOrder()->skip($skip)->limit(30)->get();
        
        if(!empty($getDetails[0])){
         
            foreach($getDetails as $val){
              
                $empDatils = AlayacareHelper::getEmployeeById($query->id,$val->emp_id);
                $alaycareEmployeeDetails = json_decode($empDatils);
             
                if(isset($alaycareEmployeeDetails->demographics)){
                 
                    $gender = '';
                    if(isset($alaycareEmployeeDetails->demographics->gender) && $alaycareEmployeeDetails->demographics->gender !=""){
                        if($alaycareEmployeeDetails->demographics->gender =='M'){
                            $gender = 'Male';
                        }else if($alaycareEmployeeDetails->demographics->gender =='F'){
                            $gender = 'Female';
                        }else{
                            $gender = 'Other';
                        }
                    }
                    $filteredString = '';
                    if(isset($alaycareEmployeeDetails->demographics->phone_main)){
                        $charactersToRemove = ['+', '-', '(', ')', '[', ']'];
                        $filteredString = str_replace($charactersToRemove, '', $alaycareEmployeeDetails->demographics->phone_main);
                        
                    }
                   
                    $empDataStore = [
                        
                        "ac_id" => isset($alaycareEmployeeDetails->ac_id) ? $alaycareEmployeeDetails->ac_id : null,
                        "external_id" => isset($alaycareEmployeeDetails->external_id) ? $alaycareEmployeeDetails->external_id : null,
                        "address" => isset($alaycareEmployeeDetails->demographics->address) ? $alaycareEmployeeDetails->demographics->address : null,
                        "city" => isset($alaycareEmployeeDetails->demographics->city) ? $alaycareEmployeeDetails->demographics->city : null,
                        "state" => isset($alaycareEmployeeDetails->demographics->state) ? $alaycareEmployeeDetails->demographics->state : null,
                        "zip" => isset($alaycareEmployeeDetails->demographics->zip) ? $alaycareEmployeeDetails->demographics->zip : null,
                        "birthday" => isset($alaycareEmployeeDetails->demographics->birthday) ? $alaycareEmployeeDetails->demographics->birthday : null,
                        "country" => isset($alaycareEmployeeDetails->demographics->country) ? $alaycareEmployeeDetails->demographics->country : null,
                        
                        "language" => isset($alaycareEmployeeDetails->language) ? $alaycareEmployeeDetails->language : null,
                        "status" =>$alaycareEmployeeDetails->status,
                        "gender" =>$gender,
                        "username" =>$alaycareEmployeeDetails->username,
                        "demographic_updated_flag" =>'Y',
                    ];
                    if($filteredString !=""){
                        $empDataStore['phone'] =$filteredString;
                    }


                    AlayacareEmployee::where('id',$val->id)->where('agency_id',$val->agency_id)->update($empDataStore);
                }
            }

            $skip = $skip +30;
            return redirect('sync-agency-employee/'.$id.'?offset='.$skip);

            //$this->getAllEmployeeDetails($id);
 
            
        }

        return response()->json(['error_msg' => 'Employee details successfully sync',  'data' => array()], 200);
        
    }


    function refreshClient(Request $request){
        ini_set('max_execution_time', -1);
        $query = Agency::where('id',$request->agency_id)->where('alaycare_status',1)->first();
        $response =  $this->getBranchWiseClient(1,$request->agency_id,"",$query->alaycare_username,$query->alaycare_password);

        $alayaEmp = new AlayacareClientService();
        $query = $alayaEmp->totalSyncClientDetails($request->agency_id);
        return response()->json(['error_msg' => 'Client successfully refresh',  'data' => array('total'=>count($query))], 200);
    }

   

    function getAllClientDetails($id){
        ini_set('max_execution_time', -1);
        $query = Agency::whereRaw('sha1(id) = "'.$id.'"')->where('alaycare_status',1)->first();
        if(isset($query->id)){
            $getDetails = AlayacareClient::where('agency_id',$query->id)->where('demographic_update_flag','N')->inRandomOrder()->limit(100)->get();

            if(!empty($getDetails[0])){
             
                foreach($getDetails as $val){
                  
                    $getClientDetailsById = AlayacareHelper::getClientDetailsById($query->alaycare_username,$query->alaycare_password,$val->client_id);
                    $getClientDetails = json_decode($getClientDetailsById);
                 
                    if(isset($getClientDetails->demographics)){
                     
                        $gender = '';
                        if(isset($getClientDetails->demographics->gender) && $getClientDetails->demographics->gender !=""){
                            if($getClientDetails->demographics->gender =='M'){
                                $gender = 'Male';
                            }else if($getClientDetails->demographics->gender =='F'){
                                $gender = 'Female';
                            }else{
                                $gender = 'Other';
                            }
                        }
                        $filteredString = '';
                        if(isset($getClientDetails->demographics->phone_main)){
                            $charactersToRemove = ['+', '-', '(', ')', '[', ']'];
                            $filteredString = str_replace($charactersToRemove, '', $getClientDetails->demographics->phone_main);
                            
                        }
                       
                        $empDataStore = [
                            "birthday" => $getClientDetails->demographics->birthday ?? "",
                            "city" => $getClientDetails->demographics->city ?? "",
                            "state" => $getClientDetails->demographics->state ?? "",
                            "gender" => $gender ?? "",
                           
                            "address" => $getClientDetails->demographics->address ?? "",
                            "country" => $getClientDetails->demographics->country ?? "",
                            "group_name" => $getClientDetails->groups[0]->name ?? "",
                            "group_id" => $getClientDetails->groups[0]->id ?? "",
                            "status" => $getClientDetails->status ?? "",
                            "demographic_update_flag" => "Y",
                        ];
                        if($filteredString !=""){
                            $empDataStore['phone_main'] =$filteredString;
                        }
    
    
                        AlayacareClient::where('client_id',$val->client_id)->where('agency_id',$val->agency_id)->update($empDataStore);
                    }
                }
               return redirect('sync-agency-client/'.$id);
     
                
            }
    
            return response()->json(['error_msg' => 'Client details successfully sync',  'data' => array()], 200);
            
        }
        
    }

    function refreshSkill(Request $request){
     
        ini_set('max_execution_time', -1);
        
       
        $response = $this->getUpdateSkill($request->agency_id,0);
       
        return response()->json(['error_msg' => 'Skill successfully sync',  'data' => array('response'=>$response)], 200);
    }

    function getUpdateSkill($agency_id,$offset){
        
        $getEmpDetails =AlayacareEmployee::select('emp_id','id')->where('agency_id',$agency_id)->whereRaw('(last_sync_skill_date IS NULL OR last_sync_skill_date < "'.date('Y-m-d',strtotime('-2 day')).'")')->limit(50)->skip($offset)->orderBy('id','desc')->get();

        $query = Agency::where('id',$agency_id)->where('alaycare_status',1)->first();
     
        if(isset($query->id)){
            if(!empty($getEmpDetails[0])){
              
                foreach($getEmpDetails as $employee){
                    $getSkillByEmployee = AlayacareHelper::loadSkills($employee->emp_id,$query->alaycare_username,$query->alaycare_password);
                    $details = json_decode($getSkillByEmployee,true);
                   
                    if(isset($details['count'])){}else{
                        if(!empty($details['items'][0])){
                            foreach($details['items'] as $val){
                                $expiry = $val['fields']['expired_date']??"";
                                if($expiry !=""){
                                    $date = date('Y-m-d H:i:s',strtotime(Utility::convertUTCToUSA($expiry)));
                                    $save = AlayacareEmployeeSkill::updateOrCreate([
                                                'employee_id'   => $employee->emp_id,
                                                'skill_id'   => $val['skill_id'],
                                                'agency_id'=>$query->id
                                            ],[
                                                'due_date'     => $date,
                                                'alayacare_emp_id' =>$employee->id,
                                                'skill_name'     => $val['name'],
                                                'created_date'=>date('Y-m-d H:i:s')

                                            ]);
                                }
                            }
                        }
                    }
                    AlayacareEmployee::where('id',$employee->id)->update(array('last_sync_skill_date'=>date('Y-m-d H:i:s')));
 
                }
                $offset = $offset+100;
                $this->getUpdateSkill($agency_id,$offset);
            
            }
        }
        return 1;
    }

    function updateEmployeeDemographic(){
  
        $getDetails = AlayacareEmployee::where('demographic_updated_flag','N')->where('agency_id',180)->inRandomOrder()->limit(500)->get();
        if(!empty($getDetails[0])){
        
            foreach($getDetails as $val){
                $query = Agency::whereRaw('id',$val->agency_id)->where('alaycare_status',1)->first();
                if(isset($query->id)){
                    
                    $empDatils = AlayacareHelper::getEmployeeById($query->id,$val->emp_id);
                    $alaycareEmployeeDetails = json_decode($empDatils);
                 
                    if(isset($alaycareEmployeeDetails->demographics)){
                     
                        $gender = '';
                        if(isset($alaycareEmployeeDetails->demographics->gender) && $alaycareEmployeeDetails->demographics->gender !=""){
                            if($alaycareEmployeeDetails->demographics->gender =='M'){
                                $gender = 'Male';
                            }else if($alaycareEmployeeDetails->demographics->gender =='F'){
                                $gender = 'Female';
                            }else{
                                $gender = 'Other';
                            }
                        }
                        $filteredString = '';
                        if(isset($alaycareEmployeeDetails->demographics->phone_main)){
                            $charactersToRemove = ['+', '-', '(', ')', '[', ']'];
                            $filteredString = str_replace($charactersToRemove, '', $alaycareEmployeeDetails->demographics->phone_main);
                            
                        }
                       
                        $empDataStore = [
                            
                            "ac_id" => isset($alaycareEmployeeDetails->ac_id) ? $alaycareEmployeeDetails->ac_id : null,
                            "external_id" => isset($alaycareEmployeeDetails->external_id) ? $alaycareEmployeeDetails->external_id : null,
                            "address" => isset($alaycareEmployeeDetails->demographics->address) ? $alaycareEmployeeDetails->demographics->address : null,
                            "city" => isset($alaycareEmployeeDetails->demographics->city) ? $alaycareEmployeeDetails->demographics->city : null,
                            "state" => isset($alaycareEmployeeDetails->demographics->state) ? $alaycareEmployeeDetails->demographics->state : null,
                            "zip" => isset($alaycareEmployeeDetails->demographics->zip) ? $alaycareEmployeeDetails->demographics->zip : null,
                            "birthday" => isset($alaycareEmployeeDetails->demographics->birthday) ? $alaycareEmployeeDetails->demographics->birthday : null,
                            "country" => isset($alaycareEmployeeDetails->demographics->country) ? $alaycareEmployeeDetails->demographics->country : null,
                            
                            "language" => isset($alaycareEmployeeDetails->language) ? $alaycareEmployeeDetails->language : null,
                            "status" =>$alaycareEmployeeDetails->status,
                            "gender" =>$gender,
                            "username" =>$alaycareEmployeeDetails->username,
                            "demographic_updated_flag" =>'Y',
                        ];
                        if($filteredString !=""){
                            $empDataStore['phone'] =$filteredString;
                        }

                        AlayacareEmployee::where('id',$val->id)->update($empDataStore);
                        info("Update Demographic");
                    }
                }
               
            }
        }
    }
}
