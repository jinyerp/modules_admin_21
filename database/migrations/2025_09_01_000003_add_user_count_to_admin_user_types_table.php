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
            $table->unsignedInteger('user_count')->default(0)->after('level');
        });

        // 기존 사용자 수를 계산하여 업데이트
        $userTypes = DB::table('admin_user_types')->get();
        foreach ($userTypes as $userType) {
            $count = DB::table('users')
                ->where('utype', $userType->code)
                ->count();
            
            DB::table('admin_user_types')
                ->where('code', $userType->code)
                ->update(['user_count' => $count]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_user_types', function (Blueprint $table) {
            $table->dropColumn('user_count');
        });
    }
};