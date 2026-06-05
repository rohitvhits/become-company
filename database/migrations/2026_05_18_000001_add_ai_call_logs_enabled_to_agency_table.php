<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAiCallLogsEnabledToAgencyTable extends Migration
{
    public function up()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->tinyInteger('ai_call_logs_enabled')->default(0)->after('restrict_service_request_update');
        });
    }

    public function down()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->dropColumn('ai_call_logs_enabled');
        });
    }
}
