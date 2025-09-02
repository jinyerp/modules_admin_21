<?php

namespace Jiny\Admin\App\Http\Controllers\Admin\AdminUser2fa;

use App\Http\Controllers\Controller;
use Jiny\Admin\App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * AdminUser2fa Edit Controller
 * 
 * 2FA 설정 관리 컨트롤러
 *
 * @package Jiny\Admin
 */
class AdminUser2faEdit extends Controller
{
    protected $google2fa;
    private $jsonData;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
        $this->jsonData = $this->loadJsonFromCurrentPath();
    }

    /**
     * __DIR__에서 AdminUser2fa.json 파일을 읽어오는 메소드
     */
    private function loadJsonFromCurrentPath()
    {
        try {
            $jsonFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminUser2fa.json';
            
            if (!file_exists($jsonFilePath)) {
                return null;
            }

            $jsonContent = file_get_contents($jsonFilePath);
            $jsonData = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            return $jsonData;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Show the form for editing 2FA settings
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // Check if data already exists in session (after generating QR code)
        $secret = session('2fa_secret_' . $user->id);
        $qrCodeImage = session('2fa_qr_' . $user->id);
        $backupCodes = session('2fa_backup_' . $user->id);
        
        // Check for regenerated backup codes
        $regeneratedBackupCodes = session('regenerated_backup_codes_' . $user->id);
        if ($regeneratedBackupCodes) {
            $backupCodes = $regeneratedBackupCodes;
            session()->forget('regenerated_backup_codes_' . $user->id);
        }
        
        return view('jiny-admin::admin.admin_user2fa.edit', compact('user', 'secret', 'qrCodeImage', 'backupCodes'));
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
        
        // Generate new secret
        $secret = $this->google2fa->generateSecretKey(32);
        
        // Generate QR code URL
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'Laravel'),
            $user->email,
            $secret
        );
        
        // Generate QR code image
        $qrCodeImage = 'data:image/svg+xml;base64,' . base64_encode(
            QrCode::size(200)->generate($qrCodeUrl)
        );
        
        // Generate backup codes
        $backupCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $backupCodes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }
        
        // Store in session temporarily
        session([
            '2fa_secret_' . $user->id => $secret,
            '2fa_qr_' . $user->id => $qrCodeImage,
            '2fa_backup_' . $user->id => $backupCodes,
        ]);
        
        return redirect()->route('admin.user.2fa.edit', $id);
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
        
        // Verify the code
        $valid = $this->google2fa->verifyKey($request->secret, $request->verification_code);
        
        if (!$valid) {
            // Keep session data for retry
            return redirect()->route('admin.user.2fa.edit', $id)
                ->with('error', '인증 코드가 올바르지 않습니다. 다시 시도해주세요.')
                ->withInput();
        }
        
        // Save 2FA settings
        $user->two_factor_secret = encrypt($request->secret);
        $user->two_factor_recovery_codes = encrypt(json_encode($request->backup_codes));
        $user->two_factor_confirmed_at = now();
        $user->two_factor_enabled = true;
        $user->save();
        
        // Clear session data
        session()->forget([
            '2fa_secret_' . $user->id,
            '2fa_qr_' . $user->id,
            '2fa_backup_' . $user->id,
        ]);
        
        return redirect()->route('admin.user.2fa.edit', $id)
            ->with('success', '2FA가 성공적으로 활성화되었습니다.');
    }

    /**
     * Regenerate backup codes
     */
    public function regenerateBackup(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if (!$user->two_factor_enabled) {
            return redirect()->route('admin.user.2fa.edit', $id)
                ->with('error', '2FA가 활성화되어 있지 않습니다.');
        }
        
        // Generate new backup codes
        $backupCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $backupCodes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }
        
        // Save new backup codes
        $user->two_factor_recovery_codes = encrypt(json_encode($backupCodes));
        $user->save();
        
        // Store in session for display
        session(['regenerated_backup_codes_' . $user->id => $backupCodes]);
        
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
        
        // Disable 2FA
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_enabled = false;
        $user->last_2fa_used_at = null;
        $user->save();
        
        // Clear any session data
        session()->forget([
            '2fa_secret_' . $user->id,
            '2fa_qr_' . $user->id,
            '2fa_backup_' . $user->id,
            'regenerated_backup_codes_' . $user->id,
        ]);
        
        return redirect()->route('admin.user.2fa.edit', $id)
            ->with('success', '2FA가 비활성화되었습니다.');
    }

    /**
     * Force disable 2FA (for admin use)
     */
    public function forceDisable($id)
    {
        $user = User::findOrFail($id);
        
        // Force disable 2FA without verification
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_enabled = false;
        $user->last_2fa_used_at = null;
        $user->save();
        
        // Clear any session data
        session()->forget([
            '2fa_secret_' . $user->id,
            '2fa_qr_' . $user->id,
            '2fa_backup_' . $user->id,
            'regenerated_backup_codes_' . $user->id,
        ]);
        
        return redirect()->route('admin.user.2fa.index')
            ->with('success', $user->name . '님의 2FA가 강제로 비활성화되었습니다.');
    }

    /**
     * Check 2FA status (AJAX)
     */
    public function status($id)
    {
        $user = User::findOrFail($id);
        
        return response()->json([
            'enabled' => $user->two_factor_enabled,
            'confirmed_at' => $user->two_factor_confirmed_at,
            'last_used_at' => $user->last_2fa_used_at,
            'backup_codes_count' => $user->two_factor_recovery_codes 
                ? count(json_decode(decrypt($user->two_factor_recovery_codes), true))
                : 0,
        ]);
    }
}