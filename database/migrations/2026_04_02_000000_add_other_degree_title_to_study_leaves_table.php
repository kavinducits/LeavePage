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
            $table->string('other_degree_title')->nullable()->after('degree_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_leaves', function (Blueprint $table) {
            $table->dropColumn('other_degree_title');
        });
    }
};
