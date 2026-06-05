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
        Schema::create('payment_import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedInteger('uploaded_by');
            $table->enum('upload_status', ['Pending', 'Processed', 'Failed'])->default('Pending');
            $table->integer('total_records')->default(0);
            $table->integer('valid_records')->default(0);
            $table->integer('invalid_records')->default(0);
            $table->json('error_log')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('uploaded_by');
            $table->index('upload_status');
            $table->index('uploaded_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_import_logs');
    }
};
