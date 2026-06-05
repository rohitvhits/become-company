<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHubImportLogsTable extends Migration
{
    public function up()
    {
        Schema::create('hub_import_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id');
            $table->string('file_name');
            $table->string('file_path')->nullable();
            $table->json('unique_fields');
            $table->integer('total_records')->default(0);
            $table->integer('inserted_count')->default(0);
            $table->integer('updated_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('inactive_count')->default(0);
            $table->enum('status', ['Processing', 'Completed', 'Failed'])->default('Processing');
            $table->json('error_details')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->datetime('created_date');

            $table->index(['agency_id', 'created_date']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hub_import_logs');
    }
}