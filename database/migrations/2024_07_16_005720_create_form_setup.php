<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormSetup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_setup', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->enum('is_default', ['0', '1'])->nullable();
            $table->enum('form_type', ['0', '1'])->nullable();// 0 Caregiver and 1 Patient
            $table->string('agency')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_setup');
    }
}
