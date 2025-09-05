<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * admin_user_types 테이블에 사용자 카운트 필드 추가
 * 
 * 각 사용자 타입별로 현재 할당된 사용자 수를
 * 추적하기 위한 user_count 필드를 추가합니다.
 * 
 * 주요 기능:
 * - user_count 필드 추가
 * - 기존 사용자 데이터 기반 초기값 설정
 * - 각 타입별 사용자 수 통계 제공
 * 
 * @table admin_user_types
 * @since 2025.09.01
 */
return new class extends Migration
{
    /**
     * 마이그레이션 실행
     * 
     * admin_user_types 테이블에 user_count 필드를 추가하고
     * 기존 데이터를 기반으로 초기값을 설정합니다.
     * 
     * 추가 필드:
     * - user_count (unsigned integer): 해당 타입의 사용자 수
     *   기본값: 0
     *   위치: level 필드 다음
     * 
     * 마이그레이션 후 처리:
     * - users 테이블에서 각 타입별 사용자 수 계산
     * - 계산된 값으로 user_count 필드 업데이트
     */
    public function up(): void
    {
        Schema::table('admin_user_types', function (Blueprint $table) {
            $table->unsignedInteger('user_count')->default(0)->after('level');
        });

        // 기존 사용자 수를 계산하여 업데이트
        // 각 타입별로 users 테이블에서 사용자 수를 카운트하고
        // admin_user_types 테이블의 user_count 필드에 저장
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
     * 마이그레이션 롤백
     * 
     * admin_user_types 테이블에서 user_count 필드를 제거합니다.
     */
    public function down(): void
    {
        Schema::table('admin_user_types', function (Blueprint $table) {
            $table->dropColumn('user_count');
        });
    }
};
