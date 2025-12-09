<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyLeaveExtension extends Model
{
    //
    protected$fillable = [
        'study_leave_id',
        'old_end_date',
        'new_end_date',
        'reason_for_extension',
        'leave_type',
        'leave_payment_type',
        'funding_type',
        'scholarship_source',
        'scholarship_amount',
        'project_name',
        'loan_handling',
        'ma_empno',
        'ma_remarks',
        'hod_empno',
        'hod_adequate_staff_available',
        'hod_teaching_covered',
        'hod_service_period',
        'hod_recommend',
        'hod_not_recommend_reason',
        'hod_remarks',
        'dean_leave_recommendation_status',
        'dean_not_recommended_reason',
        'vc_empno',
        'vc_recommend_submit_to_committee',
        'vc_council_covering_approval_status',
        'status_id',
    ];
}
