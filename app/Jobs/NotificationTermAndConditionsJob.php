<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\User;
use App\Model\TermConditionLog;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Utility;
use App\Services\NotificationUserService;
class NotificationTermAndConditionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data = "";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(isset( $this->data['id']) && !empty( $this->data['id'])){
           $getUsers = User::select('id','first_name','last_name','email')->where('delete_flag','N')->where('active','active')->get();
           $emailArray= [];
    
           if(!empty($getUsers[0])){
                foreach($getUsers as $val){
                    if($this->data['created_by'] != $val->id){
                        $getLastDetails = TermConditionLog::where('user_id',$val->id)->first();
                        if(isset($getLastDetails->id)){
                            
                            if($this->data['id'] ==1){
                                User::where('id',$val->id)->update(array('privacy_policy'=>0));
                                $typeSubject = "Privacy Policy";
                                
                                $emailArray[] = $val->email;
                                $emailData = array(
                                    'username' => $val->first_name.' '.$val->last_name,
                                    'sub' => "Policies Updated",
                                );
                                $messages = Utility::getHtmlContent('email_template.privacy_policy', $emailData);
                                $email = $val->email;
                                $subject="Policies Updated";
                                try {
    
                                    //code...
                                    $mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages) {
                                        $message->to($email, "Ny Best Medicals")
                                            ->subject($subject)->html($messages);
                                    });
    
                                }catch(\Throwable $th){
    
                                }
                            }
    
                            if($this->data['id'] ==2){
                                $typeSubject = "Terms and Conditions";
                                User::where('id',$val->id)->update(array('term_condition'=>0));
                                $emailData = array(
                                    'username' => $val->first_name.' '.$val->last_name,
                                    'sub' => "Terms and Conditions Updated",
                                  
                                );
                                $messages = Utility::getHtmlContent('email_template.term_condition', $emailData);
                                $email = $val->email;
                                $subjectT="Terms and Conditions Updated";
                                try {
    
                                    //code...
                                    $mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subjectT, $messages) {
                                        $message->to($email, "Ny Best Medicals")
                                            ->subject($subjectT)->html($messages);
                                    });
    
                                }catch(\Throwable $th){
    
                                }
                            }
    
                            $notificationData = array(
                                'type' =>$typeSubject,
                                'user_id' => $val->id,
                            
                                'created_by' => $this->data['created_by'],
                                'title' => $typeSubject,
                                'message' =>$this->data['user_name'].' has been updated '.$typeSubject,
    
                            );
                            NotificationUserService::savetoDb($notificationData);
                        }
                    }
                }
           }
        }
    }
}