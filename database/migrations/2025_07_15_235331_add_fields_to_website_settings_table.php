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
        Schema::table('website_settings', function (Blueprint $table) {
            Schema::table('website_settings', function (Blueprint $table) {
                $table->string('class')->nullable()->after('options');          // extra CSS classes
                $table->string('group_name')->nullable()->after('class');            // group
                $table->json('validation')->nullable()->after('class');         // rules like ["max"=>10, "min"=>2]
                $table->json('file_constraints')->nullable()->after('validation'); // file constraints
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn(['class', 'group_name', 'validation', 'file_constraints']);
        });
    }
};
