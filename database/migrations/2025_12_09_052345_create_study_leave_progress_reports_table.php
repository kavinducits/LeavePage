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
        Schema::create('study_leave_progress_reports', function (Blueprint $table) {
            $table->increments('id'); // auto-incrementing unsigned integer primary key
            $table->unsignedInteger('study_leave_id'); // foreign key to study_leaves table
            $table->date('due_date');
            $table->date('submitted_date')->nullable();
            $table->text('remark')->nullable();
            $table->text('document_path')->nullable();
            $table->unsignedInteger('status_id'); // e.g., pending, submitted, approved, rejected
            $table->foreign('study_leave_id')->references('id')->on('study_leaves')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_leave_progress_reports');
    }
};
