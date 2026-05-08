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
        Schema::table('students', function (Blueprint $table) {
            $table->text('guardian_occupation_details')->nullable()->after('guardian_occupation')->comment('Detailed description of father/guardian occupation');
        });
        
        Schema::table('student_applications', function (Blueprint $table) {
            $table->decimal('current_year_semester_fees', 10, 2)->nullable()->after('total_course_fees')->comment('Current year/semester fees amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('guardian_occupation_details');
        });
        
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropColumn('current_year_semester_fees');
        });
    }
};