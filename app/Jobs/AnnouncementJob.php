<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\UserService;
use App\Services\AnnouncementUserService;
use App\User;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Mail;
class AnnouncementJob implements ShouldQueue
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
        // $ids = [4117,4068];
        // if(isset( $this->data['message']) && !empty( $this->data['message'])){
        //     $getUsers = User::select('id','first_name','last_name','email')->where('delete_flag','N')->where('active','active')->whereIn([])->get();
        //     if(!empty($getUsers[0])){
        //         foreach($getUsers as $val){
                    
        //         }
        //     }
        // }

        $emailData = array(
            'username' => "Pinak",
            'sub' => "Announcement",
            'message' => $this->data['message'],
            'id'=>$this->data['id'],
            'image'=>$this->data['image'],
        );
        $messages = Utility::getHtmlContent('email_template.announcement_template', $emailData);
        $email = ['Pinak@nybestmedical.com','developer@nybestmedical.com','vishaldpatel.vhits@gmail.com'];
        $subject=$this->data['title'];

        try {
            $mail = Mail::mailer('second')->send([], [], function ($message) use ($email, $subject, $messages) {
                $message->to($email, "Ny Best Medicals")
                    ->subject($subject)->html($messages);
            });

            //code...
            
        }catch(\Throwable $th){

        }
    }
}
