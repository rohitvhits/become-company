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
            $table->string('medication_list')->nullable()->after('assigned_to');
            $table->string('insurance_elg')->nullable()->after('medication_list');
            $table->string('mdo_tag')->nullable()->after('insurance_elg');
            $table->json('branch_ids')->nullable()->after('mdo_tag');
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
            $table->dropColumn(['medication_list', 'insurance_elg', 'mdo_tag', 'branch_ids']);
        });
    }
};
