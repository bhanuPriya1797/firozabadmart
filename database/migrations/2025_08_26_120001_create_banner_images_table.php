<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('banner_images')) {
            Schema::create('banner_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('banner_id')->constrained('banners')->onDelete('cascade');
                $table->string('image_name');
                $table->string('title')->nullable();
                $table->text('sub_title')->nullable();
                $table->string('link_text_1')->nullable();
                $table->string('link_1')->nullable();
                $table->string('link_text_2')->nullable();
                $table->string('link_2')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_images');
    }
};

