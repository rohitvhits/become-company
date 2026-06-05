<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowInPortalToFieldMastersTable extends Migration
{
    public function up()
    {
        Schema::table('field_masters', function (Blueprint $table) {
            $table->boolean('show_in_portal')->default(0)->after('set_character_limit');
        });
    }

    public function down()
    {
        Schema::table('field_masters', function (Blueprint $table) {
            $table->dropColumn('show_in_portal');
        });
    }
}
