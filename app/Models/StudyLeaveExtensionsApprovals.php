<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyLeaveExtensionsApprovals extends Model
{
    //
    protected  $fillable = [
        'study_leave_extension_id',
        'ma_empno',
        'ma_recommend',
        'ma_not_recommend_reason',
        'ma_remarks',
        'acad_est_head_empno',
        'acad_est_head_recommend',
        'acad_est_head_not_recommend_reason',
        'acad_est_head_remarks',
        'hod_empno',
        'hod_recommend',
        'hod_not_recommend_reason',
        'hod_remarks',
        'dean_empno',
        'dean_recommend',
        'dean_not_recommended_reason',
        'dean_remark',
        'vc_empno',
        'vc_recommend',
        'vc_not_recommend_reason',
        'vc_remarks',
        'status_id'
    ];
}
