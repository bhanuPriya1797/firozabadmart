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
            $table->enum('application_type', ['new', 'recurring'])->default('new')->after('support_required');
            $table->unsignedBigInteger('is_recurred_from')->nullable()->after('application_type');
            $table->boolean('is_recurred')->default(false)->after('is_recurred_from');
            
            // Add foreign key constraint
            $table->foreign('is_recurred_from')->references('id')->on('student_applications')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropForeign(['is_recurred_from']);
            $table->dropColumn(['application_type', 'is_recurred_from', 'is_recurred']);
        });
    }
};
