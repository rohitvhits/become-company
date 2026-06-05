<?php

namespace App\Services;

use App\Model\HHAMDOClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use GuzzleHttp\Exception\ConnectException;
use Carbon\Carbon;
use App\Exceptions\MDOAuthenticationException;

class HHAMDOService
{
    protected string $baseUrl;
    protected string $baseUrlLogin;
    protected ?string $fhirSystem = null;
    protected const APPICATION_ACCEPT = "application/fhir+json";

    public function __construct()
    {
        $this->baseUrl = rtrim(env('HHA_MDO_INTEGRATION'), '/') . '/';
        $this->baseUrlLogin = rtrim(env('HHA_MDO_INTEGRATION_LOGIN'), '/') . '/';
        $this->fhirSystem = env('HHA_MDO_FHIR');
    }

    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = $auth['id'];
        $insert = new HHAMDOClient($data);
        $insert->save();
        return $insert->id;
    }

    public function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];
        return HHAMDOClient::where($where)->update($data);
    }

    /**
     * 🔐 Get HHA MDO Login Token (cached)
     */
    public function getHHAMDOLogin($agencyID,$type="")
    {
        
        $client = $this->getClientDetailsByAgencyId($agencyID);
        if (!$client) {
            if($type==""){
 throw new MDOAuthenticationException("MDO client details not found for agency {$agencyID}");
            }
           
        }

        $response = Http::timeout(10)
            ->withHeaders([
                'client_id' => $client->client_id??"",
                'client_secret' => $client->client_secret??"",
                'accept' => "application/fhir+json",
                'grant_type' => 'client_credentials',
            ])
            ->post($this->baseUrlLogin . 'auth/token');

        if ($response->failed()) {
            if($type==""){
                Log::error('HHA MDO Login Failed', [
                    'agencyID' => $agencyID,
                    'response' => $response->body(),
                ]);
                throw new MDOAuthenticationException('MDO Authentication failed.');
            }
        }

        $data = $response->json();

        return [
            'access_token' => $data['access_token'] ?? null,
            'apiKey' => $client->api_token??"",
            'txtId' => $client->txtID??"",
        ];
    }

    /**
     * 🧾 Fetch Client Details
     */
    public function getClientDetailsByAgencyId($agencyID)
    {
        return HHAMDOClient::select('client_id', 'client_secret', 'api_token', 'txtID')
            ->where('del_flag', 'N')->where('agency_id', $agencyID)
            ->where('is_status', 1)
            ->first();
    }

    /**
     * 📄 Fetch All Patient Document Identifiers
     */
    public function getHHAMDoPatientDocument($details, $agencyID)
    {
        $login = $this->getHHAMDOLogin($agencyID);
        $patientId = $details->patient_id;

        try {
            $url = $this->buildDocumentUrl($login, $patientId);
          
            $response = $this->fetchDocumentData($login, $url);
            
            return $this->processDocumentResponse($response);
        } catch (\Throwable $th) {
            return [
                'error_msg' => 'An unexpected error occurred.',
                'status_code' => 500,
                'data' => [],
            ];
        }
    }

    /**
     * ⚡ Fetch Multiple Document Details in Parallel (Async)
     */
    public function getDocumentListBatch($details, $agencyID, array $identifiers)
    {
        $login = $this->getHHAMDOLogin($agencyID);
        $patientId = $details->patient_id;

        $headers = [
            'accept' => self::APPICATION_ACCEPT,
            'x-api-key' => $login['apiKey'],
            'Authorization' => "Bearer {$login['access_token']}",
            'Content-Type' => self::APPICATION_ACCEPT,
        ];

        $urls = collect($identifiers)->map(function ($id) use ($login, $patientId) {
            $query = http_build_query([
                'identifier' => $this->fhirSystem . '|' . $login['txtId'],
                'subject' => 'Patient/' . $patientId,
            ]);

            $query .= '&identifier=' . urlencode($id);

            return "{$this->baseUrl}R5/DocumentReference?" . $query;
        });

        // return Cache::remember("hha-mdo-patient-document-new-multiple{$patientId}", 1, function () use ($urls, $headers) {

            $httpResponses = Http::pool(function ($pool) use ($urls, $headers) {
                return $urls->map(fn($url) =>
                    $pool->withHeaders($headers)->timeout(60)->get($url)
                )->all();
            });

            return collect($httpResponses)->map(function ($response) {
                $temp = [
                    'status' => $response->status(),
                    'data' => [],
                    'error_msg' => 'Success',
                ];

                if ($response->successful()) {
                    $data = $response->json();
                    $temp['data'] = $data['entry'] ?? [];
                } else {
                    $error = json_decode($response->body(), true);
                    $temp['error_msg'] = $error['issue'][0]['details']['text'] ?? '';
                }

                return $temp;
            })->all();
        // });
    }

    /**
     * ⚡ Fetch Single Document Details in Parallel (Async)
     */
    public function getDocumentListBatchSignle($details, $agencyID, array $identifiers)
    {
        $login = $this->getHHAMDOLogin($agencyID);
        $patientId = $details->patient_id;

        $headers = [
            'accept' => self::APPICATION_ACCEPT,
            'x-api-key' => $login['apiKey'],
            'Authorization' => "Bearer {$login['access_token']}",
            'Content-Type' => self::APPICATION_ACCEPT,
        ];

        $urls = collect($identifiers)->map(function ($id) use ($login, $patientId) {
            $query = http_build_query([
                'identifier' => $this->fhirSystem . '|' . $login['txtId'],
                'subject' => 'Patient/' . $patientId,
            ]);

            $query .= '&identifier=' . urlencode($id);

            return "{$this->baseUrl}R5/DocumentReference?" . $query;
        });

        $httpResponses = Http::pool(function ($pool) use ($urls, $headers) {
            return $urls->map(fn($url) =>
                $pool->withHeaders($headers)->timeout(60)->get($url)
            )->all();
        });

        $decodedResponses = collect($httpResponses)->map(function ($response) {
            $temp = [];
            $temp['status'] = $response->status();
            $temp['data'] = [];
            $temp['error_msg'] = "Success";

            if ($response->successful()) {
                $data = $response->json();
                $temp['data'] = $data['entry'];
            } else {
                $temp['status'] = $response->status();
                $temp['error_msg'] = "";
                $getErrorMessage = json_decode($response->body(), true);
                if (isset($getErrorMessage['issue'][0]['details']['text']) && $getErrorMessage['issue'][0]['details']['text'] != "") {
                    $temp['error_msg'] = $getErrorMessage['issue'][0]['details']['text'];
                }
            }

            return $temp;
        });

        return $decodedResponses->all();
    }

    public function getAllClientDetailsByAgencyId($agencyID)
    {
        return HHAMDOClient::where('del_flag', 'N')->where('agency_id', $agencyID)
            ->first();
    }

    private function buildDocumentUrl($login, $patientId)
    {
        $identifier = urlencode("{$this->fhirSystem}|{$login['txtId']}");
        $subject = urlencode("Patient/{$patientId}");
        return "{$this->baseUrl}R5/DocumentReference?identifier={$identifier}&subject={$subject}";
    }

    private function fetchDocumentData($login, $url)
    {
        $httpResponse = Http::timeout(20)
            ->withHeaders([
                'accept' => self::APPICATION_ACCEPT,
                'x-api-key' => $login['apiKey'],
                'Authorization' => "Bearer {$login['access_token']}",
                'Content-Type' => self::APPICATION_ACCEPT,
            ])
            ->get($url);

        return [
            'status' => $httpResponse->status(),
            'body' => $httpResponse->body(),
        ];
    }

    private function processDocumentResponse($response)
    {
        $successStatus = [200];
        $data = json_decode($response['body'], true);
        $identifiers = [];

        if (in_array($response['status'], $successStatus)) {
            $identifiers = $this->extractDocumentEntries($data);
            $message = 'Success';
        } else {
            $message = $data['issue'][0]['details']['text'] ?? '';
        }

        return [
            'error_msg' => $message,
            'status_code' => $response['status'],
            'data' => $identifiers,
        ];
    }

    private function extractDocumentEntries(array $data)
    {
        $entries = $data['entry'] ?? [];
        $identifiers = [];

        foreach ($entries as $item) {
            $resource = $item['resource'] ?? [];

            if (empty($resource['identifier'][0])) {
                continue;
            }

            $identifier = $resource['identifier'][0];
            $extensions = $resource['extension'] ?? [];

            $identifiers[] = [
                'id' => $resource['id'] ?? '',
                'document_download_url' => ($identifier['system'] ?? '') . '|' . ($identifier['value'] ?? ''),
                'start_date' => $this->formatDate($extensions[0]['valueDateTime'] ?? null),
                'end_date' => $this->formatDate($extensions[1]['valueDateTime'] ?? null),
                'docStatus' => $resource['docStatus']['coding'][0]['code'] ?? '',
            ];
        }

        return $identifiers;
    }

    private function formatDate($dateTime)
    {
        return $dateTime ? Carbon::parse($dateTime)->format('m/d/Y') : '';
    }

    public function saveDocument($data)
    {
        $login = $this->getHHAMDOLogin($data['agency_id']);

        $url = env('HHA_MDO_INTEGRATION') . 'R5/DocumentReference/' . $data['hha_document_id'];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode($data['final_data'], true),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $login['apiKey'],
                'accept: application/fhir+json',
                'Content-Type: application/fhir+json',
                'Authorization:' . $login['access_token']
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        $responseData = [];
        $message = '';
        $successStatus = [200, 201, 204];

        $decodedResponse = json_decode($response, true);

        if (in_array($httpCode, $successStatus)) {
            $responseData = $decodedResponse ?? [];
            $message = "FHIR Document updated successfully";

        } else {
            if ($curlError) {
                $message = "cURL Error: " . $curlError;
            } elseif (isset($decodedResponse['issue'][0]['details']['text'])) {
                $message = $decodedResponse['issue'][0]['details']['text'];
            } else {
                $message = "Unexpected error occurred.";
            }

        }

        return [
            'status_code' => $httpCode,
            'error_msg' => $message,
            'data' => $responseData,
        ];
    }

    public function getHHAMDOPatientList($agencyId,$page){
        $login = $this->getHHAMDOLogin($agencyId);

        $data = ['resourceType'=>'Patient','identifier'=>[['system'=>$this->fhirSystem,'value'=>$login['txtId']]]];
      
        $url = env('HHA_MDO_INTEGRATION') . 'R5/Patient?_count=50&_page=' . $page;
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data, true),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $login['apiKey'],
                'accept: application/fhir+json',
                'Content-Type: application/fhir+json',
                'Authorization:' . $login['access_token']
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        $responseData = [];
        $message = '';
        $successStatus = [200, 201, 204];

        $decodedResponse = json_decode($response, true);
        
        $responseData = $decodedResponse ?? [];
        if (in_array($httpCode, $successStatus)) {
            $message = "Success";
        } else {
            if ($curlError) {
                $message = "cURL Error: " . $curlError;
            } elseif (isset($decodedResponse['issue'][0]['details']['text'])) {
                $message = $decodedResponse['issue'][0]['details']['text'];
            } else {
                $message = "Unexpected error occurred.";
            }

        }

        return [
            'status_code' => $httpCode,
            'error_msg' => $message,
            'data' => $responseData,
        ];
    }

    public function fetchPatientDetails($agencyId,$patientId){
   
        $login = $this->getHHAMDOLogin($agencyId,'fetch');
     
        $identifier = 'https://hl7.org/fhir/sid/us-tin|'.$login['txtId'];

        $url = env('HHA_MDO_INTEGRATION') . 'R5/Patient/'.$patientId;
       
        $response = Http::withHeaders([
            'accept' => 'application/fhir+json',
            'x-api-key' =>$login['apiKey'],
            'Authorization' => $login['access_token'],
        ])->get($url, [
            'identifier' => $identifier
        ]);

        $data = $response->json();

        $final = [];
        if(isset($data['extension'])){
            $diagnosisData = collect($data['extension'])->first(function ($item) {
                $url = str_replace('http://hhaexchange.com/fhir/StructureDefinition','',$item['url']);
                return isset($item['url']) &&
                    $url === '/diagnosis';
            });

            if(isset($diagnosisData['extension'])){
                foreach ($diagnosisData['extension'] as $diagnosis) {
                    $formatted = [];
                    foreach ($diagnosis['extension'] as $item) {

                        $key = str_replace('-','',$item['url']);

                        $value = $item['valueInteger']
                            ?? $item['valueString']
                            ?? $item['valueBoolean']
                            ?? null;

                        $formatted[$key] = $value;
                    }

                    $final[] = $formatted;
                }
            }
        }
    
        return $final;
    }
}
