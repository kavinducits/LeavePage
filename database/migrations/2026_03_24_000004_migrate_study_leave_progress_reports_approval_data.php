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
        DB::statement('
            UPDATE study_leave_progress_reports p
            LEFT JOIN study_leave_progress_reports_approval a ON p.id = a.study_leave_progress_report_id
            SET
                p.approval_status_id = a.approval_status_id,
                p.ma_empno = a.ma_empno,
                p.registrar_empno = a.registrar_empno,
                p.registrar_approval_status = a.registrar_approval_status,
                p.registrar_not_approve_reason = a.registrar_not_approve_reason,
                p.registrar_remarks = a.registrar_remarks,
                p.hod_empno = a.hod_empno,
                p.hod_approval_status = a.hod_approval_status,
                p.hod_not_approve_reason = a.hod_not_approve_reason,
                p.hod_remarks = a.hod_remarks,
                p.dean_empno = a.dean_empno,
                p.dean_approval_status = a.dean_approval_status,
                p.dean_not_approve_reason = a.dean_not_approve_reason,
                p.dean_remarks = a.dean_remarks,
                p.vc_empno = a.vc_empno,
                p.vc_approval_status = a.vc_approval_status,
                p.vc_not_approve_reason = a.vc_not_approve_reason,
                p.vc_remarks = a.vc_remarks,
                p.ma_reviewed_date = a.ma_reviewed_date,
                p.ma_finalized_date = a.ma_finalized_date,
                p.registrar_reviewed_date = a.registrar_reviewed_date,
                p.hod_reviewed_date = a.hod_reviewed_date,
                p.dean_reviewed_date = a.dean_reviewed_date,
                p.vc_reviewed_date = a.vc_reviewed_date
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('
            UPDATE study_leave_progress_reports
            SET
                approval_status_id = NULL,
                ma_empno = NULL,
                registrar_empno = NULL,
                registrar_approval_status = NULL,
                registrar_not_approve_reason = NULL,
                registrar_remarks = NULL,
                hod_empno = NULL,
                hod_approval_status = NULL,
                hod_not_approve_reason = NULL,
                hod_remarks = NULL,
                dean_empno = NULL,
                dean_approval_status = NULL,
                dean_not_approve_reason = NULL,
                dean_remarks = NULL,
                vc_empno = NULL,
                vc_approval_status = NULL,
                vc_not_approve_reason = NULL,
                vc_remarks = NULL,
                ma_reviewed_date = NULL,
                ma_finalized_date = NULL,
                registrar_reviewed_date = NULL,
                hod_reviewed_date = NULL,
                dean_reviewed_date = NULL,
                vc_reviewed_date = NULL
        ');
    }
};
