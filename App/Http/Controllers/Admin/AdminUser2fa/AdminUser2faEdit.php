<?php

namespace Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Jiny\Admin\App\Models\User;
use Jiny\admin\App\Services\JsonConfigService;
use Jiny\Admin\App\Services\TwoFactorAuthService;

/**
 * AdminUser2faEdit Controller
 * 
 * 2FA 설정 편집 및 관리 기능을 제공합니다.
 * TwoFactorAuthService를 사용하여 모든 2FA 관련 작업을 처리합니다.
 */
class AdminUser2faEdit extends Controller
{
    private $jsonData;
    private $twoFactorService;

    public function __construct()
    {
        // 서비스를 사용하여 JSON 파일 로드
        $jsonConfigService = new JsonConfigService;
        $this->jsonData = $jsonConfigService->loadFromControllerPath(__DIR__);
        
        // 2FA 서비스 초기화
        $this->twoFactorService = new TwoFactorAuthService();
    }

    /**
     * Show the form for editing 2FA settings
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        // 세션에서 임시 데이터 가져오기
        $sessionData = $this->twoFactorService->getFromSession($user->id, [
            'secret',
            'qr',
            'backup',
            'regenerated_backup_codes'
        ]);
        
        $secret = $sessionData['secret'];
        $qrCodeImage = $sessionData['qr'];
        $backupCodes = $sessionData['backup'];

        // 재생성된 백업 코드 확인
        if ($sessionData['regenerated_backup_codes']) {
            $backupCodes = $sessionData['regenerated_backup_codes'];
            $this->twoFactorService->clearSession($user->id, ['regenerated_backup_codes']);
        }
        
        // 2FA 상태 정보 가져오기
        $twoFactorStatus = $this->twoFactorService->getStatus($user);

        return view('jiny-admin::admin.admin_user2fa.edit', compact(
            'user', 'secret', 'qrCodeImage', 'backupCodes', 'twoFactorStatus'
        ));
    }

    /**
     * Generate QR code and backup codes for initial setup
     */
    public function generate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Check if 2FA is already enabled
        if ($user->two_factor_enabled) {
            return redirect()->route('admin.user.2fa.edit', $id)
                ->with('error', '2FA가 이미 활성화되어 있습니다.');
        }

        // 2FA 초기 설정 생성
        $setupData = $this->twoFactorService->setupTwoFactor($user);

        // 세션에 임시 저장
        $this->twoFactorService->storeInSession($user->id, [
            'secret' => $setupData['secret'],
            'qr' => $setupData['qrCodeImage'],
            'backup' => $setupData['backupCodes']
        ]);

        return redirect()->route('admin.user.2fa.edit', $id)
            ->with('info', 'QR 코드와 백업 코드가 생성되었습니다. 인증 앱으로 QR 코드를 스캔하고 인증 코드를 입력해주세요.');
    }

    /**
     * Store and verify 2FA setup
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
            'secret' => 'required|string',
            'backup_codes' => 'required|array',
        ]);

        $user = User::findOrFail($id);

        // 2FA 활성화 시도
        $success = $this->twoFactorService->enableTwoFactor(
            $user,
            $request->secret,
            $request->backup_codes,
            $request->verification_code
        );

        if (!$success) {
            // 인증 실패 시 세션 데이터 유지
            return redirect()->route('admin.user.2fa.edit', $id)
                ->with('error', '인증 코드가 올바르지 않습니다. 다시 시도해주세요.')
                ->withInput();
        }

        // 성공 시 세션 데이터 삭제
        $this->twoFactorService->clearSession($user->id);

        return redirect()->route('admin.user.2fa.edit', $id)
            ->with('success', '2FA가 성공적으로 활성화되었습니다.');
    }

    /**
     * Regenerate backup codes
     */
    public function regenerateBackup(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 백업 코드 재생성
        $backupCodes = $this->twoFactorService->regenerateBackupCodes($user);
        
        if (!$backupCodes) {
            return redirect()->route('admin.user.2fa.edit', $id)
                ->with('error', '2FA가 활성화되어 있지 않습니다.');
        }

        // 세션에 저장하여 표시
        $this->twoFactorService->storeInSession($user->id, [
            'regenerated_backup_codes' => $backupCodes
        ]);

        return redirect()->route('admin.user.2fa.edit', $id)
            ->with('success', '백업 코드가 재생성되었습니다. 새로운 코드를 안전한 곳에 보관하세요.');
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$user->two_factor_enabled) {
            return redirect()->route('admin.user.2fa.edit', $id)
                ->with('error', '2FA가 이미 비활성화되어 있습니다.');
        }

        // 2FA 비활성화
        $this->twoFactorService->disableTwoFactor($user, false);

        // 세션 데이터 삭제
        $this->twoFactorService->clearSession($user->id);

        return redirect()->route('admin.user.2fa.edit', $id)
            ->with('success', '2FA가 비활성화되었습니다.');
    }

    /**
     * Force disable 2FA (for admin use)
     */
    public function forceDisable($id)
    {
        $user = User::findOrFail($id);

        // 강제로 2FA 비활성화
        $this->twoFactorService->disableTwoFactor($user, true);

        // 세션 데이터 삭제
        $this->twoFactorService->clearSession($user->id);

        return redirect()->route('admin.user.2fa.index')
            ->with('success', $user->name.'님의 2FA가 강제로 비활성화되었습니다.');
    }

    /**
     * Check 2FA status (AJAX)
     */
    public function status($id)
    {
        $user = User::findOrFail($id);
        
        // 2FA 상태 정보 가져오기
        $status = $this->twoFactorService->getStatus($user);

        return response()->json($status);
    }
}
