<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('archers')) {
            Schema::create('archers', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('surname')->nullable();
                $table->string('father_name');
                $table->string('mother_name');
                $table->string('email')->unique();
                $table->string('phone');
                $table->string('alternate_phone')->nullable();
                $table->string('whatsapp_number');
                $table->enum('gender', ['Male', 'Female', 'Other']);
                $table->date('dob')->nullable();
                $table->enum('marital_status', ['Married', 'Single']);
                $table->boolean('is_minor')->default(false);
                $table->string('aadhar_card_number', 50);
                $table->string('aadhar_document')->nullable();
                $table->string('password');
                $table->tinyInteger('status')->default(0);
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('archer_applications')) {
            Schema::create('archer_applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('archer_id')->constrained('archers')->onDelete('cascade');
                $table->string('application_number', 20)->unique()->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamp('submitted_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('archer_applications');
        Schema::dropIfExists('archers');
    }
};

