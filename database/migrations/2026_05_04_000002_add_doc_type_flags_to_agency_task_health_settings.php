<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocTypeFlagsToAgencyTaskHealthSettings extends Migration
{
    public function up()
    {
        Schema::table('agency_task_health_settings', function (Blueprint $table) {
            $table->tinyInteger('assessment')->default(0)->after('upload_document_cron');
            $table->tinyInteger('kardex')->default(0)->after('assessment');
            $table->tinyInteger('cms_mdo_485')->default(0)->after('kardex');
            $table->tinyInteger('patient_package_doc')->default(0)->after('cms_mdo_485');
        });
    }

    public function down()
    {
        Schema::table('agency_task_health_settings', function (Blueprint $table) {
            $table->dropColumn(['assessment', 'kardex', 'cms_mdo_485', 'patient_package_doc']);
        });
    }
}
