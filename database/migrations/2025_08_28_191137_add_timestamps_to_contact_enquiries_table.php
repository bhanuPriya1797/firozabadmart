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
        if (Schema::hasTable('contact_enquiries') && (!Schema::hasColumn('contact_enquiries', 'created_at') || !Schema::hasColumn('contact_enquiries', 'updated_at'))) {
            Schema::table('contact_enquiries', function (Blueprint $table) {
                if (!Schema::hasColumn('contact_enquiries', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('contact_enquiries', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('contact_enquiries')) {
            Schema::table('contact_enquiries', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('contact_enquiries', 'created_at')) {
                    $columnsToDrop[] = 'created_at';
                }
                if (Schema::hasColumn('contact_enquiries', 'updated_at')) {
                    $columnsToDrop[] = 'updated_at';
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
