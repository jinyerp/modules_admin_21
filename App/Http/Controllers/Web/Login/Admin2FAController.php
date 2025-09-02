<?php

namespace Jiny\Admin\App\Http\Controllers\Web\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use Jiny\Admin\App\Models\AdminUserLog;
use Jiny\Admin\App\Models\AdminUserSession;

class Admin2FAController extends Controller
{
    /**
     * 2FA 인증 페이지 표시
     */
    public function showChallenge(Request $request)
    {
        // 세션에 사용자 ID가 없으면 로그인 페이지로 리다이렉트
        if (!$request->session()->has('2fa_user_id')) {
            return redirect()->route('admin.login');
        }
        
        return view('jiny-admin::Site.Login.2fa_challenge');
    }
    
    /**
     * 2FA 코드 검증
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);
        
        // 세션에서 사용자 ID 가져오기
        $userId = $request->session()->get('2fa_user_id');
        
        if (!$userId) {
            return redirect()->route('admin.login')
                ->with('error', '세션이 만료되었습니다. 다시 로그인해주세요.');
        }
        
        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('admin.login')
                ->with('error', '사용자를 찾을 수 없습니다.');
        }
        
        $code = $request->input('code');
        $useBackup = $request->input('use_backup', false);
        
        // 시도 횟수 추적
        $attempts = $request->session()->get('2fa_attempts', 0) + 1;
        $request->session()->put('2fa_attempts', $attempts);
        
        $verified = false;
        $method = 'none';
        
        if ($useBackup) {
            // 백업 코드 검증
            $backupCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
            
            if (in_array($code, $backupCodes)) {
                // 사용된 백업 코드 제거
                $backupCodes = array_diff($backupCodes, [$code]);
                $user->two_factor_recovery_codes = encrypt(json_encode(array_values($backupCodes)));
                $user->save();
                
                $verified = true;
                $method = 'backup';
            }
        } else {
            // Google Authenticator 코드 검증
            $google2fa = new Google2FA();
            $secret = decrypt($user->two_factor_secret);
            
            $verified = $google2fa->verifyKey($secret, $code);
            if ($verified) {
                $method = 'app';
            }
        }
        
        if ($verified) {
            // 2FA 검증 성공 정보를 세션에 저장
            $request->session()->put('2fa_completed', [
                'user_id' => $user->id,
                'method' => $method,
                'verified_at' => now(),
                'attempts' => $attempts
            ]);
            
            // 2FA 검증 성공
            Auth::login($user);
            
            // 마지막 2FA 사용 시간 및 로그인 횟수 업데이트
            $user->last_2fa_used_at = now();
            $user->last_login_at = now();
            $user->login_count = ($user->login_count ?? 0) + 1;
            $user->save();
            
            // 로그인 성공 로그 기록 (2FA 포함)
            AdminUserLog::log('login', $user, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'two_factor_used' => true,
                'two_factor_method' => $method,
                'two_factor_required' => true,
                'two_factor_verified_at' => now(),
                'two_factor_attempts' => $attempts,
            ]);
            
            // 세션 추적
            $session = AdminUserSession::track($user, $request, true);
            if (!$session) {
                \Log::warning('Failed to track session for user with 2FA: ' . $user->email);
            }
            
            // 세션 정리
            $request->session()->forget(['2fa_user_id', '2fa_user_email', '2fa_attempts']);
            
            // Remember me 처리
            if ($request->session()->has('2fa_remember')) {
                Auth::login($user, true);
                $request->session()->forget('2fa_remember');
            }
            
            return redirect()->intended(route('admin.dashboard'));
        }
        
        // 실패 시 최대 시도 횟수 체크
        if ($attempts >= 5) {
            // 세션 초기화하고 로그인 페이지로
            $request->session()->forget(['2fa_user_id', '2fa_user_email', '2fa_attempts', '2fa_remember']);
            return redirect()->route('admin.login')
                ->with('error', '너무 많은 시도로 인해 세션이 종료되었습니다. 다시 로그인해주세요.');
        }
        
        return back()->with('error', '인증 코드가 올바르지 않습니다. (' . $attempts . '/5 시도)');
    }
    
    /**
     * 로그인 시 2FA 체크 (미들웨어에서 호출)
     */
    public static function check2FARequired($user, Request $request)
    {
        if ($user->two_factor_enabled && $user->two_factor_secret) {
            // 사용자 정보를 세션에 저장
            $request->session()->put('2fa_user_id', $user->id);
            $request->session()->put('2fa_user_email', $user->email);
            
            // Remember me 옵션 저장
            if ($request->has('remember')) {
                $request->session()->put('2fa_remember', true);
            }
            
            // 로그아웃 (2FA 검증 전까지 로그인 상태가 되면 안됨)
            Auth::logout();
            
            return true;
        }
        
        return false;
    }
}