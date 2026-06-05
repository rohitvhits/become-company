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
            $table->boolean('show_outliers')->default(true)->after('service_ids');
            $table->boolean('show_highest_weight')->default(true)->after('show_outliers');
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
            $table->dropColumn(['show_outliers', 'show_highest_weight']);
        });
    }
};
