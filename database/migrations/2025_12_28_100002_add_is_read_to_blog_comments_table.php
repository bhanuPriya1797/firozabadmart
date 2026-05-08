<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('blog_comments') && !Schema::hasColumn('blog_comments', 'is_read')) {
            Schema::table('blog_comments', function (Blueprint $table) {
                $table->boolean('is_read')->default(false)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('blog_comments') && Schema::hasColumn('blog_comments', 'is_read')) {
            Schema::table('blog_comments', function (Blueprint $table) {
                $table->dropColumn('is_read');
            });
        }
    }
};
