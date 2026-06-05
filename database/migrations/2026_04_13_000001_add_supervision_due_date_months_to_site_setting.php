<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupervisionDueDateMonthsToSiteSetting extends Migration
{
    public function up()
    {
        Schema::table('site_setting', function (Blueprint $table) {
            $table->tinyInteger('supervision_due_date_months')->default(12)->after('announcement_popup_enabled');
        });
    }

    public function down()
    {
        Schema::table('site_setting', function (Blueprint $table) {
            $table->dropColumn('supervision_due_date_months');
        });
    }
}
