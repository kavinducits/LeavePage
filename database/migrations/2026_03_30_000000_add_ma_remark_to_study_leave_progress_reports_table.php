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
            $table->text('ma_remark')->nullable()->after('ma_empno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_leave_progress_reports', function (Blueprint $table) {
            $table->dropColumn('ma_remark');
        });
    }
};
