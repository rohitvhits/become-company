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
        Schema::create('patient_auto_call_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auto_call_log_id');
            $table->enum('call_type', ['initial', 'reminder'])->default('initial');
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->string('conversation_id')->nullable();
            $table->longText('transcript')->nullable();
            $table->string('status')->nullable();
            $table->text('call_response')->nullable();
            $table->timestamp('fired_at')->nullable();
            $table->timestamps();

            $table->foreign('auto_call_log_id')->references('id')->on('patient_auto_call_logs')->onDelete('cascade');
            $table->index('auto_call_log_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_auto_call_attempts');
    }
};
