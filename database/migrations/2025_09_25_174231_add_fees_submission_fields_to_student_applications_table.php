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
        Schema::table('student_applications', function (Blueprint $table) {
            $table->date('last_fees_submission_date')->nullable()->after('current_year_semester_fees');
            $table->enum('fees_submission_status', ['date', 'not_yet_known'])->default('not_yet_known')->after('last_fees_submission_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropColumn(['last_fees_submission_date', 'fees_submission_status']);
        });
    }
};
