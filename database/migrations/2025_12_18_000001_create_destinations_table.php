<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->string('destination_name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('destination_type')->default(0);
            $table->string('latitude')->nullable();
            $table->string('longtitude')->nullable();
            $table->text('brief')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('feature_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->json('best_months')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};

