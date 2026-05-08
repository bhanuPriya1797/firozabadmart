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
        // application_number
        if (!Schema::hasColumn('student_applications', 'application_number')) {
            Schema::table('student_applications', function (Blueprint $table) {
                if (Schema::hasColumn('student_applications', 'id')) {
                    $table->string('application_number', 20)->unique()->nullable()->after('id');
                } else {
                    $table->string('application_number', 20)->unique()->nullable();
                }
            });
        }

        // submitted_at
        if (!Schema::hasColumn('student_applications', 'submitted_at')) {
            Schema::table('student_applications', function (Blueprint $table) {
                if (Schema::hasColumn('student_applications', 'is_submitted')) {
                    $table->timestamp('submitted_at')->nullable()->after('is_submitted');
                } else {
                    $table->timestamp('submitted_at')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropColumn(['application_number', 'submitted_at']);
        });
    }
};
