<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('destination_id')->nullable();
            $table->string('name');
            $table->string('type')->default('group');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('max_seats')->default(0);
            $table->integer('booked_seats')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('price_individual', 10, 2)->nullable();
            $table->boolean('status')->default(true);
            $table->string('main_banner')->nullable();
            $table->json('included')->nullable();
            $table->json('excluded')->nullable();
            $table->timestamps();

            $table->foreign('destination_id')->references('id')->on('destinations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};

