<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleAppointment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_appointment', function (Blueprint $table) {
            $table->id();
            $table->integer('agency_id')->nullable();
            $table->integer('patient_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('schedule_id')->nullable();
            $table->string('status')->default('Pending');
            $table->dateTime('created_date')->nullable();
            $table->integer('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_appointment');
    }
}
