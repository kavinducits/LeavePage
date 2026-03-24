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
        Schema::create('study_leave_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('study_leave_id');
            $table->string('document_type', 64);
            $table->string('document_path');
            $table->timestamps();

            $table->foreign('study_leave_id', 'study_leave_documents_study_leave_fk')
                ->references('id')
                ->on('study_leaves')
                ->onDelete('cascade');

            $table->index('study_leave_id', 'study_leave_documents_study_leave_idx');
            $table->index(['study_leave_id', 'document_type'], 'study_leave_documents_leave_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_leave_documents');
    }
};
