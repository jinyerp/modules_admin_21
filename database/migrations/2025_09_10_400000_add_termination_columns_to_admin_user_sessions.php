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
        Schema::table('admin_user_sessions', function (Blueprint $table) {
            $table->timestamp('terminated_at')->nullable()->after('is_active');
            $table->string('termination_reason', 50)->nullable()->after('terminated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_user_sessions', function (Blueprint $table) {
            $table->dropColumn(['terminated_at', 'termination_reason']);
        });
    }
};