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
        Schema::create('vns_procedure_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vns_procedure_id');
            $table->string('name');
            $table->string('del_flag')->default('N');
            $table->timestamp('created_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_date')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('deleted_date')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('vns_procedure_id')->references('id')->on('vns_procedure')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vns_procedure_results');
    }
};
