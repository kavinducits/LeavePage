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
        'approval_status_id',
        'ma_empno',
        'ma_remark',
        'registrar_empno',
        'registrar_approval_status',
        'registrar_remarks',
        'hod_empno',
        'hod_approval_status',
        'hod_remarks',
        'dean_empno',
        'dean_approval_status',
        'dean_remarks',
        'vc_empno',
        'vc_approval_status',
        'vc_remarks',
        'ma_reviewed_date',
        'ma_finalized_date',
        'registrar_reviewed_date',
        'hod_reviewed_date',
        'dean_reviewed_date',
        'vc_reviewed_date',
    ];

    /**
     * Get the study leave that owns the progress report
     */
    public function studyLeave()
    {
        return $this->belongsTo(StudyLeave::class, 'study_leave_id');
    }
}
