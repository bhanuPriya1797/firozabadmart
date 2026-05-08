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
        // Update student document file paths from old structure to new structure
        \DB::table('student_documents')
            ->where('document_file', 'like', 'student-documents/%')
            ->update([
                'document_file' => \DB::raw("REPLACE(document_file, 'student-documents/', 'uploads/student/docs/')")
            ]);
        
        // Update any remaining old paths
        \DB::table('student_documents')
            ->where('document_file', 'like', 'uploads/students/docs/%')
            ->update([
                'document_file' => \DB::raw("REPLACE(document_file, 'uploads/students/docs/', 'uploads/student/docs/')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the changes
        \DB::table('student_documents')
            ->where('document_file', 'like', 'uploads/student/docs/%')
            ->update([
                'document_file' => \DB::raw("REPLACE(document_file, 'uploads/student/docs/', 'student-documents/')")
            ]);
    }
};
