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
        Schema::table('study_leave_extensions', function (Blueprint $table) {
            // MA Approval Fields
            $table->string('ma_empno')->nullable()->after('extension_payment_type');
            $table->integer('ma_recommend')->nullable()->after('ma_empno');
            $table->text('ma_not_recommend_reason')->nullable()->after('ma_recommend');
            $table->text('ma_remarks')->nullable()->after('ma_not_recommend_reason');
            $table->date('ma_reviewed_date')->nullable()->after('ma_remarks');
            $table->date('ma_finalized_date')->nullable()->after('ma_reviewed_date');

            // Academic Establishment Head Fields
            $table->string('acad_est_head_empno')->nullable()->after('ma_finalized_date');
            $table->integer('acad_est_head_recommend')->nullable()->after('acad_est_head_empno');
            $table->text('acad_est_head_not_recommend_reason')->nullable()->after('acad_est_head_recommend');
            $table->text('acad_est_head_remarks')->nullable()->after('acad_est_head_not_recommend_reason');

            // HOD Fields
            $table->string('hod_empno')->nullable()->after('acad_est_head_remarks');
            $table->integer('hod_recommend')->nullable()->after('hod_empno');
            $table->text('hod_not_recommend_reason')->nullable()->after('hod_recommend');
            $table->text('hod_remarks')->nullable()->after('hod_not_recommend_reason');
            $table->date('hod_reviewed_date')->nullable()->after('hod_remarks');

            // Dean Fields
            $table->string('dean_empno')->nullable()->after('hod_reviewed_date');
            $table->integer('dean_recommend')->nullable()->after('dean_empno');
            $table->text('dean_not_recommended_reason')->nullable()->after('dean_recommend');
            $table->text('dean_remark')->nullable()->after('dean_not_recommended_reason');
            $table->date('dean_reviewed_date')->nullable()->after('dean_remark');

            // VC Fields
            $table->string('vc_empno')->nullable()->after('dean_reviewed_date');
            $table->integer('vc_recommend')->nullable()->after('vc_empno');
            $table->text('vc_not_recommend_reason')->nullable()->after('vc_recommend');
            $table->text('vc_remarks')->nullable()->after('vc_not_recommend_reason');
            $table->date('vc_reviewed_date')->nullable()->after('vc_remarks');

            // Registrar Field
            $table->date('registrar_reviewed_date')->nullable()->after('vc_reviewed_date');

            // Status Field
            $table->unsignedInteger('status_id')->nullable()->after('registrar_reviewed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_leave_extensions', function (Blueprint $table) {
            $table->dropColumn([
                'ma_empno',
                'ma_recommend',
                'ma_not_recommend_reason',
                'ma_remarks',
                'ma_reviewed_date',
                'ma_finalized_date',
                'acad_est_head_empno',
                'acad_est_head_recommend',
                'acad_est_head_not_recommend_reason',
                'acad_est_head_remarks',
                'hod_empno',
                'hod_recommend',
                'hod_not_recommend_reason',
                'hod_remarks',
                'hod_reviewed_date',
                'dean_empno',
                'dean_recommend',
                'dean_not_recommended_reason',
                'dean_remark',
                'dean_reviewed_date',
                'vc_empno',
                'vc_recommend',
                'vc_not_recommend_reason',
                'vc_remarks',
                'vc_reviewed_date',
                'registrar_reviewed_date',
                'status_id',
            ]);
        });
    }
};
