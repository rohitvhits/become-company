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
            $table->string('import_status')->nullable()->after('status');
            $table->dateTime('approved_date')->nullable()->after('import_status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_date');
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
            $table->dropColumn(['import_status', 'approved_date', 'approved_by']);
        });
    }
};
