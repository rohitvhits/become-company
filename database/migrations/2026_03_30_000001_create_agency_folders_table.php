<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencyFoldersTable extends Migration
{
    public function up()
    {
        Schema::create('agency_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['agency_id', 'parent_id']);
            $table->index('agency_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('agency_folders');
    }
}
