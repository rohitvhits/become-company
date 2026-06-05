<?php

namespace App\Http\Controllers;

use App\Model\Logs;
use App\Model\Patient;
use App\PatientCronLog;
use App\Master;
use App\Model\HHACaregivers;
use App\Helpers\HHACaregiversHelper;
use Illuminate\Support\Facades\Mail;
use App\Agency;
use App\Helpers\Utility;

class CronJobNewController extends Controller
{
    public function cronJobBookedStatusUpdateNoShow(){
        $patientList =  Patient::patientBookedGetData();
     
        $fileArray = public_path().'/sendmail/noreplymsg.csv';
			
        $file = fopen($fileArray, 'w');
        $delimiter = ",";
        $columns = array();
        $columns = array('Record Id','Agency Name','Patient Name','Services');
        fputcsv($file, $columns, $delimiter);

        if(!empty($patientList[0])){
            foreach($patientList as $patient){
                $updateStatus = Patient::BookedStatusUpdateNoShow($patient->id);
               // $updateStatus = 1;
                if($updateStatus){
                    $explode = explode(',',$patient->service_id);
                    foreach($explode as $service){
                        $getMaster = Master::select('name')->where('id', $service)->where('del_flag', 'N')->first();
                        $namearray[] = $getMaster->name;
                    }
                    $agencyName = '';
                    if(isset($patient->agencyDetail->id) && $patient->agencyDetail->id !=""){
                        $agencyName = $patient->agencyDetail->agency_name;
                    }
                    $final =array($patient->id,$agencyName,$patient->first_name.' '.$patient->last_name,implode(',',$namearray));

                    fputcsv($file, $final);
                    
                    $finalData = array(
                        'patient_id'=>$patient->id,
                        'type'=>'Cron',
                        'old_response'=>serialize($patient),
                        'appointment_date'=>$patient->appointment_date,
                        'created_date'=>date('Y-m-d H:i:s'),
                    );
                    $saveDetails = new PatientCronLog($finalData);
                    $saveDetails->save();
                }
                
            }
        }
       
		 $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $fileArray ,
			
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $newmailarra = ['marina@nybestmedical.com','jromero@nybestmedical.com'];
        $subject ='[NyBestMedicals]NoShow List';
        // $messages = 'Hello NyBest<br>';
        // $messages .= 'Today’s Noshow :'.count($patientList).'<br>';
        // $messages .= 'Attached the report<br>';
        $attachment = 'noreplymsg.csv';
        
        $emailData = array(
            'no_show_count' => count($patientList),
        );
        $messages  = Utility::getHtmlContent('email_template.email_sent_no_show_count',$emailData);

        try {
            foreach($newmailarra as $emailId){
                $mail= Mail::mailer('second')->send( [],[], function ($message) use($emailId,$subject, $messages,$attachment){
                    $message->to($emailId,"NyBestMedicals") 
                      ->subject($subject)->html( $messages);
                      $message->attach(public_path().'/sendmail/'.$attachment);
                        
                  });
            }
            
        } catch (\Throwable $th) {
            //throw $th;
        }
       

    }

    function updateLastWorkDate(){
       
        $query = HHACaregivers::whereNull('last_work_date')->inRandomOrder()->limit(100)->get();
         
         foreach($query as $val){
             HHACaregiversHelper::getLastDateUpdate($val);
         }
     } 
}
