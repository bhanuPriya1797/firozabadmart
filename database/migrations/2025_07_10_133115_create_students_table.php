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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('surname');
            $table->enum('family_lineage', ['SYED', 'NON-SYED']);
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('dob')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->text('address')->nullable();
            $table->string('state');
            $table->string('district');
            $table->string('pincode')->nullable();
            $table->string('contact_no');
            $table->string('guardian_name')->nullable();
            $table->string('guardian_contact_no')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->integer('family_members')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
