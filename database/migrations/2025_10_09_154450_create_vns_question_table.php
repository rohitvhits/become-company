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
        Schema::create('vns_question', function (Blueprint $table) {
            $table->id();
            $table->longText('question_name')->nullable();
            $table->string('template_type', 255)->nullable();
            $table->string('del_flag',1)->default('N');
            $table->timestamp('created_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_date')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('deleted_date')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            // Foreign key constraints
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vns_question');
    }
};
