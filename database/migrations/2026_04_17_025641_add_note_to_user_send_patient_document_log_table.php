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
        Schema::table('user_send_patient_document_log', function (Blueprint $table) {
            $table->text('note')->nullable()->after('send_back_to_agency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_send_patient_document_log', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
