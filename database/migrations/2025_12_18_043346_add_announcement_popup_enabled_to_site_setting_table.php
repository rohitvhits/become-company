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
        Schema::table('site_setting', function (Blueprint $table) {
            $table->boolean('announcement_popup_enabled')->default(true)->after('health_check_enabled')->comment('Enable/disable announcement popup on login');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_setting', function (Blueprint $table) {
            $table->dropColumn('announcement_popup_enabled');
        });
    }
};
