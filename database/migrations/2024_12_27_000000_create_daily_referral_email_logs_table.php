<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyReferralEmailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_referral_email_logs', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->string('email_subject');
            $table->json('email_recipients');
            $table->json('report_data');
            $table->longText('email_content');
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(['report_date', 'status']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_referral_email_logs');
    }
}