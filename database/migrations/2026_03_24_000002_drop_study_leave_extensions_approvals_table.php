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
        // Drop study_leave_extensions_approvals table
        Schema::dropIfExists('study_leave_extensions_approvals');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the table if rolling back
        Schema::create('study_leave_extensions_approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('study_leave_extension_id');
            $table->foreign('study_leave_extension_id', 'sl_ext_approvals_ext_id_foreign')->references('id')->on('study_leave_extensions')->onDelete('cascade');
            
            $table->string('ma_empno')->nullable();
            $table->integer('ma_recommend')->nullable();
            $table->text('ma_not_recommend_reason')->nullable();
            $table->text('ma_remarks')->nullable();
            $table->date('ma_reviewed_date')->nullable();
            $table->date('ma_finalized_date')->nullable();
            
            $table->string('acad_est_head_empno')->nullable();
            $table->integer('acad_est_head_recommend')->nullable();
            $table->text('acad_est_head_not_recommend_reason')->nullable();
            $table->text('acad_est_head_remarks')->nullable();
            
            $table->string('hod_empno')->nullable();
            $table->integer('hod_recommend')->nullable();
            $table->text('hod_not_recommend_reason')->nullable();
            $table->text('hod_remarks')->nullable();
            $table->date('hod_reviewed_date')->nullable();
            
            $table->string('dean_empno')->nullable();
            $table->integer('dean_recommend')->nullable();
            $table->text('dean_not_recommended_reason')->nullable();
            $table->text('dean_remark')->nullable();
            $table->date('dean_reviewed_date')->nullable();
            
            $table->string('vc_empno')->nullable();
            $table->integer('vc_recommend')->nullable();
            $table->text('vc_not_recommend_reason')->nullable();
            $table->text('vc_remarks')->nullable();
            $table->date('vc_reviewed_date')->nullable();
            
            $table->date('registrar_reviewed_date')->nullable();
            
            $table->unsignedInteger('status_id')->nullable();
            $table->timestamps();
        });
    }
};
