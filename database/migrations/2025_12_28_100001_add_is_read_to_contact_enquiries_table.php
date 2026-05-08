<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('contact_enquiries') && !Schema::hasColumn('contact_enquiries', 'is_read')) {
            Schema::table('contact_enquiries', function (Blueprint $table) {
                $table->boolean('is_read')->default(false)->after('ip_address');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('contact_enquiries') && Schema::hasColumn('contact_enquiries', 'is_read')) {
            Schema::table('contact_enquiries', function (Blueprint $table) {
                $table->dropColumn('is_read');
            });
        }
    }
};
