<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add to tour_packages
        Schema::table('tour_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_packages', 'additional_info')) {
                $table->longText('additional_info')->nullable()->after('description');
            }
        });

        // Remove from tour_package_itineraries
        Schema::table('tour_package_itineraries', function (Blueprint $table) {
            if (Schema::hasColumn('tour_package_itineraries', 'additional_info')) {
                $table->dropColumn('additional_info');
            }
        });
    }

    public function down(): void
    {
        // Add back to tour_package_itineraries
        Schema::table('tour_package_itineraries', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_package_itineraries', 'additional_info')) {
                $table->longText('additional_info')->nullable();
            }
        });

        // Remove from tour_packages
        Schema::table('tour_packages', function (Blueprint $table) {
            if (Schema::hasColumn('tour_packages', 'additional_info')) {
                $table->dropColumn('additional_info');
            }
        });
    }
};
