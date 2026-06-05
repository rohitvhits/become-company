<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMdoTelehealthToUsers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_mdo')->default(0)->after('is_nurse');
            $table->tinyInteger('is_telehealth')->default(0)->after('is_mdo');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_mdo', 'is_telehealth']);
        });
    }
}
