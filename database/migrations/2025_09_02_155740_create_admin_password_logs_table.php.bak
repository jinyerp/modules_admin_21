<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 관리자 비밀번호 시도 로그 테이블 생성
 * 
 * 비밀번호 입력 시도를 추적하고 무차별 대입 공격(brute force)을
 * 방지하기 위한 admin_password_logs 테이블을 생성합니다.
 * 
 * 주요 기능:
 * - 비밀번호 실패 시도 추적
 * - IP/이메일별 시도 횟수 카운트
 * - 자동 차단 및 해제 관리
 * - 브라우저/디바이스 정보 수집
 * 
 * @table admin_password_logs
 * @since 2025.09.02
 */
return new class extends Migration
{
    /**
     * 마이그레이션 실행
     * 
     * admin_password_logs 테이블을 생성합니다.
     * 
     * 테이블 구조:
     * - id: 기본 키
     * - email: 시도한 이메일 주소
     * - user_id: 존재하는 사용자의 ID (nullable)
     * - ip_address: 접속 IP 주소 (IPv4/IPv6 지원, 최대 45자)
     * - user_agent: 브라우저 User-Agent 전체 문자열
     * - browser: 파싱된 브라우저 정보 (Chrome, Firefox 등)
     * - platform: 운영체제 정보 (Windows, macOS, Linux 등)
     * - device: 디바이스 타입 (Desktop, Mobile, Tablet)
     * - attempt_count: 누적 시도 횟수
     * - first_attempt_at: 첫 번째 실패 시도 시간
     * - last_attempt_at: 마지막 실패 시도 시간
     * - is_blocked: 현재 차단 상태 여부
     * - blocked_at: 차단 처리 시간
     * - unblocked_at: 차단 해제 시간
     * - status: 현재 상태
     *   - failed: 실패하지만 차단 안 됨
     *   - blocked: 차단됨
     *   - resolved: 해결됨
     * - details: 추가 상세 정보 (JSON)
     * - timestamps: 생성/수정 시간
     * 
     * 인덱스:
     * - [email, ip_address]: 이메일+IP 조합 조회
     * - [email, last_attempt_at]: 시간대별 시도 조회
     * - is_blocked: 차단된 계정 필터링
     * - created_at: 시간 기반 정렬
     */
    public function up(): void
    {
        Schema::create('admin_password_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index(); // 시도한 이메일
            $table->unsignedBigInteger('user_id')->nullable()->index(); // 존재하는 사용자인 경우
            $table->string('ip_address', 45); // IP 주소 (IPv4/IPv6)
            $table->text('user_agent')->nullable(); // User Agent
            $table->string('browser')->nullable(); // 브라우저 정보
            $table->string('platform')->nullable(); // 플랫폼 정보
            $table->string('device')->nullable(); // 디바이스 종류
            $table->integer('attempt_count')->default(1); // 시도 횟수
            $table->timestamp('first_attempt_at'); // 첫 시도 시간
            $table->timestamp('last_attempt_at'); // 마지막 시도 시간
            $table->boolean('is_blocked')->default(false); // 차단 여부
            $table->timestamp('blocked_at')->nullable(); // 차단 시간
            $table->timestamp('unblocked_at')->nullable(); // 차단 해제 시간
            $table->string('status')->default('failed'); // 상태
            $table->json('details')->nullable(); // 추가 정보
            $table->timestamps();

            // 성능 최적화를 위한 복합 인덱스
            $table->index(['email', 'ip_address']);
            $table->index(['email', 'last_attempt_at']);
            $table->index('is_blocked');
            $table->index('created_at');
        });
    }

    /**
     * 마이그레이션 롤백
     * 
     * admin_password_logs 테이블을 삭제합니다.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_password_logs');
    }
};
