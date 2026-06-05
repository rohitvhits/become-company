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
        Schema::create('vns_social_history', function (Blueprint $table) {
            $table->id();
            $table->integer('template_id')->unsigned();
            $table->string('name');
            $table->string('del_flag')->default('N');
            $table->timestamp('created_date')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->timestamp('updated_date')->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamp('deleted_date')->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vns_social_history');
    }
};
