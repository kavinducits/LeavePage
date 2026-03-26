<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('study_leave_extensions', function (Blueprint $table) {
            $columns = [
                'acad_est_head_not_recommend_reason',
                'hod_not_recommend_reason',
                'dean_not_recommended_reason',
                'vc_not_recommend_reason',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('study_leave_extensions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_leave_extensions', function (Blueprint $table) {
            if (!Schema::hasColumn('study_leave_extensions', 'acad_est_head_not_recommend_reason')) {
                $table->text('acad_est_head_not_recommend_reason')->nullable()->after('acad_est_head_recommend');
            }

            if (!Schema::hasColumn('study_leave_extensions', 'hod_not_recommend_reason')) {
                $table->text('hod_not_recommend_reason')->nullable()->after('hod_recommend');
            }

            if (!Schema::hasColumn('study_leave_extensions', 'dean_not_recommended_reason')) {
                $table->text('dean_not_recommended_reason')->nullable()->after('dean_recommend');
            }

            if (!Schema::hasColumn('study_leave_extensions', 'vc_not_recommend_reason')) {
                $table->text('vc_not_recommend_reason')->nullable()->after('vc_recommend');
            }
        });
    }
};
