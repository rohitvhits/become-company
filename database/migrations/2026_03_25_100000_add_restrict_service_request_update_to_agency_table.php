<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->tinyInteger('restrict_service_request_update')->default(0);
        });
    }

    public function down()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->dropColumn('restrict_service_request_update');
        });
    }
};
