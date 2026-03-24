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
        Schema::table('study_leaves', function (Blueprint $table) {
            if (!Schema::hasColumn('study_leaves', 'status_id')) {
                $table->unsignedInteger('status_id')->nullable()->after('current_step');
            }

            if (!Schema::hasColumn('study_leaves', 'is_completed')) {
                $table->boolean('is_completed')->nullable()->after('status_id');
            }

            $columns = [
                'ma_empno' => fn () => $table->string('ma_empno')->nullable(),
                'ma_remarks' => fn () => $table->text('ma_remarks')->nullable(),
                'ma_approve_leave_committee' => fn () => $table->integer('ma_approve_leave_committee')->nullable(),
                'ma_leave_committee_number' => fn () => $table->string('ma_leave_committee_number')->nullable(),
                'ma_leave_committee_date' => fn () => $table->date('ma_leave_committee_date')->nullable(),
                'ma_approve_council' => fn () => $table->integer('ma_approve_council')->nullable(),
                'ma_council_number' => fn () => $table->string('ma_council_number')->nullable(),
                'ma_council_date' => fn () => $table->date('ma_council_date')->nullable(),
                'ma_reviewed_date' => fn () => $table->date('ma_reviewed_date')->nullable(),
                'ma_finalized_date' => fn () => $table->date('ma_finalized_date')->nullable(),
                'registrar_empno' => fn () => $table->string('registrar_empno')->nullable(),
                'registrar_recommendation' => fn () => $table->integer('registrar_recommendation')->nullable(),
                'registrar_not_recommend_reason' => fn () => $table->text('registrar_not_recommend_reason')->nullable(),
                'registrar_remarks' => fn () => $table->text('registrar_remarks')->nullable(),
                'registrar_reviewed_date' => fn () => $table->date('registrar_reviewed_date')->nullable(),
                'hod_empno' => fn () => $table->string('hod_empno')->nullable(),
                'hod_adequate_staff_available' => fn () => $table->integer('hod_adequate_staff_available')->nullable(),
                'hod_teaching_covered' => fn () => $table->integer('hod_teaching_covered')->nullable(),
                'hod_service_period' => fn () => $table->integer('hod_service_period')->nullable(),
                'hod_recommend' => fn () => $table->integer('hod_recommend')->nullable(),
                'hod_not_recommend_reason' => fn () => $table->text('hod_not_recommend_reason')->nullable(),
                'hod_remarks' => fn () => $table->text('hod_remarks')->nullable(),
                'hod_reviewed_date' => fn () => $table->date('hod_reviewed_date')->nullable(),
                'dean_empno' => fn () => $table->string('dean_empno')->nullable(),
                'dean_leave_recommendation_status' => fn () => $table->integer('dean_leave_recommendation_status')->nullable(),
                'dean_not_recommended_reason' => fn () => $table->text('dean_not_recommended_reason')->nullable(),
                'dean_remarks' => fn () => $table->text('dean_remarks')->nullable(),
                'dean_reviewed_date' => fn () => $table->date('dean_reviewed_date')->nullable(),
                'vc_empno' => fn () => $table->string('vc_empno')->nullable(),
                'vc_recommend_submit_to_committee' => fn () => $table->integer('vc_recommend_submit_to_committee')->nullable(),
                'vc_council_covering_approval_status' => fn () => $table->integer('vc_council_covering_approval_status')->nullable(),
                'vc_not_approve_reason' => fn () => $table->text('vc_not_approve_reason')->nullable(),
                'vc_remarks' => fn () => $table->text('vc_remarks')->nullable(),
                'vc_reviewed_date' => fn () => $table->date('vc_reviewed_date')->nullable(),
            ];

            foreach ($columns as $column => $adder) {
                if (!Schema::hasColumn('study_leaves', $column)) {
                    $adder();
                }
            }
        });

        if (Schema::hasTable('study_leave_approvals')) {
            DB::statement(<<<'SQL'
                UPDATE study_leaves sl
                INNER JOIN study_leave_approvals sla ON sla.study_leave_id = sl.id
                SET
                    sl.library_and_property_handling = COALESCE(sl.library_and_property_handling, sla.library_and_property_handling),
                    sl.loan_handling = COALESCE(sl.loan_handling, sla.loan_handling),
                    sl.status_id = sla.status_id,
                    sl.is_draft = COALESCE(sla.is_draft, sl.is_draft),
                    sl.is_completed = sla.is_completed,
                    sl.current_step = COALESCE(sla.current_step, sl.current_step),
                    sl.ma_empno = sla.ma_empno,
                    sl.ma_remarks = sla.ma_remarks,
                    sl.ma_approve_leave_committee = sla.ma_approve_leave_committee,
                    sl.ma_leave_committee_number = sla.ma_leave_committee_number,
                    sl.ma_leave_committee_date = sla.ma_leave_committee_date,
                    sl.ma_approve_council = sla.ma_approve_council,
                    sl.ma_council_number = sla.ma_council_number,
                    sl.ma_council_date = sla.ma_council_date,
                    sl.ma_reviewed_date = sla.ma_reviewed_date,
                    sl.ma_finalized_date = sla.ma_finalized_date,
                    sl.registrar_empno = sla.registrar_empno,
                    sl.registrar_recommendation = sla.registrar_recommendation,
                    sl.registrar_not_recommend_reason = sla.registrar_not_recommend_reason,
                    sl.registrar_remarks = sla.registrar_remarks,
                    sl.registrar_reviewed_date = sla.registrar_reviewed_date,
                    sl.hod_empno = sla.hod_empno,
                    sl.hod_adequate_staff_available = sla.hod_adequate_staff_available,
                    sl.hod_teaching_covered = sla.hod_teaching_covered,
                    sl.hod_service_period = sla.hod_service_period,
                    sl.hod_recommend = sla.hod_recommend,
                    sl.hod_not_recommend_reason = sla.hod_not_recommend_reason,
                    sl.hod_remarks = sla.hod_remarks,
                    sl.hod_reviewed_date = sla.hod_reviewed_date,
                    sl.dean_empno = sla.dean_empno,
                    sl.dean_leave_recommendation_status = sla.dean_leave_recommendation_status,
                    sl.dean_not_recommended_reason = sla.dean_not_recommended_reason,
                    sl.dean_remarks = sla.dean_remarks,
                    sl.dean_reviewed_date = sla.dean_reviewed_date,
                    sl.vc_empno = sla.vc_empno,
                    sl.vc_recommend_submit_to_committee = sla.vc_recommend_submit_to_committee,
                    sl.vc_council_covering_approval_status = sla.vc_council_covering_approval_status,
                    sl.vc_not_approve_reason = sla.vc_not_approve_reason,
                    sl.vc_remarks = sla.vc_remarks,
                    sl.vc_reviewed_date = sla.vc_reviewed_date
            SQL);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_leaves', function (Blueprint $table) {
            $dropColumns = [
                'status_id',
                'is_completed',
                'ma_empno',
                'ma_remarks',
                'ma_approve_leave_committee',
                'ma_leave_committee_number',
                'ma_leave_committee_date',
                'ma_approve_council',
                'ma_council_number',
                'ma_council_date',
                'ma_reviewed_date',
                'ma_finalized_date',
                'registrar_empno',
                'registrar_recommendation',
                'registrar_not_recommend_reason',
                'registrar_remarks',
                'registrar_reviewed_date',
                'hod_empno',
                'hod_adequate_staff_available',
                'hod_teaching_covered',
                'hod_service_period',
                'hod_recommend',
                'hod_not_recommend_reason',
                'hod_remarks',
                'hod_reviewed_date',
                'dean_empno',
                'dean_leave_recommendation_status',
                'dean_not_recommended_reason',
                'dean_remarks',
                'dean_reviewed_date',
                'vc_empno',
                'vc_recommend_submit_to_committee',
                'vc_council_covering_approval_status',
                'vc_not_approve_reason',
                'vc_remarks',
                'vc_reviewed_date',
            ];

            foreach ($dropColumns as $column) {
                if (Schema::hasColumn('study_leaves', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
