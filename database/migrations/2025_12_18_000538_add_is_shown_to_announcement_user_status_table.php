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
        Schema::table('announcement_user_status', function (Blueprint $table) {
            $table->enum('is_shown', ['0', '1'])->default('0')->after('is_read');
            $table->datetime('shown_at')->nullable()->after('is_shown');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcement_user_status', function (Blueprint $table) {
            $table->dropColumn(['is_shown', 'shown_at']);
        });
    }
};
