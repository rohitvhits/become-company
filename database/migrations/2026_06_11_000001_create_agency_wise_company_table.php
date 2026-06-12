<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agency_wise_company', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id');
            $table->unsignedBigInteger('domain_config_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agency_wise_company');
    }
};
