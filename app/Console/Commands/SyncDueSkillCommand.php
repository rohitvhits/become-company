<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Services\AgencySkillService;
use App\Services\AlayacareService;
use App\Helpers\AlayacareHelper;
use App\Helpers\Utility;
use App\Model\AlayacareEmployee;
use App\Model\AlayacareEmployeeSkill;
use App\Model\AlayacareCronLog;
use App\Model\AgencySkill;

class SyncDueSkillCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alayacare-due:process-due-skill {--limit=10 : Number of records to process per run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $totalSkillsAdded = 0;
    private $totalSkillsUpdated = 0;
    private $totalErrors = 0;
    private $agencySummary = [];
    private $employeeService;
    private $agencyDueSkillService;
    public function __construct(AlayacareService $employeeService,AgencySkillService $agencyDueSkillService)
    {
        parent::__construct();
        $this->employeeService = $employeeService;
        $this->agencyDueSkillService = $agencyDueSkillService;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $runningDueSkill = $this->agencyDueSkillService->runningDueSkill();

        if($runningDueSkill){
            die();
        }

        $newDueSkill = $this->agencyDueSkillService->getAllDueSkillList();
       
        if ($newDueSkill) {
            $page = 1;
            do {
                $this->agencyDueSkillService->update(array('current_status'=>'running','last_sync_date'=>date('Y-m-d H:i:s')),array('id'=>$newDueSkill->id));

                $getEmployeeDetails = $this->employeeService->getEmployeeListByAgencyId($newDueSkill->agency_id, $page);

                foreach ($getEmployeeDetails as $emp) {
                    try {
                        $this->syncEmployeeSkill($emp, $newDueSkill->agency_id, $newDueSkill->skill_id);
                    } catch (\Exception $e) {
                        $this->totalErrors++;
                        $this->error("Error syncing employee {$emp->emp_id}: " . $e->getMessage());

                        AlayacareCronLog::create([
                            'error_log'   => $e->getMessage(),
                            'line'        => $e->getLine(),
                            'trace'       => $e->getTraceAsString(),
                            'created_at'  => date('Y-m-d H:i:s'),
                            'employee_id' => $emp->emp_id ?? null,
                            'type'        => 'Due Skill',
                            'agency_id'   => $newDueSkill->agency_id,
                        ]);
                    }
                }

                $page++;
            } while ($getEmployeeDetails->hasMorePages());
            $this->agencyDueSkillService->update(['current_status'=>'completed','last_sync_date' => date('Y-m-d H:i:s')],array('id'=>$newDueSkill->id,'agency_id'=>$newDueSkill->agency_id));
        }

    }

    private function syncEmployeeSkill($emp,$agencyId,$skillId){

        $details = AlayacareHelper::getEmployeeSkillDetailsCron($skillId,$emp->emp_id,$agencyId);
      
        $response = json_decode($details,true);

        if(isset($response['code'])){}
        else{
            
            $expiry = $response['fields']['expired_date']??"";
            if($expiry !=""){
                $date =Utility::convertYMDTime(Utility::convertUTCToUSA($expiry));

                $checkExistingRecord = AlayacareEmployeeSkill::where('employee_id',$emp->emp_id)->where('skill_id',$response['skill_id'])->where('agency_id',$agencyId)->first();

                $dataArray = [
                    'due_date'     => $date,
                    'alayacare_emp_id'     =>$emp->id,
                    'skill_name'     =>$response['name'],
                ];
                if(isset($checkExistingRecord->id)){
                    $dataArray['updated_at'] = date('Y-m-d H:i:s');
                   
                }else{
                    $dataArray['created_date'] = date('Y-m-d H:i:s');
                    $this->totalSkillsAdded++;
                 
                }
                $save = AlayacareEmployeeSkill::updateOrCreate([
                    'employee_id'   => $emp->emp_id,
                    'skill_id'   => $response['skill_id'],
                    'agency_id'=>$agencyId
                ],$dataArray);
            }

        }
        AlayacareEmployee::where('id',$emp->id)->where('agency_id',$agencyId)->update(array('last_sync_skill_date'=>date('Y-m-d H:i:s')));
    }
    
}
