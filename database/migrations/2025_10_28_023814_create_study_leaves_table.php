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
        Schema::create('study_leaves', function (Blueprint $table) {
        $table->increments('id'); // auto-incrementing unsigned integer primary key
        $table->string('reference_no')->unique()->nullable();
            $table->string('empno')->index();
            $table->string('academic_year')->nullable();
            $table->string('passport_no')->nullable();
            $table->date('passport_validity')->nullable();
            $table->string('leave_type')->nullable();
            $table->string('leave_payment_type')->nullable();
            $table->date('study_leave_from')->nullable();
            $table->date('study_leave_to')->nullable();
            $table->string('degree_title')->nullable();
            $table->string('university_institute')->nullable();
            $table->string('country')->nullable();
            $table->string('field_of_study')->nullable();
            $table->text('study_program_details')->nullable();
            $table->string('funding_type')->nullable();
            $table->text('any_other_details')->nullable();
            $table->string('air_passage_request')->nullable();
            $table->string('warm_cloth_allowance_request')->nullable();
            $table->string('scholarship_source')->nullable();
            $table->decimal('scholarship_amount', 12, 2)->nullable();
            $table->string('project_name')->nullable();
            $table->string('nominee_teaching_empno')->nullable();
            $table->string('nominee_admin_empno')->nullable();
            $table->string('nominee_other_empno')->nullable();
            $table->text('library_and_property_handling')->nullable();
            $table->text('loan_handling')->nullable();
             $table->string('ma_empno')->nullable();
             $table->string('ma_remarks')->nullable();
            $table->string('hod_empno')->nullable();
            $table->string('hod_adequate_staff_available')->nullable();
            $table->string('hod_teaching_covered')->nullable();
            $table->string('hod_service_period')->nullable();
            $table->string('hod_recommend')->nullable();
            $table->text('hod_not_recommend_reason')->nullable();
             $table->text('hod_remarks')->nullable();
            $table->string('vc_empno')->nullable();
            $table->string('vc_recommend_submit_to_committee')->nullable();
            $table->string('vc_council_covering_approval_status')->nullable();
            $table->text('vc_not_approve_reason')->nullable();
            $table->text('vc_remarks')->nullable();
            $table->string('dean_empno')->nullable();
            $table->string('dean_leave_recommendation_status')->nullable();
            $table->text('dean_not_recommended_reason')->nullable();
            $table->text('dean_remarks')->nullable();
            $table->unsignedInteger('status_id')->nullable();
            $table->timestamps();
            $table->boolean('is_draft')->default(false);
            $table->string('self_funding_declaration')->nullable();
            $table->string('placement_letter')->nullable();
            $table->boolean('is_completed')->nullable();
            $table->unsignedInteger('current_step')->nullable();

            
            //$table->foreign('status_id')->references('id')->on('study_leave_statuses')->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_leaves');
    }
};
