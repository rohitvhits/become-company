<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMdoTelehealthToAgencyFolders extends Migration
{
    public function up()
    {
        Schema::table('agency_folders', function (Blueprint $table) {
            $table->tinyInteger('is_mdo')->default(0)->after('name');
            $table->tinyInteger('is_telehealth')->default(0)->after('is_mdo');
        });
    }

    public function down()
    {
        Schema::table('agency_folders', function (Blueprint $table) {
            $table->dropColumn(['is_mdo', 'is_telehealth']);
        });
    }
}
