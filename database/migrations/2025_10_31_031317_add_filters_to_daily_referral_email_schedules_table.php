<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_referral_email_schedules', function (Blueprint $table) {
            $table->json('agency_ids')->nullable()->after('notes');
            $table->json('service_ids')->nullable()->after('agency_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_referral_email_schedules', function (Blueprint $table) {
            $table->dropColumn(['agency_ids', 'service_ids']);
        });
    }
};
