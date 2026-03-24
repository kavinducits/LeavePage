<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate data from study_leave_extensions_approvals to study_leave_extensions
        DB::statement('
            UPDATE study_leave_extensions e
            LEFT JOIN study_leave_extensions_approvals a ON e.id = a.study_leave_extension_id
            SET
                e.ma_empno = a.ma_empno,
                e.ma_recommend = a.ma_recommend,
                e.ma_not_recommend_reason = a.ma_not_recommend_reason,
                e.ma_remarks = a.ma_remarks,
                e.ma_reviewed_date = a.ma_reviewed_date,
                e.ma_finalized_date = a.ma_finalized_date,
                e.acad_est_head_empno = a.acad_est_head_empno,
                e.acad_est_head_recommend = a.acad_est_head_recommend,
                e.acad_est_head_not_recommend_reason = a.acad_est_head_not_recommend_reason,
                e.acad_est_head_remarks = a.acad_est_head_remarks,
                e.hod_empno = a.hod_empno,
                e.hod_recommend = a.hod_recommend,
                e.hod_not_recommend_reason = a.hod_not_recommend_reason,
                e.hod_remarks = a.hod_remarks,
                e.hod_reviewed_date = a.hod_reviewed_date,
                e.dean_empno = a.dean_empno,
                e.dean_recommend = a.dean_recommend,
                e.dean_not_recommended_reason = a.dean_not_recommended_reason,
                e.dean_remark = a.dean_remark,
                e.dean_reviewed_date = a.dean_reviewed_date,
                e.vc_empno = a.vc_empno,
                e.vc_recommend = a.vc_recommend,
                e.vc_not_recommend_reason = a.vc_not_recommend_reason,
                e.vc_remarks = a.vc_remarks,
                e.vc_reviewed_date = a.vc_reviewed_date,
                e.registrar_reviewed_date = a.registrar_reviewed_date,
                e.status_id = a.status_id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all migrated approval fields to NULL if rolling back
        DB::statement('
            UPDATE study_leave_extensions
            SET
                ma_empno = NULL,
                ma_recommend = NULL,
                ma_not_recommend_reason = NULL,
                ma_remarks = NULL,
                ma_reviewed_date = NULL,
                ma_finalized_date = NULL,
                acad_est_head_empno = NULL,
                acad_est_head_recommend = NULL,
                acad_est_head_not_recommend_reason = NULL,
                acad_est_head_remarks = NULL,
                hod_empno = NULL,
                hod_recommend = NULL,
                hod_not_recommend_reason = NULL,
                hod_remarks = NULL,
                hod_reviewed_date = NULL,
                dean_empno = NULL,
                dean_recommend = NULL,
                dean_not_recommended_reason = NULL,
                dean_remark = NULL,
                dean_reviewed_date = NULL,
                vc_empno = NULL,
                vc_recommend = NULL,
                vc_not_recommend_reason = NULL,
                vc_remarks = NULL,
                vc_reviewed_date = NULL,
                registrar_reviewed_date = NULL,
                status_id = NULL
        ');
    }
};
