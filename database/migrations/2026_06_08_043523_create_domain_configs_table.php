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
        Schema::create('domain_configs', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('company_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('title')->nullable();
            $table->string('login_bg', 20)->default('#0F0D0B');
            $table->string('theme_color', 20)->default('#0F0D0B');
            $table->string('logo_style')->default('width:100%;');
            $table->string('login_image')->default('img/pana.png');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domain_configs');
    }
};
