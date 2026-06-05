<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStreamlinedEsignColumnsToDocumentSentReport extends Migration
{
    public function up()
    {
        Schema::table('document_sent_report', function (Blueprint $table) {
            $table->string('streamlined_action', 30)->nullable()->after('pdf_status_reason');
            $table->tinyInteger('auto_notified')->default(0)->after('streamlined_action');
            $table->timestamp('auto_notified_at')->nullable()->after('auto_notified');
        });
    }

    public function down()
    {
        Schema::table('document_sent_report', function (Blueprint $table) {
            $table->dropColumn(['streamlined_action', 'auto_notified', 'auto_notified_at']);
        });
    }
}
