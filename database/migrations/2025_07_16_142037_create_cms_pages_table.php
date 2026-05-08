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
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('brief')->nullable();

            $table->string('slug')->unique();
            $table->string('heading')->nullable();
            $table->string('template')->nullable();
            $table->string('banner')->nullable();

            $table->longText('description')->nullable();

            $table->json('seo')->nullable(); // ['title', 'description', 'keywords']

            $table->boolean('featured')->default(false); // Mark as featured
            $table->unsignedBigInteger('parent_id')->nullable(); // For page hierarchy
            $table->integer('sort_order')->default(0); // Sorting purpose

            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('cms_pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};
