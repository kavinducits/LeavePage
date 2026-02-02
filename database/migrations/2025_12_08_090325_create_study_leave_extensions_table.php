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
        Schema::create('study_leave_extensions', function (Blueprint $table) {
            $table->increments('id'); // auto-incrementing unsigned integer primary key
            $table->unsignedInteger('study_leave_id');
            $table->foreign('study_leave_id')->references('id')->on('study_leaves')->onDelete('cascade');
            $table->date('old_end_date');
            $table->date('new_end_date');
            $table->text('reason_for_extension');
             $table->string('leave_type')->nullable();
            $table->string('leave_payment_type')->nullable();
            $table->string('funding_type')->nullable();
            $table->string('scholarship_source')->nullable();
            $table->decimal('scholarship_amount', 12, 2)->nullable();
            $table->string('project_name')->nullable();  
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
            $table->string('dean_leave_recommendation_status')->nullable();
            $table->string('dean_empno')->nullable();
            $table->text('dean_remark')->nullable();
            $table->text('dean_not_recommended_reason')->nullable();
            $table->string('vc_empno')->nullable();
            $table->string('vc_recommend')->nullable();
            $table->text('vc_not_recommend_reason')->nullable();
            $table->text('vc_remarks')->nullable();
            $table->unsignedInteger('status_id')->nullable();     
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_leave_extensions');
    }
};
