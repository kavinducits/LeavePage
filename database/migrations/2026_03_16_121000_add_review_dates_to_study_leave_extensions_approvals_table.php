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
        Schema::table('study_leave_extensions_approvals', function (Blueprint $table) {
            if (!Schema::hasColumn('study_leave_extensions_approvals', 'ma_reviewed_date')) {
                $table->date('ma_reviewed_date')->nullable();
            }

            if (!Schema::hasColumn('study_leave_extensions_approvals', 'ma_finalized_date')) {
                $table->date('ma_finalized_date')->nullable();
            }

            if (!Schema::hasColumn('study_leave_extensions_approvals', 'registrar_reviewed_date')) {
                $table->date('registrar_reviewed_date')->nullable();
            }

            if (!Schema::hasColumn('study_leave_extensions_approvals', 'hod_reviewed_date')) {
                $table->date('hod_reviewed_date')->nullable();
            }

            if (!Schema::hasColumn('study_leave_extensions_approvals', 'vc_reviewed_date')) {
                $table->date('vc_reviewed_date')->nullable();
            }

            if (!Schema::hasColumn('study_leave_extensions_approvals', 'dean_reviewed_date')) {
                $table->date('dean_reviewed_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_leave_extensions_approvals', function (Blueprint $table) {
            if (Schema::hasColumn('study_leave_extensions_approvals', 'ma_reviewed_date')) {
                $table->dropColumn('ma_reviewed_date');
            }

            if (Schema::hasColumn('study_leave_extensions_approvals', 'ma_finalized_date')) {
                $table->dropColumn('ma_finalized_date');
            }

            if (Schema::hasColumn('study_leave_extensions_approvals', 'registrar_reviewed_date')) {
                $table->dropColumn('registrar_reviewed_date');
            }

            if (Schema::hasColumn('study_leave_extensions_approvals', 'hod_reviewed_date')) {
                $table->dropColumn('hod_reviewed_date');
            }

            if (Schema::hasColumn('study_leave_extensions_approvals', 'vc_reviewed_date')) {
                $table->dropColumn('vc_reviewed_date');
            }

            if (Schema::hasColumn('study_leave_extensions_approvals', 'dean_reviewed_date')) {
                $table->dropColumn('dean_reviewed_date');
            }
        });
    }
};
