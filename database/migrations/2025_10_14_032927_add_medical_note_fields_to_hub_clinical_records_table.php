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
        Schema::table('hub_clinical_records', function (Blueprint $table) {
            // Medical Note Specific Fields
            $table->string('excuse')->nullable()->after('medications');
            $table->string('work')->nullable()->after('excuse');
            $table->string('school')->nullable()->after('work');
            $table->string('other')->nullable()->after('school');
            $table->string('injury')->nullable()->after('other');
            $table->string('illness')->nullable()->after('injury');
            $table->string('due_to_other')->nullable()->after('illness');
            $table->text('doc_comment')->nullable()->after('due_to_other');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hub_clinical_records', function (Blueprint $table) {
            $table->dropColumn([
                'excuse', 'work', 'school', 'other', 'injury', 'illness', 'due_to_other', 'doc_comment'
            ]);
        });
    }
};
