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
            // Section toggles for report customization
            $table->boolean('show_forms_breakdown')->default(true)->after('show_highest_weight');
            $table->boolean('show_referral_sources')->default(true)->after('show_forms_breakdown');
            $table->boolean('show_resolution')->default(true)->after('show_referral_sources');
            $table->boolean('show_requests_per_agency')->default(true)->after('show_resolution');
            $table->boolean('show_portal_processing')->default(true)->after('show_requests_per_agency');
            $table->boolean('show_refusals_insights')->default(true)->after('show_portal_processing');
            $table->boolean('show_cancellations_insights')->default(true)->after('show_refusals_insights');
            $table->boolean('show_non_mdo_forms')->default(true)->after('show_cancellations_insights');
            $table->boolean('show_mdo_completed')->default(true)->after('show_non_mdo_forms');
            $table->boolean('show_updates_per_agency')->default(true)->after('show_mdo_completed');
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
            $table->dropColumn([
                'show_forms_breakdown',
                'show_referral_sources',
                'show_resolution',
                'show_requests_per_agency',
                'show_portal_processing',
                'show_refusals_insights',
                'show_cancellations_insights',
                'show_non_mdo_forms',
                'show_mdo_completed',
                'show_updates_per_agency',
            ]);
        });
    }
};
