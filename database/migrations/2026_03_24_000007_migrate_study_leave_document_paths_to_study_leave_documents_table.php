<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backfill legacy placement_letter paths.
        DB::statement("\n            INSERT INTO study_leave_documents (study_leave_id, document_type, document_path, created_at, updated_at)\n            SELECT sl.id, 'placement_letter', sl.placement_letter, NOW(), NOW()\n            FROM study_leaves sl\n            WHERE sl.placement_letter IS NOT NULL\n              AND sl.placement_letter <> ''\n              AND NOT EXISTS (\n                  SELECT 1\n                  FROM study_leave_documents d\n                  WHERE d.study_leave_id = sl.id\n                    AND d.document_type = 'placement_letter'\n                    AND d.document_path = sl.placement_letter\n              )\n        ");

        // Backfill legacy self_funding_declaration paths.
        DB::statement("\n            INSERT INTO study_leave_documents (study_leave_id, document_type, document_path, created_at, updated_at)\n            SELECT sl.id, 'self_funding_declaration', sl.self_funding_declaration, NOW(), NOW()\n            FROM study_leaves sl\n            WHERE sl.self_funding_declaration IS NOT NULL\n              AND sl.self_funding_declaration <> ''\n              AND NOT EXISTS (\n                  SELECT 1\n                  FROM study_leave_documents d\n                  WHERE d.study_leave_id = sl.id\n                    AND d.document_type = 'self_funding_declaration'\n                    AND d.document_path = sl.self_funding_declaration\n              )\n        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('study_leave_documents')
            ->whereIn('document_type', ['placement_letter', 'self_funding_declaration'])
            ->delete();
    }
};
