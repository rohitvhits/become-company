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
        Schema::create('client_review_feedback_questionn', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->integer('created_by', false, true)->length(11)->nullable();
            $table->integer('updated_by', false, true)->length(11)->nullable();
            $table->char('delete_flag', 1)->default('N');
            $table->integer('deleted_by', false, true)->length(11)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_review_feedback_question');
    }
};
