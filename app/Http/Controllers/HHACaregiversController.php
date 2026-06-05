<?php

namespace App\Http\Controllers;

use App\Agency;
use App\Helpers\HHAAppointmentHelper;
use App\Helpers\HHACaregiverHelper;
use App\Model\HHACaregivers;
use Illuminate\Http\Request;
use App\Helpers\HHACaregiversHelper;
use App\Model\HHAVisit;
use App\Model\HhaAppointment;
use App\Model\HhaOtherComplience;
use App\Model\HHAPatient;
use Illuminate\Routing\Controller as BaseController;
use App\Services\HHACaregiverMedicalService;
use App\Services\HHACaregiverService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\PatientService;
use App\Model\CaregiverComplianceI9s;
use App\Helpers\Utility;
use App\Services\LogsService;
use App\Services\HHALogService;

class HHACaregiversController extends BaseController
{
    protected $hhaCaregiverMedical,$hhaCaregiverService,$patientService = "";
    protected $hhaLogService;
    public function __construct(HHACaregiverMedicalService $hhaCaregiverMedical,HHACaregiverService $hhaCaregiverService,PatientService $patientService,HHALogService $hhaLogService)
    {
        $this->hhaCaregiverMedical = $hhaCaregiverMedical;
        $this->hhaCaregiverService = $hhaCaregiverService;
        $this->patientService = $patientService;
        $this->hhaLogService = $hhaLogService;
    }
    public static function caregiverList(Request $request)
    {

        $agencyId = $request->get('agencyid') ? $request->get('agencyid') : NULL;
        HHACaregiversHelper::getCaregiverIDListByAgencyId($agencyId);
        echo "<b>Synce done!!!!</b>";
        return true;
    }

    public static function caregiverMedicalDetailsByCaregiverId(Request $request)
    {

        $test = HHACaregiversHelper::GetCaregiverMedicalDetailsByCaregiverId();

        return;
    }

    public static function caregiverSync(Request $request)
    {
        HHACaregiversHelper::getunsynccaregiver();
        echo "<script>window.location.reload()</script>";
        return true;
    }

    public static function syncVisit(Request $request)
    {
        $getAppointment = HHACaregiversHelper::getCaregiverDetailsByAgencyId($request->id,$request->agency_id);

        if(isset($getAppointment->id)){
            $query = $getAppointment;
        }else{
           $getAppointment=  $query = self::commonResponse($request->id);
          
        }

        $tempArray = array();
        $final_array = array();
        
        if (isset($getAppointment)) { 
            $response = HHACaregiversHelper::getVisitNew($query, $request->start, $request->end);
           
            if (!empty($response[0])) {
                foreach ($response as $val) {
                    if(isset($val['patient_first_name']) && $val['patient_first_name'] !=""){
                        $pName="P: ".$val['patient_first_name'].' '.$val['patient_last_name'];
                   
                        $tempArray['title'] = "V :" . date('h:i a', strtotime($val['schedule_start_time'])) . ' - ' . date('h:i a', strtotime($val['schedule_end_time']));
                        $tempArray['label'] ="C: ". $val['first_name'] . ' ' . $val['last_name'].' <br>'.$pName;
                        $tempArray['start'] =date('m/d/Y h:i a',strtotime($val['schedule_start_time']));
                        $tempArray['end'] =date('m/d/Y h:i a',strtotime($val['schedule_end_time']));
                        $tempArray['caregiver_id'] = $val['caregiver_id'];
                        $tempArray['agency_id'] =$request->agency_id;
                        $tempArray['caregiver_full_name'] =$val['first_name'].' '.$val['last_name'];
                        $tempArray['patient_full_name'] =$val['patient_first_name'].' '.$val['patient_last_name'];
                        $tempArray['patient_id'] = $val['patient_id'];
                        $final_array[] = $tempArray;
                    }
                    
                }
            }

        }

        echo json_encode($final_array);
    }

    public function getAppoinmentList(Request $request)
    {
        $getAppointment = Self::commonResponse($request->id);

        $final_array = array();
        if (isset($getAppointment->id)) {
            $final = $getAppointment;
            $caregiverDetails = HHACaregiversHelper::getCaregiverDetails($getAppointment->caregiver_id);

            if (isset($caregiverDetails->id)) {
                $final = $caregiverDetails;
            }
            $query = HHAVisit::with(['patientDetails:patient_id,first_name,last_name'])->where('caregiver_id', $final->caregiver_id)->whereDate('visit_date', '>=', $request->start)->whereDate('visit_date', '<=', $request->end)->get();

            $tempArray = array();
            if (!empty($query)) {
                foreach ($query as $val) {

                    $pName="";
                    if(isset($val->patientDetails) && $val->patientDetails !=""){
                        $pName = "P: ".$val->patientDetails->first_name.' '.$val->patientDetails->lasat_name;

                    }
                    $tempArray['title'] = "V : " . date('h:i A', strtotime($val->schedule_start_time)) . ' - ' . date('h:i A', strtotime($val->schedule_end_time));
                    $tempArray['label'] ="C: ". $val->first_name . ' ' . $val->last_name.' <br>'.$pName;
                    $tempArray['start'] = $val->schedule_start_time;

                    $final_array[] = $tempArray;
                }
            }
        }
        echo json_encode($final_array);
    }
    public  function    syncHHACaregiverNotes(Request $request)
    {
        $getAppointment = Self::commonResponse($request->id);

        $startDate = date("Y-m-d", strtotime("-1 year"));
        $endDate = date("Y-m-d");
        $response = [];

        if (isset($getAppointment)) {
            $final = $getAppointment;

            $query = HHACaregiversHelper::getCaregiverDetails($getAppointment->caregiver_id);
            if (isset($query->id)) {
                $final = $query;
            }
            $response = HHACaregiversHelper::getHHACaregiverNotes($final, $startDate, $endDate);
        }
        $response = [
            'message' => "success",
            'status' => 1,
            'data'    => $response,
        ];
        return response()->json($response, 200);
    }

    public function syncHHACaregiverSubject(Request $request)
    {
        $getAppointment = Self::commonResponse($request->id);
        $response = [];
        if (isset($getAppointment)) {
            $final = $getAppointment;

            $query = HHACaregiversHelper::getCaregiverDetails($getAppointment->caregiver_id);
            if (isset($query->id)) {
                $final = $query;
            }
            $response = HHACaregiversHelper::getHHACaregiverSubject($final);
        }

        $response = [
            'message' => "success",
            'status' => 1,
            'data'    => $response,
        ];
        return response()->json($response, 200);
    }

    function HHACaregiverCreateNotes(Request $request)
    {


        $getAppointment = Self::commonResponse($request->id);
        $response = [];
        if (isset($getAppointment)) {
            $final = $getAppointment;
            $query = HHACaregiversHelper::getCaregiverDetails($getAppointment->caregiver_id);

            if (isset($query->id)) {
                $final = $query;
            }
            $response = HHACaregiversHelper::createHHACaregiverNotes($final, $request->all());

            $ipaddress = Utility::getIP();
            $hhaLogData = [
                'patient_id'=>$request->patient_id,
                'hha_patient_id'=>$getAppointment->caregiver_id,
                'type'=>$request->type,
                'hha_module_type'=>'HHA Exchange',
                'send_response'=>serialize($request->except('_token')),
                'ip_address' => $ipaddress,
                'action'=>'Add',
                
            ];
            $this->hhaLogService->save($hhaLogData);

            $response = [
                'message' => "success",
                'status' => 1,
                'data'    => $response,
            ];
            return response()->json($response, 200);
        }
        $response = [
            'message' => "Something went wrong.",

            'data'    => array(),
        ];
        return response()->json($response, 500);
    }

    public static function commonResponse($id)
    {
        $query = HHACaregivers::where('caregiver_id',$id)->where('hha_delete_flag','N')->first();
        if(isset($query->id)){
            return $query;
        }else{
            return   HHAAppointmentHelper::getByIdNewId($id);
        }
    }

    public    function syncHHACaregiverMedical(Request $request)
    {
       // $query = Self::commonResponse($request->id);
        $query= HHACaregiversHelper::getCaregiverDetails($request->id);
      
        if (isset($query->id)) {
            $final = $query;
            

            if (isset($query1->id)) {
                $final = $query1;
            }
            $response = HHACaregiversHelper::getCaregiverMedicalDetails($final, $final->caregiver_id);
           
            if (!empty($response)) {
                foreach ($response as $k) {
                    $query = $this->hhaCaregiverMedical->getDetails($k['caregiver_id'], $k['medical_id']);
                    

                    if (!$query) {
                        $insert = $this->hhaCaregiverMedical->save($k);
                    }
                }
            }
            $response = [
                'message' => "success",
                'status' => 1,
                'data'    => "",
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'message' => "Sorry, something went wrong. Please try again.",
                'status' => 1,
                'data'    => "",
            ];
            return response()->json($response, 500);
        }
    }

    public function syncHHACaregiverOtherCompliance(Request $request)
    {
        $query = Self::commonResponse($request->id);
        $response = [];
        if (isset($query->id)) {
            $final = $query;
            $queries = HHACaregiversHelper::getCaregiverDetails($query->caregiver_id);

            if (isset($queries->id)) {
                $final = $queries;
            }
            $response = HHACaregiversHelper::getCaregiverOtherComplianceDetails($final, $final->caregiver_id);
        }


        return response()->json(['message' => "success", 'status' => 1, 'data' => $response], 200);
    }

    public function syncHHACaregiverInService(Request $request)
    {
        $getAppointment = Self::commonResponse($request->id);
        $response = [];
        if (isset($getAppointment)) {
            $final = $getAppointment;
            $query = HHACaregiversHelper::getCaregiverDetails($getAppointment->caregiver_id);

            if (isset($query->id)) {
                $final = $query;
            }

            $response = HHACaregiversHelper::getHHACaregiverInServices($final, $getAppointment->caregiver_id);
        }
        usort($response, array($this, "date_sort"));
        $response = [
            'message' => "success",
            'status' => 1,
            'data'    => $response,
        ];
        return response()->json($response, 200);
    }
    function date_sort($a, $b)
    {

        $t1 = strtotime($a['inservice_date']);

        $t2 = strtotime($b['inservice_date']);

        return $t2 - $t1;
    }
    function linktoHHACaregiver(Request $request)
    {

        $query = HHACaregiversHelper::searchCaregiverWithAgencyId($request->agency_id, $request->q);
        $data = [];

        foreach ($query as $val) {
            $status = "";
            if ($val->status != "") {
                $status = '-' . $val->status;
            }
            $temp = [];
            $temp['id'] = $val->id;
            $temp['name'] = $val->name . '(' . $val->caregiver_code . ') ' . $status;

            $data[] = $temp;
        }


        return json_encode($data);
    }

    public function HHACaregiverAvaibility(Request $request)
    {
        $query = HHACaregiversHelper::getCaregiverDetails($request->id);
        if(isset($query->id)){
            $getAppointment = $query;
        }else{
            $getAppointment = Self::commonResponse($request->id);
        }
       

        $response = [];
        $getCaregiverAvainiltiyData = "";
        if (isset($getAppointment)) {
            $query = HHACaregiversHelper::getCaregiverDetails($getAppointment->caregiver_id);
           
            if (isset($query->id)) {
                $getCaregiverAvainiltiyData = HHACaregiversHelper::GetCaregiverAvailabilityById($query);

            }
        }

        $response = [
            'message' => "success",
            'status' => 1,
            'data'    => $getCaregiverAvainiltiyData,
        ];
        return response()->json($response, 200);
    }
    public function getSyncCaregiverData()
    {
    
       // ini_set('memory_limit', -1);
        $HHACaregivers =  HHACaregivers::whereNull('deleted_at')->whereNull('deleted_at')->whereRaw("(last_medical_sync is null or DATE_FORMAT(last_medical_sync,'%Y-%m-%d') <'" . date('Y-m-d') . "')")->inRandomOrder()->paginate(50);
        if (!empty($HHACaregivers[0])) {
            foreach ($HHACaregivers as $caregiver) {
                echo $caregiver->id . '- ' . $caregiver->agency_fk . '-';
                $agency = Agency::getAllDetailsbyAgencyId($caregiver->agency_fk);
                if ($agency) {

                    $getAllCaregiverDetails = HHACaregiversHelper::GetCaregiverDetailByCareGiverID($caregiver->caregiver_id, $caregiver->agency_fk);
                }
            }
            echo "<script>window.location.replace('/sync-hha-caregiver');</script>";
        } else {
            return response()->json(['error_msg' => 'Caregivers details successfully sync', 'data' => array()], 200);
        }
    }
    public function agencyWiseCaregiverAdd($id)
    {
        
        //    $query = HHACaregiversHelper::tempCaregiverSYNC($id);
        //$caregiverList = HHACaregivers::whereNull('officeId')->where('hhasyncdatetime', '<', '2023-08-07')->inRandomOrder()->limit(1000)->get();
        
        /****VIshal */

       
        $caregiverList = HHACaregivers::where('hha_delete_flag','N')->where('agency_fk', $id)->whereDate('hhasyncdatetime', '<', '2025-01-06')->inRandomOrder()->limit(200)->get();
   
        foreach ($caregiverList as $list) {
            
            if ($list->agency_fk != "") {
                HHACaregiversHelper::GetCaregiverDetailByCareGiverID($list->caregiver_id, $list->agency_fk);
            }
            HHACaregivers::where('id', $list->id)->update(array("hha_sync" => 'Y', "hhasyncdatetime" => date('Y-m-d H:i:s')));
        }
       
    }


    public function fetchCaregiver(Request $request)
    {



        $fetchCaregiver = HHACaregiversHelper::getCaregiverIDListByAgencyIdNew($request->agency_id);

        $query = HHACaregivers::fetchCaregiverCount($request->agency_id);
        return response()->json(['error_msg' => 'Caregiver successfully refresh',  'data' => array('total' => count($query))], 200);
    }

    public function getAllCaregiverDetails($id)
    {
       // ini_set('memory_limit', -1);
        $HHACaregivers = HHACaregivers::getAllCaregiverDetailsOnlyNewCaregiver($id);
    
        //  print_r($HHACaregivers);

        if (!empty($HHACaregivers[0])) {
            foreach ($HHACaregivers as $caregiver) {
                $getAllCaregiverDetails = HHACaregiversHelper::GetCaregiverDetailByCareGiverID($caregiver->caregiver_id, $caregiver->agency_fk);
            }
            echo "<script>window.location.replace('/sync-agency-caregiver/" . $id . "');</script>";
        } else {
            return response()->json(['error_msg' => 'Caregivers details successfully sync', 'data' => array()], 200);
        }
    }

    public function fetchAllUnsyncedMedical()
    { ini_set('memory_limit', -1);
        //$hhaCaregiverDetails =  HHACaregivers::whereNull('deleted_at')->whereRaw("(last_medical_sync is null or last_medical_sync <'" . date('Y-m-d H:i:s', strtotime('-5 day')) . "')")->inRandomOrder()->limit(1000)->get();
        $hhaCaregiverDetails =  HHACaregivers::whereNull('deleted_at')->whereRaw("(last_medical_sync is null )")->inRandomOrder()->limit(1000)->get();


        if (!empty($hhaCaregiverDetails[0])) {
            foreach ($hhaCaregiverDetails as $caregiver) {
                $agency = Agency::getAllDetailsbyAgencyId($caregiver->agency_fk);
                // $query = HHAAppointmentHelper::getCaregiverOrNot($caregiver->caregiver_id,$caregiver->agency_fk);
                // if(isset($query->id)){
                //     HHAAppointmentHelper::update(array('del_flag'=>'Y'),array('id'=>$query->id));
                // }else{
                //     HHAAppointmentHelper::insert(array('caregiver_id'=>$caregiver->caregiver_id,'agency_id'=>$caregiver->agency_fk));
                // }
                // //
                if ($agency) {

                    $getAllCaregiverDetails = HHACaregiversHelper::getsyncAppointmentNew($caregiver);
                }
            }
            echo "<script>window.location.replace('/sync-hha-medical');</script>";
            // sleep(10);
            // return redirect('/fetch-hha-medical/'.$id);
        } else {
            return response()->json(['error_msg' => 'HHA Medical successfully sync', 'data' => array()], 200);
        }
    }

    public function fetchHHAMedical($id)
    {

        $hhaCaregiverDetails = HHACaregivers::getAllCaregiverDetails($id);
        
        
        if (!empty($hhaCaregiverDetails[0])) {
            foreach ($hhaCaregiverDetails as $caregiver) {
                // $query = HHAAppointmentHelper::getCaregiverOrNot($caregiver->caregiver_id,$caregiver->agency_fk);
                // if(isset($query->id)){
                //     HHAAppointmentHelper::update(array('del_flag'=>'Y'),array('id'=>$query->id));
                // }else{
                //     HHAAppointmentHelper::insert(array('caregiver_id'=>$caregiver->caregiver_id,'agency_id'=>$caregiver->agency_fk));
                // }
                // //

                $getAllCaregiverDetails = HHACaregiversHelper::getsyncAppointmentNew($caregiver);
            }
            echo "<script>window.location.replace('/fetch-hha-medical/" . $id . "');</script>";
            // sleep(10);
            // return redirect('/fetch-hha-medical/'.$id);
        } else {
            return response()->json(['error_msg' => 'HHA Medical successfully sync', 'data' => array()], 200);
        }
    }

    public function autoUpdateDetails(Request $request)
    {

        $query = HHACaregivers::where('office_name','')->where('agency_fk',  379)->where('hha_delete_flag', 'N')->whereNull('deleted_at')->paginate(500);

        foreach ($query as $val) {
            $resp = HHACaregiversHelper::getCaregiverDetailUpdate($val->caregiver_id, $val->agency_fk);
        }
    }

    public function caregiverDetails(Request $request)
    {
        $query = $this->hhaCaregiverService->getDetailByIdWithAgencyFk($request->caregiver_id,$request->agency_id);
        if(isset($query->id)){
            $query = $query;
        }else{
            $query = Self::commonResponse($request->caregiver_id);
            if(isset($query->id)){
                $query->agency_fk = $query->agency_id;
            }
        }
        $finalarray = [];
        if (isset($query->id)) {

            $finalarray = HHACaregiversHelper::getHHACaregiverDetails($query->caregiver_id, $query->agency_fk);
        }
        return response()->json(['error_msg' => 'HHA Caregiver Details successfully sync', 'data' => array($finalarray)], 200);
    }

    public function fetchCaregiverDetails(Request $request)
    {

        $query = HHACaregivers::where('agency_fk', $request->agency_id)->where('hha_delete_flag', 'N')->inRandomOrder()->limit(300)->get();
        $finalarray = [];

        foreach ($query as $val) {
            $finalarray = HHACaregiversHelper::getHHACaregiverDetails($val->caregiver_id, $val->agency_fk);
            HHACaregivers::where('caregiver_id', $val->caregiver_id)->update(array('status' => $finalarray['status']));
        }
        if (isset($query->id)) {
        }
        return response()->json(['error_msg' => 'HHA Caregiver Details successfully sync', 'data' => array($finalarray)], 200);
    }

    function searchCaregiverCode(Request $request)
    {

        $query = HHACaregiversHelper::searchCaregiverCodeWithAgencyId($request->agency_id, $request->all());
        return response()->json(['status' => true, 'data' => $query], 200);
    }

    public function searchCaregiverDocument(Request $request)
    {
        $response = [];
        //$docData = HHACaregiversHelper::getDocumentData($request->id);
        $docData = HHACaregiversHelper::getDocumentData($request->id,$request->agency);

        

        usort($docData, array($this, "sortingDateWise"));
        return response()->json(['error_msg' => "Success", 'data' => $docData], 200);
    }

    public function getCaregiverDocumentType(Request $request)
    {
        $response = [];
        $docTypeData = HHACaregiversHelper::getDocumentTypeData($request->id);
        return response()->json(['error_msg' => "Success", 'data' => $docTypeData], 200);
    }

    public function saveCaregiverDocument(Request $request)
    {
        $response = [];
        $data = $request->except('_token');
        $getAppointment = Self::commonResponse($request->id);
        $data['post_caregiver_id'] = $data['id'];
        $data['id'] = $getAppointment->caregiver_id;
        $response = HHACaregiversHelper::saveDocumentData($data);
        if($response){
            $ipaddress = Utility::getIP();
            $hhaLogData = [
                'patient_id'=>$request->doc_hha_patient_id,
                'hha_patient_id'=>$getAppointment->caregiver_id,
                'type'=>$request->doc_hha_patient_type,
                'hha_module_type'=>'HHA Exchange',
                'send_response'=>serialize($data),
                'ip_address' => $ipaddress,
                'action'=>'Add',
                
            ];
            $this->hhaLogService->save($hhaLogData);
            return response()->json(['error_msg' => "Document details added successfully.",'status' => 1, 'data' => $response], 200);
        }else{
            return response()->json(['error_msg' => "Something went to wrong.", 'status' => 0, 'data' => $response], 422);
        }
    }

    function sortingDateWise($a, $b){ 
        $a = date('Y-m-d H:i:s', strtotime($a['CreatedOn']));
        $b = date('Y-m-d H:i:s', strtotime($b['CreatedOn']));
    
        if ($a == $b) {
            return 0;
        }
        return ($b < $a) ? -1 : 1;
    }

    public function getCaregiverAppointmentData(Request $request)
    {
        $final_array = array();
        $caregiverDetails = HHACaregiversHelper::getCaregiverDetails($request->id);

        if (isset($caregiverDetails->id)) {
            $query = HHACaregiversHelper::getVisitCalenderdata($caregiverDetails,$request->start,$request->end);
        }

        $tempArray = array();
        if (isset($query)  && !empty($query)) {
            foreach ($query as $val) {

                $pName=$cName="";

                if(isset($val['caregiver_first_name']) && $val['caregiver_last_name'] !=""){
                    $cName = "C: ".$val['caregiver_first_name'].' '.$val['caregiver_last_name'];
                }
                $tempArray['title'] = "V : " . date('h:i A', strtotime($val['schedule_start_time'])) . ' - ' . date('h:i A', strtotime($val['schedule_end_time']));
                $tempArray['label'] = $cName.'</br>'.$pName;
                $tempArray['start'] = $val['schedule_start_time'];

                $final_array[] = $tempArray;
            }
        }
        echo json_encode($final_array);
    }

    public function getDownloadDocument(Request $request)
    {
        $docData = HHACaregiversHelper::getDownloadDocumentData($request->id,$request->docid);
        return response()->json(['error_msg' => "Success", 'data' => $docData], 200);
    }

    public function getCaregiverPrefrences(Request $request)
    {
    
        $docTypeData = HHACaregiversHelper::getPrefrencesData($request->id);
        return response()->json(['error_msg' => "Success", 'data' => $docTypeData], 200);
    }

    public function syncHHACaregiverOtherComplianceWithAgencyId($id){
       $query = Agency::whereRaw('sha1(id)="'.$id.'" and enable_hha=1 and app_name !=""')->first();
       $data = 0;
       if(isset($query->id)){
            $data = HHACaregiversHelper::GetCaregiverComplianceItemDueByAgencyId($query->id);

        }

        if($data ==1){
            echo "<script>
        setTimeout(function() {
            window.location.replace('/sync-hha-other-compliance/" . $id . "');
        }, 5000);
    </script>";
        }else{
            return response()->json(['error_msg' => "HHA Other Compliance successfull sync", 'data' => array()], 200);
        }
    }

    
    function agencyWiseSYNCCaregiver(){
     
        $query = Agency::whereNull('last_sync_date')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->orderBy('id','desc')->first();
       
        if(isset($query->id)){
            DB::table('hha_log')->insert(
                ['agency_id' => $query->id, 'created_date' => date('Y-m-d H:i:s'), 'type' =>"Caregiver SYNC"]
            );
            $getActiveCaregivers = HHACaregivers::where('agency_fk',$query->id)->where('hha_delete_flag','N')->where('status','Active')->pluck('caregiver_id');
           
            $getInactiveCaregivers = HHACaregivers::where('agency_fk',$query->id)->where('hha_delete_flag','N')->where('status','Inactive')->pluck('caregiver_id');
            $getTerminatedCaregivers = HHACaregivers::where('agency_fk',$query->id)->where('hha_delete_flag','N')->where('status','Terminated')->pluck('caregiver_id');
            $getHoldCaregivers = HHACaregivers::where('agency_fk',$query->id)->where('hha_delete_flag','N')->where('status','Hold')->pluck('caregiver_id');
            $getOnLeaveCaregivers = HHACaregivers::where('agency_fk',$query->id)->where('hha_delete_flag','N')->where('status','On Leave')->pluck('caregiver_id');
            $getRejectedCaregivers = HHACaregivers::where('agency_fk',$query->id)->where('hha_delete_flag','N')->where('status','Rejected')->pluck('caregiver_id');
            $getStatusNUllCaregivers = HHACaregivers::where('agency_fk',$query->id)->where('hha_delete_flag','N')->whereRaw('status IS NULL or status =""')->pluck('caregiver_id');

            $hhaActiveCaregiverResponse = HHACaregiversHelper::autoSYNCCaregiverWithStatus($query,'Active');

            $finalAll = array_merge($getActiveCaregivers->toArray(),$getInactiveCaregivers->toArray(),$getTerminatedCaregivers->toArray(),$getHoldCaregivers->toArray(),$getOnLeaveCaregivers->toArray(),$getRejectedCaregivers->toArray(),$getStatusNUllCaregivers->toArray());
           
            if($hhaActiveCaregiverResponse =='Invalid application key.'){

            }else{
             
                $activeCaregiver= array_diff($hhaActiveCaregiverResponse,$finalAll);
             
                if(count($activeCaregiver) >0){
                    
                    foreach($activeCaregiver as $act){
        
                        HHACaregivers::updateOrCreate([
                            "agency_fk"      => $query->id,
                            "caregiver_id"        => $act,
                        ], [
                          'hha_delete_flag'=>'N',
                        ]);
                    }
                }
        
                $hhaInactiveCaregiverResponse = HHACaregiversHelper::autoSYNCCaregiverWithStatus($query,'Inactive');
                $hhaInactiveCaregiverResponse =  array_intersect($hhaInactiveCaregiverResponse,$getInactiveCaregivers->toArray());
                if(count($hhaInactiveCaregiverResponse) >0){
                    foreach($hhaInactiveCaregiverResponse as $iact){
                        HHACaregivers::where('agency_fk',$query->id)->where('caregiver_id',$iact)->update(array('status'=>'Inactive','hhasyncdatetime'=>date('Y-m-d H:i:s')));
        
                    }
                }
                $hhaTerminatedCaregiverResponse = HHACaregiversHelper::autoSYNCCaregiverWithStatus($query,'Terminated');
                $hhaTerminatedCaregiverResponse =  array_intersect($hhaTerminatedCaregiverResponse,$getTerminatedCaregivers->toArray());
                if(count($hhaTerminatedCaregiverResponse) >0){
                    foreach($hhaTerminatedCaregiverResponse as $tct){
                        
                        HHACaregivers::where('agency_fk',$query->id)->where('caregiver_id',$tct)->update(array('status'=>'Terminated','hhasyncdatetime'=>date('Y-m-d H:i:s')));
                        
                    }
                }
                $hhaHoldCaregiverResponse = HHACaregiversHelper::autoSYNCCaregiverWithStatus($query,'Hold');
                $hhaHoldCaregiverResponse =  array_intersect($hhaHoldCaregiverResponse,$getHoldCaregivers->toArray());
                if(count($hhaHoldCaregiverResponse) >0){
                    foreach($hhaHoldCaregiverResponse as $ohct){
                        HHACaregivers::where('agency_fk',$query->id)->where('caregiver_id',$ohct)->update(array('status'=>'Hold','hhasyncdatetime'=>date('Y-m-d H:i:s')));
                       
                    }
                }
                
                $hhaOnLeaveCaregiverResponse = HHACaregiversHelper::autoSYNCCaregiverWithStatus($query,'On Leave');
                $hhaOnLeaveCaregiverResponse =  array_intersect($hhaOnLeaveCaregiverResponse,$getOnLeaveCaregivers->toArray());
                if(count($hhaOnLeaveCaregiverResponse) >0){
                    foreach($hhaOnLeaveCaregiverResponse as $olct){
                        HHACaregivers::where('agency_fk',$query->id)->where('caregiver_id',$olct)->update(array('status'=>'On Leave','hhasyncdatetime'=>date('Y-m-d H:i:s')));
                        
                    }
                }
                $hhaRejectedCaregiverResponse = HHACaregiversHelper::autoSYNCCaregiverWithStatus($query,'Rejected');
                $hhaRejectedCaregiverResponse =  array_intersect($hhaRejectedCaregiverResponse,$getRejectedCaregivers->toArray());
                if(count($hhaRejectedCaregiverResponse) >0){
                    foreach($hhaRejectedCaregiverResponse as $rct){
                        HHACaregivers::where('agency_fk',$query->id)->where('caregiver_id',$rct)->update(array('status'=>'Rejected','hhasyncdatetime'=>date('Y-m-d H:i:s')));
                    }
                }
            }
            
            Agency::where('id',$query->id)->update(array('last_sync_date'=>date('Y-m-d H:i:s')));
           
        }
        
    }


    function agencyWiseSYNCMedicalOlds(){
        // $query = Agency::whereNull('last_sync_medical')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->first();
        // DB::table('hha_log')->insert(
        //     ['agency_id' => $query->id, 'created_date' => date('Y-m-d H:i:s'), 'type' =>"Caregiver Medical"]
        // );
   
        $getCaregiverIds =  HHACaregivers::where('hha_delete_flag','N')->where('status','Active')->whereRaw("(last_medical_sync is null or last_medical_sync <'".date('Y-m-d H:i:s',strtotime('-5 day') )."')" )->groupBy('caregiver_id')->inRandomOrder()->limit(1000)->pluck('caregiver_id','agency_fk');
      
        $finalCaregiversIds = [];
        $finalOfficeCaregiversIds = [];
        $finalNotOfficeCaregiversIds = [];
        $duplicate = [];
        
        if(count($getCaregiverIds) >0){
            foreach($getCaregiverIds as $key=> $cid){
                if(in_array($cid,$finalCaregiversIds)){
                    $duplicate[] =$cid;
                    
                }else{
                    $query = Agency::where('id',$key)->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->first();
                
                    $hhaMedicalDetails = HHACaregiversHelper::autoSYNCCaregiverMedicals($query,$cid);
                    if(count($hhaMedicalDetails) >0){
                        foreach($hhaMedicalDetails as $medical){
                            $subQuery = HhaAppointment::where('agency_id',$query->id)->where('caregiver_id',$cid)->where('caregiver_medical_id',$medical['caregiver_medical_id'])->first();
                        
                            if($query->office_id !=""){
                               
                                
                                if($query->office_id == $medical['office_id']){
                                    $datas = [
                                        'medical_id' => $medical['medical_id'],
                                        'medical_name' => $medical['medical_name'],
                                       
                                        'due_date' =>$medical['due_date'],
                                        'status' => $medical['status'],
                                        'office_id' => $medical['office_id'],
                                        'del_flag' => 'N',
                                        
                                        'updated_date' => date('Y-m-d H:i:s'),
                                        'date_perform' => $medical['date_perform'],
                                        'sync_status' => 1
        
                                    ];
                                    if(isset($subQuery->id)){
                                        $datas['updated_date'] = date('Y-m-d H:i:s');
                                    }else{
                                        $datas['created_date'] = date('Y-m-d H:i:s');
                                    }
                                    HhaAppointment::updateOrCreate([
                                        'agency_id'        => $query->id,
                                        'caregiver_id' => $cid,
                                        'caregiver_medical_id' => $medical['caregiver_medical_id'],
                                        'del_flag'=>'N'
                                    ],$datas );
    
                                    $finalOfficeCaregiversIds[] =$cid;
                                }
                               
                            }else{
                                $datas = [
                                    'medical_id' => $medical['medical_id'],
                                    'medical_name' => $medical['medical_name'],
                                    'due_date' =>$medical['due_date'],
                                    'status' => $medical['status'],
                                    'office_id' => $medical['office_id'],
                                    'date_perform' => $medical['date_perform'],
                                    'sync_status' => 1,
                                    'del_flag' => 'N',
                                ];
                                if(isset($subQuery->id)){
                                    $datas['updated_date'] = date('Y-m-d H:i:s');
                                }else{
                                    $datas['created_date'] = date('Y-m-d H:i:s');
                                }
    
                                HhaAppointment::updateOrCreate([
                                    'agency_id'        => $query->id,
                                    'caregiver_id' => $cid,
                                    'caregiver_medical_id' => $medical['caregiver_medical_id'],
                                    'del_flag'=>'N'
                                ], $datas);
    
                               $finalNotOfficeCaregiversIds[] = $cid;
                            }
                        }
                    }
                    HHACaregivers::where('caregiver_id', $cid)->update(array("last_medical_sync" => date('Y-m-d H:i:s')));
                    $finalCaregiversIds = array_unique(array_merge($finalOfficeCaregiversIds,$finalNotOfficeCaregiversIds));
                   
                }
            }
            
        }
      
        // if(empty($getCaregiverIds[0])){
        //     Agency::where('id',$query->id)->update(array('last_sync_medical'=>date('Y-m-d H:i:s')));
        // }
        
    }

    function agencyWiseSYNCMedical(){
        // $query = Agency::whereNull('last_sync_medical')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->first();
        // DB::table('hha_log')->insert(
        //     ['agency_id' => $query->id, 'created_date' => date('Y-m-d H:i:s'), 'type' =>"Caregiver Medical"]
        // );

        $getCaregiverIds =  HHACaregivers::select('caregiver_id','agency_fk')->where('hha_delete_flag','N')->where('status','Active')->whereRaw("(last_medical_sync is null or last_medical_sync <'".date('Y-m-d H:i:s',strtotime('-5 day') )."')" )->inRandomOrder()->limit(500)->get();
   
        if(count($getCaregiverIds) >0){
            foreach($getCaregiverIds as  $caregiver){
                $query = Agency::where('id',$caregiver->agency_fk)->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->first();
                $hhaMedicalDetails = HHACaregiversHelper::autoSYNCCaregiverMedicals($query,$caregiver->caregiver_id);
                if(count($hhaMedicalDetails) >0){
                    foreach($hhaMedicalDetails as $medical){
                        $subQuery = HhaAppointment::where('agency_id',$query->id)->where('caregiver_id',$caregiver->caregiver_id)->where('caregiver_medical_id',$medical['caregiver_medical_id'])->first();
                        if($query->office_id !=""){
                            if($query->office_id == $medical['office_id']){
                                $datas = [
                                    'medical_id' => $medical['medical_id'],
                                    'medical_name' => $medical['medical_name'],
                                    
                                    'due_date' =>$medical['due_date'],
                                    'status' => $medical['status'],
                                    'office_id' => $medical['office_id'],
                                    'del_flag' => 'N',
                                    
                                    'updated_date' => date('Y-m-d H:i:s'),
                                    'date_perform' => $medical['date_perform'],
                                    'sync_status' => 1
    
                                ];

                                if(isset($subQuery->id)){
                                    $datas['updated_date'] = date('Y-m-d H:i:s');
                                   
                                }else{
                                    $datas['created_date'] = date('Y-m-d H:i:s');
                                   
                                }

                                HhaAppointment::updateOrCreate([
                                    'agency_id'  => $query->id,
                                    'caregiver_id' => $caregiver->caregiver_id,
                                    'caregiver_medical_id' => $medical['caregiver_medical_id'],
                                    'del_flag'=>'N'
                                ],$datas );
                            }
                        }else{
                            $datas = [
                                'medical_id' => $medical['medical_id'],
                                'medical_name' => $medical['medical_name'],
                                'due_date' =>$medical['due_date'],
                                'status' => $medical['status'],
                                'office_id' => $medical['office_id'],
                                'date_perform' => $medical['date_perform'],
                                'sync_status' => 1,
                                'del_flag' => 'N',
                            ];
                            if(isset($subQuery->id)){
                                $datas['updated_date'] = date('Y-m-d H:i:s');
                                
                            }else{
                                $datas['created_date'] = date('Y-m-d H:i:s');
                               
                            }

                            HhaAppointment::updateOrCreate([
                                'agency_id' => $query->id,
                                'caregiver_id' => $caregiver->caregiver_id,
                                'caregiver_medical_id' => $medical['caregiver_medical_id'],
                                'del_flag'=>'N'
                            ], $datas);
                        }
                    }

                }
                HHACaregivers::where('caregiver_id', $caregiver->caregiver_id)->where('agency_fk',$query->id)->update(array("last_medical_sync" => date('Y-m-d H:i:s')));
            }
        }
        
    }

    public function dashboard(){

        $startDate = date("Y-m-d", strtotime("- 5days"));
      
        $totalCaregivers = HHACaregivers::where('hha_delete_flag','N')->where('status','Active')->whereRaw("(last_medical_sync is null or last_medical_sync <'".$startDate."')" )->get();

        //$totalMedical = HhaAppointment::where('del_flag','N')->where('sync_status',0)->get();
        $totalPatient = HHAPatient::where('hha_delete_flag','N')->whereRaw("hha_sync ='N'" )->get();
       
        $totalOtherCompliance = HHACaregivers::where('hha_delete_flag','N')->whereDate('hhasync_othecomplience','<=',$startDate)->get();
       
        return view("hha_caregiver/dashboard", compact('totalCaregivers','totalPatient','totalOtherCompliance'));
    }

    public function updateCaregiverDemographics(){
      
        $query = HHACaregivers::where('hha_delete_flag','N')->whereNull('first_name')->inrandomOrder()->limit(500)->get();

        if(count($query) >0){
           
            foreach($query as $val){
                   
                $subquery =  HHACaregiversHelper::getCaregiverDemographicDetails($val->caregiver_id,$val->agency_fk);

                if(isset($subquery['caregiver_id']) && $subquery['caregiver_id'] !=""){
                   $updateArray = [
                    'officeId'=>$subquery['officeId'],
                    'first_name'=>$subquery['firstName'],
                    'middle_name'=>$subquery['middleName'],
                    'last_name'=>$subquery['lastName'],
                    'gender'=>$subquery['gender'],
                    'dob'=>date('Y-m-d',strtotime($subquery['dob'])),
                    'ssn'=>$subquery['ssn'],
                    'caregiver_code'=>$subquery['caregiverCode'],
                    'applicationDate'=>date('Y-m-d',strtotime($subquery['applicationDate'])),
                    'mobile_or_sms'=>$subquery['notificationMobile'],
                    'hha_sync'=>"Y",
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'hhasyncdatetime'=>date('Y-m-d H:i:s'),
                    'EmploymentTypesDiscipline'=>$subquery['EmploymentTypesDiscipline'],
                    'TeamName'=>$subquery['teamName'],
                    'location'=>$subquery['location'],
                    'first_work_date'=>date('Y-m-d',strtotime($subquery['firstWorkDate'])),
                    'hire_date'=>date('Y-m-d',strtotime($subquery['hireDate'])),
                    'last_work_date'=>date('Y-m-d',strtotime($subquery['lastWorkDate'])),
                    'language'=>$subquery['lang'],
                    'status'=>$subquery['status'],
                    'address1'=>$subquery['address'],
                    'address2'=>$subquery['address2'],
                    'City'=>$subquery['city'],
                    'State'=>$subquery['state'],
                    'Zip5'=>$subquery['zip'],
                    'HomePhone'=>$subquery['phone'],
                    'notification_mobile_no'=>$subquery['notificationMobile'],
                    'Language2'=>$subquery['lang2'],
                    'Language3'=>$subquery['lang3'],
                    'office_name'=>$subquery['office_name'],
                    'emergency_contact_name'=>$subquery['emergencyName'],
                    'emergency_contact_phone'=>$subquery['emergencyPhone1'],
                    'emergency_contact_relation_ship'=>$subquery['emergencyRelationShip'],
                    'employment_type'=>$subquery['employment_type']??"",
                   ];

                   HHACaregivers::where('hha_delete_flag','N')->where('hha_sync','N')->where('agency_fk',$val->agency_fk)->where('caregiver_id',$val->caregiver_id)->update($updateArray);
                }
            }
        }
    }
    
    public function caregiverModifiedCaregiverIds(){
        $query = Agency::whereNull('last_sync_modifled_date')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->first();
        if(isset($query->id)){
            
            $modifiedCaregiverIds = HHACaregiversHelper::getCaregiverUpdateDetails($query);
            if(count($modifiedCaregiverIds) >0){
                foreach($modifiedCaregiverIds as $val){
                    $subquery =  HHACaregiversHelper::getCaregiverDemographicDetails($val,$query->id);
                    if(isset($subquery['caregiver_id']) && $subquery['caregiver_id'] !=""){
                        $updateArray = [
                         'officeId'=>$subquery['officeId'],
                         'first_name'=>$subquery['firstName'],
                         'middle_name'=>$subquery['middleName'],
                         'last_name'=>$subquery['lastName'],
                         'gender'=>$subquery['gender'],
                         'dob'=>$subquery['dob'],
                         'ssn'=>$subquery['ssn'],
                         'caregiver_code'=>$subquery['caregiverCode'],
                         'applicationDate'=>$subquery['applicationDate'],
                         'mobile_or_sms'=>$subquery['notificationMobile'],
                         'hha_sync'=>"Y",
                         'updated_at'=>date('Y-m-d H:i:s'),
                         'hhasyncdatetime'=>date('Y-m-d H:i:s'),
                         'EmploymentTypesDiscipline'=>$subquery['EmploymentTypesDiscipline'],
                         'TeamName'=>$subquery['teamName'],
                         'location'=>$subquery['location'],
                         'first_work_date'=>$subquery['firstWorkDate'],
                         'hire_date'=>$subquery['hireDate'],
                         'last_work_date'=>$subquery['lastWorkDate'],
                         'language'=>$subquery['lang'],
                         'status'=>$subquery['status'],
                         'address1'=>$subquery['address'],
                         'address2'=>$subquery['address2'],
                         'City'=>$subquery['city'],
                         'State'=>$subquery['state'],
                         'Zip5'=>$subquery['zip'],
                         'HomePhone'=>$subquery['phone'],
                         'notification_mobile_no'=>$subquery['notificationMobile'],
                         'Language2'=>$subquery['lang2'],
                         'Language3'=>$subquery['lang3'],
                         'office_name'=>$subquery['office_name'],
                         'emergency_contact_name'=>$subquery['emergencyName'],
                         'emergency_contact_phone'=>$subquery['emergencyPhone1'],
                         'emergency_contact_relation_ship'=>$subquery['emergencyRelationShip'],
                         'employment_type'=>$subquery['employment_type']??"",
                        ];
     
                        HHACaregivers::where('hha_delete_flag','N')->where('agency_fk',$query->id)->where('caregiver_id',$val)->update($updateArray);
                     }
                }
            }

            Agency::where('id',$query->id)->update(array('last_sync_modifled_date'=>date('Y-m-d H:i:s')));
        }
        
    }

    public function caregiverSyncOtherCompliance(){
        $getCaregiverIds =  HHACaregivers::where('hha_delete_flag','N')->where('status','Active')->whereRaw("(hhasync_othecomplience is null or hhasync_othecomplience <'".date('Y-m-d H:i:s',strtotime('-5 day') )."')" )->inRandomOrder()->limit(500)->get();
        $finalCaregiversIds= [];
        if(count($getCaregiverIds) >0){
            foreach($getCaregiverIds as $caregiver){

                $getOtherOtherCompliance = HHACaregiversHelper::getCaregiverOtherCompliance($caregiver->agency_fk,$caregiver->officeId);
                if(count($getOtherOtherCompliance) >0){
                    foreach($getOtherOtherCompliance as $otc){
                        $agencyDetails = Agency::getDetailsByAgencyId($caregiver->agency_fk);
                        $medicalDetails = HHACaregiversHelper::GetAllCaregiverComplianceItemDue($agencyDetails,$caregiver->officeId,$caregiver->caregiver_id,$otc['id']);
                       if(count($medicalDetails) >0){
                            foreach($medicalDetails as $doc){
                                HhaOtherComplience::updateOrCreate([
                                    'agency_id'        => $caregiver->agency_fk,
                                    'caregiver_id' => $caregiver->caregiver_id,
                                    'caregiver_medical_id' => $doc['caregiver_medical_id'],
                                ], [
                                    'medical_id' => $doc['medical_id'],
                                    'medical_name' =>  $doc['medical_name'],
                                  
                                    'due_date' =>  $doc['due_date'],
                                    'status' => $doc['status'],
                                    'office_id' => $doc['office_id'],
                                    'del_flag' => 'N',
                                    'updated_date' => date('Y-m-d H:i:s')
                                ]);
                            }
                       }
                    }
                }

                HHACaregivers::where('id', $caregiver->id)->update(array("hhasync_othecomplience" => date('Y-m-d H:i:s')));
                $finalCaregiversIds[] = $caregiver->id;
            }
      
        }
    }

    function updateCaregiverDOB(){
        $query = HHACaregivers::where('hha_delete_flag','N')->where('hha_sync','Y')->where('dob','0000-00-00')->inrandomOrder()->limit(100)->get();
        if(count($query) >0){
           
            foreach($query as $val){
                   
                $subquery =  HHACaregiversHelper::getCaregiverDemographicDetails($val->caregiver_id,$val->agency_fk);
                if(isset($subquery['dob'])){
                    HHACaregivers::where('id',$val->id)->update(array('dob'=>date('Y-m-d',strtotime($subquery['dob'])),'hire_date'=>date('Y-m-d',strtotime($subquery['hireDate'])),'first_work_date'=>date('Y-m-d',strtotime($subquery['firstWorkDate'])),'last_work_date'=>date('Y-m-d',strtotime($subquery['lastWorkDate'])),'applicationDate'=>date('Y-m-d',strtotime($subquery['applicationDate']))));
                }
                
                
            }
        }
    }

    function agencyWiseSYNCCaregiverWIthAgency(Request $request){
        $query = Agency::where('id',$request->id)->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->first();

        if(isset($query->id)){
            // DB::table('hha_log')->insert(
            //     ['agency_id' => $query->id, 'created_date' => date('Y-m-d H:i:s'), 'type' =>"Caregiver SYNC"]
            // );
            $getActiveCaregivers = HHACaregivers::where('agency_fk',$query->id)->where('hha_delete_flag','N')->pluck('caregiver_id');
           
            $hhaActiveCaregiverResponse = HHACaregiversHelper::autoSYNCCaregiverWithStatus($query,'Active');
            if($hhaActiveCaregiverResponse =='Invalid application key.'){

            }else{
                $activeCaregiver= array_diff($hhaActiveCaregiverResponse,$getActiveCaregivers->toArray());
    
                if(count($activeCaregiver) >0){
                    
                    foreach($activeCaregiver as $act){
        
                        HHACaregivers::updateOrCreate([
                            "agency_fk"      => $query->id,
                            "caregiver_id"        => $act,
                        ], [
                          'hha_delete_flag'=>'N',
                        ]);
                    }
                }
            }
        
                
            
            Agency::where('id',$query->id)->update(array('last_sync_date'=>date('Y-m-d H:i:s')));
           
        }
        
    }

    function checkForAllCaregiverSYNCOrNot(){
        $query = Agency::whereNull('last_sync_date')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->get();
        if(count($query) ==0){
            Agency::whereNotNull('last_sync_date')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->update(array('last_sync_date'=>NULL));
        }


        $patientSYNC = Agency::whereNull('last_sync_patient')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->get();
        if(count($patientSYNC) ==0){
            Agency::whereNotNull('last_sync_patient')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->update(array('last_sync_patient'=>NULL));
        }

        $all = Agency::whereNull('last_sync_modifled_date')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->get();
        if(count($all) ==0){
            Agency::whereNotNull('last_sync_modifled_date')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->update(array('last_sync_modifled_date'=>NULL));
        }

        $allPatientModified = Agency::whereNull('last_patient_sync_modifled_date')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->get();
        if(count($allPatientModified) ==0){
            Agency::whereNotNull('last_patient_sync_modifled_date')->where('enable_hha',1)->whereNotNull('app_name')->whereNotNull('app_key')->update(array('last_patient_sync_modifled_date'=>NULL));
        }
    }

    function hhaTrackerReport(Request $request){
        $agencyList = Agency::getHHAAgencyList();
        return view('hha_caregiver_report.index',compact('agencyList'));
    }

    public function ajaxSyncRemainingCaregiver(Request $request){
        $page =$request->page;
        $response = $this->hhaCaregiverService->ajaxSyncData($request->all());
       return view('hha_caregiver_report.ajax_list',compact('response','page'));
    }

    public function updateCaregiverEmploymentType(){
        $query =HHACaregivers::select('id','caregiver_id','agency_fk')->where('hha_delete_flag','N')->whereNull('employment_type')->limit(500)->get();
       if(count($query) >0){
        foreach($query as $val){
            $subquery =  HHACaregiversHelper::getCaregiverDemographicDetails($val->caregiver_id,$val->agency_fk);
            HHACaregivers::where('id',$val->id)->update(array('employment_type'=>$subquery['employment_type']));
        }
       }
    }

    public function getHHACaregiverMeddicalList(Request $request){
        $query =  $this->hhaCaregiverService->getDetailByIdWithAgencyFk($request->id,$request->agency_id);
        $data = [];
        if(isset($query->id)){
            $data = HHACaregiversHelper::getCaregiverMedicalDocument($request->agency_id,$query->officeId);
        }

        return response()->json(['data'=>$data],200);
    }

    public function  saveHHACaregiverMedical(Request $request){
        $validator = Validator::make($request->all(), [
            'hha_medical_document_medical_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        }
        $getDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->patient_id);
        if(isset($getDetails->id)){
            if($getDetails->link_hha_caregiver !=""){
                $caregiverId = $getDetails->link_hha_caregiver;
            }else{
                $getAppointmentDetails = HHAAppointmentHelper::getById($getDetails->hha_id);
                $caregiverId = $getAppointmentDetails->caregiver_id;
            }

            $data = $request->all();
            $data['caregiver_id'] = $caregiverId;
            $data['agency_id'] = $getDetails->agency_id;
           
            $image = "";
            if($request->hasFile('hha_medical_document_file')){
                $file = $request->file('hha_medical_document_file');
                $image = base64_encode($file->getRealPath());
            }
            $data['image'] = $image;
         
            $response = HHACaregiversHelper::createNewCaregiverMedicalTest($data);
            if($response ==1){
                $ipaddress = Utility::getIP();
                $hhaLogData = [
                    'patient_id'=>$request->patient_id,
                    'hha_patient_id'=>$caregiverId,
                    'type'=>$getDetails->type,
                    'hha_module_type'=>'HHA Exchange Medical',
                    'send_response'=>serialize($request->except('_token')),
                    'ip_address' => $ipaddress,
                    'action'=>'Add',
                ];
                $this->hhaLogService->save($hhaLogData);
                return response()->json(['error_msg'=>"Medical successfully added"],200);
            }else{
                if($response ==0){
                    $error_msg = "Sorry, something went wrong. Please try again.";
                }else{
                    $error_msg = $response;
                }
                return response()->json(['error_msg'=>$error_msg],500);
            }
        }
    }

    public function getCaregiverComplianceI9Details(Request $request){
        $query = CaregiverComplianceI9s::select('id','hire_date','column_ab_document','columnc_document','expiration_date','i9_verified','everify_number','i9_notes','columnc_document_name','column_ab_document_name')->where('patient_id',$request->patient_id)->where('del_flag','N')->first();
        if(isset($query->id)){
            $shortText = (strlen($query->i9_notes) > 50) ? substr($query->i9_notes, 0, 50) . '...' : $query->i9_notes;
            $query->i9_notes_new = $shortText;
            $query->expiration_date = Utility::convertMDY($query->expiration_date);
            $query->hire_date = Utility::convertMDY($query->hire_date);
        }
        
        return response()->json(['data'=>$query,'error_msg'=>'Success'],200);
    }

    public function getI9ABCDocument(Request $request){
        $getAbDocuments = HHACaregiversHelper::getAbDocumentByCaregiverI9Requirements($request->agencyId);
        $getCDocuments = HHACaregiversHelper::getCDocumentByCaregiverI9Requirements($request->agencyId);
        return response()->json(['data'=>array('ab_document'=>$getAbDocuments,'caregiveri9_cdocument'=>$getCDocuments),'error_msg'=>'Success'],200);
    }

    public function updateCaregiverI9Requirement(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required',
            'agency_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => array()], 422);
        }

        $getDetails = $this->patientService->getPatientDetailsByIdWhitoutAgency($request->patient_id);
        if(isset($getDetails->id)){
            if($getDetails->link_hha_caregiver !=""){
                $caregiverId = $getDetails->link_hha_caregiver;
            }else{
                $getAppointmentDetails = HHAAppointmentHelper::getById($getDetails->hha_id);
                $caregiverId = $getAppointmentDetails->caregiver_id;
            }

            $data = [
                'caregiverId'            => $caregiverId,
                'I9ABDocumentID'         => $request->hha_caregiver_i9_requirement_ab_document,
                'I9CDocumentID'          => $request->hha_caregiver_i9_requirement_cdocument,
                'I9Verified'             => $request->hha_caregiver_i9_requirement_verified,
                'I9DocumentExpiration'   => $request->hha_caregiver_i9_requirement_doc_exp_date != ""
                                            ? Utility::convertYMD($request->hha_caregiver_i9_requirement_doc_exp_date)
                                            : "",
                'HireDate'               => $request->hha_caregiver_i9_requirement_hire_date != ""
                                            ? Utility::convertYMD($request->hha_caregiver_i9_requirement_hire_date)
                                            : "",
                'I9Notes'                => $request->hha_caregiver_i9_requirement_note,
                'I9EVerifyNumber'        => $request->hha_caregiver_i9_requirement_verify_number,
            ];

            $response = HHACaregiversHelper::hhaUpdateCaregiverI9Requirements($data,$request->agency_id);
          
            if($response ==1){
                $updateOrNot = CaregiverComplianceI9s::where('patient_id',$request->patient_id)->where('agency_id',$request->agency_id)->first();
                $oldResponse = [];
                $saveData =[
                    'hire_date'=>$data['HireDate'],
                    'column_ab_document'=>$data['I9ABDocumentID'],
                    'columnc_document'=>$data['I9CDocumentID'],
                    'expiration_date'=>$data['I9DocumentExpiration'],
                    'i9_verified'=>$data['I9Verified'],
                    'everify_number'=>$data['I9EVerifyNumber'],
                    'i9_notes'=>$data['I9Notes'],
                    'columnc_document_name'=>$request->columnc_document,
                    'column_ab_document_name'=>$request->column_ab_document,
                    
                ];
                if(isset($updateOrNot->id)){
                    $oldResponse = $updateOrNot->toArray();
                    $saveData['updated_date'] = date('Y-m-d H:i:s');
                    $saveData['updated_by'] = $user->id;

                    CaregiverComplianceI9s::where('id',$updateOrNot->id)->update($saveData);
                }else{
                    $saveData['created_date'] = date('Y-m-d H:i:s');
                    $saveData['created_by'] = $user->id;
                    $saveData['agency_id'] = $request->agency_id;
                    $saveData['caregiver_id'] = $caregiverId;
                    $saveData['patient_id'] = $request->patient_id;
                    
                    $saveDetails = new CaregiverComplianceI9s($saveData);
                    $saveDetails->save();
                }

                $ipaddress = Utility::getIP();
				$insertLog = [
					'type' => 'Update Caregiver I-9 Requirements',
					'link' => url('/update-caregiver-i9-requirement'),
					'module' => 'Patient Appointment',
					'object_id' => $request->patient_id,
					'message' => $user->first_name . ' ' . $user->last_name . ' has updated Caregiver I-9 requirements',
					'new_response' => serialize($saveData),
                    'old_response' => serialize($oldResponse),
					'ip' => $ipaddress,
				];
				LogsService::save($insertLog);

                return response()->json(['error_msg' => "Caregiver I-9 requirements updated successfully"], 200);
            }else{
                
                if($response != '0'){
                    $errors = json_decode(json_encode($response,true),true);
                    
                    $error_msg = "";
                    if (is_array($errors)) {
                        // It's an array, you can loop through it
                        $tempErrorMessage = "";
                        $cnt =1;
                        foreach ($errors as $error) {
                            $tempErrorMessage .= $cnt.". ".preg_replace('/^\d+\s/', '', $error).'<br>';
                            $cnt++;
                        }

                        $error_msg = $tempErrorMessage;
                    } else {

                        $error_msg = $response[0]??"Sorry, something went wrong. Please try again.";
                    }
                    
                }else{
                    $error_msg = "Sorry, something went wrong. Please try again1.";
                }

                return response()->json(['error_msg'=>$error_msg],500);
            }
        }
    }

    /**
     * Get caregiver tab details based on tab name
     * Handles dynamic loading for different tabs in the caregiver modal
     *
     * @param Request $request
     * @param string $tabName
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCaregiverTabDetails(Request $request, $tabName)
    {
        try {
            $caregiverId = $request->caregiver_id;

            if (!$caregiverId) {
               
                return response()->json([
                    'error_msg' => 'Caregiver ID is required',
                    'data' => array()
                ], 400);
            }

            // Get caregiver details
            $query = HHACaregivers::where('caregiver_id', $caregiverId)->where('agency_fk',$request->agency_id)
                ->where('hha_delete_flag', 'N')
                ->first();

            if (!isset($query->id)) {
                return response()->json([
                    'error_msg' => 'Caregiver not found',
                    'data' => []
                ], 404);
            }

            $data = [];

            // Route to appropriate method based on tab name
            switch ($tabName) {
                case 'demographic':
                    $data = $this->getDemographicData($request->all());
                    break;

                case 'calendar':
                    $data = $this->getCalendarData($query);
                    break;

                case 'availability':
                    $data = $this->getAvailabilityData($query, $request);
                    break;

                case 'notes':
                    $data = $this->getNotesData($query, $request);
                    break;

                case 'inservice':
                    $data = $this->getInServiceData($query, $request);
                    break;

                case 'medical':
                    $data = $this->getMedicalData($query,$request);
                    break;

                case 'compliance':
                    $data = $this->getComplianceData($query, $request);
                    break;

                case 'document':
                    $data = $this->getDocumentData($query, $request);
                    break;

                case 'preferences':
                    $data = $this->getPreferencesData($query, $request);
                    break;

                default:
                    return response()->json([
                        'status' => 0,
                        'error_msg' => 'Invalid tab name',
                        'data' => []
                    ], 400);
            }

            return response()->json([
                'error_msg' => 'Success',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error_msg' => 'Error loading data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get demographic data for caregiver
     */
    private function getDemographicData($data)
    {
        $response = HHACaregiversHelper::getHHACaregiverDetails($data['caregiver_id'], $data['agency_id']);
        return $response;
    }

    /**
     * Get calendar visits for caregiver (separate endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCaregiverCalendarVisits(Request $request)
    {
        try {
            $caregiverId = $request->caregiver_id;
            $agencyId = $request->agency_id;
            $startDate = $request->start;
            $endDate = $request->end;

            if (!$caregiverId || !$agencyId) {
                return response()->json([
                    'status' => 0,
                    'error_msg' => 'Caregiver ID and Agency ID are required',
                    'data' => []
                ], 400);
            }

            // If dates not provided, default to current month
            if (!$startDate || !$endDate) {
                $startDate = date('Y-m-01'); // First day of current month
                $endDate = date('Y-m-t');    // Last day of current month
            }

            // Get caregiver details
            $caregiver = HHACaregivers::where('caregiver_id', $caregiverId)
                ->where('agency_fk', $agencyId)
                ->where('hha_delete_flag', 'N')
                ->first();

            if (!isset($caregiver->id)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Caregiver not found',
                    'data' => []
                ], 404);
            }
            $agency = Agency::getAllDetailsbyAgencyId($agencyId);
            $caregiver->agencyDetails = $agency;
            // Get caregiver visits for calendar with date range
            $visits = HHACaregiversHelper::getVisitNew($caregiver, $startDate, $endDate);
            // Format visits for FullCalendar
            $formattedVisits = [];
            if (!empty($visits)) {
                foreach ($visits as $visit) {
                    $visitDate = $visit['visit_date'] ?? $visit['VisitDate'] ?? $visit['start_date'] ?? null;
                    // Filter visits by date range (in case helper doesn't do it)

                    $pName="P: ".$visit['patient_first_name'].' '.$visit['patient_last_name'];

                    $formattedVisits[] = [
                        'visit_id' => $visit['visit_id'] ?? $visit['id'] ?? null,
                        'title'=>"V :" . Utility::convertTwelveHourTime($visit['schedule_start_time']) . ' - ' .Utility::convertTwelveHourTime($visit['schedule_end_time']),
                        'patient_name' => $pName ?? 'N/A',
                        'label' =>"C: ". $visit['first_name'] . ' ' . $visit['last_name'].' <br>'.$pName,
                        'patient_id' => $visit['patient_id'] ?? null,
                        'visit_date' => $visitDate,
                        'start' =>Utility::convertMDYTime($visit['schedule_start_time']) ?? null,
                        'status' => $visit['status'] ?? $visit['Status'] ?? 'Scheduled',
                        'end' => Utility::convertMDYTime($visit['schedule_end_time']) ?? null,
                        'notes' => $visit['notes'] ?? $visit['Notes'] ?? null,
                        'start_time' => $visit['start_time'] ?? $visit['StartTime'] ?? null,
                        'end_time' => $visit['end_time'] ?? $visit['EndTime'] ?? null,
                    ];
                   
                }
            }

            return response()->json([
                'status' => 1,
                'error_msg' => 'Success',
                'data' => [
                    'caregiver_id' => $caregiver->caregiver_id,
                    'visits' => $formattedVisits,
                    'total_visits' => count($formattedVisits),
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'error_msg' => 'Error loading calendar data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get calendar data for caregiver (for tab switching - returns minimal data)
     */
    private function getCalendarData($caregiver)
    {
        // For the calendar tab, just return a flag to load the calendar
        // Actual data will be loaded via separate AJAX call
        return [
            'caregiver_id' => $caregiver->caregiver_id,
            'load_calendar' => true,
            'error_msg' => 'Calendar will be loaded dynamically'
        ];
    }

    /**
     * Get availability data for caregiver
     */
    private function getAvailabilityData($caregiver,$request)
    {
        $availabilityData = HHACaregiversHelper::GetCaregiverAvailabilityById($caregiver,$request->agency_id);

        return [
            'availability' => $availabilityData ?? [],
            'caregiver_id' => $caregiver->caregiver_id
        ];
    }

    /**
     * Get notes data for caregiver
     */
    private function getNotesData($caregiver, $request)
    {

        if(!empty($request->date)){
            $explode = explode('-',$request->date);
            $startDate = date("Y-m-d", strtotime($explode[0]));
            $endDate = date("Y-m-d", strtotime($explode[1]));
        }else{
            $startDate = date("Y-m-d", strtotime("-1 year"));
            $endDate = date("Y-m-d");
        }
        

        $notes = HHACaregiversHelper::getHHACaregiverNotes($caregiver, $startDate, $endDate,$request->agency_id);

        return [
            'notes' => $notes ?? [],
            'caregiver_id' => $caregiver->caregiver_id
        ];
    }

    /**
     * Get inservice data for caregiver
     */
    private function getInServiceData($caregiver,$request)
    {
        $inservices = HHACaregiversHelper::getHHACaregiverInServices($caregiver, $caregiver->caregiver_id,$request->agency_id);

        // Sort by date
        if (!empty($inservices)) {
            usort($inservices, function($a, $b) {
                $t1 = strtotime($a['inservice_date'] ?? '0');
                $t2 = strtotime($b['inservice_date'] ?? '0');
                return $t2 - $t1;
            });
        }

        return [
            'inservices' => $inservices ?? [],
            'caregiver_id' => $caregiver->caregiver_id
        ];
    }

    /**
     * Get medical data for caregiver
     */
    private function getMedicalData($caregiver,$request)
    {
        $agency = Agency::getAllDetailsbyAgencyId($request->agency_id);
        $caregiver->agencyDetails = $agency;
        $medicals = HHACaregiversHelper::getCaregiverMedicalDetails($caregiver, $caregiver->caregiver_id,$request->status,$request);

        return [
            'medicals' => $medicals ?? [],
            'caregiver_id' => $caregiver->caregiver_id
        ];
    }

    /**
     * Get compliance data for caregiver
     */
    private function getComplianceDataOld($caregiver,$request)
    {
        $agency = Agency::getAllDetailsbyAgencyId($request->agency_id);
        $getOtherOtherCompliance = HHACaregiversHelper::getCaregiverOtherCompliance($request->agency_id,$caregiver->officeId);
        $final = [];
        echo "<pre>";print_R($getOtherOtherCompliance);die();
        if(count($getOtherOtherCompliance) >0){
            foreach($getOtherOtherCompliance as $otc){
                $medicalDetails = HHACaregiversHelper::GetAllCaregiverComplianceItemDue($agency,$caregiver->officeId,$caregiver->caregiver_id,$otc['id']);
                if(!empty($medicalDetails[0])){
                    $final[] = $medicalDetails[0];
                }
            }
        }
        
        return [
            'compliance' => $final ?? [],
            'caregiver_id' => $caregiver->caregiver_id
        ];
    }

    private function getComplianceData($caregiver,$request)
    {
        $agency = Agency::getAllDetailsbyAgencyId($request->agency_id);
        $getOtherOtherCompliance = HHACaregiversHelper::getCaregiverOtherCompliance($request->agency_id,$caregiver->officeId);
        $final = [];
        $response = HHACaregiversHelper::getCaregiverMedicalDetails($caregiver,$caregiver->caregiver_id);

        if(count($getOtherOtherCompliance) >0){
            foreach($getOtherOtherCompliance as $otc){
                $result = array_filter($response, function($item) use ($otc) {
                    return $item['medical_id'] == $otc['id'];
                });
        
                $final = array_merge($final, $result);
            }
        }
        
        return [
            'compliance' => $final ?? [],
            'caregiver_id' => $caregiver->caregiver_id
        ];
    }
    /**
     * Get document data for caregiver
     */
    private function getDocumentData($caregiver,$request)
    {
        $documents = HHACaregiversHelper::getDocumentData($caregiver->caregiver_id, $request->agency_id);

        // Sort by date
        if (!empty($documents)) {
            usort($documents, function($a, $b) {
                $t1 = strtotime($a['CreatedOn'] ?? '0');
                $t2 = strtotime($b['CreatedOn'] ?? '0');
                return ($t2 < $t1) ? -1 : 1;
            });
        }

        return [
            'documents' => $documents ?? [],
            'caregiver_id' => $caregiver->caregiver_id
        ];
    }

    /**
     * Get preferences data for caregiver
     */
    private function getPreferencesData($caregiver,$request)
    {
        $preferences = HHACaregiversHelper::getPrefrencesData($caregiver->caregiver_id,$request->agency_id);

        return [
            'preferences' => $preferences ?? [],
            'caregiver_id' => $caregiver->caregiver_id
        ];
    }

}