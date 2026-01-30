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
        Schema::table('study_leaves', function (Blueprint $table) {
            $table->string('consent_letter_teaching_path')->nullable()->after('nominee_teaching_empno');
            $table->string('consent_letter_admin_path')->nullable()->after('nominee_admin_empno');
            $table->string('consent_letter_other_path')->nullable()->after('nominee_other_empno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_leaves', function (Blueprint $table) {
            $table->dropColumn(['consent_letter_teaching_path', 'consent_letter_admin_path', 'consent_letter_other_path']);
        });
    }
};
