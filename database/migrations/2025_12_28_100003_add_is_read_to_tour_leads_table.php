<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tour_leads') && !Schema::hasColumn('tour_leads', 'is_read')) {
            Schema::table('tour_leads', function (Blueprint $table) {
                $table->boolean('is_read')->default(false)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tour_leads') && Schema::hasColumn('tour_leads', 'is_read')) {
            Schema::table('tour_leads', function (Blueprint $table) {
                $table->dropColumn('is_read');
            });
        }
    }
};
