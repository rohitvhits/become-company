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
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('dob')->nullable();
            $table->string('patient_id')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('service_type')->nullable()->comment('Initial or Annual');
            $table->text('services')->nullable();
            $table->string('ppd_q')->nullable()->comment('Per patient/day or quarterly');
            $table->decimal('bill', 10, 2)->default(0);
            $table->decimal('cash', 10, 2)->default(0);
            $table->decimal('card', 10, 2)->default(0);
            $table->decimal('insurance', 10, 2)->default(0);
            $table->string('location')->nullable();
            $table->string('initials')->nullable();
            $table->timestamps();

            // Indexes for better search performance
            $table->index('name');
            $table->index('vendor_name');
            $table->index('portal_id');
            $table->index('location');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_logs');
    }
};
