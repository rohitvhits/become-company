<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReminderCallToPatientAutoCallLogs extends Migration
{
    public function up()
    {
        Schema::table('patient_auto_call_logs', function (Blueprint $table) {
            $table->timestamp('reminder_call_fired_at')->nullable()->after('reminder_sms_sent');
        });
    }

    public function down()
    {
        Schema::table('patient_auto_call_logs', function (Blueprint $table) {
            $table->dropColumn('reminder_call_fired_at');
        });
    }
}
