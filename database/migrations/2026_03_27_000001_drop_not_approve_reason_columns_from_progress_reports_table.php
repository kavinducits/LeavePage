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
            $table->dropColumn([
                'registrar_not_approve_reason',
                'hod_not_approve_reason',
                'dean_not_approve_reason',
                'vc_not_approve_reason',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_leave_progress_reports', function (Blueprint $table) {
            $table->text('registrar_not_approve_reason')->nullable()->after('registrar_approval_status');
            $table->text('hod_not_approve_reason')->nullable()->after('hod_approval_status');
            $table->text('dean_not_approve_reason')->nullable()->after('dean_approval_status');
            $table->text('vc_not_approve_reason')->nullable()->after('vc_approval_status');
        });
    }
};
