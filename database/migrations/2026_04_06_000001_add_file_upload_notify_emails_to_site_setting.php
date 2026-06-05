<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileUploadNotifyEmailsToSiteSetting extends Migration
{
    public function up()
    {
        Schema::table('site_setting', function (Blueprint $table) {
            $table->text('mdo_upload_notify_email')->nullable()->after('hub_nybest_email');
            $table->text('telehealth_upload_notify_email')->nullable()->after('mdo_upload_notify_email');
        });
    }

    public function down()
    {
        Schema::table('site_setting', function (Blueprint $table) {
            $table->dropColumn(['mdo_upload_notify_email', 'telehealth_upload_notify_email']);
        });
    }
}
