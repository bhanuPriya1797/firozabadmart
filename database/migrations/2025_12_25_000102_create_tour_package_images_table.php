<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_package_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_package_id');
            $table->string('file_name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('tour_package_id')->references('id')->on('tour_packages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_package_images');
    }
};

