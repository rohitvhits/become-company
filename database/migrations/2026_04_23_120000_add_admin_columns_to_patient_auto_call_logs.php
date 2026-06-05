<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminColumnsToPatientAutoCallLogs extends Migration
{
    public function up()
    {
        Schema::table('patient_auto_call_logs', function (Blueprint $table) {
            $table->string('conversation_id')->nullable()->after('unit_id');
            $table->longText('transcript')->nullable()->after('conversation_id');
            $table->longText('extracted_data')->nullable()->after('transcript');
            $table->boolean('admin_verified')->default(false)->after('extracted_data');
            $table->timestamp('admin_verified_at')->nullable()->after('admin_verified');
            $table->unsignedBigInteger('admin_verified_by')->nullable()->after('admin_verified_at');
            $table->boolean('converted_to_appointment')->default(false)->after('admin_verified_by');
            $table->timestamp('converted_at')->nullable()->after('converted_to_appointment');
            $table->unsignedBigInteger('converted_by')->nullable()->after('converted_at');
            $table->boolean('confirmation_sms_sent')->default(false)->after('converted_by');
            $table->boolean('reminder_sms_sent')->default(false)->after('confirmation_sms_sent');
            $table->text('notes')->nullable()->after('reminder_sms_sent');
        });
    }

    public function down()
    {
        Schema::table('patient_auto_call_logs', function (Blueprint $table) {
            $table->dropColumn([
                'conversation_id', 'transcript', 'extracted_data',
                'admin_verified', 'admin_verified_at', 'admin_verified_by',
                'converted_to_appointment', 'converted_at', 'converted_by',
                'confirmation_sms_sent', 'reminder_sms_sent', 'notes',
            ]);
        });
    }
}
