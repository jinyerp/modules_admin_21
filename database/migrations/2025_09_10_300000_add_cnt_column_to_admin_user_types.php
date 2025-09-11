<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admin_user_types', function (Blueprint $table) {
            // cnt 컬럼 추가 (user_count와 동일한 용도)
            $table->integer('cnt')->default(0)->after('user_count');
        });
        
        // 기존 user_count 값을 cnt로 복사
        DB::statement('UPDATE admin_user_types SET cnt = user_count');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_user_types', function (Blueprint $table) {
            $table->dropColumn('cnt');
        });
    }
};