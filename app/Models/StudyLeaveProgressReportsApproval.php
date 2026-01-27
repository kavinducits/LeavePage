<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyLeaveProgressReportsApproval extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'study_leave_progress_reports_approval';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'approval_status_id',
        'study_leave_progress_report_id',
        'ma_empno',
        'registrar_empno',
        'registrar_approval_status',
        'registrar_not_approve_reason',
        'registrar_remarks',
        'hod_empno',
        'hod_approval_status',
        'hod_not_approve_reason',
        'hod_remarks',
        'dean_empno',
        'dean_approval_status',
        'dean_not_approve_reason',
        'dean_remarks',
        'vc_empno',
        'vc_approval_status',
        'vc_not_approve_reason',
        'vc_remarks',
    ];

    /**
     * Get the progress report that this approval belongs to
     */
    public function progressReport()
    {
        return $this->belongsTo(StudyLeaveProgressReports::class, 'study_leave_progress_report_id');
    }

    /**
     * Check if the approval is approved
     */
    public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if the approval is not approved
     */
    public function isNotApproved()
    {
        return $this->approval_status === 'not_approved';
    }

    /**
     * Check if the approval is pending
     */
    public function isPending()
    {
        return $this->approval_status === 'pending';
    }
}
