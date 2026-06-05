<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AgencyService;
use App\Helpers\AlayacareHelper;
use App\Model\AlayacareClient;
use App\Model\AlayacareCronLog;

use App\Agency;
use App\Services\AlayacareClientService;
class SyncClientDetail extends Command
{
    protected $signature = 'clientDetail:process-client-detail-sync';
    protected $description = 'Sync client demographic details from Alayacare';

    protected $agencyService;
    protected const COMMON_FORMAT_YMD ='Y-m-d H:i:s';
    protected $alayacareClientService;

    public function __construct(AgencyService $agencyService,AlayacareClientService $alayacareClientService)
    {
        parent::__construct();
        $this->agencyService = $agencyService;
        $this->alayacareClientService = $alayacareClientService;
    }

    public function handle()
    {
        try {
            $clients = $this->alayacareClientService->totalSyncClientDetailsWithLimit();

            if ($clients->isEmpty()) {
                return;
            }

            foreach ($clients as $client) {
                
                try {
                    $agency = $this->getValidAgency($client->agency_id);
                    if (!$agency) {
                        continue;
                    }

                    $clientDetails = $this->fetchClientDetails($agency, $client->client_id);
                    if (!$clientDetails) {
                        continue;
                    }
                    
                    $dataStore = $this->prepareClientData($clientDetails);
                    if (empty($dataStore)) {
                        continue;
                    }

                    $this->saveClient($client, $dataStore);
                   
                } catch (\Throwable $e) {
                    $this->errorLog($e, $client);
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Sync Client Details Fatal Error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            AlayacareCronLog::create([
                'error_log'   => $e->getMessage(),
                'line'        => $e->getLine(),
                'trace'       => $e->getTraceAsString(),
                'created_at'  => date(self::COMMON_FORMAT_YMD),
                'type'        => 'ClientDetails',
                'agency_id'   => $agency->id,
                'employee_id'   =>$client->client_id ?? null,
            ]);
        }
    }

    private function getValidAgency($agencyId)
    {
        $agency = Agency::where('id', $agencyId)->where('alaycare_status', 1)->first();

        if (
            !$agency ||
            empty($agency->alaycare_username) ||
            empty($agency->alaycare_password)
        ) {
            return null;
        }

        return $agency;
    }

    private function fetchClientDetails($agency, $clientId)
    {
        $response = AlayacareHelper::getClientDetailsById($agency->id, $clientId);

        if (empty($response)) {
            return null;
        }

        $data = json_decode($response);

        return $data && isset($data->demographics) ? $data : null;
    }

    private function prepareClientData($data)
    {
        $demo = $data->demographics;
        $genderMap = ['M' => 'Male', 'F' => 'Female'];

        return [
            "ac_id"              => $data->ac_id ?? null,
            "status"             => $data->status ?? null,
            "first_name"         => $demo->first_name ?? null,
            "last_name"          => $demo->last_name ?? null,
            "city"               => $demo->city ?? "",
            "state"              => $demo->state ?? "",
            "gender"             => $genderMap[$demo->gender ?? ''] ?? ($demo->gender ?? ""),
            "phone_main"         => $this->cleanPhone($demo->phone_main ?? null),
            "phone_other"        => $this->cleanPhone($demo->phone_other ?? null),
            "phone_personal"     => $demo->phone_personal ?? "",
            "address"            => $demo->address ?? "",
            "address_suite"      => $demo->address_suite ?? "",
            "country"            => $demo->country ?? "",
            "zip"                => $demo->zip ?? "",
            "uid"                => $demo->uid ?? "",
            "birthday"           => $demo->birthday ?? null,
            "start_on"           => $demo->start_on ?? null,
            "member_id"          => $demo->member_id ?? "",
            "insurance_id"       => $demo->insurance_id ?? "",
            "insured_unique_id"  => $demo->insured_unique_id ?? "",
            "secondary_language" => $demo->secondary_language ?? "",
            "First_Language"     => $demo->First_Language ?? "",
            "Other_Languages"    => $demo->Other_Languages ?? "",
            "living_situation"   => $demo->living_situation ?? "",
            "county"             => $demo->county ?? "",
            "group_name"         => $data->groups[0]->name ?? "",
            "group_id"           => $data->groups[0]->id ?? "",
            "profile_id"         => $data->profile_id ?? "",
            "external_id"        => $data->external_id ?? "",
            "branch_id"          => $data->branch_id ?? "",
            "tags"               => (isset($data->tags) && is_array($data->tags))
                                        ? implode(',', $data->tags)
                                        : "",
            "demographic_update_flag" => "Y",
            "updated_at"         => date(self::COMMON_FORMAT_YMD),
            'last_sync_date'     => date(self::COMMON_FORMAT_YMD)
        ];
    }

    private function cleanPhone($phone)
    {
        if (!$phone) return null;

        return preg_replace('/[\+\-\(\)\[\]\s]/', '', $phone);
    }

    private function saveClient($client, $data)
    {
        AlayacareClient::updateOrCreate(
            [
                'client_id'  => $client->client_id,
                'agency_id'  => $client->agency_id,
            ],
            $data
        );
    }

    private function errorLog($e, $client)
    {
        try {
            AlayacareCronLog::create([
                'error_log'   => $e->getMessage(),
                'line'        => $e->getLine(),
                'trace'       => $e->getTraceAsString(),
                'created_at'  => date(self::COMMON_FORMAT_YMD),
                'employee_id' => $client->client_id ?? null,
                'type'        => 'ClientDetails',
                'agency_id'   => $client->agency_id ?? null,
            ]);
        } catch (\Throwable $logException) {
            \Log::error('Failed to save AlayacareCronLog: ' . $logException->getMessage(), [
                'original_error' => $e->getMessage(),
                'agency_id' => $client->agency_id ?? null,
                'client_id' => $client->client_id ?? null,
            ]);
        }
    }
}
