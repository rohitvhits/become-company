<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task_health_flags', function (Blueprint $table) {
            $table->tinyInteger('assessment_check')->default(0)->after('supervision_check_date')->comment('Patient Assessment doc uploaded');
            $table->unsignedBigInteger('assessment_check_by')->nullable()->after('assessment_check');
            $table->dateTime('assessment_check_date')->nullable()->after('assessment_check_by');

            $table->tinyInteger('kardex_check')->default(0)->after('assessment_check_date')->comment('Emergency Kardex doc uploaded');
            $table->unsignedBigInteger('kardex_check_by')->nullable()->after('kardex_check');
            $table->dateTime('kardex_check_date')->nullable()->after('kardex_check_by');
        });
    }

    public function down()
    {
        Schema::table('task_health_flags', function (Blueprint $table) {
            $table->dropColumn([
                'assessment_check', 'assessment_check_by', 'assessment_check_date',
                'kardex_check',     'kardex_check_by',     'kardex_check_date',
            ]);
        });
    }
};
