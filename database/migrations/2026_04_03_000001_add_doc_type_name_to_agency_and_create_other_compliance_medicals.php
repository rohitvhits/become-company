<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocTypeNameToAgencyAndCreateOtherComplianceMedicals extends Migration
{
    public function up()
    {
        // Add name columns to agency table so names show on page load
        Schema::table('agency', function (Blueprint $table) {
            $table->string('supervision_document_type_name', 255)->nullable()->after('supervision_document_type_id');
            $table->string('medical_name', 255)->nullable()->after('medical_id');
        });

        // New table for multiple medical IDs per agency (All Other Compliance)
        Schema::create('agency_other_compliance_medicals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id');
            $table->integer('medical_id');
            $table->string('medical_name', 255)->nullable();
            $table->char('del_flag', 1)->default('N');
            $table->timestamps();

            $table->index(['agency_id', 'del_flag']);
        });
    }

    public function down()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->dropColumn(['supervision_document_type_name', 'medical_name']);
        });

        Schema::dropIfExists('agency_other_compliance_medicals');
    }
}
