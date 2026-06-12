<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->dropColumn('domain_config_id');
        });
    }

    public function down()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->unsignedBigInteger('domain_config_id')->nullable()->after('id');
        });
    }
};
