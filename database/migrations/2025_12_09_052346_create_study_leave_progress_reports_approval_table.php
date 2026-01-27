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
        Schema::create('study_leave_progress_reports_approval', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('approval_status_id')->nullable();
            $table->unsignedInteger('study_leave_progress_report_id');
            $table->foreign('study_leave_progress_report_id', 'slpr_approval_progress_report_fk')->references('id')->on('study_leave_progress_reports')->onDelete('cascade');
            $table->string('ma_empno')->nullable();
            $table->string('registrar_empno')->nullable();
            $table->unsignedInteger('registrar_approval_status')->nullable();
            $table->text('registrar_not_approve_reason')->nullable();
            $table->text('registrar_remarks')->nullable();
            $table->string('hod_empno')->nullable();
            $table->unsignedInteger('hod_approval_status')->nullable();
            $table->text('hod_not_approve_reason')->nullable();
            $table->text('hod_remarks')->nullable();
            $table->string('dean_empno')->nullable();
            $table->unsignedInteger('dean_approval_status')->nullable();
            $table->text('dean_not_approve_reason')->nullable();
            $table->text('dean_remarks')->nullable();
            $table->string('vc_empno')->nullable();
            $table->unsignedInteger('vc_approval_status')->nullable();
            $table->text('vc_not_approve_reason')->nullable();
            $table->text('vc_remarks')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_leave_progress_reports_approval');
    }
};
