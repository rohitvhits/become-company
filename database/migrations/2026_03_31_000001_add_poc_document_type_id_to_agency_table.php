<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPocDocumentTypeIdToAgencyTable extends Migration
{
    public function up()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->unsignedInteger('poc_document_type_id')->nullable()->after('view_payment_report');
        });
    }

    public function down()
    {
        Schema::table('agency', function (Blueprint $table) {
            $table->dropColumn('poc_document_type_id');
        });
    }
}
