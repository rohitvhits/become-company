<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use URL;
use App\Helpers\HHACaregiversHelper;
use App\Services\HHAOfficeService;
use App\Model\AgencyWiseHHAMedical;
use App\Helpers\HHAOfficeHelper;
use App\Services\AgencyWiseHHAMedicalsService;
use App\Agency;
use Carbon\Carbon;
use Cache;
use Illuminate\Support\Facades\DB;

class HHAMedicalsController extends BaseController
{

    protected $hhaOfficeService;
    protected $agencyWiseHHAMedicalsService;
	public function __construct(HHAOfficeService $hhaOfficeService,AgencyWiseHHAMedicalsService $agencyWiseHHAMedicalsService)
	{
        $this->middleware('permission:hha-medical-service|sync-hha-medical-service|active-hha-medical-service', ['only' => ['index','ajaxList']]);
        $this->middleware('permission:sync-hha-medical-service', ['only' => ['syncMedical']]);
        $this->middleware('permission:active-hha-medical-service', ['only' => ['toggleStatus']]);
        $this->hhaOfficeService = $hhaOfficeService;
        $this->agencyWiseHHAMedicalsService = $agencyWiseHHAMedicalsService;
	}

    public function syncAgencyWiseMedical(Request $request){
        HHAOfficeHelper::syncOffice($request->id);
        $officeList = $this->hhaOfficeService->getOfficeListByAgencyId($request->id);
   
        if(!empty($officeList[0])){
            foreach($officeList as $val){
                
                $getAllMedicalSync = HHACaregiversHelper::getCaregiverMedicalDocument($request->id,$val->office_id);
                 
                if(!empty($getAllMedicalSync[0])){
                    foreach($getAllMedicalSync as $val){
                        $final = [
                            'agency_id'=>$request->id,
                            'office_id'=>$val['office_id'],
                            'medical_id'=>$val['id'],
                            'medical_name'=>$val['name'],

                        ];

                        $getDetails = AgencyWiseHHAMedical::where('agency_id',$request->id)->where('office_id',$val['office_id'])->where('medical_id',$val['id'])->first();
                        if(isset($getDetails->id)){
                            $final['updated_date'] = date('Y-m-d H:i:s');
                            $final['updated_by'] = auth()->user()->id;
                        }else{
                            $final['created_date'] = date('Y-m-d H:i:s');
                            $final['created_by'] = auth()->user()->id;
                            
                        }
                        AgencyWiseHHAMedical::updateOrCreate(
                            [
                                // 🔑 Unique / matching columns
                                'agency_id'  => $request->id,
                                'office_id'  => $val['office_id'],
                                'medical_id' => $val['id'],
                            ],
                            $final
                        );
                    }
                }
            }
        }
        return response()->json(['error_msg' => 'Success'], 200);
    }

    public function index(){
        $data['menu'] = "user";
        $data['auth'] = $data['user'] = $user = auth()->user();

        if (empty($user)) {
            return redirect('/login');
        }

        if (in_array($user['user_type_fk'], array(3, 4, 5, 6))) {
            abort(404);
        }

        $data['status_list'] =  Cache::get('hha_appointment_status_list', function () {
			return  HHACaregiversHelper::getHHACaregiverStatus();
		}, 10 * 60);
  
        $data['office_table_list'] =  Cache::get('hha_office_table_list', function () {
			return  $this->hhaOfficeService->getALLOfficeList();
		}, 10 * 60);

        $data['agency_list'] =  Cache::get('hha_agency_table_list', function () {
            return  Agency::getHHAAgencyList();
        }, 10 * 60);
        $data['startDate'] = Carbon::now()->subMonths(2)->startOfMonth()->format('m/d/Y');
        $data['endDate'] = Carbon::now()->format('m/d/Y');
        return view("hha_medicals.hha_medicals_list", $data);
    }

    public function ajaxList(Request $request){

        $data['query'] = $this->agencyWiseHHAMedicalsService->ajaxList($request->all());
        
        return view('hha_medicals.hha_medicals_ajax_list',$data);
    }

    public function toggleStatus(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data provided'
                ], 400);
            }

            $medical = AgencyWiseHHAMedical::find($request->id);

            if (!$medical) {
                return response()->json([
                    'success' => false,
                    'message' => 'Medical record not found'
                ], 404);
            }

            $medical->status = $request->status;
            $medical->updated_by = auth()->user()->id;
            $medical->updated_date = date('Y-m-d H:i:s');
            $medical->save();

            $statusText = $request->status == 1 ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Medical record {$statusText} successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportCSV(Request $request){
        try {
            $data = $this->agencyWiseHHAMedicalsService->ajaxList($request->all(),'export');

            $filename = 'hha_medicals_' . date('Y-m-d_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, [
                    'No',
                    'Agency Name',
                    'Office Name',
                    'Medical ID',
                    'Medical Name',
                    'Status'
                ]);

                // Add data rows
                $i = 1;
                foreach ($data as $row) {
                    fputcsv($file, [
                        $i++,
                        $row->agency->agency_name ?? 'N/A',
                        $row->office->office_name ?? 'N/A',
                        $row->medical_id,
                        $row->medical_name,
                        $row->status == 1 ? 'Active' : 'Inactive'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOfficesByAgency(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'agency_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agency ID is required'
                ], 400);
            }

            $offices = $this->hhaOfficeService->getOfficeDetailsByAgencyId($request->agency_id);
           
            return response()->json([
                'success' => true,
                'data' => $offices
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch offices: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMedicalsByAgencyOffice(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'agency_id' => 'required|integer',
                'office_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agency ID and Office ID are required'
                ], 400);
            }

            $getMedicalList = $this->agencyWiseHHAMedicalsService->getAgencyMedicalList($request->agency_id,$request->office_id);
            if (empty($getMedicalList)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No medicals found from external system'
                ]);
            }

            // Format the data for dropdown
            $formattedMedicals = [];
            foreach ($getMedicalList as $medical) {
                $formattedMedicals[] = [
                 
                    'medical_id' => $medical->medical_id,
                    'medical_name' => $medical->medical_name
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedMedicals
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch medicals: ' . $e->getMessage()
            ], 500);
        }
    }

    public function syncMedical(Request $request){
        
            $validator = Validator::make($request->all(), [
                'agency_fk' => 'required|integer',
                'office_fk' => 'required|integer',
                'medicals' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'All fields are required',
                    'errors' => $validator->errors()
                ], 400);
            }

           if(isset($request->agency_fk) && isset($request->office_fk) && isset($request->medicals) ){
                $sequence=0;
                $updateSequece=true;
                if($request->sequence){
                    $sequence=$request->sequence;
                }

                $agencyDetails = Agency::getDetailsByAgencyId($request->agency_fk);

                $getCaregiverMedical = HHACaregiversHelper::GetAllCaregiverComplianceItemDueMedical($agencyDetails,$request->office_fk,$request->medicals,$sequence); 

                foreach ($getCaregiverMedical as $key => $Medical) {
                    DB::table("hha_due_medical")->updateOrInsert([
                        'agency_id'        => $request->agency_fk,
                        'caregiver_id' => $Medical['caregiverID'],
                        'caregiver_medical_id' =>  $Medical['caregiver_medical_id'],
                    ], [
                        'medical_id' =>  $Medical['medical_id'],
                        'medical_name' =>  $Medical['medical_name'],
                       
                        'due_date' =>  $Medical['due_date'],
                        'status' => $Medical['status'],
                        'office_id' =>  $Medical['office_id'],
                        'caregiver_medical_id' => $Medical['caregiver_medical_id'],
                        'del_flag' => 'N',
                        'updated_date' => date('Y-m-d H:i:s')

                    ]);
                }

                if(  count($getCaregiverMedical)==100){
                    $sequence= $sequence +count($getCaregiverMedical);
                    return response()->json(['error_msg'=>"",'data'=>array('sequence'=>$sequence,'agency_id'=>$request->agency_fk,'office_id'=>$request->office_fk,'medicals'=>$request->medicals)],200);
                }else{
                    return response()->json(['error_msg'=>"Medical successfully sync",'data'=>[]],200);
                }
                
           }
        
    }
}