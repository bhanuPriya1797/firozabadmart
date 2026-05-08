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
            if (!Schema::hasColumn('student_applications', 'approved_date')) {
                if (Schema::hasColumn('student_applications', 'status')) {
                    $table->timestamp('approved_date')->nullable()->after('status');
                } else {
                    $table->timestamp('approved_date')->nullable();
                }
            }
            if (!Schema::hasColumn('student_applications', 'closed_date')) {
                if (Schema::hasColumn('student_applications', 'approved_date')) {
                    $table->timestamp('closed_date')->nullable()->after('approved_date');
                } else {
                    $table->timestamp('closed_date')->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            if (Schema::hasColumn('student_applications', 'approved_date')) {
                $table->dropColumn('approved_date');
            }
            if (Schema::hasColumn('student_applications', 'closed_date')) {
                $table->dropColumn('closed_date');
            }
        });
    }
};
