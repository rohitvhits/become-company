<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSupervisionSyncToTaskHealthMaster extends Migration
{
    public function up()
    {
        Schema::table('task_health_master', function (Blueprint $table) {
            $table->tinyInteger('is_supervision_sync')->nullable()->after('is_poc_sync')->comment('1 = synced, null = pending');
        });
    }

    public function down()
    {
        Schema::table('task_health_master', function (Blueprint $table) {
            $table->dropColumn('is_supervision_sync');
        });
    }
}
