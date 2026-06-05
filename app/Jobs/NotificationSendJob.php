<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Services\NotificationUserService;

class NotificationSendJob implements ShouldQueue
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
                $agency_fk = $this->data['agency_fk']??'';
                $notificationData = array(
                    'type' => $this->data['type'],
                    'user_id' => $user_id,
                    'agency_fk' => $agency_fk,
                    'record_id' => $this->data['record_id'],
                    'created_by' => $this->data['created_by'],
                    'title' => $this->data['title'],
                    'message' => $this->data['msg'],
                    'sms' => $this->data['sms'],
                    'email' => $this->data['email'],
                );
                NotificationUserService::savetoDb($notificationData);
            }
        }
    }
}