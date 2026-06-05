<?php

namespace App\Helpers;
use App\Model\TaskHealthLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use App\Services\AgencyService;
use App\Services\LogsService;
use App\Services\DocumentPatientService;
use App\Model\Patient;
use App\Model\HHAPatient;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
class TaskHealthApiHelper
{
    private static function baseUrl(): string
    {
        return rtrim(env('TASK_HEALTH_BASE_URL', 'https://api.taskshealth.com'), '/');
    }

    private static function apiKey(): string
    {
        return env('TASK_HEALTH_TOKEN', '');
    }

    private static function request(string $method, string $endpoint, array $params = [], array $body = [],string $type=""): array
    {
        $url = self::baseUrl() . $endpoint;
        if ($method === 'GET' && !empty($params)) {
            // Use PHP_QUERY_RFC3986 + array brackets so arrays become
            // agencyIds[]=1&agencyIds[]=2 instead of agencyIds[0]=1&agencyIds[1]=2
            $url .= '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        }

        $curl = curl_init();
        $opts = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . self::apiKey(),
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ];
        if (!empty($body)) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($body);
        }
        curl_setopt_array($curl, $opts);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error    = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['error' => $error, 'status' => 0, 'http_code' => 0];
        }

        $decoded = json_decode($response, true);

        if($type !="cron"){
            self::storeLog($url,$method,$body);
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            $msg = $decoded['message'] ?? $decoded['error'] ?? 'HTTP ' . $httpCode . ' error';
            return ['error' => $msg, 'status' => 0, 'http_code' => $httpCode, 'data' => $decoded];
        }
        return ['data' => $decoded, 'status' => 1, 'http_code' => $httpCode];
    }

    public static function getAgencies(): array
    {
        return self::request('GET', '/ny-best/agencies');
    }

    public static function getVisits(array $params): array
    {
        return self::request('GET', '/ny-best/visits', $params);
    }

    public static function getVisitDetail(int $taskId,string $type=""): array
    {
        return self::request('GET', '/ny-best/visits/' . $taskId,[],[],$type);
    }

    public static function createVisit(array $payload): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => self::baseUrl() . '/ny-best/visits',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . self::apiKey(),
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error    = curl_error($curl);
        curl_close($curl);

        self::storeLog(self::baseUrl() . '/ny-best/visits','POST',$payload);
        if ($error) {
            return ['error' => $error, 'status' => 0, 'http_code' => 0];
        }

        $decoded = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300) {
            $msg = $decoded['message'] ?? $decoded['error'] ?? 'HTTP ' . $httpCode . ' error';
            return ['error' => $msg, 'status' => 0, 'http_code' => $httpCode, 'data' => $decoded];
        }

        return ['data' => $decoded, 'status' => 1, 'http_code' => $httpCode];
    }

    public static function cancelVisit(int $taskId): array
    {
        return self::request('DELETE', '/ny-best/visits/' . $taskId);
    }

    public static function approveDocument(int $taskId, int $scheduledDocId): array
    {
        return self::request('POST', '/ny-best/visits/' . $taskId . '/documents/' . $scheduledDocId . '/approve');
    }

    public static function openDocumentForChanges(int $taskId, int $scheduledDocId, array $rejections): array
    {
        return self::request('POST', '/ny-best/visits/' . $taskId . '/documents/' . $scheduledDocId . '/open_for_changes', [], ['rejections' => $rejections]);
    }

    public static function editVisit(int $taskId, string $instruction): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => self::baseUrl() . '/ny-best/visits/' . $taskId . '/edit',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode(['instruction' => $instruction]),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . self::apiKey(),
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error    = curl_error($curl);
        curl_close($curl);

        self::storeLog(self::baseUrl() . '/ny-best/visits/' . $taskId . '/edit','POST',['instruction' => $instruction]);
        if ($error) {
            return ['error' => $error, 'status' => 0, 'http_code' => 0];
        }

        $decoded = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300) {
            $msg = $decoded['message'] ?? $decoded['error'] ?? 'HTTP ' . $httpCode . ' error';
            return ['error' => $msg, 'status' => 0, 'http_code' => $httpCode, 'data' => $decoded];
        }
        return ['data' => $decoded, 'status' => 1, 'http_code' => $httpCode];
    }

    public static function storeLog($url,$method,$body){
        $ipaddress = $_SERVER['REMOTE_ADDR']??'';
        $useragent = $_SERVER['HTTP_USER_AGENT']??'';
		$remotehost = @getHostByAddr($ipaddress);
		$user_info = json_encode(array("Ip" => $ipaddress, "UserAgent" => $useragent, "RemoteHost" => $remotehost));
        $user_track_data = array("url" => $url,'type'=>$method, 'api_key' => self::apiKey(), 'ip' => $ipaddress,'response'=>$user_info,'created_date'=>date('Y-m-d H:i:s'),'data'=>json_encode($body));
        $saveLog = new TaskHealthLog($user_track_data);
		$saveLog->save();
    }

    public static function detectLocalAgency(int $agency_id): ?array
    {
        if (empty($agency_id)) {
            return null;
        }

        // Cache agencies list for 60 min — it changes very rarely
        $agencies = Cache::remember('th_agencies_list', 3600, function () {
            $api = TaskHealthApiHelper::getAgencies();
            return ($api['status'] && !empty($api['data'])) ? $api['data'] : [];
        });

        if (empty($agencies)) {
            return null;
        }

        $result = collect($agencies)->firstWhere('taskHealthAgencyId', $agency_id);
        if (!$result) {
            return null;
        }
        return ['id' => $result['nyBestId']];
    }

    /**
     * Cached variant of getVisitDetail (5-min TTL).
     * Used by checkExistingMasterRecord so repeated calls for the same
     * task ID within the same session don't hit the external API again.
     */
    public static function getVisitDetailCached(int $taskId): array
    {
        return Cache::remember('th_visit_detail_' . $taskId, 300, function () use ($taskId) {
            return TaskHealthApiHelper::getVisitDetail($taskId);
        });
    }

    public static function linkHHAPatientData($sendResponseData,$patientId)
    {
        $sendData['hha_patient_first_name'] = $sendResponseData['first_name'];
        $sendData['hha_patient_last_name'] = $sendResponseData['last_name'];
        $sendData['hha_patient_phone_no'] = $sendResponseData['phone'];
        $sendData['status'] = 'Active';
        $agencyId = $sendResponseData['agency_id'];
        $getPatientData = Patient::where('deleted_flag', 'N')->where('id', $patientId)->first();
        if(empty($getPatientData->link_hha_patient))
        {
            $hhaPatientData = HHAPatientHelper::searchPatientForHHAWithAllCondition($agencyId, $sendData);
            if(isset($hhaPatientData[0]) && !empty($hhaPatientData[0])){
                $getHHAPatientDetails = HHAPatient::where('patient_id',$hhaPatientData[0]['patient_id'])->where('agency_fk',$agencyId)->where('hha_delete_flag','N')->first();
                if (!isset($getHHAPatientDetails->id)) {
                    HHAPatientHelper::saveData($hhaPatientData[0]['patient_id'], $agencyId);
                }
                $data = array(
                    'link_hha_patient' => $hhaPatientData[0]['patient_id'],
                    'is_already_linked' => 0
                );
                $data['updated_date'] = date('Y-m-d H:i:s');
                $data['updated_by'] = env('TASK_API_USER_ID');
                $patient = Patient::where('id',$patientId)->first();
                $patient->fill($data);
                $patient->save();
                return $data;
            }
        }else if(!empty($getPatientData->link_hha_patient)){
            $data = array(
                'link_hha_patient' => $getPatientData->link_hha_patient,
                'is_already_linked' => 1
            );
            return $data;
        }
    }

    public static function getCommonDocumentCreate($detail){

        $visitTaskHealth = $detail['visitTaskHealth'];
        $request = $detail['requestAll'];
        $agencyService = new AgencyService();
        $docUrl = $detail['url'];
        $title = $detail['title'];
        $fileName = uniqid().'-'.$detail['requestAll']['visit_task_health_id'].'.pdf';
        $file = file_get_contents($docUrl);
        $filPath = public_path('/allupload').'/task_health/'.$request['visit_task_health_id'];
        if (!File::exists($filPath)) {
            File::makeDirectory($filPath, 0775, true); // recursive = true
        }
        file_put_contents($filPath.'/'.$fileName, $file);
        if(env('HHA_DEVELOPEMENT_CRED') == 'development'){
            $visitTaskHealth['data']['task']['agencyId'] = env('HHA_DEVELOPEMENT_AGENCY_ID');
        }
        $agencyId = TaskHealthApiHelper::detectLocalAgency($visitTaskHealth['data']['task']['agencyId']);

        $agencyDetails = $agencyService->getDetailsById($agencyId['id']);
        $url = URL::to('/').'/allupload/task_health/'.$request['visit_task_health_id'].'/'.$fileName;
        $assessmentDueDate = $visitTaskHealth['data']['lastScheduleDetails']['assessmentDueDate'] ?? null;
        return ['url'=>$url,'task_url'=>$docUrl,'documentType'=>$agencyDetails->poc_document_type_id,'supervisionDocumentTypeId' =>$agencyDetails->supervision_document_type_id,'title'=>$title,'assessmentDueDate'=>$assessmentDueDate];
    }

    public static function getTaskHealthDocData(array $visitTaskHealth, $taskId,$patient_id)
    {
        $docUrl = '';
        $title  = '';
        $documents = $visitTaskHealth['data']['task']['documents'] ?? [];
        foreach ($documents as $doc){
            if (in_array($doc['type']['id'], [80752])) {
                $docUrl = $doc['url'];
                $title  = $doc['type']['title'];
            }
        }

        if (empty($docUrl)) {
            return null;
        }
        self::commonDocCreate($patient_id,$docUrl,$title);
    }

    public static function commonDocCreate($patientId, $url, $title)
    {
        try {
            $fileContent = file_get_contents($url);
            if ($fileContent === false) {
                return ['status' => 0, 'message' => 'Failed to download document: ' . $title];
            }

            $urlPath      = parse_url($url, PHP_URL_PATH) ?? '';
            $ext          = pathinfo($urlPath, PATHINFO_EXTENSION) ?: 'pdf';
            $originalName = basename($urlPath) ?: (uniqid() . '.' . $ext);
            $name         = uniqid() . time() . '_' . $originalName;
            $fileSize     = strlen($fileContent);
            $fileType     = 'application/pdf';

            if (env('FILE_UPLOAD_PERMISSION') == 'development') {
                Storage::disk('public')->put('patientdocument/' . $name, $fileContent);
                Storage::disk('public')->put('patientWriteDocument/' . $name, $fileContent);
            } else {
                Storage::disk('s3')->put('patientdocument/' . $name, $fileContent);
                Storage::disk('s3')->put('patientWriteDocument/' . $name, $fileContent);
            }

            $data = [
                'document_name'          => $title,
                'attachment'             => $name,
                'patient_id'             => $patientId,
                'is_checked'             => 0,
                'internal_use'           => 1,
                'assign_document_review' => null,
                'created_date'           => date('Y-m-d H:i:s'),
                'created_by'             => 482,
                'document_review_status' => 'Approved',
                'extension'              => $ext,
                'size_in_bytes'          => $fileSize,
                'pdf_type'               => $fileType,
            ];

            $documentPatientService = new DocumentPatientService();
            $documentPatientService->save($data);

            LogsService::save([
                'type'         => 'Add Document From Task Health Appointment',
                'link'         => url('/api/lead/save-task-health-appointment'),
                'module'       => 'Patient Appointment',
                'object_id'    => $patientId,
                'message'      => 'Task Health Appointment has added document from Appointment',
                'new_response' => serialize($data),
            ]);

            return ['status' => 1, 'message' => 'Document saved: ' . $title, 'file' => $name];

        } catch (\Exception $e) {
            return ['status' => 0, 'message' => 'commonDocCreate exception: ' . $e->getMessage()];
        }
    }

    public static function sendToHHAExtraDocument($docUrl,$title,$visitTaskHealth,$request,$agency_id,$link_patient,$doc_type_id=""){
        $details = [
            'url'=>$docUrl,
            'title'=>$title,
            'visitTaskHealth'=>$visitTaskHealth,
            'requestAll'=>$request
        ];
        $getExtraDocument = TaskHealthApiHelper::getCommonDocumentCreate($details);
        $path = parse_url($getExtraDocument['url'], PHP_URL_PATH);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $docData = HHAPatientHelper::getSendHHADocument($agency_id,$getExtraDocument['title'],$extension,$doc_type_id,$link_patient,file_get_contents($getExtraDocument['task_url']));
        $filePath = public_path('allupload/task_health/' . $request['visit_task_health_id']);
        try{
        if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
        }catch (\Exception $e) {}
        return $docData;
    }

    public static function getHhaDocType($docId, int $agencyId = 0, string $title = ''): string
    {
        if ($agencyId > 0) {
            $agencyService = new AgencyService();
            $agency = $agencyService->getDetailsById($agencyId);
            if ($agency) {
                $map = [
                    '80752' => (string) ($agency->patient_assessment_document_type_id ?? ''),
                    '81049' => (string) ($agency->emergency_kardex_document_type_id ?? ''),
                    '81082' => (string) ($agency->cms_485_document_type_id ?? ''),
                    '80950' => (string) ($agency->supervision_document_type_id ?? ''),
                    '80983' => (string) ($agency->poc_document_type_id ?? ''),
                    '81016' => (string) ($agency->patient_package_document_type_id ?? ''),
                ];
                $resolved = $map[(string) $docId] ?? '';
                if ($resolved !== '') {
                    return $resolved;
                }

                // Title-based fallback when type ID is unknown or agency setting not configured
                if ($title !== '') {
                    $lowerTitle = strtolower($title);
                    if (stripos($lowerTitle, 'supervisory') !== false) {
                        $id = (string) ($agency->supervision_document_type_id ?? '');
                        if ($id !== '') return $id;
                    }
                    if (stripos($lowerTitle, 'poc') !== false || stripos($lowerTitle, 'plan of care') !== false) {
                        $id = (string) ($agency->poc_document_type_id ?? '');
                        if ($id !== '') return $id;
                    }
                }
            }
        }

        // fallback to hardcoded defaults if agency setting is not configured
        $defaults = [
            '80752' => '36781',
            '81049' => '37675',
            '81082' => '538',
        ];
        return $defaults[(string) $docId] ?? '538';
    }
}
