<?php

namespace App\Http\Controllers;
use App\Services\EventService;
use Log;
use App\Model\SMSLogs;
use App\Helpers\Common;

class CronjobEventStatusUpdateController extends Controller
{
    protected $eventService= "";
	public function __construct(EventService $eventService)
	{
        $this->eventService  =$eventService;
    }

    function updateStatus(){
		// Get all records with start_date and end_date
        $records = $this->eventService->getExpiredDateEvent();
        if(count($records) > 0){
            foreach ($records as $record) {
                // Check if current date is beyond the end date	
                $data['deactivated_datetime'] = date('Y-m-d H:i:s');
                $data['deactivated_by'] = NULL;
                $data['status'] = 0;
                $data['deactivate_by_cron'] = 1;
    
                $this->eventService->statusUpdate($data, array('id' => $record->id));
                
                
            }
        }
	}

    public function updateSMSStatus(){
       $query = SMSLogs::select('id','send_sms_id')->where('send_sms_status', 'queued')->inRandomOrder()->limit(1000)->get();
        if(count($query) > 0){
            foreach ($query as $row) {
            $subquery = Common::fetchSingleMessage($row->send_sms_id);
                $json = json_decode($subquery, true);
                if(isset($json['status'])){
                    $final_status = ['send_sms_status'=>$json['status'],'send_sms_updated_date'=>date('Y-m-d H:i:s',strtotime($json['date_updated']))];
                    SMSLogs::where('id', $row->id)->update($final_status);
                }
            }
        }
    }
}