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
        Schema::table('appointment_import_file', function (Blueprint $table) {
            // Add columns for progress tracking
            $table->integer('processed_records')->default(0)->after('total_record');
            $table->integer('failed_records')->default(0)->after('processed_records');
            $table->dateTime('completed_at')->nullable()->after('failed_records');
            $table->text('error_message')->nullable()->after('completed_at');
            $table->integer('success_records')->default(0)->after('error_message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointment_import_file', function (Blueprint $table) {
            // Remove progress tracking columns
            $table->dropColumn(['processed_records', 'failed_records', 'completed_at', 'error_message','success_records']);
        });
    }
};
