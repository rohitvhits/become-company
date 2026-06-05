<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRetryColumnsToPatientAutoCallLogs extends Migration
{
    public function up()
    {
        Schema::table('patient_auto_call_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('call_attempts')->default(0)->after('call_status');
            $table->unsignedTinyInteger('reminder_call_attempts')->default(0)->after('reminder_call_fired_at');
        });
    }

    public function down()
    {
        Schema::table('patient_auto_call_logs', function (Blueprint $table) {
            $table->dropColumn(['call_attempts', 'reminder_call_attempts']);
        });
    }
}
