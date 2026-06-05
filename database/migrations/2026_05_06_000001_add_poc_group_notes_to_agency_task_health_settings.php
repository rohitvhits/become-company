<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPocGroupNotesToAgencyTaskHealthSettings extends Migration
{
    public function up()
    {
        Schema::table('agency_task_health_settings', function (Blueprint $table) {
            $table->text('poc_group_notes')->nullable()->after('upload_hha_poc');
        });
    }

    public function down()
    {
        Schema::table('agency_task_health_settings', function (Blueprint $table) {
            $table->dropColumn('poc_group_notes');
        });
    }
}
