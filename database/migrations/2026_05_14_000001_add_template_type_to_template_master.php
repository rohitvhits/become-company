<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTemplateTypeToTemplateMaster extends Migration
{
    public function up()
    {
        Schema::table('template_master', function (Blueprint $table) {
            $table->string('template_type')->nullable()->after('resolution_update');
        });
    }

    public function down()
    {
        Schema::table('template_master', function (Blueprint $table) {
            $table->dropColumn('template_type');
        });
    }
};
