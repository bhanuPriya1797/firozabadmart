<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('student_applications', 'admin_message')) {
            Schema::table('student_applications', function (Blueprint $table) {
                if (Schema::hasColumn('student_applications', 'status')) {
                    $table->text('admin_message')->nullable()->after('status');
                } else {
                    $table->text('admin_message')->nullable();
                }
            });
        }
        if (!Schema::hasColumn('student_applications', 'admin_message_at')) {
            Schema::table('student_applications', function (Blueprint $table) {
                if (Schema::hasColumn('student_applications', 'admin_message')) {
                    $table->timestamp('admin_message_at')->nullable()->after('admin_message');
                } else {
                    $table->timestamp('admin_message_at')->nullable();
                }
            });
        }
    }

    public function down()
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropColumn(['admin_message', 'admin_message_at']);
        });
    }
};
