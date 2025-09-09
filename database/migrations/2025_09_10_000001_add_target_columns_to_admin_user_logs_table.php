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
            // target_type과 target_id 컬럼이 없으면 추가
            if (!Schema::hasColumn('admin_user_logs', 'target_type')) {
                $table->string('target_type', 50)->nullable()->after('action');
            }
            if (!Schema::hasColumn('admin_user_logs', 'target_id')) {
                $table->unsignedBigInteger('target_id')->nullable()->after('target_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_user_logs', function (Blueprint $table) {
            if (Schema::hasColumn('admin_user_logs', 'target_type')) {
                $table->dropColumn('target_type');
            }
            if (Schema::hasColumn('admin_user_logs', 'target_id')) {
                $table->dropColumn('target_id');
            }
        });
    }
};