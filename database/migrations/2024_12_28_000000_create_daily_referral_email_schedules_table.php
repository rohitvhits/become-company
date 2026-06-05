<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyReferralEmailSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_referral_email_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Schedule name/description
            $table->json('recipients'); // Email recipients
            $table->json('cc_emails')->nullable(); // CC recipients
            $table->string('email_subject'); // Email subject template
            $table->time('send_time'); // Time to send (HH:MM)
            $table->json('send_days')->default('["monday","tuesday","wednesday","thursday","friday"]'); // Days to send
            $table->boolean('is_active')->default(true); // Active status
            $table->date('start_date')->nullable(); // When to start sending
            $table->date('end_date')->nullable(); // When to stop sending (optional)
            $table->timestamp('last_sent_at')->nullable(); // Last successful send
            $table->integer('timezone_offset')->default(0); // Timezone offset in minutes
            $table->text('notes')->nullable(); // Additional notes
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['is_active', 'send_time']);
            $table->index(['send_days', 'is_active']);
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_referral_email_schedules');
    }
}