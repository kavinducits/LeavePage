<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyLeaveApproval extends Model
{
    //
    protected $fillable = [
        'study_leave_id',
        'library_and_property_handling',
        'loan_handling',
        'ma_empno',
        'registrar_empno',
        'registrar_recommendation',
        'registrar_not_recommend_reason',
        'registrar_remarks',
        'hod_empno',
        'hod_staff_adequacy_recommendation',
        'hod_teaching_coverage_recommendation',
        'hod_one_year_service_verification',
        'hod_leave_recommendation_status',
        'hod_not_recommended_reason',
        'dean_leave_recommendation_status',
        'dean_not_recommended_reason',
        'vc_empno',
        'vc_recommend_submit_to_committee',
        'vc_council_covering_approval_status',
        'vc_not_approve_reason',
        'vc_remarks',
        'dean_empno',
        'dean_remarks',
        'status_id',
        'is_draft',
        'is_completed',
        'ma_remarks',
        'current_step',
    ];
    
}
