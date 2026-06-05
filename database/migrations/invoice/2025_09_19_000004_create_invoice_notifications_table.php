<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->enum('type', ['invoice_sent', 'payment_received', 'reminder', 'overdue']);
            $table->string('sent_to');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->index(['invoice_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_notifications');
    }
};