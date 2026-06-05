<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoCallLogIdToCallAppointments extends Migration
{
    public function up()
    {
        Schema::table('call_appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('call_appointments', 'auto_call_log_id')) {
                $table->unsignedBigInteger('auto_call_log_id')->nullable()->after('id')
                      ->comment('Links back to patient_auto_call_logs.id when booked via AI call');
                $table->index('auto_call_log_id');
            }
        });
    }

    public function down()
    {
        Schema::table('call_appointments', function (Blueprint $table) {
            $table->dropIndex(['auto_call_log_id']);
            $table->dropColumn('auto_call_log_id');
        });
    }
}
