<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            $table->string('category')->nullable()->after('marital_status');
            $table->string('member_id')->nullable()->after('category');
            $table->string('member_association')->nullable()->after('member_id');
        });
    }

    public function down(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            $table->dropColumn(['category', 'member_id', 'member_association']);
        });
    }
};

