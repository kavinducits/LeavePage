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
            $table->integer('registrar_empno')->nullable()->after('ma_remarks');
            $table->text('registrar_remarks')->nullable()->after('registrar_empno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_leave_extensions', function (Blueprint $table) {
            $table->dropColumn(['registrar_empno', 'registrar_remarks']);
        });
    }
};
