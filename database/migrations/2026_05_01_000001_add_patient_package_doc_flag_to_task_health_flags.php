<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task_health_flags', function (Blueprint $table) {
            $table->tinyInteger('patient_package_doc_check')->default(0)->after('kardex_check_date')->comment('Patient Welcome Package uploaded to HHA');
            $table->unsignedBigInteger('patient_package_doc_check_by')->nullable()->after('patient_package_doc_check');
            $table->dateTime('patient_package_doc_check_date')->nullable()->after('patient_package_doc_check_by');
        });
    }

    public function down()
    {
        Schema::table('task_health_flags', function (Blueprint $table) {
            $table->dropColumn([
                'patient_package_doc_check',
                'patient_package_doc_check_by',
                'patient_package_doc_check_date',
            ]);
        });
    }
};
