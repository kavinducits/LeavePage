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
        Schema::create('leave_summary_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('empno')->nullable();
            $table->integer('leave_type')->nullable();
            $table->string('reference_no')->unique()->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_summary_table');
    }
};
