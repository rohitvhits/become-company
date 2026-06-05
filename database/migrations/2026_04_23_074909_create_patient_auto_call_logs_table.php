<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientAutoCallLogsTable extends Migration
{
    public function up()
    {
        Schema::create('patient_auto_call_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->string('mobile');
            $table->string('patient_name');
            $table->unsignedBigInteger('agency_id')->nullable();
            $table->string('location_id')->nullable();
            $table->string('service_id')->nullable();
            $table->string('agency_name')->nullable();
            $table->text('sms_link')->nullable();
            $table->timestamp('sms_sent_at')->nullable();
            $table->timestamp('appointment_deadline')->nullable(); // sms_sent_at + 4 hours
            $table->timestamp('booked_at')->nullable();           // filled when they book
            $table->string('unit_id')->nullable();       // added to link with unit for better tracking and debugging
            $table->timestamp('call_fired_at')->nullable();
            $table->string('call_status')->default('pending');    // pending | booked | called | failed
            $table->text('call_response')->nullable();
            $table->string('triggered_by')->default('savePatient'); // savePatient | updatePatient
            $table->timestamps();

            $table->index('patient_id');
            $table->index('agency_id');
            $table->index(['call_status', 'appointment_deadline']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_auto_call_logs');
    }
}
