<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            if (!Schema::hasColumn('faqs', 'page_type')) {
                $table->string('page_type')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('faqs', 'page_id')) {
                $table->unsignedBigInteger('page_id')->nullable()->after('page_type');
            }
            $table->index(['page_type', 'page_id']);
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            if (Schema::hasColumn('faqs', 'page_type')) {
                $table->dropColumn('page_type');
            }
            if (Schema::hasColumn('faqs', 'page_id')) {
                $table->dropIndex(['page_type', 'page_id']);
                $table->dropColumn('page_id');
            }
        });
    }
};

