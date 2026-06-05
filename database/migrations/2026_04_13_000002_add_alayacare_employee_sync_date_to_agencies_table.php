<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->timestamp('alayacare_employee_sync_date')->nullable()->after('alaycare_status');
        });
    }

    public function down()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->dropColumn('alayacare_employee_sync_date');
        });
    }
};
