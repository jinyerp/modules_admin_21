<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * admin_user_logs 테이블에 2차 인증 관련 필드 추가
 * 
 * 사용자 활동 로그에 2차 인증 사용 정보를 기록하기 위한
 * 필드를 추가합니다.
 * 
 * 기록 내용:
 * - 2FA 사용 여부
 * - 2FA 인증 방법 (앱, 복구 코드)
 * - 2FA 인증 시도 횟수
 * - 2FA 인증 성공 시간
 * 
 * @table admin_user_logs
 * @since 2025.09.02
 */
return new class extends Migration
{
    /**
     * 마이그레이션 실행
     * 
     * admin_user_logs 테이블에 2차 인증 관련 필드를 추가합니다.
     * 
     * 추가 필드:
     * - two_factor_used (boolean): 
     *   2FA 사용 여부 (기본값: false)
     *   로그인 시 2FA를 사용했는지 기록
     * 
     * - two_factor_method (enum): 
     *   2FA 인증 방법 (['app', 'backup', 'none'])
     *   - app: TOTP 앱 사용
     *   - backup: 복구 코드 사용
     *   - none: 2FA 미사용 (기본값)
     * 
     * - two_factor_required (boolean): 
     *   2FA 필수 여부 (기본값: false)
     *   해당 사용자에게 2FA가 필수였는지 기록
     * 
     * - two_factor_verified_at (timestamp, nullable): 
     *   2FA 인증 성공 시간
     * 
     * - two_factor_attempts (integer): 
     *   2FA 시도 횟수 (기본값: 0)
     *   실패 횟수 추적용
     * 
     * 인덱스:
     * - two_factor_used: 2FA 사용 통계 조회
     * - two_factor_method: 인증 방법별 통계
     */
    public function up(): void
    {
        Schema::table('admin_user_logs', function (Blueprint $table) {
            // 2FA 관련 컬럼 추가
            $table->boolean('two_factor_used')->default(false)->after('status')->comment('2FA 사용 여부');
            $table->enum('two_factor_method', ['app', 'backup', 'none'])->default('none')->after('two_factor_used')->comment('2FA 인증 방법');
            $table->boolean('two_factor_required')->default(false)->after('two_factor_method')->comment('2FA 필수 여부');
            $table->timestamp('two_factor_verified_at')->nullable()->after('two_factor_required')->comment('2FA 인증 시간');
            $table->integer('two_factor_attempts')->default(0)->after('two_factor_verified_at')->comment('2FA 시도 횟수');

            // 성능 최적화를 위한 인덱스
            $table->index('two_factor_used');
            $table->index('two_factor_method');
        });
    }

    /**
     * 마이그레이션 롤백
     * 
     * admin_user_logs 테이블에서 2차 인증 관련 필드를 제거합니다.
     * 인덱스를 먼저 제거한 후 컬럼을 삭제합니다.
     */
    public function down(): void
    {
        Schema::table('admin_user_logs', function (Blueprint $table) {
            // 인덱스 제거 (컬럼 제거 전에 수행)
            $table->dropIndex(['two_factor_used']);
            $table->dropIndex(['two_factor_method']);

            // 2FA 관련 컬럼 제거
            $table->dropColumn([
                'two_factor_used',
                'two_factor_method',
                'two_factor_required',
                'two_factor_verified_at',
                'two_factor_attempts',
            ]);
        });
    }
};
