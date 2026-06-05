<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('agency_id');
            $table->enum('type', ['uploaded_pdf', 'quick', 'detailed']);
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue'])->default('draft');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->date('due_date');
            $table->text('terms_conditions')->nullable();
            $table->string('pdf_path')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // $table->foreign('agency_id')->references('id')->on('agency')->onDelete('cascade');
            // $table->foreign('created_by')->references('id')->on(table: 'users')->onDelete('cascade');

            $table->index(['agency_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index('invoice_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};