<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('application_comments')) {
            Schema::table('application_comments', function (Blueprint $table) {
                if (!Schema::hasColumn('application_comments', 'show_in_front')) {
                    if (Schema::hasColumn('application_comments', 'comment')) {
                        $table->boolean('show_in_front')->default(false)->after('comment');
                    } else {
                        $table->boolean('show_in_front')->default(false);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('application_comments', function (Blueprint $table) {
            if (Schema::hasColumn('application_comments', 'show_in_front')) {
                $table->dropColumn('show_in_front');
            }
        });
    }
};
