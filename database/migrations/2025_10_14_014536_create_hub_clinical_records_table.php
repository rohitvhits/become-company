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
        Schema::create('hub_clinical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hub_record_id');
            $table->string('name');
            $table->enum('pdf_type', ['medical_visit', 'medical_note']);
            $table->text('notes')->nullable();
            $table->date('visit_date')->nullable();
            $table->string('doctor_name')->nullable();
            $table->date('excuse_from')->nullable();
            $table->date('excuse_to')->nullable();
            $table->text('pdf_content');
            $table->string('pdf_path')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hub_clinical_records');
    }
};
