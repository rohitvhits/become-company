<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AgencyService;
use App\Services\AlayacareClientService;
use App\Helpers\AlayacareHelper;
use App\Model\AlayacareClient;
use App\Model\EmployeeSyncLog;
use App\Model\AlayacareCronLog;
use App\Agency;
use Carbon\Carbon;

class SyncClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:process-client-sync {--limit=10 : Number of records to process per run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $agencyService;
    protected $alayacareClientService;
    /**
     * Execute the console command.
     *
     * @return int
     */

    public function __construct(AgencyService $agencyService,AlayacareClientService $alayacareClientService)
    {
        parent::__construct();
        $this->agencyService = $agencyService;
        $this->alayacareClientService = $alayacareClientService;
    }

    public function handle()
    {
        \Log::info('Sync Client Cron Started');
       try {
            $agency = $this->agencyService->getSyncClientAlayacareAgencyList();

            if (!$agency) {
                \Log::info('Sync Client - No agency found for sync');
                return;
            }

            $page = 1;
            $agencySyncFailed = false;

            try {
                do {

                    $existingClients = $this->alayacareClientService->getAllClientIdByAgencyId($agency->id);

                    $response = AlayacareHelper::getClientRecordByAgency($agency->id, $page);
                    $alayacareClientDetails = json_decode($response, true);

                    $clients = $alayacareClientDetails['items'] ?? [];

                    if (empty($clients)) {
                        break;
                    }

                    $clientIds = [];
                    $branchDetails = [];
                    foreach($clients as $client){
                        $clientIds[] = $client['id'];
                        $branchDetails[$client['id']] = $client['branch']??[];
                    }

                    $final = array_diff($clientIds, $existingClients->toArray());
                  
                    foreach ($final as $clientId) {
                        $data = [
                            'client_id' => $clientId,
                            'agency_id' => $agency->id,
                            'demographic_update_flag' => 'N',
                            'branch_id'=> $branchDetails[$clientId]['id'] ?? null,
                            'branch_name'=>$branchDetails[$clientId]['name'] ?? null
                        ];
                        $this->alayacareClientService->alayacareClientUpdate($data, $clientId, $agency->id);
                      
                    }

                    $page++;

                } while ($page <= ($alayacareClientDetails['total_pages'] ?? $page));

            } catch (\Throwable $e) {
                $agencySyncFailed = true;
                \Log::error("Sync Client - Agency {$agency->id} Failed: " . $e->getMessage());

                AlayacareCronLog::create([
                    'error_log'   => $e->getMessage(),
                    'line'        => $e->getLine(),
                    'trace'       => $e->getTraceAsString(),
                    'created_at'  => date('Y-m-d H:i:s'),
                    'type'        => 'Client',
                    'agency_id'   => $agency->id,
                    'employee_id'   => $clientId ?? null,
                ]);
            }

            if (!$agencySyncFailed) {
                Agency::where('id', $agency->id)->update([
                    'alayacare_client_sync_date' => Carbon::now(),
                ]);
            }

        } catch (\Throwable $e) {
            \Log::error('Sync Client Cron Failed: ' . $e->getMessage());

            AlayacareCronLog::create([
                'error_log'     => $e->getMessage(),
                'line'          => $e->getLine(),
                'trace'         => $e->getTraceAsString(),
                'created_at'  => date('Y-m-d H:i:s'),
                'employee_id'   => $clientId ?? null,
                'type'          => 'Client',
                'agency_id'=>$agency->id
            ]);
        }
    }
}
