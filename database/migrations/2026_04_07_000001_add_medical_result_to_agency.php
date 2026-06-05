<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMedicalResultToAgency extends Migration
{
    public function up()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->integer('medical_result_id')->nullable()->after('medical_name');
            $table->string('medical_result_name', 255)->nullable()->after('medical_result_id');
        });
    }

    public function down()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->dropColumn(['medical_result_id', 'medical_result_name']);
        });
    }
}
