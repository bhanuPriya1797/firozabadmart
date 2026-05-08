<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('students')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'father_name')) {
                $table->string('father_name')->nullable()->after('surname');
            }
            if (!Schema::hasColumn('students', 'mother_name')) {
                $table->string('mother_name')->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('students', 'marital_status')) {
                $table->string('marital_status')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('students', 'is_minor')) {
                $table->boolean('is_minor')->default(false)->after('marital_status');
            }
            if (!Schema::hasColumn('students', 'alternate_phone')) {
                $table->string('alternate_phone')->nullable()->after('contact_no');
            }
            if (!Schema::hasColumn('students', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('alternate_phone');
            }
            if (!Schema::hasColumn('students', 'aadhar_card_number')) {
                $table->string('aadhar_card_number', 50)->nullable()->after('whatsapp_number');
            }
            if (!Schema::hasColumn('students', 'aadhar_document')) {
                $table->string('aadhar_document')->nullable()->after('aadhar_card_number');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('students')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'father_name')) {
                $table->dropColumn('father_name');
            }
            if (Schema::hasColumn('students', 'mother_name')) {
                $table->dropColumn('mother_name');
            }
            if (Schema::hasColumn('students', 'marital_status')) {
                $table->dropColumn('marital_status');
            }
            if (Schema::hasColumn('students', 'is_minor')) {
                $table->dropColumn('is_minor');
            }
            if (Schema::hasColumn('students', 'alternate_phone')) {
                $table->dropColumn('alternate_phone');
            }
            if (Schema::hasColumn('students', 'whatsapp_number')) {
                $table->dropColumn('whatsapp_number');
            }
            if (Schema::hasColumn('students', 'aadhar_card_number')) {
                $table->dropColumn('aadhar_card_number');
            }
            if (Schema::hasColumn('students', 'aadhar_document')) {
                $table->dropColumn('aadhar_document');
            }
        });
    }
};

