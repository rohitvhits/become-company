<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameAgencySettingsToAgencyTaskHealthSettings extends Migration
{
    public function up()
    {
        Schema::rename('agency_settings', 'agency_task_health_settings');
    }

    public function down()
    {
        Schema::rename('agency_task_health_settings', 'agency_settings');
    }
}
