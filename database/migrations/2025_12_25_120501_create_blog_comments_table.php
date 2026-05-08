<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('blog_comments')) {
            Schema::create('blog_comments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('blog_id');
                $table->string('name', 150);
                $table->string('email', 150);
                $table->string('website', 255)->nullable();
                $table->text('comment');
                $table->boolean('save_info')->default(false);
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};
