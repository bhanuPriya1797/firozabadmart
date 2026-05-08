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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('key')->unique();
            $table->string('type'); // text, textarea, file, editor, etc.
            $table->string('group_name')->nullable();
            $table->string('class')->nullable();
            $table->json('options')->nullable(); // for select, checkbox, etc
            $table->json('validation')->nullable();
            $table->json('file_constraints')->nullable();
            $table->unsignedBigInteger('ref_id')->default(0);
            $table->string('module')->nullable(); // 'cms', 'blog', etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
