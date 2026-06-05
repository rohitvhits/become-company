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
        Schema::table('appointment_portal_merge_logs', function (Blueprint $table) {
            // Root patient ID - the ultimate parent in the merge chain
            $table->unsignedBigInteger('root_patient_id')->nullable()->after('merge_patient_id');

            // Direct parent patient ID - for tracking immediate parent in chain
            $table->unsignedBigInteger('parent_patient_id')->nullable()->after('root_patient_id');

            // Merge depth - level in the merge hierarchy (0 = root, 1 = first child, etc.)
            $table->integer('merge_depth')->default(0)->after('parent_patient_id');

            // Merge path - full chain from root to current (e.g., "1,5,10")
            $table->text('merge_path')->nullable()->after('merge_depth');

            // Add indexes for performance
            $table->index('root_patient_id', 'idx_root_patient_id');
            $table->index('parent_patient_id', 'idx_parent_patient_id');
            $table->index(['main_patient_id', 'del_flag'], 'idx_main_patient_del_flag');
            $table->index(['merge_patient_id', 'del_flag'], 'idx_merge_patient_del_flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointment_portal_merge_logs', function (Blueprint $table) {
            $table->dropIndex('idx_root_patient_id');
            $table->dropIndex('idx_parent_patient_id');
            $table->dropIndex('idx_main_patient_del_flag');
            $table->dropIndex('idx_merge_patient_del_flag');

            $table->dropColumn([
                'root_patient_id',
                'parent_patient_id',
                'merge_depth',
                'merge_path'
            ]);
        });
    }
};
