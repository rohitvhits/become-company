<?php

namespace App\Http\Controllers;

use App\Helpers\AlayacareHelper;
use App\Model\BranchMaster;
use App\Model\GroupMaster;
use App\Model\Patient;
use Illuminate\Http\Request;

class AlayCareController extends Controller
{
    public function getBranchAlaycare(){
        $getAllBranch = BranchMaster::pluck('branch_id');
        $getBranch = AlayacareHelper::getBranches();
        $data = json_decode($getBranch);
        
        if(isset($data)){
            foreach($data->items as $branchData){
           
                if(!in_array($branchData->id,$getAllBranch->toArray())){
                    $branchDataStore = [
                        "branch_id" => $branchData->id ?? "",
                        "branch_name" => $branchData->name ?? "",
                    ];
                    BranchMaster::create($branchDataStore);
                }
            }
        }

        return response()->json(['error_msg' => '', 'status' => 1, 'data' => $data], 200);

    }

    public function getGroupByBranchId(Request $request)
    {
        $getAllGroup = GroupMaster::where('branch_id',$request->branchId)->pluck('group_id');
        $getGroupByBranchId = AlayacareHelper::getGroups($request->branchId);
        $data = json_decode($getGroupByBranchId);

        if(isset($data)){
            foreach($data->items as $groupData){

                if(!in_array($groupData->id,$getAllGroup->toArray())){
                    $groupDataStore = [
                        "branch_id" => $groupData->branch->id ?? "",
                        "group_id" => $groupData->id ?? "",
                        "group_name" => $groupData->name ?? "",
                        "description" => $groupData->description ?? "",
                       
                    ];
                    GroupMaster::create($groupDataStore);
                }
              
            }
        }
        return response()->json(['error_msg' => '', 'status' => 1, 'data' => $data], 200);
    }

    public function alayacarePost(Request $request){

        $this->validate($request, [
            'branchId' => 'required',
            'groupId' => 'required',
        ]);
        
        $patinetId = $request->patient_id;
        $branchId = $request->branchId;
        $groupId = $request->groupId;
        $patientGetData =  Patient::with('users')->find($patinetId);
        $language = AlayacareHelper::getLanguages();
        $insertArray = array(
            'demographics' => array(
                'first_name' => (isset($patientGetData)) ? $patientGetData->first_name : '',
                "last_name" => (isset($patientGetData)) ? $patientGetData->last_name : '',
                "gender" => (isset($patientGetData)) ? $patientGetData->gender : '',
                'address' => (isset($patientGetData)) ? $patientGetData->address1 : '',
                'address_suite' => (isset($patientGetData)) ? $patientGetData->address2 : '',
                'email' => (isset($patientGetData->users)) ? $patientGetData->users->email : '',
                'phone_other' => (isset($patientGetData)) ? $patientGetData->phone : '',
                'phone_personal' => (isset($patientGetData)) ? $patientGetData->phone : '',
                'birthday' => (isset($patientGetData)) ? $patientGetData->dob : '',
                "city" => (isset($patientGetData)) ? $patientGetData->city : '',
                "state" => (isset($patientGetData)) ? $patientGetData->state : '',
                "country" => (isset($patientGetData)) ? $patientGetData->county : '',
                'zip' => (isset($patientGetData)) ? $patientGetData->zip_code : '',
            ),
            "external_id" => '00',
            "branch_id" => (isset($branchId)) ? $branchId : "",
            "language" => $language[3][0],
            "timezone" => "America/Toronto"
        );
       
        $saveClient = AlayacareHelper::createClient($insertArray);

        if($saveClient){
            return response()->json(['error_msg' => 'Insert Successfully', 'status' => 1, 'data' => $saveClient], 200);
        }else{
            return response()->json(['error_msg' => '', 'status' => 0, 'data' => ''], 500);
        }
        

    }
}
