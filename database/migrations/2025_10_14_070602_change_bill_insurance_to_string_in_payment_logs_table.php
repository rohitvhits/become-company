<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to modify columns without doctrine/dbal
        DB::statement('ALTER TABLE payment_logs MODIFY COLUMN bill VARCHAR(255) NULL');
        DB::statement('ALTER TABLE payment_logs MODIFY COLUMN insurance VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to decimal
        DB::statement('ALTER TABLE payment_logs MODIFY COLUMN bill DECIMAL(10, 2) DEFAULT 0');
        DB::statement('ALTER TABLE payment_logs MODIFY COLUMN insurance DECIMAL(10, 2) DEFAULT 0');
    }
};
