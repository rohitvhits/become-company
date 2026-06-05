<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->tinyInteger('enable_file_manager')->default(0)->after('view_payment_report');
        });
    }

    public function down()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->dropColumn('enable_file_manager');
        });
    }
};
