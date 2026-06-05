<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencyNotesTable extends Migration
{
    public function up()
    {
        Schema::create('agency_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id');
            $table->enum('note_type', ['info', 'warning', 'danger'])->default('info')->comment('info=blue, warning=yellow, danger=red');
            $table->text('note');
            $table->string('created_by_name', 100)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('del_flag', 1)->default('N');
            $table->tinyInteger('is_active')->default(1)->comment('1=active, 0=inactive');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
           
        });
    }

    public function down()
    {
        Schema::dropIfExists('agency_notes');
    }
}
