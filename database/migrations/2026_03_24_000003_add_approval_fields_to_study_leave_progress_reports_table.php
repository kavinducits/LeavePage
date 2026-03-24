<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('study_leave_progress_reports', function (Blueprint $table) {
            $table->unsignedInteger('approval_status_id')->nullable()->after('status_id');

            $table->string('ma_empno')->nullable()->after('approval_status_id');
            $table->string('registrar_empno')->nullable()->after('ma_empno');
            $table->unsignedInteger('registrar_approval_status')->nullable()->after('registrar_empno');
            $table->text('registrar_not_approve_reason')->nullable()->after('registrar_approval_status');
            $table->text('registrar_remarks')->nullable()->after('registrar_not_approve_reason');

            $table->string('hod_empno')->nullable()->after('registrar_remarks');
            $table->unsignedInteger('hod_approval_status')->nullable()->after('hod_empno');
            $table->text('hod_not_approve_reason')->nullable()->after('hod_approval_status');
            $table->text('hod_remarks')->nullable()->after('hod_not_approve_reason');

            $table->string('dean_empno')->nullable()->after('hod_remarks');
            $table->unsignedInteger('dean_approval_status')->nullable()->after('dean_empno');
            $table->text('dean_not_approve_reason')->nullable()->after('dean_approval_status');
            $table->text('dean_remarks')->nullable()->after('dean_not_approve_reason');

            $table->string('vc_empno')->nullable()->after('dean_remarks');
            $table->unsignedInteger('vc_approval_status')->nullable()->after('vc_empno');
            $table->text('vc_not_approve_reason')->nullable()->after('vc_approval_status');
            $table->text('vc_remarks')->nullable()->after('vc_not_approve_reason');

            $table->date('ma_reviewed_date')->nullable()->after('vc_remarks');
            $table->date('ma_finalized_date')->nullable()->after('ma_reviewed_date');
            $table->date('registrar_reviewed_date')->nullable()->after('ma_finalized_date');
            $table->date('hod_reviewed_date')->nullable()->after('registrar_reviewed_date');
            $table->date('dean_reviewed_date')->nullable()->after('hod_reviewed_date');
            $table->date('vc_reviewed_date')->nullable()->after('dean_reviewed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_leave_progress_reports', function (Blueprint $table) {
            $table->dropColumn([
                'approval_status_id',
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
                'ma_reviewed_date',
                'ma_finalized_date',
                'registrar_reviewed_date',
                'hod_reviewed_date',
                'dean_reviewed_date',
                'vc_reviewed_date',
            ]);
        });
    }
};
