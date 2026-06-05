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
        Schema::table('template_master', function (Blueprint $table) {
            $table->string('esign_workflow', 50)->default('normal')->after('esign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_master', function (Blueprint $table) {
            $table->dropColumn('esign_workflow');
        });
    }
};
