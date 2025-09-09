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
        Schema::table('admin_user_logs', function (Blueprint $table) {
            // data 컬럼이 없으면 추가
            if (!Schema::hasColumn('admin_user_logs', 'data')) {
                $table->json('data')->nullable()->after('user_agent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_user_logs', function (Blueprint $table) {
            if (Schema::hasColumn('admin_user_logs', 'data')) {
                $table->dropColumn('data');
            }
        });
    }
};