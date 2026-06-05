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
            $table->boolean('health_check_enabled')->default(true)->after('document_dashboard_status')->comment('Enable/disable health check endpoint');
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
            $table->dropColumn('health_check_enabled');
        });
    }
};
