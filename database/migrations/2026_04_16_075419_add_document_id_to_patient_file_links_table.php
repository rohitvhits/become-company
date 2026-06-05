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
        Schema::table('patient_file_links', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id')->nullable()->after('agency_file_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_file_links', function (Blueprint $table) {
            $table->dropColumn('document_id');
        });
    }
};
