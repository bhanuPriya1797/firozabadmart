<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('winner_name');
            $table->string('medal')->nullable(); // gold, silver, bronze, other
            $table->string('event_name')->nullable();
            $table->string('location')->nullable();
            $table->string('category')->nullable(); // e.g., Recurve/Compound/Para etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};

