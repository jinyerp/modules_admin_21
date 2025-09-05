<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 관리자 사용자 세션 테이블 생성
 * 
 * 활성 세션을 추적하고 동시 로그인을 관리하기 위한
 * admin_user_sessions 테이블을 생성합니다.
 * 
 * 주요 기능:
 * - 사용자별 활성 세션 관리
 * - 동시 로그인 제어
 * - 세션별 디바이스/브라우저 정보 추적
 * - 비활성 세션 자동 정리
 * 
 * @table admin_user_sessions
 * @since 2025.09.02
 */
return new class extends Migration
{
    /**
     * 마이그레이션 실행
     * 
     * admin_user_sessions 테이블을 생성합니다.
     * 
     * 테이블 구조:
     * - id: 기본 키
     * - user_id: 사용자 ID (users.id 참조)
     * - session_id: Laravel 세션 ID (unique)
     * - ip_address: 접속 IP 주소 (IPv4/IPv6)
     * - user_agent: 브라우저 User-Agent
     * - last_activity: 마지막 활동 시간
     * - login_at: 로그인 시간
     * - is_active: 세션 활성 상태
     * - browser: 브라우저 이름 (Chrome, Firefox 등)
     * - browser_version: 브라우저 버전
     * - platform: 운영체제 (Windows, macOS 등)
     * - device: 디바이스 타입 (Desktop, Mobile, Tablet)
     * - two_factor_used: 2FA 사용 여부
     * - payload: 추가 세션 데이터 (JSON)
     * - timestamps: 생성/수정 시간
     * 
     * 외래 키:
     * - user_id -> users.id (CASCADE 삭제)
     *   사용자 삭제 시 관련 세션도 함께 삭제
     * 
     * 인덱스:
     * - user_id: 사용자별 세션 조회
     * - session_id: 세션 ID 검색 (unique)
     * - last_activity: 비활성 세션 정리용
     * - [user_id, is_active]: 사용자의 활성 세션 조회
     */
    public function up(): void
    {
        Schema::create('admin_user_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('session_id')->unique();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('last_activity')->index();
            $table->timestamp('login_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('platform')->nullable();
            $table->string('device')->nullable();
            $table->boolean('two_factor_used')->default(false);
            $table->text('payload')->nullable();
            $table->timestamps();

            // 사용자 삭제 시 관련 세션도 함께 삭제
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // 성능 최적화를 위한 복합 인덱스
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * 마이그레이션 롤백
     * 
     * admin_user_sessions 테이블을 삭제합니다.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user_sessions');
    }
};
