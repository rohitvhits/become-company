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
        Schema::table('payment_logs', function (Blueprint $table) {
            $table->unsignedInteger('patient_id')->nullable()->after('id');
            $table->enum('status', ['Pending', 'Verified'])->default('Pending')->after('initials');

            $table->index('patient_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_logs', function (Blueprint $table) {
            $table->dropIndex(['patient_id']);
            $table->dropIndex(['status']);
            $table->dropColumn(['patient_id', 'status']);
        });
    }
};
