<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMergeAgencyDeletionDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merge_agency_deletion_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->comment('Patient record ID to be updated');
            $table->unsignedBigInteger('filter_agency_id')->nullable()->comment('Deleted agency ID used in filter');
            $table->unsignedBigInteger('old_agency_id')->comment('Current agency ID of the patient');
            $table->unsignedBigInteger('new_agency_id')->comment('Target agency ID to merge to');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->comment('Processing status');
            $table->text('error_message')->nullable()->comment('Error message if processing failed');
            $table->unsignedBigInteger('created_by')->nullable()->comment('User who initiated the merge');
            $table->timestamp('created_at')->useCurrent()->comment('When the merge request was created');
            $table->timestamp('processed_at')->nullable()->comment('When the record was processed');
            $table->unsignedInteger('retry_count')->default(0)->comment('Number of processing attempts');

            // Indexes
            $table->index('patient_id');
            $table->index('status');
            $table->index('created_at');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merge_agency_deletion_data');
    }
}
