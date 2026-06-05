<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TaskHealthFlagsService;
use App\Services\TaskHealthMasterService;
use App\Helpers\TaskHealthApiHelper;
use App\Services\MapTaskHealthService;
use App\Services\PatientService;
use App\Helpers\HHAPatientHelper;

class SendPOCTaskHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hha:send-poc-task';

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
       $taskHealthPOCFlag = new TaskHealthFlagsService();
       $taskHealthIdsForPoc = $taskHealthPOCFlag->getTaskIdsByPOC();

       $getTaskList = new TaskHealthMasterService();

       $getTaskListByMain = $getTaskList->taskLithWihoutPOCLink($taskHealthIdsForPoc->toArray());
        
       if(count($getTaskListByMain) >0){
            foreach($getTaskListByMain as $patientId=>$taskId){
                
                $patientService = new PatientService();
                $getPatientDetails = $patientService->getPatientDetailsByIdWhitoutAgency($patientId);

                $mapTaskDetails = new MapTaskHealthService();
                $getList =  $mapTaskDetails->getMapTaskListByPatientAndVisitId($patientId,$taskId);
            
                if(count($getList) >0){
                  
                    $getVisitDetails = TaskHealthApiHelper::getVisitDetail($taskId,'cron');
                    
                    if(!empty($getVisitDetails['data']['planOfCareItems'][0])){
                        $finalResponse = [
                            'start_date'=>$getVisitDetails['data']['task']['certificationPeriod']['startDate'],
                            'stop_date'=>$getVisitDetails['data']['task']['certificationPeriod']['endDate'],
                            'shift'=>1
                        ];

                        $taskIds = [];
                        $minites = [];
                        $mintime = [];
                        $maxtime = [];
                        $asNeeded = [];
                        foreach($getList as $val){
                            $taskIds[] = $val->task_id;
                            $minites[] = 50;
                            $mintime[] = 1;
                            $maxtime[] = 7;
                            $asNeeded[] = "false";
                            if($val['frequency'] =='As needed'){
                                $asNeeded[] = "true";
                            }
                        }
                        $finalResponse['task_id'] = $taskIds;
                        $finalResponse['minutes'] = $minites;
                        $finalResponse['mintime'] = $mintime;
                        $finalResponse['maxtime'] = $maxtime;
                        $finalResponse['as_requested'] = $asNeeded;
                      $reponse = HHAPatientHelper::createPatientPOCDetails($getPatientDetails->link_hha_patient,$finalResponse);
                      if($reponse == 1){
                   
                        $taskHealthPOCFlag->saveFlagsOnlyPOCCron($getVisitDetails['data']['patient']['id'],$taskId,$patientId,1);
                      }
                    }
                }
            }
        }
    }
}
