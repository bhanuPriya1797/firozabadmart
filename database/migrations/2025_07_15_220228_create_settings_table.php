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
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();                 // e.g., site_name, meta_title
            $table->string('label');                         // e.g., "Website Name"
            $table->text('value')->nullable();               // Value of setting
            $table->text('old_value')->nullable();           // Value of setting
            $table->string('type')->default('text');         // text, textarea, checkbox, file, select
            $table->json('options')->nullable();             // for select/checkbox/radio
            $table->boolean('is_fixed')->default(false);     // For fixed fields like logo, favicon
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
