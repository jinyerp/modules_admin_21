<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 관리자 사용자 타입 테이블 생성
 * 
 * 이 마이그레이션은 관리자 시스템의 사용자 타입(역할)을 정의하는
 * admin_user_types 테이블을 생성합니다.
 * 
 * 주요 기능:
 * - 사용자 역할 분류 (Super Admin, Admin, Staff 등)
 * - 각 역할별 권한 레벨 관리
 * - 초기 데이터 자동 삽입
 * 
 * @table admin_user_types
 * @since 2025.08.31
 */
return new class extends Migration
{
    /**
     * 마이그레이션 실행
     * 
     * admin_user_types 테이블을 생성하고 초기 데이터를 삽입합니다.
     * 
     * 테이블 구조:
     * - id: 기본 키
     * - code: 유니크한 타입 코드 (super, admin, staff 등)
     * - name: 타입 표시 명칭
     * - description: 상세 설명
     * - level: 권한 레벨 (0-100, 높을수록 높은 권한)
     * - enable: 활성화 여부
     * - pos: 정렬 순서
     * - timestamps: 생성/수정 시간
     */
    public function up(): void
    {
        Schema::create('admin_user_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('타입 코드');
            $table->string('name')->comment('타입 명칭');
            $table->string('description')->nullable()->comment('설명');
            $table->integer('level')->default(0)->comment('권한 레벨 (높을수록 높은 권한)');
            $table->boolean('enable')->default(true)->comment('활성화 여부');
            $table->integer('pos')->default(0)->comment('정렬 순서');
            $table->timestamps();

            $table->index('code');
            $table->index('enable');
            $table->index('level');
        });

        // 초기 데이터 삽입
        $this->insertInitialData();
    }

    /**
     * 마이그레이션 롤백
     * 
     * admin_user_types 테이블을 삭제합니다.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user_types');
    }

    /**
     * 초기 사용자 타입 데이터 삽입
     * 
     * 기본 사용자 타입 3개를 생성합니다:
     * 
     * 1. Super Admin (level: 100)
     *    - 최고 관리자
     *    - 모든 권한 보유
     *    - 시스템 전체 제어 가능
     * 
     * 2. Administrator (level: 50)
     *    - 일반 관리자
     *    - 시스템 설정을 제외한 대부분의 권한
     *    - 콘텐츠 및 사용자 관리
     * 
     * 3. Staff (level: 10)
     *    - 스태프 사용자
     *    - 콘텐츠 관리 및 기본 운영 권한
     *    - 제한된 관리 기능
     */
    private function insertInitialData(): void
    {
        $now = now();

        DB::table('admin_user_types')->insert([
            [
                'code' => 'super',
                'name' => 'Super Admin',
                'description' => '최고 관리자 - 모든 권한을 가진 시스템 관리자',
                'level' => 100,
                'enable' => true,
                'pos' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'admin',
                'name' => 'Administrator',
                'description' => '일반 관리자 - 시스템 설정을 제외한 대부분의 권한',
                'level' => 50,
                'enable' => true,
                'pos' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'staff',
                'name' => 'Staff',
                'description' => '스태프 - 콘텐츠 관리 및 기본 운영 권한',
                'level' => 10,
                'enable' => true,
                'pos' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
};
