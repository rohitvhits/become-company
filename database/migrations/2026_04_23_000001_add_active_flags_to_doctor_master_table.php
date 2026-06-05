<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveFlagsToDoctorMasterTable extends Migration
{
    public function up()
    {
        Schema::table('doctor_master', function (Blueprint $table) {
            $table->tinyInteger('is_active')->default(1)->after('npi_number');
            $table->tinyInteger('is_signature_stamp_active')->default(1)->after('is_active');
        });
    }

    public function down()
    {
        Schema::table('doctor_master', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'is_signature_stamp_active']);
        });
    }
}
