<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('site_setting', function (Blueprint $table) {
            $table->boolean('task_health_cron_enabled')->default(0)->after('supervision_due_date_months')->comment('Enable/disable task health HHA link cron');
        });
    }

    public function down()
    {
        Schema::table('site_setting', function (Blueprint $table) {
            $table->dropColumn('task_health_cron_enabled');
        });
    }
};
