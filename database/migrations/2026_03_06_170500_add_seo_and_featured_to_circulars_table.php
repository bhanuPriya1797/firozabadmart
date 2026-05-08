<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('circulars', function (Blueprint $table) {
            if (!Schema::hasColumn('circulars', 'seo')) {
                $table->json('seo')->nullable()->after('document_path');
            }
            if (!Schema::hasColumn('circulars', 'featured')) {
                $table->boolean('featured')->default(false)->after('seo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('circulars', function (Blueprint $table) {
            if (Schema::hasColumn('circulars', 'featured')) {
                $table->dropColumn('featured');
            }
            if (Schema::hasColumn('circulars', 'seo')) {
                $table->dropColumn('seo');
            }
        });
    }
};

