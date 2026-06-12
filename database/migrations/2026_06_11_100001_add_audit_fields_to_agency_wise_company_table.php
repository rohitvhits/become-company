<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('agency_wise_company', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('created_at');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
            $table->timestamp('deleted_at')->nullable()->after('deleted_by');
            $table->tinyInteger('delete_flag')->default(0)->after('deleted_at');
        });
    }

    public function down()
    {
        Schema::table('agency_wise_company', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'deleted_by', 'deleted_at', 'delete_flag']);
        });
    }
};
