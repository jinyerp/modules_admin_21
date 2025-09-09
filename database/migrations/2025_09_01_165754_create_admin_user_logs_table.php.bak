<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 관리자 사용자 활동 로그 테이블 생성
 * 
 * 관리자 시스템에서 사용자의 모든 활동을 기록하는
 * admin_user_logs 테이블을 생성합니다.
 * 
 * 기록 항목:
 * - 로그인/로그아웃 시도
 * - 로그인 실패 기록
 * - IP 주소 및 브라우저 정보
 * - 세션 정보
 * - 기타 중요 활동
 * 
 * @table admin_user_logs
 * @since 2025.09.01
 */
return new class extends Migration
{
    /**
     * 마이그레이션 실행
     * 
     * admin_user_logs 테이블을 생성합니다.
     * 
     * 테이블 구조:
     * - id: 기본 키
     * - user_id: 사용자 ID (로그인 성공 시)
     * - email: 시도한 이메일 주소
     * - name: 사용자 이름
     * - action: 행동 타입 (login, logout, failed_login, password_change 등)
     * - ip_address: 접속 IP 주소
     * - user_agent: 브라우저 User-Agent
     * - details: 추가 상세 정보 (JSON)
     * - session_id: 세션 ID
     * - logged_at: 활동 발생 시간
     * - timestamps: 생성/수정 시간
     * 
     * 인덱스:
     * - user_id: 사용자별 로그 조회
     * - email: 이메일별 로그 조회
     * - action: 활동 타입별 필터링
     * - logged_at: 시간대별 조회
     * - [user_id, action]: 복합 인덱스
     */
    public function up(): void
    {
        Schema::create('admin_user_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('action'); // login, logout, failed_login, password_change, etc.
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('details')->nullable(); // 추가 상세 정보
            $table->string('session_id')->nullable();
            $table->timestamp('logged_at');
            $table->timestamps();

            // 성능 최적화를 위한 인덱스
            $table->index('user_id');
            $table->index('email');
            $table->index('action');
            $table->index('logged_at');
            $table->index(['user_id', 'action']);
        });
    }

    /**
     * 마이그레이션 롤백
     * 
     * admin_user_logs 테이블을 삭제합니다.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user_logs');
    }
};
