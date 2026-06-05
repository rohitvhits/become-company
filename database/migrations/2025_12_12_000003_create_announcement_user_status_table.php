<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementUserStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_user_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('announcement_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('is_read', ['0', '1'])->default('0');
            $table->datetime('read_at')->nullable();
            $table->enum('del_flag', ['Y', 'N'])->default('N');
            $table->datetime('created_date')->nullable();
            $table->datetime('updated_date')->nullable();

            $table->foreign('announcement_id')
                  ->references('id')
                  ->on('announcements_master')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Unique constraint to prevent duplicate entries
            $table->unique(['announcement_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcement_user_status');
    }
}
