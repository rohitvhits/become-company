<?php

namespace App\Http\Controllers;

use App\Agency;
use App\Model\AlayacareClient;
use Illuminate\Http\Request;
use App\Services\PatientService;
use App\Services\AlayacareClientService;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AlayacareHelper;
use App\Services\PatientServicesRequest;
use App\Services\PatientWiseServicesRequests;
use App\Helpers\Utility;
use Exception;
use App\Services\AgencyService;
use Illuminate\Support\Facades\Cache;
use App\Services\LogsService;
use App\Services\MasterService;
class AlayacareClientController extends Controller
{
    protected $patientService;
    protected $alayacareClientService;
    protected $patientServicesRequest;
    protected $patientWiseServicesRequests;
    protected $agencyService;
    protected $masterService;
    public function __construct(PatientService $patientService,AlayacareClientService $alayacareClientService,PatientServicesRequest $patientServicesRequest,PatientWiseServicesRequests $patientWiseServicesRequests,AgencyService $agencyService,MasterService $masterService)
	{
        $this->middleware('auth');
        $this->middleware('permission:alayacare-client-list|alayacare-client-add-appointment|alayacare-client-list-export', ['only' => ['getAlaycareClientList','clientAddAppointment','alaycareClientExport']]);
        $this->middleware('permission:alayacare-client-list', ['only' => ['getAlaycareClientList','getAlaycareClientListAjax']]);
        $this->middleware('permission:alayacare-client-add-appointment', ['only' => ['clientAddAppointment']]);
		$this->patientService = $patientService;
        $this->alayacareClientService = $alayacareClientService;
        $this->patientServicesRequest = $patientServicesRequest;
        $this->patientWiseServicesRequests = $patientWiseServicesRequests;
        $this->agencyService = $agencyService;
        $this->masterService = $masterService;
	}

    public function getAlaycareClientList(){

        $data['menu'] = "user";

		$data['user'] = $user = auth()->user();
        $data['agencyList'] = $this->agencyService->getAlayacareAgencyList();

        $data['masterData'] = Cache::get('alayacare-client-discipline', function (){
            return $this->masterService->getAllDataByMasterTypeFk([26]);
        }, 10 * 60);
   
        return view('alaycare-client.index',$data);

    }

    public function alaycareClientExport(Request $request){
        $user = auth()->user();
       
        $users = $this->alayacareClientService->getDataExportClient($request->all(),'export');

        $filename = 'Alaycare Client' . date("m-d-Y");
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $columns = array('First Name', 'Last Name', 'Branch Name' , 'Phone No' , 'City' , 'State' , 'Gender' , 'Status');

        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($users as $list) {
                fputcsv($file, array($list->first_name, $list->last_name, $list->branch_name,$list->phone_main,$list->city,$list->state,$list->gender,$list->status));
            }

            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function getAlaycareClientListAjax(Request $request){
       
        $data['query'] = $this->alayacareClientService->getAllAlaycareClient($request->all());
        return view('alaycare-client.alaycare-list-ajax',$data);
    }

    public function clientAddAppointment(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
			'service_id' => 'required',
			'ids' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['error_msg' => $validator->errors()->all()[0], 'data' => array()], 400);
		}else{
            $ids = $request->input('ids');
            $idsArray = explode(",", $ids);
            
            if(!empty($idsArray[0])){
                foreach($idsArray as $id){
                    $clientDetailsById = AlayacareClient::find($id);
                    $data = [
                        'first_name' => $clientDetailsById->first_name,
                        'last_name' => $clientDetailsById->last_name,
                        'full_name' => $clientDetailsById->first_name.' '.$clientDetailsById->last_name,
                        'type' => 'Patient',
                        'dob' => $clientDetailsById->birthday,
                        'phone' => $clientDetailsById->phone_main,
                        'mobile' => $clientDetailsById->phone_main,
                        'gender' => $clientDetailsById->gender,
                        'agency_id' =>$clientDetailsById->agency_id,
                        'service_id' => implode(',', $request->service_id),
                        'diciplin' => $request->diciplin,
                        'language' => "",
                        'county' => $clientDetailsById->country,
                        'alaycare_id' => $clientDetailsById->client_id,
                        'alaycare_name' =>  $clientDetailsById->first_name .' '. $clientDetailsById->last_name,
                        'address1'=>$clientDetailsById->address,
                        'state'=>$clientDetailsById->state,
                        'city'=>$clientDetailsById->city,
                        'zip_code'=>$clientDetailsById->zip,
                        'patient_code'=>$clientDetailsById->client_id,
                         'referral_type'=>'Alayacare'
                    ];
                    $data['status'] = Utility::getStatusFromServiceId($request->service_id);
                    $insert = $this->patientService->save($data); 
                    $clientDetailsById->update(['patient_id' => $insert]);
                    $serviceRequestStatus = $data['status'];
                    $patientServiceLastId = $this->patientServicesRequest->save([
                        'patient_id' => $insert,
                        'status' => $serviceRequestStatus
                    ]);
                    $addServiceIds = $request->input('service_id');

                    if (is_array($addServiceIds)) {
                        foreach ($addServiceIds as $serviceId) {
                            if($serviceId !=""){
                                $patientWiseServiceRequest = [
                                    'patient_id' => $insert,
                                    'service_id'=> $serviceId,
                                    'patient_service_request_id' => $patientServiceLastId,
                                
                                ];
                                $this->patientWiseServicesRequests->save($patientWiseServiceRequest);
                            }
                            
                        }
                    }

                    try{
                        Utility::saveResolutionLogForms($serviceRequestStatus,$patientServiceLastId,$insert);
                    }catch(Exception $e){}
                }
                if($insert){
                    $ipaddress = Utility::getIP();
                    $insertLog = [
                        'type' => 'Add Appointment',
                        'link' => url('/alayacare/alayacare-client/client-add-appointment'),
                        'module' => 'Patient Appointment',
                        'object_id' => $insert,
                        'message' => $user->first_name . ' ' . $user->last_name . ' has added an appointment via AlayaCare',
                        'new_response' => serialize($data),
                        'ip' => $ipaddress,
                    ];
                    LogsService::save($insertLog);
                    return response()->json(['error_msg' => "Appointment  successfully Added", 'status' => 1, 'data' => ""], 200);
                }else{
                    return response()->json(['error_msg' => "Some thing wrong", 'status' => 1, 'data' => ""], 500);
                }
            }
        }
    }

    public function alaycareClientData(Request $request){
        $query = $this->alayacareClientService->searchData($request->q,$request->agency_id);
		$data = [];
		foreach ($query as $val) {
			$temp = [];
			$temp['emp_id'] = $val->client_id;
			$temp['name'] = $val->first_name . ' ' . $val->last_name.' ( '.ucfirst($val->client_id).' ) ';

			$data[] = $temp;
		}

		return json_encode($data);
    }

    public function searchAlayacareClients(Request $request){
        $agencyDetails  = Agency::getIdById($request->agency_id);
        $query = AlayacareHelper::searchClient($agencyDetails->alaycare_username,$agencyDetails->alaycare_password,$request->q);
        $finalArray = [];
        $response = json_decode($query,true);
       
        if($response['count'] !=0){
            $finalArray = $response['items'];
        }
       
        return response()->json(['error_msg'=>'asdassd','data'=>$finalArray]);
    }
}
