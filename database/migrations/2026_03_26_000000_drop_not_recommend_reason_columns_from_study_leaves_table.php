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
        Schema::table('study_leaves', function (Blueprint $table) {
            $columns = [
                'registrar_not_recommend_reason',
                'hod_not_recommend_reason',
                'dean_not_recommended_reason',
                'vc_not_approve_reason',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('study_leaves', $column)) {
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
        Schema::table('study_leaves', function (Blueprint $table) {
            if (!Schema::hasColumn('study_leaves', 'registrar_not_recommend_reason')) {
                $table->text('registrar_not_recommend_reason')->nullable()->after('registrar_recommendation');
            }

            if (!Schema::hasColumn('study_leaves', 'hod_not_recommend_reason')) {
                $table->text('hod_not_recommend_reason')->nullable()->after('hod_recommend');
            }

            if (!Schema::hasColumn('study_leaves', 'dean_not_recommended_reason')) {
                $table->text('dean_not_recommended_reason')->nullable()->after('dean_leave_recommendation_status');
            }

            if (!Schema::hasColumn('study_leaves', 'vc_not_approve_reason')) {
                $table->text('vc_not_approve_reason')->nullable()->after('vc_council_covering_approval_status');
            }
        });
    }
};
