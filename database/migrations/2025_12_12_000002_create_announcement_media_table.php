<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('announcement_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->enum('media_type', ['photo', 'video']);
            $table->integer('sort_order')->default(0);
            $table->enum('del_flag', ['Y', 'N'])->default('N');
            $table->datetime('created_date')->nullable();
            $table->string('created_by')->nullable();
            $table->datetime('deleted_date')->nullable();
            $table->string('deleted_by')->nullable();

            $table->foreign('announcement_id')
                  ->references('id')
                  ->on('announcements_master')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcement_media');
    }
}
