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
        if (!Schema::hasColumn('agency', 'domain_config_id')) {
            Schema::table('agency', function (Blueprint $table) {
                $table->unsignedBigInteger('domain_config_id')->nullable()->after('id');
            });
        }
    }

    public function down()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->dropColumn('domain_config_id');
        });
    }
};
