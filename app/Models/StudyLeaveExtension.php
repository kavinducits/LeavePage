<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyLeaveExtension extends Model
{
    //
    protected $fillable = [
        'study_leave_id',
        'old_end_date',
        'new_end_date',
        'extension_payment_type',
        'reason_for_extension',
        'leave_type',
        'leave_payment_type',
        'funding_type',
        'scholarship_source',
        'scholarship_amount',
        'project_name',
        'loan_handling',
        // MA Approval Fields
        'ma_empno',
        'ma_recommend',
        'ma_not_recommend_reason',
        'ma_remarks',
        'ma_reviewed_date',
        'ma_finalized_date',
        // Registrar Fields
        'registrar_empno',
        'registrar_remarks',
        'registrar_reviewed_date',
        // Academic Establishment Head Fields
        'acad_est_head_empno',
        'acad_est_head_recommend',
        'acad_est_head_remarks',
        // HOD Fields
        'hod_empno',
        'hod_adequate_staff_available',
        'hod_teaching_covered',
        'hod_service_period',
        'hod_recommend',
        'hod_remarks',
        'hod_reviewed_date',
        // Dean Fields
        'dean_leave_recommendation_status',
        'dean_empno',
        'dean_recommend',
        'dean_remark',
        'dean_reviewed_date',
        // VC Fields
        'vc_empno',
        'vc_recommend',
        'vc_remarks',
        'vc_reviewed_date',
        // Status
        'status_id',
    ];
}
