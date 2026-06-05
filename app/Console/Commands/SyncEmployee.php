<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AgencyService;
use App\Services\BranchMasterService;
use App\Helpers\AlayacareHelper;
use App\Services\AlayacareService;
use App\Agency;
use Carbon\Carbon;
use App\Model\AlayacareCronLog;
class SyncEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process-employee-sync {--limit=10 : Number of records to process per run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info('Sync Employee Cron Started');
        $agencyService = new AgencyService();
        $alayaEmpService = new AlayacareService();

        try {
            $agency = $agencyService->getSyncAlayacareAgencyList();
           
            $page = 1;
           
            $agencySyncFailed = false;

            try {
                do {
                    $response = AlayacareHelper::getEmployeeRecordByAgency($agency->id, $page);
                    $alayacareEmpDetails = json_decode($response, true);
                    $employees = $alayacareEmpDetails['items'] ?? [];

                    $getExistingRecord = $alayaEmpService->getDetailsByWithAgencyId($agency->id);
                    $alayacareEmpId = [];
                    $branchDetails = [];
                    foreach ($employees as $employee) {
                        $alayacareEmpId[] = $employee['id'];
                        $branchDetails[$employee['id']] = $employee['branch']??[];
                    }
                      
                    $final = array_diff($alayacareEmpId,$getExistingRecord->toArray());
                   
                    foreach ($final as $employee) {
                        $data = [
                            'emp_id' => $employee,
                            'demographic_updated_flag' => "N",
                            'branch_id'=> $branchDetails[$employee]['id'] ?? null,
                            'branch_name'=>$branchDetails[$employee]['name'] ?? null
                        ];
                        $alayaEmpService->alayacareEmployeeUpdate($data,$employee,$agency->id);
                    }

                    $page++;

                } while ($page <= ($alayacareEmpDetails['total_pages'] ?? $page));

            } catch (\Exception $e) {
                $agencySyncFailed = true;
                $errorData = [
                    'error_log'     => $e->getMessage(),
                    'line'          => $e->getLine(),
                    'trace'         => $e->getTraceAsString(),
                    'created_at'  => date('Y-m-d H:i:s'),
                    'employee_id'   => $employee ?? null,
                    'type'          => 'Employee',
                    'agency_id'=>$agency->id
                ];
        
                AlayacareCronLog::create($errorData);
            }

            // Update sync date only on success
            if (!$agencySyncFailed) {
                Agency::where('id', $agency->id)->update([
                    'alayacare_employee_sync_date' => Carbon::now(),
                ]);
            }

        } catch (\Exception $e) {
           $errorData = [
                'error_log'     => $e->getMessage(),
                'line'          => $e->getLine(),
                'trace'         => $e->getTraceAsString(),
                'created_at'  => date('Y-m-d H:i:s'),
                'employee_id'   => $employee ?? null,
                'type'          => 'Employee',
                'agency_id'=>$agency->id
            ];
    
            AlayacareCronLog::create($errorData);
        }
    }
}
