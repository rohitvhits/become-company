<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskHealthCronLogTable extends Migration
{
    public function up()
    {
        Schema::create('task_health_cron_log', function (Blueprint $table) {
            $table->id();
            $table->string('cron_name')->nullable();
            $table->unsignedBigInteger('task_health_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('agency_id')->nullable();
            $table->string('type')->nullable()->comment('start, end, success, error, skip');
            $table->text('message')->nullable();
            $table->longText('data')->nullable()->comment('Serialized request/response data');
            $table->timestamps();

            $table->index('task_health_id');
            $table->index('patient_id');
            $table->index('cron_name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_health_cron_log');
    }
}
