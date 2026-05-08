<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_packages', 'is_sukoon')) {
                $table->boolean('is_sukoon')->default(false)->after('status');
            }
            if (!Schema::hasColumn('tour_packages', 'sukoon_featured')) {
                $table->boolean('sukoon_featured')->default(false)->after('is_sukoon');
            }
            if (!Schema::hasColumn('tour_packages', 'currency_code')) {
                $table->string('currency_code', 8)->nullable()->after('price');
            }
            if (!Schema::hasColumn('tour_packages', 'badge_label')) {
                $table->string('badge_label', 64)->nullable()->after('currency_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            if (Schema::hasColumn('tour_packages', 'is_sukoon')) {
                $table->dropColumn('is_sukoon');
            }
            if (Schema::hasColumn('tour_packages', 'sukoon_featured')) {
                $table->dropColumn('sukoon_featured');
            }
            if (Schema::hasColumn('tour_packages', 'currency_code')) {
                $table->dropColumn('currency_code');
            }
            if (Schema::hasColumn('tour_packages', 'badge_label')) {
                $table->dropColumn('badge_label');
            }
        });
    }
};
