<?php

namespace App\Jobs;

use App\Agency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\UserService;
use App\Services\NotificationUserService;
use App\User;
use Illuminate\Support\Facades\URL;
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
        if(isset( $this->data['user']) && !empty( $this->data['user'])){
            foreach ($this->data['user'] as $user_id) {
               
                
                $agency_fk = $this->data['agency_fk'];
                $notificationData = array(
                    'type' => 'Appointment',
                    'user_id' => $user_id,
                    'agency_fk' => $agency_fk,
                    'record_id' => $this->data['record_id'],
                    'created_by' => $this->data['created_by'],
                    'title' => 'New Portal Created',
                    'message' => ''
                );
                NotificationUserService::savetoDb($notificationData);
            }
        }
    }
}