<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AgencyService;
use App\Services\ThirdPartyPatientMasterService;
use Illuminate\Support\Facades\Validator;
use App\Services\PendingVisitingMedicalService;
use Illuminate\Pagination\LengthAwarePaginator;

class PendingMedicalsController extends Controller
{
    protected const MODULE_NAME = "Pending Medicals";
    protected const AUTH_KEY = 'AuthKey';
    protected const AUTH_PWD = 'AuthPWD';
    protected $agencyService;
    protected $thirdPartyMasterService;
    protected $pendingVisitingMedicalService;
    /**
     * Constructor
     */
    public function __construct(AgencyService $agencyService,ThirdPartyPatientMasterService $thirdPartyMasterService,PendingVisitingMedicalService $pendingVisitingMedicalService)
    {
        $this->middleware('auth');
        $this->middleware('permission:pending-visiting-medical', ['only' => ['index', 'getData','exportCsv']]);
        $this->middleware('permission:pending-visiting-medical-export', ['only' => ['exportCsv']]);
        $this->agencyService = $agencyService;
        $this->thirdPartyMasterService = $thirdPartyMasterService;
        $this->pendingVisitingMedicalService = $pendingVisitingMedicalService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agencies = $this->agencyService->getAllVisitingAidAgencyList();
        return view('pending_medicals.index', compact('agencies'));
    }

    /**
     * Get pending medicals data via API
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        $agency_id = $request->agency_id;

        if (!$agency_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please select an agency',
                'data' => []
            ]);
        }

        $response = $this->thirdPartyMasterService->getPendingMedicalList($agency_id,$request->medical_due_date);
        if (isset($response['data']) && is_array($response['data']) && count($response['data']) > 0) {
            $data = $response['data']['pendingmedicals'] ?? [];
            // Return all data as JSON for JavaScript pagination
            return response()->json([
                'status' => true,
                'data' => array_values($data),
                'total' => count($data)
            ]);
        } else {
            return response()->json([
                'status' => true,
                'data' => [],
                'total' => 0
            ]);
        }
    }

    public function getDataOld(Request $request)
    {
        $agency_id = $request->agency_id;

        if (!$agency_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please select an agency',
                'data' => []
            ]);
        }

        $response = $this->pendingVisitingMedicalService->getPendingMedicalList($agency_id,$request->medical_due_date);
        if (isset($response) && count($response) > 0) {
            $data = $response;
            // Return all data as JSON for JavaScript pagination
            return response()->json([
                'status' => true,
                'data' => $data,
                'total' => $data->total()
            ]);
        } else {
            return response()->json([
                'status' => true,
                'data' => [],
                'total' => 0
            ]);
        }
    }

    public function syncMedicalWithDetails(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ],
        [
            'id.required' => 'Agency ID is required.'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()[0]], 422);
        }

        $saveStatus = $this->sync(0,$request->id);
       
        if($saveStatus){
            return response()->json(['error_msg' => "Medical successfully sync", 'status' => 1, 'data' => array()], 200);
        }
        return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again.', 'status' => 0, 'data' => array()], 500);
    }

    public function sync($offset,$agencyId){
      
        $limit = 50;
        $response = $this->thirdPartyMasterService->getPendingMedicalList($agencyId);
        $data = array_slice($response['data']['pendingmedicals'], $offset, $limit);
        
        if (empty($data[0])) {
            
            return 1;
        }else{
            $insertData = [];
            $missingDetails = [];
            if(!empty($data[0])){
                foreach($data as $val){
                   
                        $insertData = [
                            'first_name'=>$val['FirstName']??"",
                            
                            'last_name'=>$val['LastName']??"",
                            'dob'=>date('Y-m-d',strtotime($val['DOB']))??"",
                            'gender'=>$val['Gender'],
                            'mobile'=>str_replace(['(', ')', '-'], '', $val['Cell']),
                            'phone'=>str_replace(['(', ')', '-'], '', $val['Cell']),
                           
                            'medical_id'=>$val['MedicalID'],
                            'medical_name'=>$val['MedicalName'],
                            'medical_ref_id'=>$val['MedicalRefID'],
                            'medical_status'=>$val['Status'],
                            'medical_due_date'=>date('Y-m-d',strtotime($val['MedicalDue'])),
                        ];
                    
                        $check = $this->pendingVisitingMedicalService->checkExistingRecordOrNot($agencyId,$val['EmployeeCode']);
                        if($check){
                            $this->pendingVisitingMedicalService->update($insertData,['agency_id'=>$agencyId,'employee_code'=>$val['EmployeeCode']]);
                        }else{
                            $insertData['agency_id'] = $agencyId;
                            $insertData['employee_code'] = $val['EmployeeCode'];
                            $this->pendingVisitingMedicalService->save($insertData);
                        }
                }
            }
        
            $offset += $limit;
            return $this->sync($offset,$agencyId);
        }
    
    }

    public function exportCsv(Request $request){
        $response = $this->thirdPartyMasterService->getPendingMedicalList($request->agency_id,$request->medical_due_date,'export');
        
        $response = $response['data']['pendingmedicals'];
        
        $fileName = 'pending_medical_list_'.date('Ymd_His').'.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Employee Code',
            'Employee Name',
            'DOB',
            'Gender',
            'Mobile',
            'Medical ID',
            'Medical Name',
            'Medical Due Date',
            'Medical Status',
         
        ];

        $callback = function() use ($response, $columns) {

            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
    
            foreach ($response as $row) {
                $fName = $row['FirstName'] ?? '';
                $lName = $row['LastName'] ?? '';
                fputcsv($file, [
                    $row['EmployeeCode'] ?? '',
                    $fName.' '.$lName,
                    $row['DOB'] ?? '',
                    $row['Gender'] ?? '',
                    $row['Cell'] ?? '',
                    $row['MedicalID'] ?? '',
                    $row['MedicalName'] ?? '',
                    $row['MedicalDue'] ?? '',
                    $row['Status'] ?? '',
                  
                ]);
            }
    
            fclose($file);
        };
    
        return response()->stream($callback, 200, $headers);
    }
}
