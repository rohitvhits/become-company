<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Validator;
use App\Services\PatientService;

use App\Services\LogsService;
use App\Services\ThirdPartyPatientMasterService;
use App\Services\DocumentPatientService;

use App\User;
use DB;
use App\Master;
use App\Helpers\ThirdPartyWebHookHelper;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\RnpadSendDocumentLogService;
use App\Services\SendRNPadDocumentService;
use App\Model\SendRNPadDocument;
use App\Services\DocumentUploadService;
use App\Services\PatientWiseServicesRequests;
use Illuminate\Support\Facades\Cache;
use App\Agency;
class RNPadRecordController extends BaseController
{
    protected $thirdPartyMasterService;
    protected $documentPatientService;
    protected $rnpadSendDocumentLogService;
    protected $sendRNPadDocumentService;
    protected $documentUploadService;
    protected $patientWiseServiceRequest;
    public function __construct(
        ThirdPartyPatientMasterService $thirdPartyMasterService,
        DocumentPatientService $documentPatientService,
        RnpadSendDocumentLogService $rnpadSendDocumentLogService,
        SendRNPadDocumentService $sendRNPadDocumentService,
        DocumentUploadService $documentUploadService,
        PatientWiseServicesRequests $patientWiseServiceRequest
        )
    {
        $this->middleware('permission:rn-pad-report|rn-pad-export', ['only' => ['index', 'documentAjax', 'exportCsv']]);
        $this->middleware('permission:rn-pad-report', ['only' => ['index', 'documentAjax']]);
        $this->middleware('permission:rn-pad-export', ['only' => ['exportCsv']]);
        $this->middleware('permission:send-to-rnpad', ['only' => ['saveRNPad']]);

        $this->middleware('auth');
        $this->thirdPartyMasterService = $thirdPartyMasterService;
        $this->documentPatientService = $documentPatientService;
        $this->rnpadSendDocumentLogService = $rnpadSendDocumentLogService;
        $this->sendRNPadDocumentService = $sendRNPadDocumentService;
        $this->documentUploadService = $documentUploadService;
        $this->patientWiseServiceRequest = $patientWiseServiceRequest;
    }

    /**
     * Display RN Pad Document List
     */
    public function index(Request $request)
    {
        $services = Cache::get('rnpad_services_master', function () {
			return Master::where('del_flag', 'N')->where('master_type_fk',11)->get();
		}, 10 *60);

        $agency_list = Cache::get('rnpad_agency_list', function () {
			return Agency::getAgencyList();
		}, 10 *60);

        $statuses = Cache::get('rnpad_services_status', function () {
            return  Utility::getUniqueStatusDataNew();
        }, 10 *60);
        
        return view('rnpad.rnpad_document_list', compact('services','statuses','agency_list'));
    }

    /**
     * Get RN Pad Documents via AJAX
     */
    public function documentAjax(Request $request)
    {
        $documentIds = [];
        if(!empty($request->service)){
    
            $getDocumentServices = $this->documentUploadService->getDocumentIdsByServices($request->service);
            $documentIds = $getDocumentServices->toArray();
        }
       
        $query = $this->sendRNPadDocumentService->getAllData($request->all(),$documentIds);
        if(!empty($query[0])){
            foreach($query as $newVal){
                $getServices = $this->documentUploadService->getDocumentListByDocumentId($newVal->document_id);
              
                $finalData = "";
                if(!empty($getServices[0])){
                    $final = [];
                    foreach($getServices as $va){
                       
                        $final[] = $va->masterDetails->name;
                    }

                   $finalData = implode(',',$final);
                }
                $newVal->service = $finalData;
            }
        }
       
        return view('rnpad.rnpad_document_ajax_list', compact('query'));
    }

    /**
     * Export RN Pad Documents to CSV
     */
    public function exportCsv(Request $request)
    {
        $documentIds = [];
        if(!empty($request->service)){
    
            $serviceDetails = Master::getRNPadServicesByID($request->service);
            
            $getDocumentServices = $this->documentUploadService->getDocumentIdsByServices($serviceDetails);
           $documentIds = $getDocumentServices->toArray();
        }
        $query = $this->sendRNPadDocumentService->getAllData($request->all(),$documentIds,"export");
        if(!empty($query[0])){
            foreach($query as $newVal){
                $getServices = $this->documentUploadService->getDocumentListByDocumentId($newVal->document_id);
              
                $finalData = "";
                if(!empty($getServices[0])){
                    $final = [];
                    foreach($getServices as $va){
                       
                        $final[] = $va->masterDetails->name;
                    }

                   $finalData = implode(',',$final);
                }
                $newVal->service = $finalData;
            }
        }

        $filename = 'rnpad_documents_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($query) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'No',
                'Agency Name',
                'Patient Name',
                'Document Name',
                'Service',
                'Status',
                'Document Review Status',
               'Internal Use',
                'Created Date',
                'Created By',
                'Send Status'
            ]);

            // Add data rows
            $i = 1;
            foreach ($query as $row) {
                fputcsv($file, [
                    $i++,
                    ($row->agency_name ? $row->agency_name : 'N/A'),
                    ($row->full_name ? $row->full_name : 'N/A'),
                    $row->document_name ?? 'N/A',
                    ($row->service ? $row->service: 'N/A'),
                    $row->patientServiceStatus ?? 'N/A',
                    $row->document_review_status ?? 'N/A',
                    ($row->internal_use == 1 ? 'Yes' : 'No'),
                    ($row->created_date ? date('m/d/Y h:i A', strtotime($row->created_date)) : 'N/A'),
                    ($row->createdBy ? $row->createdBy->first_name . ' ' . $row->createdBy->last_name : 'N/A'),
                    ($row->send_third_party_document_date ? "Yes" : 'No')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function rnpadServicesList(Request $request)
    {
        $response = $this->thirdPartyMasterService->getTPUrlByAgencyAndPortal($request->id, $request->agency_id);
        $finalResponse = [];
     
        if (!empty($response[0])) {
            foreach ($response as $tpd) {
                $explodeService = explode(',', $tpd->service_id);
                $serviceArray = [];
                
                foreach ($explodeService as $sd) {
                    $srv = Master::getDetailsById($sd);
                    if (isset($srv->name)) {
                        $serviceArray[] = $srv->name;
                    }
                }

                $temp = [];
                $temp['id'] = $tpd->id;
                $temp['services'] = implode(',', $serviceArray);
                $temp['created_date'] = Utility::convertMDYTime($tpd->created_date);
                $temp['status'] = $tpd->patientServiceRequestStatus;
                $finalResponse[] = $temp;
            }
        }
     
        return response()->json(['success' => 'success', 'data' => $finalResponse]);
    }

    public function saveRNPad(Request $request)
    {
        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'third_party_id' => 'required',
            'appointment_id' => 'required',
            'document_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error_msg' => $validator->errors()->all()[0], 'status' => 0, 'data' => []], 422);
        } else {
        
            $getDocumentDetails = $this->documentPatientService->getDocumentByDocIdAndPatientId($request->document_id, $request->appointment_id);
            $getThirdPartyDetails = $this->thirdPartyMasterService->getDetailsByIdAndPatientId($request->third_party_id, $request->appointment_id);

            if (env('FILE_UPLOAD_PERMISSION') != "development") {
                $expiry = Carbon::now()->addMinutes(10);
                $path = 'patientdocument/' . $getDocumentDetails->attachment;
                $inputPath = Storage::disk('s3')->temporaryUrl($path, $expiry);
            } else {
                $inputPath =public_path('/patientdocument/') .  $getDocumentDetails->attachment;
              
            }


            $response = ThirdPartyWebHookHelper::sendRnPadWebHook(
                $request->agency_id,
                $getThirdPartyDetails->third_party_callback_url,
                $inputPath,
                $getDocumentDetails->attachment
            );
        
            if ($response['status'] ==200) {
                $this->rnpadSendDocumentLogService->save([
                    'patient_id' => $request->appointment_id,
                    'document_id' => $request->document_id,
                    'third_party_id' => $request->third_party_id,
                    'send_response' => serialize($request->except('_token')),
                    'return_response' => serialize($response),
                    'agency_id' => $request->agency_id,
                    'attachment' => $getDocumentDetails->attachment
                ]);

                $this->documentPatientService->update(array('send_rnpad_document_date'=>date('Y-m-d H:i:s'),'send_rnpad_document_by'=>$auth->id),array('id'=>$request->document_id,'patient_id'=>$request->appointment_id));
                $this->sendRNPadDocumentService->update(array('send_third_party_document_date'=>date('Y-m-d H:i:s'),'send_third_party_document_by'=>$auth->id),array('document_id'=>$request->document_id,'patient_id'=>$request->appointment_id));
                $ipaddress = Utility::getIP();
                $insertLog = [
                    'type' => 'Send RnPad Document',
                    'link' => url('/rnpad/send-rnpad-document'),
                    'module' => 'Patient Appointment',
                    'object_id' => $request->appointment_id,
                    'message' => $auth->first_name . ' ' . $auth->last_name . ' has send rnpad document',
                    'old_response' => serialize($getDocumentDetails->toArray()),
                    'new_response' => serialize($request->except('_token')),
                    'ip' => $ipaddress,
                ];
                LogsService::save($insertLog);
            }
            return response()->json(['error_msg' => $response['error_msg']], $response['status']);
        }
    }
}