<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\UserService;
use App\Services\AnnouncementUserService;

class NotificationSaveJob implements ShouldQueue
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
        \Log::info('Processing Order: ' . print_r($this->data));
        if($this->data['type'] == 'agency_user'){
            $users = UserService::getUserListids();
        }else if($this->data['type'] == 'nybest_user'){
            $users = UserService::getAgencyUserListids();
        }
        if(isset($users) && !empty($users)){
            foreach($users as $user_id){
                $insert['user_id'] =  $user_id;
                $insert['announcement_id'] = $this->data['insert_id']; 
                $insert['created_by'] = $this->data['createdBy'];
                AnnouncementUserService::save($insert);
            }
        }
    }
}
