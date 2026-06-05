<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientCustomDataSubmit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_custom_data_submit', function (Blueprint $table) {
            $table->id();
            $table->string('agency_id')->nullable();
            $table->string('patient_id')->nullable();
            $table->string('field_id')->nullable();
            $table->string('form_id')->nullable();
            $table->string('value')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_custom_data_submit');
    }
}
