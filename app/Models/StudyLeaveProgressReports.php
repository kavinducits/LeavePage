<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyLeaveProgressReports extends Model
{
    protected $fillable = [
        'study_leave_id',
        'due_date',
        'submitted_date',
        'remark',
        'document_path',
        'status_id',
    ];

    /**
     * Get the study leave that owns the progress report
     */
    public function studyLeave()
    {
        return $this->belongsTo(StudyLeave::class, 'study_leave_id');
    }

    /**
     * Get the approval for this progress report
     */
    public function approval()
    {
        return $this->hasOne(StudyLeaveProgressReportsApproval::class, 'study_leave_progress_report_id');
    }
}
