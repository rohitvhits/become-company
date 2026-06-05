<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements_master', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('steps_summary')->nullable();
            $table->enum('is_published', ['0', '1'])->default('0');
            $table->enum('del_flag', ['Y', 'N'])->default('N');
            $table->datetime('created_date')->nullable();
            $table->string('created_by')->nullable();
            $table->datetime('updated_date')->nullable();
            $table->string('updated_by')->nullable();
            $table->datetime('deleted_date')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcements_master');
    }
}
