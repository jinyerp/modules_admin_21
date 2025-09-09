<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * users 테이블에 2차 인증(2FA) 관련 필드 추가
 * 
 * 관리자 보안 강화를 위한 2차 인증 기능을 지원하기 위해
 * users 테이블에 필요한 필드를 추가합니다.
 * 
 * 주요 기능:
 * - TOTP 기반 2차 인증
 * - 복구 코드 지원
 * - 2FA 사용 기록 추적
 * 
 * @table users
 * @since 2025.09.02
 */
return new class extends Migration
{
    /**
     * 마이그레이션 실행
     * 
     * users 테이블에 2차 인증 관련 필드를 추가합니다.
     * 
     * 추가 필드:
     * - two_factor_secret (string, nullable): 
     *   2FA 비밀 키 (암호화된 TOTP secret)
     * 
     * - two_factor_recovery_codes (text, nullable): 
     *   복구 코드 목록 (JSON 형식으로 저장)
     *   비상시 2FA를 우회할 수 있는 일회용 코드
     * 
     * - two_factor_confirmed_at (timestamp, nullable): 
     *   2FA 설정 확인 시간
     *   최초 2FA 설정 완료 시간
     * 
     * - two_factor_enabled (boolean): 
     *   2FA 활성화 여부 (기본값: false)
     * 
     * - last_2fa_used_at (timestamp, nullable): 
     *   마지막 2FA 사용 시간
     *   2FA 사용 패턴 분석에 활용
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('two_factor_secret')->nullable()->after('password');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            $table->boolean('two_factor_enabled')->default(false)->after('two_factor_confirmed_at');
            $table->timestamp('last_2fa_used_at')->nullable()->after('two_factor_enabled');
        });
    }

    /**
     * 마이그레이션 롤백
     * 
     * users 테이블에서 2차 인증 관련 필드를 제거합니다.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
                'two_factor_enabled',
                'last_2fa_used_at',
            ]);
        });
    }
};
