<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('blogs')) {
            Schema::create('blogs', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('category_id')->nullable();
                $table->string('title');
                $table->unsignedInteger('post_by')->nullable();
                $table->string('slug')->unique();
                $table->text('brief')->nullable();
                $table->longText('content')->nullable();
                $table->string('image')->nullable();
                $table->string('meta_title')->nullable();
                $table->string('meta_keyword')->nullable();
                $table->text('meta_description')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('featured')->default(0);
                $table->date('blog_date')->nullable();
                $table->string('posted_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};

