<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUploadDocumentCronToAgencyTaskHealthSettings extends Migration
{
    public function up()
    {
        Schema::table('agency_task_health_settings', function (Blueprint $table) {
            $table->tinyInteger('upload_document_cron')->default(0)->after('send_to_supervision');
        });
    }

    public function down()
    {
        Schema::table('agency_task_health_settings', function (Blueprint $table) {
            $table->dropColumn('upload_document_cron');
        });
    }
}
