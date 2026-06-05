<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTelehealthSendSmsToAgencyTable extends Migration
{
    public function up()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->tinyInteger('is_telehealth_send_sms')->default(1)->after('enable_portal_archive');
        });
    }

    public function down()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->dropColumn('is_telehealth_send_sms');
        });
    }
}
