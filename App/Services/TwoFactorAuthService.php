<?php

namespace Jiny\Admin\App\Services;

use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FAQRCode\Google2FA as Google2FAQRCode;
use Jiny\Admin\App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Two-Factor Authentication Service
 * 
 * 2FA 관련 모든 기능을 처리하는 서비스 클래스
 * QR 코드 생성, 백업 코드 관리, 인증 검증 등을 담당합니다.
 * 
 * @package Jiny\Admin\App\Services
 * @author  @jiny/admin Team
 * @since   1.0.0
 */
class TwoFactorAuthService
{
    /**
     * Google2FA 인스턴스
     *
     * @var Google2FAQRCode
     */
    protected $google2fa;

    /**
     * 생성자
     */
    public function __construct()
    {
        $this->google2fa = new Google2FAQRCode();
    }

    /**
     * 새로운 2FA 비밀키 생성
     *
     * @param  int  $length  비밀키 길이 (기본: 32)
     * @return string
     */
    public function generateSecretKey($length = 32)
    {
        return $this->google2fa->generateSecretKey($length);
    }

    /**
     * QR 코드 URL 생성
     *
     * @param  string  $companyName  회사/앱 이름
     * @param  string  $email  사용자 이메일
     * @param  string  $secret  비밀키
     * @return string
     */
    public function getQRCodeUrl($companyName, $email, $secret)
    {
        return $this->google2fa->getQRCodeUrl(
            $companyName,
            $email,
            $secret
        );
    }

    /**
     * QR 코드 이미지 생성 (Base64 인코딩)
     *
     * @param  string  $companyName  회사/앱 이름
     * @param  string  $email  사용자 이메일
     * @param  string  $secret  비밀키
     * @param  int  $size  QR 코드 크기 (기본: 200)
     * @return string  Base64 인코딩된 이미지
     */
    public function generateQRCodeImage($companyName, $email, $secret, $size = 200)
    {
        // Google2FAQRCode의 getQRCodeInline 메소드 사용
        return $this->google2fa->getQRCodeInline(
            $companyName,
            $email,
            $secret,
            $size
        );
    }

    /**
     * 백업 코드 생성
     *
     * @param  int  $count  생성할 코드 개수 (기본: 8)
     * @return array
     */
    public function generateBackupCodes($count = 8)
    {
        $backupCodes = [];
        for ($i = 0; $i < $count; $i++) {
            $backupCodes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }
        return $backupCodes;
    }

    /**
     * 인증 코드 검증
     *
     * @param  string  $secret  비밀키
     * @param  string  $code  인증 코드
     * @param  int|null  $window  시간 창 (기본: null)
     * @return bool
     */
    public function verifyCode($secret, $code, $window = null)
    {
        return $this->google2fa->verifyKey($secret, $code, $window);
    }

    /**
     * 백업 코드 검증 및 사용
     *
     * @param  User  $user  사용자 모델
     * @param  string  $code  백업 코드
     * @return bool
     */
    public function verifyBackupCode(User $user, $code)
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $backupCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        $code = strtoupper($code);

        if (in_array($code, $backupCodes)) {
            // 사용된 코드 제거
            $backupCodes = array_values(array_diff($backupCodes, [$code]));
            
            // 업데이트된 코드 저장
            $user->two_factor_recovery_codes = encrypt(json_encode($backupCodes));
            $user->save();

            // 사용 로그 기록
            $this->logBackupCodeUsage($user, $code);

            return true;
        }

        return false;
    }

    /**
     * 2FA 초기 설정
     *
     * @param  User  $user  사용자 모델
     * @return array  설정 정보 (secret, qrCode, backupCodes)
     */
    public function setupTwoFactor(User $user)
    {
        // 비밀키 생성
        $secret = $this->generateSecretKey();

        // QR 코드 이미지 생성 (직접 이미지 생성)
        $companyName = config('app.name', 'Laravel');
        $qrCodeImage = $this->generateQRCodeImage($companyName, $user->email, $secret);

        // 백업 코드 생성
        $backupCodes = $this->generateBackupCodes();

        return [
            'secret' => $secret,
            'qrCodeImage' => $qrCodeImage,
            'backupCodes' => $backupCodes,
        ];
    }

    /**
     * 2FA 활성화
     *
     * @param  User  $user  사용자 모델
     * @param  string  $secret  비밀키
     * @param  array  $backupCodes  백업 코드
     * @param  string  $verificationCode  검증 코드
     * @return bool
     */
    public function enableTwoFactor(User $user, $secret, array $backupCodes, $verificationCode)
    {
        // 인증 코드 검증
        if (!$this->verifyCode($secret, $verificationCode)) {
            return false;
        }

        // 2FA 설정 저장
        $user->two_factor_secret = encrypt($secret);
        $user->two_factor_recovery_codes = encrypt(json_encode($backupCodes));
        $user->two_factor_confirmed_at = now();
        $user->two_factor_enabled = true;
        $user->save();

        // 활성화 로그 기록
        $this->logTwoFactorAction($user, 'enabled', '2FA가 활성화되었습니다');

        return true;
    }

    /**
     * 2FA 비활성화
     *
     * @param  User  $user  사용자 모델
     * @param  bool  $force  강제 비활성화 여부
     * @return void
     */
    public function disableTwoFactor(User $user, $force = false)
    {
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_enabled = false;
        $user->last_2fa_used_at = null;
        $user->save();

        // 비활성화 로그 기록
        $action = $force ? 'force_disabled' : 'disabled';
        $description = $force ? '2FA가 강제로 비활성화되었습니다' : '2FA가 비활성화되었습니다';
        $this->logTwoFactorAction($user, $action, $description);
    }

    /**
     * 백업 코드 재생성
     *
     * @param  User  $user  사용자 모델
     * @return array|null  새로운 백업 코드
     */
    public function regenerateBackupCodes(User $user)
    {
        if (!$user->two_factor_enabled) {
            return null;
        }

        $backupCodes = $this->generateBackupCodes();
        
        $user->two_factor_recovery_codes = encrypt(json_encode($backupCodes));
        $user->save();

        // 재생성 로그 기록
        $this->logTwoFactorAction($user, 'backup_codes_regenerated', '백업 코드가 재생성되었습니다');

        return $backupCodes;
    }

    /**
     * 2FA 상태 확인
     *
     * @param  User  $user  사용자 모델
     * @return array
     */
    public function getStatus(User $user)
    {
        $backupCodesCount = 0;
        if ($user->two_factor_recovery_codes) {
            $backupCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            $backupCodesCount = count($backupCodes);
        }

        return [
            'enabled' => $user->two_factor_enabled,
            'confirmed_at' => $user->two_factor_confirmed_at,
            'last_used_at' => $user->last_2fa_used_at,
            'backup_codes_count' => $backupCodesCount,
            'has_secret' => !empty($user->two_factor_secret),
        ];
    }

    /**
     * 2FA 사용 시간 업데이트
     *
     * @param  User  $user  사용자 모델
     * @return void
     */
    public function updateLastUsedAt(User $user)
    {
        $user->last_2fa_used_at = now();
        $user->save();
    }

    /**
     * 2FA 작업 로그 기록
     *
     * @param  User  $user  사용자 모델
     * @param  string  $action  작업 유형
     * @param  string  $description  설명
     * @param  array|null  $metadata  추가 메타데이터
     * @return void
     */
    protected function logTwoFactorAction(User $user, $action, $description, $metadata = null)
    {
        DB::table('admin_user2fas')->insert([
            'user_id' => $user->id,
            'method' => 'google2fa',
            'enabled' => $user->two_factor_enabled,
            'last_used_at' => $user->last_2fa_used_at,
            'backup_codes_used' => 0,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 일반 로그에도 기록
        DB::table('admin_user_logs')->insert([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'action' => '2fa_' . $action,
            'description' => $description,
            'details' => json_encode([
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email ?? 'system',
                'metadata' => $metadata,
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'logged_at' => now(),
            'created_at' => now(),
        ]);
    }

    /**
     * 백업 코드 사용 로그 기록
     *
     * @param  User  $user  사용자 모델
     * @param  string  $code  사용된 백업 코드
     * @return void
     */
    protected function logBackupCodeUsage(User $user, $code)
    {
        // 사용된 백업 코드 수 업데이트
        DB::table('admin_user2fas')
            ->where('user_id', $user->id)
            ->increment('backup_codes_used');

        // 로그 기록
        $this->logTwoFactorAction(
            $user,
            'backup_code_used',
            '백업 코드가 사용되었습니다',
            ['code' => substr($code, 0, 4) . '****'] // 보안을 위해 일부만 저장
        );
    }

    /**
     * 세션에 임시 2FA 데이터 저장
     *
     * @param  int  $userId  사용자 ID
     * @param  array  $data  저장할 데이터
     * @return void
     */
    public function storeInSession($userId, array $data)
    {
        foreach ($data as $key => $value) {
            session(['2fa_' . $key . '_' . $userId => $value]);
        }
    }

    /**
     * 세션에서 임시 2FA 데이터 가져오기
     *
     * @param  int  $userId  사용자 ID
     * @param  array  $keys  가져올 키 목록
     * @return array
     */
    public function getFromSession($userId, array $keys)
    {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = session('2fa_' . $key . '_' . $userId);
        }
        return $data;
    }

    /**
     * 세션에서 임시 2FA 데이터 삭제
     *
     * @param  int  $userId  사용자 ID
     * @param  array|null  $keys  삭제할 키 목록 (null이면 모든 키 삭제)
     * @return void
     */
    public function clearSession($userId, array $keys = null)
    {
        if ($keys === null) {
            $keys = ['secret', 'qr', 'backup', 'regenerated_backup_codes'];
        }

        $sessionKeys = [];
        foreach ($keys as $key) {
            $sessionKeys[] = '2fa_' . $key . '_' . $userId;
        }

        session()->forget($sessionKeys);
    }
}