<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientFileLinksTable extends Migration
{
    public function up()
    {
        Schema::create('patient_file_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agency_file_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('linked_by')->nullable();
            $table->timestamps();

            $table->foreign('agency_file_id')->references('id')->on('agency_files')->onDelete('cascade');
            $table->unique('agency_file_id'); // one file → max one patient
            $table->index('patient_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_file_links');
    }
}
