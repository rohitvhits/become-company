<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Model\ResolutionSmsTemplate;

class CreateResolutionSmsTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('resolution_sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('status', 100)->unique();
            $table->text('message');
            $table->string('del_flag', 1)->default('N');
            $table->timestamps();
        });

        // Seed default messages
        $templates = [
            [
                'status'  => 'Appointment Missed',
                'message' => "it looks like you missed your telehealth appointment scheduled on {appointment_date}. Please call us at 1-833-532-4200 to reschedule as soon as possible.",
            ],
            [
                'status'  => 'Unable to Contact',
                'message' => "we attempted to reach you regarding scheduling a telehealth appointment but couldn't connect. Our call would have come from 1-833-532-4200. Please call us back to reschedule your appointment.",
            ],
            [
                'status'  => 'Require Medication List',
                'message' => "you have an upcoming appointment and we require your current medication list prior to your visit. Please email it to telehealthreferrals@nybestmedical.com or have it faxed to 929-407-2300. It is very important that we receive this information before your appointment.",
            ],
            [
                'status'  => 'Additional Documentaion',
                'message' => "you have an upcoming appointment and we require your insurance information prior to your visit. Please email it to telehealthreferrals@nybestmedical.com. It is very important that we receive this information before your appointment.",
            ],
            [
                'status'  => 'Auto-Appts (Made via the website)',
                'message' => "thank you for taking the time to schedule your appointment. Please note that your appointment is not yet confirmed. To complete your scheduling, please send your current medication list and insurance information to telehealthreferrals@nybestmedical.com. Once received, we will reach out to confirm your appointment. If you have any questions or concerns, please call 1-833-532-4200.",
            ],
        ];

        foreach ($templates as $template) {
            ResolutionSmsTemplate::create(array_merge($template, ['del_flag' => 'N']));
        }
    }

    public function down()
    {
        Schema::dropIfExists('resolution_sms_templates');
    }
}
