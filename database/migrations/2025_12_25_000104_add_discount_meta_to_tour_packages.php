<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_packages', 'discount_type')) {
                $table->enum('discount_type', ['flat', 'percent'])->nullable()->after('price_individual');
            }
            if (!Schema::hasColumn('tour_packages', 'discount_value')) {
                $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            }
            if (!Schema::hasColumn('tour_packages', 'discounted_price')) {
                $table->decimal('discounted_price', 10, 2)->nullable()->after('discount_value');
            }
            if (!Schema::hasColumn('tour_packages', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('excluded');
            }
            if (!Schema::hasColumn('tour_packages', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('tour_packages', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_keywords');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            if (Schema::hasColumn('tour_packages', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
            if (Schema::hasColumn('tour_packages', 'discount_value')) {
                $table->dropColumn('discount_value');
            }
            if (Schema::hasColumn('tour_packages', 'discounted_price')) {
                $table->dropColumn('discounted_price');
            }
            if (Schema::hasColumn('tour_packages', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
            if (Schema::hasColumn('tour_packages', 'meta_keywords')) {
                $table->dropColumn('meta_keywords');
            }
            if (Schema::hasColumn('tour_packages', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
        });
    }
};
