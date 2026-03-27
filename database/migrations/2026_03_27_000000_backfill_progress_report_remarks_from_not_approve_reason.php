<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            UPDATE study_leave_progress_reports
            SET registrar_remarks = registrar_not_approve_reason
            WHERE (registrar_remarks IS NULL OR TRIM(registrar_remarks) = '')
              AND registrar_not_approve_reason IS NOT NULL
              AND TRIM(registrar_not_approve_reason) <> ''
        ");

        DB::statement("
            UPDATE study_leave_progress_reports
            SET hod_remarks = hod_not_approve_reason
            WHERE (hod_remarks IS NULL OR TRIM(hod_remarks) = '')
              AND hod_not_approve_reason IS NOT NULL
              AND TRIM(hod_not_approve_reason) <> ''
        ");

        DB::statement("
            UPDATE study_leave_progress_reports
            SET dean_remarks = dean_not_approve_reason
            WHERE (dean_remarks IS NULL OR TRIM(dean_remarks) = '')
              AND dean_not_approve_reason IS NOT NULL
              AND TRIM(dean_not_approve_reason) <> ''
        ");

        DB::statement("
            UPDATE study_leave_progress_reports
            SET vc_remarks = vc_not_approve_reason
            WHERE (vc_remarks IS NULL OR TRIM(vc_remarks) = '')
              AND vc_not_approve_reason IS NOT NULL
              AND TRIM(vc_not_approve_reason) <> ''
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: backfill is intentionally non-reversible.
    }
};
