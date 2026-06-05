<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldMasters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_masters', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->string('type')->nullable();
            $table->JSON('options')->nullable();
            $table->string('custom')->nullable();
            $table->string('size')->nullable();
            $table->string('set_character_limit')->nullable();
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
        Schema::dropIfExists('field_masters');
    }
}
