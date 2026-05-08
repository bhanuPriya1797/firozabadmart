<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $tables = [
            'student_documents',
            'student_notes',
            'application_requests',
            'student_applications',
            'students',
            'volunteer_applications',
            'tour_package_images',
            'tour_package_itineraries',
            'tour_packages',
            'destination_images',
            'destinations',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::drop($table);
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
    }
};

