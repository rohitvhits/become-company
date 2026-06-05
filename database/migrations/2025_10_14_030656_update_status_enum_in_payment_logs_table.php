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
        // Use raw SQL to modify the ENUM column
        DB::statement("ALTER TABLE payment_logs MODIFY COLUMN status ENUM('draft', 'Pending', 'Verified', 'bill') DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to original ENUM values
        DB::statement("ALTER TABLE payment_logs MODIFY COLUMN status ENUM('Pending', 'Verified') DEFAULT 'Pending'");
    }
};
