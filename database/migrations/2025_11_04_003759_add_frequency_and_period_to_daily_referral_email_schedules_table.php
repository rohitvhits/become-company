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
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('daily')->after('send_days');
            $table->integer('period_days')->nullable()->after('frequency'); // For custom period in days
            $table->enum('weekly_day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable()->after('period_days'); // For weekly frequency
            $table->integer('monthly_date')->nullable()->after('weekly_day'); // Day of month (1-31) for monthly frequency
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
            $table->dropColumn(['frequency', 'period_days', 'weekly_day', 'monthly_date']);
        });
    }
};
