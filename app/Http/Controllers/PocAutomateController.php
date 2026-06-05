<?php
namespace App\Http\Controllers;
use App\Helpers\HHAPatientHelper;
use App\Helpers\TaskHealthApiHelper;
use App\Services\HHAPatientService;
use App\Services\TaskHealthMasterService;
use Illuminate\Http\Request;
use App\Services\TaskHealthFlagsService;
use App\Services\PatientService;
use App\Services\MapTaskHealthService;
use App\Model\HHAAuditLog;
use App\Services\HHAPOCTaskService;
use App\Services\PocMatchedTaskService;
use App\Services\VisitTaskHealthService;
use Illuminate\Support\Facades\Validator;
use App\Services\AgencyPocDocumentTypeService;
use App\Helpers\Utility;
use Illuminate\Support\Facades\File;
use URL;
use App\Services\AgencyService;
use App\Services\AgencyTaskHealthSettingService;
class PocAutomateController extends Controller
{
    protected $hhaPatientService;
    protected $taskHealthMasterService = "";
    protected $hhaPOCTaskService;
    protected $pocMatchedTaskService;
    protected $visitTaskHealthService;
    protected $agencyPOCDocumentTypeService;
    protected $agencyService;
    protected $agencyTaskHealthSettingService;

    public function __construct(
        HHAPatientService $hhaPatientService,
        TaskHealthMasterService $taskHealthMasterService,
        HHAPOCTaskService $hhaPOCTaskService,
        PocMatchedTaskService $pocMatchedTaskService,
        VisitTaskHealthService $visitTaskHealthService,
        AgencyPocDocumentTypeService $agencyPOCDocumentTypeService,
        AgencyService $agencyService,
        AgencyTaskHealthSettingService $agencyTaskHealthSettingService
    ) {
        $this->hhaPatientService = $hhaPatientService;
        $this->taskHealthMasterService = $taskHealthMasterService;
        $this->hhaPOCTaskService = $hhaPOCTaskService;
        $this->pocMatchedTaskService = $pocMatchedTaskService;
        $this->visitTaskHealthService = $visitTaskHealthService;
        $this->agencyPOCDocumentTypeService = $agencyPOCDocumentTypeService;
        $this->agencyService = $agencyService;
        $this->agencyTaskHealthSettingService = $agencyTaskHealthSettingService;
    }

    public function sendHHAPoc(Request $request)
    {

    $validator = Validator::make($request->all(), [
            'visit_task_health_id'   => 'required',
            'portal_id' => 'required',
        ], [
            'visit_task_health_id.required'   => 'Task id is required',
            'portal_id.required' => 'Portal id is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error_msg' => $validator->errors()[0]
            ], 422);
        }
        $patientService = new PatientService();
        $getPatientDetails = $patientService->getPatientDetailsByIdWhitoutAgency($request->portal_id);
        $getVisitDetails = TaskHealthApiHelper::getVisitDetail($request->visit_task_health_id, 'cron');
        $taskHealthVisitPOCId = [];

        if (count($getVisitDetails['data']['planOfCareItems']) > 0) {
            foreach ($getVisitDetails['data']['planOfCareItems'] as $val) {
                $taskHealthVisitPOCId[] = $val['taskHealthId'];
            }
        }

        $agencyId = $this->detectLocalAgency($getVisitDetails['data']['task']['agencyId']);
        
        $mapTaskDetails = new MapTaskHealthService();
        $getList = $mapTaskDetails->getMapTaskListByWithCodeId($taskHealthVisitPOCId,$agencyId['id']);
        if (count($getList) ==0) {
            return response()->json([
                'error_msg' => 'POC Task does not link for hha.'
            ], 500);
        }

        if (count($getList) >= 5) {

            if (!empty($getVisitDetails['data']['planOfCareItems'][0])) {
                $getPOCDocument = $this->getPOCDocument($getVisitDetails,$request);

                $contex = [
                    'visitDetails'=>$getVisitDetails,
                    'patient'=>$getPatientDetails,
                    'pocTask'=>$getList,
                    'requestAll'=>$request,
                    'documentPOCDetails'=>$getPOCDocument,
                    'agencyId'=>$agencyId['id']
                ];
                $reponse = $this->sendHHAPOCDetails($contex);
                if (isset($reponse['status']) && $reponse['status'] == 1) {
                    return response()->json([
                        'error_msg' => 'POC has been successfully created in HHA.',
                        'data' => $reponse['data']
                    ], 200);
                }
                return response()->json(['error_msg' => $reponse['message']], 500);
            }

            return response()->json(['error_msg' => "NO plan of care available for this visit"], 500);
        }
        return response()->json([
            'error_msg' => 'POC was not created because at least 5 tasks are required.'
        ], 500);
    }

    public function syncAutoPopulateVisit(Request $request)
    {
        $taskId = $request->task_id;
        $visitTaskHealth = TaskHealthApiHelper::getVisitDetail($taskId);

        $agencyId = $this->detectLocalAgency($visitTaskHealth['data']['task']['agencyId']);

        if (isset($agencyId['id']) && $agencyId['id'] != "") {
            $pocTask = $this->getAllPOCTaskAgencyWise($agencyId['id']);

            $data = $visitTaskHealth['data'] ?? [];

            if (!empty($data['planOfCareItems'])) {
                foreach ($data['planOfCareItems'] as $item) {
                    $this->visitTaskHealthService->save($item);

                    if (isset($pocTask[$item['code']])) {
                        $this->pocMatchedTaskService->save($item, $pocTask);
                    }
                }
            }
        }
    }

    private function detectLocalAgency(int $agency_id): ?array
    {
        if (isset($agency_id) && !empty($agency_id)) {
            $agencyApi = TaskHealthApiHelper::getAgencies();

            if ($agencyApi['status'] && !empty($agencyApi['data'])) {
                $result = collect($agencyApi['data'])
                    ->firstWhere('taskHealthAgencyId', $agency_id);

                return ['id' => $result['nyBestId']];
            }
        }

        return null;
    }

    private function getAllPOCTaskAgencyWise($agencyId)
    {
        $getAllPOCTask = $this->hhaPOCTaskService
            ->getAllPOCTaskWithAgencyId($agencyId);

        $pocTask = [];

        if (!empty($getAllPOCTask[0])) {
            foreach ($getAllPOCTask as $val) {
                $tempPoc = [];
                $tempPoc['hha_task_id'] = $val->task_id;
                $tempPoc['hha_task_code'] = $val->task_code;
                $tempPoc['hha_task_name'] = $val->task_name;

                $pocTask[$val->task_code] = $tempPoc;
            }
        }

        return $pocTask;
    }

    public function showPOCFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visit_task_health_id'   => 'required',
            'portal_id' => 'required',
        ], [
            'visit_task_health_id.required'   => 'Task id is required',
            'portal_id.required' => 'Portal id is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error_msg' => $validator->errors()[0]
            ], 422);
        }

        $visitTaskHealth = TaskHealthApiHelper::getVisitDetail($request->visit_task_health_id, 'cron');

        $getTaskDocument = $visitTaskHealth['data']['task']['documents'];
        $docUrl = "";
        $title = "";
        if(count($getTaskDocument) >0){
            foreach($getTaskDocument as $doc){
                if(in_array($doc['type']['id'],Utility::getPOCDocumentTypeId())){
                    $docUrl = $doc['url'];
                    $title = $doc['type']['title'];
                }
            }
        }

        $details = [
            'url'=>$docUrl,
            'title'=>$title,
            'visitTaskHealth'=>$visitTaskHealth,
            'requestAll'=>$request
        ];
        $response = TaskHealthApiHelper::getCommonDocumentCreate($details);

        // continue logic here
        return response()->json([
            'status' => true,
            'error_msg' => 'success',
            'data'=>$response
        ]);
    }

    private function syncAgencyDocument($agencyId){
        $final = [];
        $getDocumentType = HHAPatientHelper::GetPatientDocumentType($agencyId);

        if(!empty($getDocumentType[0])){
            foreach($getDocumentType as $doc){
                $this->agencyPOCDocumentTypeService->saveOrUpdate([
                    'agency_id'     => $agencyId,
                    'document_id'   => $doc['id'],
                    'document_name' => $doc['name'],
                    'status'        => $doc['Status'],
                ]);

                $final[] = [
                    'document_id'   => $doc['id'],
                    'document_name' => $doc['name'],
                ];
            }
        }

        return $final;
    }

    private function sendHHAPatientDocument($context){

        $path = parse_url($context['documentPOCDetails']['url'], PHP_URL_PATH);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return HHAPatientHelper::getSendHHADocument($context['agencyId'],$context['documentPOCDetails']['title'],$extension,$context['documentPOCDetails']['documentType'],$context['patient']->link_hha_patient,file_get_contents($context['documentPOCDetails']['task_url']));
    }

    private function sendHHAPOCDetails($context){
        $getVisitDetails = $context['visitDetails'];
        $getList = $context['pocTask'];
        $getPatientDetails = $context['patient'];
        $request = $context['requestAll'];
        $documentPOCDetails = $context['documentPOCDetails'];

        $taskHealthPOCFlag = new TaskHealthFlagsService();

        $finalResponse = [
            'start_date' => $getVisitDetails['data']['task']['certificationPeriod']['startDate'],
            'stop_date' => $getVisitDetails['data']['task']['certificationPeriod']['endDate'],
            'shift' => 1
        ];

        $taskIds = [];
        $mintime = [];
        $maxtime = [];
        $asNeeded = [];

        foreach ($getList as $val) {
            $taskIds[] = $val->hha_task_id;
            $mintime[] = 1;
            $maxtime[] = 7;
            $asNeeded[] = "true";
        }

        $finalResponse['task_id'] = $taskIds;
        $finalResponse['mintime'] = $mintime;
        $finalResponse['maxtime'] = $maxtime;
        $finalResponse['as_requested'] = $asNeeded;
        $agencyNotes = $this->agencyTaskHealthSettingService->getTaskHealthSettingById($context['agencyId']); /* 'Visit needed'; */
        $notes = isset($agencyNotes->poc_group_notes) && !empty($agencyNotes->poc_group_notes) ? $agencyNotes->poc_group_notes :'';
        $reponse = HHAPatientHelper::createPatientPOCDetails(
            $getPatientDetails->link_hha_patient,
            $finalResponse,
            $notes
        );

        if (isset($reponse['status']) && $reponse['status'] == 1) {
            $path = parse_url($documentPOCDetails['task_url'], PHP_URL_PATH);
            $filename = basename($path);

            $finalResponse['file_name'] = $filename;
            $finalResponse['hha_document_type'] = $documentPOCDetails['documentType'];
            $saves = new HHAAuditLog([
                'type' => 'POC',
                'patient_id' => $request->portal_id,
                'ref_id' => $request->visit_task_health_id,
                'ref_obj' =>'Task Health',
                'status' => 'Sent',
                'send_response' => serialize($finalResponse),
                'hha_patient_id' => $getPatientDetails->link_hha_patient,
                'return_response' => serialize($reponse['data']),
                'created_by'=>auth()->user()->id
            ]);
            $saves->save();

            $taskHealthPOCFlag->saveFlagsOnlyPOCCron(
                $getVisitDetails['data']['patient']['id'],
                $request->visit_task_health_id,
                $request->portal_id,
                1
            );

            $this->sendHHAPatientDocument($context);
            $path = parse_url($request->url, PHP_URL_PATH);
            $filename = basename($path);
            $path = public_path('allupload/task_health/' . $request->visit_task_health_id . '/' . $filename);
            if (File::exists($path)) {
                File::deleteDirectory(public_path('allupload/task_health/' . $request->visit_task_health_id));
            }

        }
        return $reponse;
    }

    private function getPOCDocument($visitTaskHealth,$request){
        $getTaskDocument = $visitTaskHealth['data']['task']['documents'];
        $docUrl = "";
        $title = "";
        if(count($getTaskDocument) >0){
            foreach($getTaskDocument as $doc){
                if(in_array($doc['type']['id'],Utility::getPOCDocumentTypeId())){
                    $docUrl = $doc['url'];
                    $title = $doc['type']['title'];
                }
            }
        }

        $details = [
            'url'=>$docUrl,
            'title'=>$title,
            'visitTaskHealth'=>$visitTaskHealth,
            'requestAll'=>$request
        ];
        return TaskHealthApiHelper::getCommonDocumentCreate($details);
    }
}
