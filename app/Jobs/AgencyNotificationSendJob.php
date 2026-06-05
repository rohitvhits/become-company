<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Services\AgencyNotificationService;

class AgencyNotificationSendJob implements ShouldQueue
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
                $agency_id = $this->data['agencyid']??'';
                $notificationData = array(
                    'agency_id' => $agency_id,
                    'title' => $this->data['title'],
                    'created_by' => $this->data['created_by'],
                    'record_id' => $this->data['record_id'],
                    'record_type' => $this->data['record_type'],
                    'message' => $this->data['msg'],
                    'user_id' => $user_id,
                );
                AgencyNotificationService::savetoDb($notificationData);
            }
        }
    }
}