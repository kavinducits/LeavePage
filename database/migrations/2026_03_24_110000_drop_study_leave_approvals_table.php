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
        Schema::dropIfExists('study_leave_approvals');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('study_leave_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('study_leave_id')->nullable();
            $table->integer('library_and_property_handling')->nullable();
            $table->integer('loan_handling')->nullable();
            $table->string('ma_empno')->nullable();
            $table->text('ma_remarks')->nullable();
            $table->string('registrar_empno')->nullable();
            $table->integer('registrar_recommendation')->nullable();
            $table->text('registrar_not_recommend_reason')->nullable();
            $table->text('registrar_remarks')->nullable();
            $table->string('hod_empno')->nullable();
            $table->integer('hod_adequate_staff_available')->nullable();
            $table->integer('hod_teaching_covered')->nullable();
            $table->integer('hod_service_period')->nullable();
            $table->integer('hod_recommend')->nullable();
            $table->text('hod_not_recommend_reason')->nullable();
            $table->text('hod_remarks')->nullable();
            $table->string('vc_empno')->nullable();
            $table->integer('vc_recommend_submit_to_committee')->nullable();
            $table->integer('vc_council_covering_approval_status')->nullable();
            $table->text('vc_not_approve_reason')->nullable();
            $table->text('vc_remarks')->nullable();
            $table->string('dean_empno')->nullable();
            $table->integer('dean_leave_recommendation_status')->nullable();
            $table->text('dean_not_recommended_reason')->nullable();
            $table->text('dean_remarks')->nullable();
            $table->unsignedInteger('status_id')->nullable();
            $table->boolean('is_draft')->default(false);
            $table->boolean('is_completed')->nullable();
            $table->unsignedInteger('current_step')->nullable();
            $table->integer('ma_approve_leave_committee')->nullable();
            $table->string('ma_leave_committee_number')->nullable();
            $table->date('ma_leave_committee_date')->nullable();
            $table->integer('ma_approve_council')->nullable();
            $table->string('ma_council_number')->nullable();
            $table->date('ma_council_date')->nullable();
            $table->date('ma_reviewed_date')->nullable();
            $table->date('ma_finalized_date')->nullable();
            $table->date('registrar_reviewed_date')->nullable();
            $table->date('hod_reviewed_date')->nullable();
            $table->date('vc_reviewed_date')->nullable();
            $table->date('dean_reviewed_date')->nullable();
            $table->timestamps();

            $table->foreign('study_leave_id')->references('id')->on('study_leaves')->onDelete('cascade');
        });
    }
};
