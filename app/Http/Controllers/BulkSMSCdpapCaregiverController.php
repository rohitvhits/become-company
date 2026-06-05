<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Agency;
use App\Services\PatientV2Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Services\BulkSMSCdpapCaregiverService;
use App\Model\BulkSMSCdpapCaregiverDetail;
use App\Services\LogsService;
use App\Services\BulkSMSCdpapCaregiverDetailService;
use App\Helpers\Utility;
use App\Model\BulkViewAppointment;
class BulkSMSCdpapCaregiverController extends BaseController
{
    protected $patientV2Service;
    protected $bulkSMSCdpapCaregiverService;
    protected $bulkSMSCdpapCaregiverDetailService;
    protected const VALIDATION_CODE=422;
    protected const ERROR_CODE = 500;
    protected const SUCCESS_CODE = 200;
    public function __construct(PatientV2Service $patientV2Service,BulkSMSCdpapCaregiverService $bulkSMSCdpapCaregiverService, BulkSMSCdpapCaregiverDetailService $bulkSMSCdpapCaregiverDetailService)
    {
        $this->middleware('permission:bulk-sms-cdpap-caregiver', ['only' => ['index', 'ajaxList','save']]);
        $this->middleware('permission:bulk-sms-cdpap-caregiver-save', ['only' => ['save']]);
        $this->middleware('auth');
        $this->patientV2Service = $patientV2Service;
        $this->bulkSMSCdpapCaregiverService = $bulkSMSCdpapCaregiverService;
        $this->bulkSMSCdpapCaregiverDetailService = $bulkSMSCdpapCaregiverDetailService;
    }

    public function index(){
        $data['menu'] = "user";
        $data['user']= auth()->user();
      
        return view("bulkCdpapCaregiver/index", $data);
    }

    public function ajaxList(Request $request){
        $data['page'] = $request->page;
        $data['query'] = $this->bulkSMSCdpapCaregiverService->getList($request->all());
        return view('bulkCdpapCaregiver.ajax_list',$data);
    }

    public function saveOld(Request $request)
    {
        $auth = auth()->user();

        $validator = Validator::make($request->all(), [
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'data' => []
            ], self::VALIDATION_CODE);
        }

        $finalData = ['message' => $request->message];
        $saveId = $this->bulkSMSCdpapCaregiverService->save($finalData);

        if (!$saveId) {
            return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again', 'data' => []], self::ERROR_CODE);
        }

        $patientList = $this->patientV2Service->getPatientIds();
        $savePatientDetails = $this->preparePatientDetails($patientList, $saveId);

        if (!empty($savePatientDetails)) {
            $chunkSize = 500;

            if (!empty($savePatientDetails)) {
                foreach (array_chunk($savePatientDetails, $chunkSize) as $chunk) {
                    BulkSMSCdpapCaregiverDetail::insert($chunk);
                }
            }
            // BulkSMSCdpapCaregiverDetail::insert($savePatientDetails);
        }

        $this->logBulkSmsAction($auth, $saveId);

        return response()->json(['success' => true], self::SUCCESS_CODE);
    }

    public function save(Request $request)
    {
        $auth = auth()->user();

        $validator = Validator::make($request->all(), [
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'data' => []
            ], self::VALIDATION_CODE);
        }

        $getBookingAppointment = BulkViewAppointment::where('del_flag','N')->pluck('phone');
        
        if(!empty($getBookingAppointment)){
            $getDeliveredMobile = BulkSMSCdpapCaregiverDetail::whereNotIn('mobile_deliver_sms_status',['undelivered'])->where('del_flag','N')->whereNotIn('mobile',$getBookingAppointment->toArray())->pluck('mobile','id');
           
        }else{
            $getDeliveredMobile = BulkSMSCdpapCaregiverDetail::whereNotIn('mobile_deliver_sms_status',['undelivered'])->where('del_flag','N')->pluck('mobile','id');
        }
        
        $dMobile = [];
        $ids = [];
        if(!empty($getDeliveredMobile)){
            foreach($getDeliveredMobile as $key=>$mobile){
                $dMobile[] = $mobile;
                $ids[] = $key;
            }
        }
       
        $allMobile = array_merge($getBookingAppointment->toArray(),$dMobile);
     
        $getNullMobile = BulkSMSCdpapCaregiverDetail::whereNull('mobile_deliver_sms_status')->where('del_flag','N')->whereNotIn('mobile',$allMobile)->pluck('mobile','id');
        $nullMobile = [];
     
        if(!empty($getNullMobile)){
            foreach($getNullMobile as $key=>$nMobile){
                $nullMobile[] = $nMobile;
                $ids[] = $key;
            }
        }
       
        $finalData = ['message' => $request->message];
        $saveId = $this->bulkSMSCdpapCaregiverService->save($finalData);
        
        if (!$saveId) {
            return response()->json(['error_msg' => 'Sorry, something went wrong. Please try again', 'data' => []], self::ERROR_CODE);
        }
        
        // $patientList = $this->patientV2Service->getPatientIds();
       

        $savePatientDetails = $this->preparePatientDetails($ids, $saveId);

        if (!empty($savePatientDetails)) {
            $chunkSize = 500;
           
            if (!empty($savePatientDetails)) {
                foreach (array_chunk($savePatientDetails, $chunkSize) as $chunk) {
                   
                   BulkSMSCdpapCaregiverDetail::insert($chunk);
                }
            }
           
        }

        $this->logBulkSmsAction($auth, $saveId);

        return response()->json(['error_msg' => "Successfully uploaded"], self::SUCCESS_CODE);
    }

    private function preparePatientDetailsold($patientList, $saveId): array
    {
        $details = [];
        foreach ($patientList as $val) {
            if (!empty($val->mobile)) {
                $details[] = [
                    'patient_id' => $val->id,
                    'mobile' => $val->mobile,
                    'bulk_sms_cdpap_caregiver_id' => $saveId,
                    'created_date'=>date('Y-m-d H:i:s'),
                    'created_by'=>auth()->user()->id
                ];
            }
            if (!empty($val->phone)) {
                $details[] = [
                    'patient_id' => $val->id,
                    'mobile' => $val->phone,
                    'bulk_sms_cdpap_caregiver_id' => $saveId,
                    'created_date'=>date('Y-m-d H:i:s'),
                    'created_by'=>auth()->user()->id
                ];
            }
        }
        return $details;
    }

    private function preparePatientDetails($patientIds, $saveId): array
    {
       
        $details = [];
        $commonMobile = [];
        foreach ($patientIds as $val) {
           $subQuery =  BulkSMSCdpapCaregiverDetail::select('mobile','patient_id')->where('id',$val)->where('del_flag','N')->first();
          
           if(isset($subQuery->patient_id)){
                if(!in_array($subQuery->mobile,$commonMobile)){
                        $details[] = [
                            'patient_id' => $subQuery->patient_id,
                            'mobile' => $subQuery->mobile,
                            'bulk_sms_cdpap_caregiver_id' => $saveId,
                            'created_date'=>date('Y-m-d H:i:s'),
                            'created_by'=>auth()->user()->id
                        ];
                        $commonMobile[] = $subQuery->mobile;
                }
            }
            
        }
        
         return $details;
    }

    private function logBulkSmsAction($auth, $saveId): void
    {
        $insertLog = [
            'type' => 'Add Bulk Send SMS',
            'link' => url('/bulk-sms-cdpap-caregiver/save-bulk-sms'),
            'module' => 'Bulk Send SMS CDPAP Caregiver',
            'object_id' => $saveId,
            'message' => $auth->first_name . ' ' . $auth->last_name . ' has added new bulk SMS CDPAP caregiver',
            'ip' => Utility::getIP(),
        ];
        LogsService::save($insertLog);
    }
    
    public function viewDetails($id){
        $data['id'] = $id;
        return view('bulkCdpapCaregiver.view_detail_page',$data);
    }

    public function viewDetailsAjaxList(Request $request){
        $data['details'] = $this->bulkSMSCdpapCaregiverDetailService->getList($request->bulk_sms_id);
        return view('bulkCdpapCaregiver.view_detail_ajax_list',$data);
    }
}