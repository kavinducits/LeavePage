<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyLeave extends Model
{
    //
   protected $fillable = [
    'reference_no',
        'empno',
        'academic_year',
        'passport_no',
        'passport_validity',
        'leave_type',
        'leave_payment_type',
        'study_leave_from',
        'study_leave_to',
        'degree_title',
        'university_institute',
        'country',
        'field_of_study',
        'study_program_details',
        'funding_type',
        'any_other_details',
        'air_passage_request',
        'warm_cloth_allowance_request',
        'scholarship_source',
        'scholarship_amount',
        'project_name',
        'nominee_teaching_empno',
        'nominee_admin_empno',
        'nominee_other_empno',
        'library_and_property_handling',
        'loan_handling',
        'ma_empno',
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
        'self_funding_declaration',
        'placement_letter',
        'is_completed',
        'ma_remarks',
        'current_step',
    ];

    /**
     * Get the progress reports for the study leave
     */
    public function progressReports()
    {
        return $this->hasMany(StudyLeaveProgressReports::class, 'study_leave_id');
    }

    /**
     * Get the approval record for the study leave
     */
    public function studyLeaveApproval()
    {
        return $this->hasOne(StudyLeaveApproval::class, 'study_leave_id');
    }
}
