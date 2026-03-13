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
            $table->integer('extension_payment_type')->nullable()->after('reason_for_extension');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_leave_extensions', function (Blueprint $table) {
            $table->dropColumn('extension_payment_type');
        });
    }
};
