<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('study_leave_documents', function (Blueprint $table) {
            $table->unsignedTinyInteger('document_type_int')->nullable()->after('document_type');
        });

        DB::statement("\n            UPDATE study_leave_documents\n            SET document_type_int = CASE\n                WHEN document_type = 'placement_letter' THEN 0\n                WHEN document_type = 'self_funding_declaration' THEN 1\n                ELSE NULL\n            END\n        ");

        $unconvertedCount = DB::table('study_leave_documents')
            ->whereNotNull('document_type')
            ->whereNull('document_type_int')
            ->count();

        if ($unconvertedCount > 0) {
            throw new RuntimeException("Unable to convert {$unconvertedCount} document_type values to integer mapping.");
        }

        Schema::table('study_leave_documents', function (Blueprint $table) {
            $table->dropIndex('study_leave_documents_leave_type_idx');
            $table->dropColumn('document_type');
        });

        DB::statement('ALTER TABLE study_leave_documents CHANGE document_type_int document_type TINYINT UNSIGNED NOT NULL');

        Schema::table('study_leave_documents', function (Blueprint $table) {
            $table->index(['study_leave_id', 'document_type'], 'study_leave_documents_leave_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_leave_documents', function (Blueprint $table) {
            $table->string('document_type_key', 64)->nullable()->after('document_type');
        });

        DB::statement("\n            UPDATE study_leave_documents\n            SET document_type_key = CASE\n                WHEN document_type = 0 THEN 'placement_letter'\n                WHEN document_type = 1 THEN 'self_funding_declaration'\n                ELSE NULL\n            END\n        ");

        Schema::table('study_leave_documents', function (Blueprint $table) {
            $table->dropIndex('study_leave_documents_leave_type_idx');
            $table->dropColumn('document_type');
        });

        DB::statement('ALTER TABLE study_leave_documents CHANGE document_type_key document_type VARCHAR(64) NOT NULL');

        Schema::table('study_leave_documents', function (Blueprint $table) {
            $table->index(['study_leave_id', 'document_type'], 'study_leave_documents_leave_type_idx');
        });
    }
};
