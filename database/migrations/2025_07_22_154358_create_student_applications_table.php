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
        Schema::create('student_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');

            // Financial Aid-specific fields
            $table->longText('reason_for_aid')->nullable();
            $table->enum('received_support_from_others', ['yes', 'no'])->default('no');
            $table->enum('received_scholarship', ['yes', 'no'])->default('no');
            $table->longText('scholarship_details')->nullable();
            $table->longText('how_fees_paid_before')->nullable();

            // Course Enrollment Info
            $table->string('course_name');
            $table->string('branch')->nullable();
            $table->string('edu_stage');
            $table->integer('course_duration_years')->nullable();
            $table->integer('course_duration_months')->nullable();
            $table->year('enrolment_year');
            $table->string('current_year_or_sem'); // 'Year' or 'Semester'
            $table->integer('current_number'); // number of year or semester
            $table->decimal('total_course_fees', 10, 2)->nullable();
            $table->string('fees_type'); // yearly or semester
            $table->decimal('fees_amount', 10, 2)->nullable();
            $table->decimal('amount_needed', 10, 2)->nullable();
            $table->enum('support_required', ['One-time', 'Recurring']);
            $table->string('college_name');
            $table->text('college_address');

            // Bank details
            $table->string('account_no')->nullable();
            $table->string('account_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('ifsc_code')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_applications');
    }
};
