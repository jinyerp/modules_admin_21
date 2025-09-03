<?php

namespace Jiny\Admin\App\Http\Controllers\Web\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Validation\ValidationException; // Not needed anymore
use Jiny\Admin\App\Models\AdminUserLog;
use Jiny\Admin\App\Models\AdminUsertype;
use Jiny\Admin\App\Models\AdminUserSession;
use Jiny\Admin\App\Models\AdminPasswordLog;
use Jiny\Admin\App\Models\User;
use Jiny\Admin\App\Http\Controllers\Web\Login\Admin2FAController;

/**
 * 관리자 인증 컨트롤러
 * 
 * 관리자 로그인, 로그아웃, 권한 검증 및 세션 관리를 담당합니다.
 * IP 차단, 비밀번호 실패 횟수 추적, 2FA 인증 등의 보안 기능을 포함합니다.
 */
class AdminAuthController extends Controller
{
    /**
     * 관리자 로그인 처리
     * 
     * 사용자 인증을 수행하고 다음과 같은 보안 기능을 처리합니다:
     * - IP 차단 검사
     * - 관리자 권한 검증
     * - 사용자 타입 검증
     * - 2FA 인증 필요 여부 확인
     * - 로그인 실패 횟수 기록
     * - 세션 추적 및 로그 기록
     * 
     * @param Request $request HTTP 요청 객체
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // ValidationException을 사용하지 않고 직접 리다이렉트 처리
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.login')
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $validator->validated();
        
        // 차단된 IP인지 확인
        if (AdminPasswordLog::isBlocked($credentials['email'], $request->ip())) {
            session()->flash('notification', [
                'type' => 'error',
                'title' => '접근 차단',
                'message' => '너무 많은 로그인 시도로 인해 접근이 차단되었습니다. 관리자에게 문의하세요.',
            ]);
            
            return redirect()->route('admin.login')
                ->withErrors(['email' => '접근이 차단되었습니다. 관리자에게 문의하세요.'])
                ->withInput($request->except('password'));
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // 관리자 권한 검증
            // 1. isAdmin이 true여야 함
            // 2. utype이 admin_user_types 테이블에 존재해야 함
            if (!$user->isAdmin) {
                Auth::logout();
                
                // 권한 없음 로그 기록
                AdminUserLog::log('unauthorized_login', null, [
                    'email' => $request->input('email'),
                    'reason' => 'Not an admin user (isAdmin=false)',
                    'ip_address' => $request->ip(),
                    'attempt_time' => now()->toDateTimeString(),
                ]);
                
                session()->flash('notification', [
                    'type' => 'error',
                    'title' => '접근 거부',
                    'message' => '관리자 권한이 없습니다.',
                ]);
                
                return redirect()->route('admin.login')
                    ->withErrors(['email' => '관리자 권한이 없습니다.'])
                    ->withInput($request->except('password'));
            }
            
            // utype이 설정되어 있고 admin_user_types 테이블에 존재하는지 확인
            if ($user->utype) {
                $adminUserType = AdminUsertype::where('code', $user->utype)
                    ->where('enable', true)  // 활성화된 타입만
                    ->first();
                    
                if (!$adminUserType) {
                    Auth::logout();
                    
                    // 권한 없음 로그 기록
                    AdminUserLog::log('unauthorized_login', null, [
                        'email' => $request->input('email'),
                        'reason' => 'Invalid or inactive user type: ' . $user->utype,
                        'ip_address' => $request->ip(),
                        'attempt_time' => now()->toDateTimeString(),
                    ]);
                    
                    session()->flash('notification', [
                        'type' => 'error',
                        'title' => '접근 거부',
                        'message' => '유효하지 않은 사용자 유형입니다.',
                    ]);
                    
                    return redirect()->route('admin.login')
                        ->withErrors(['email' => '유효하지 않은 사용자 유형입니다.'])
                        ->withInput($request->except('password'));
                }
            } else {
                // utype이 null인 경우도 관리자로 접근 불가
                Auth::logout();
                
                // 권한 없음 로그 기록
                AdminUserLog::log('unauthorized_login', null, [
                    'email' => $request->input('email'),
                    'reason' => 'User type not set',
                    'ip_address' => $request->ip(),
                    'attempt_time' => now()->toDateTimeString(),
                ]);
                
                session()->flash('notification', [
                    'type' => 'error',
                    'title' => '접근 거부',
                    'message' => '사용자 유형이 설정되지 않았습니다.',
                ]);
                
                return redirect()->route('admin.login')
                    ->withErrors(['email' => '사용자 유형이 설정되지 않았습니다.'])
                    ->withInput($request->except('password'));
            }
            
            // 로그인 성공 시 실패 카운트 초기화
            AdminPasswordLog::resetFailedAttempts($request->input('email'), $request->ip());
            
            // 2FA 체크
            if (Admin2FAController::check2FARequired($user, $request)) {
                // 2FA가 필요한 경우 리다이렉트 (로그인 카운트와 로그는 2FA 완료 후 기록됨)
                return redirect()->route('admin.2fa.challenge');
            }
            
            // 2FA가 필요없는 경우에만 여기서 처리
            $request->session()->regenerate();
            
            // 마지막 로그인 시간 및 로그인 횟수 업데이트
            $user->last_login_at = now();
            $user->login_count = ($user->login_count ?? 0) + 1;
            $user->save();
            
            // 브라우저 정보 파싱
            $userAgent = $request->header('User-Agent');
            $browser = $this->getBrowserInfo($userAgent);
            
            // 로그인 성공 로그 기록 (2FA 없이)
            AdminUserLog::log('login', $user, [
                'remember' => $request->boolean('remember'),
                'ip_address' => $request->ip(),
                'user_agent' => $userAgent,
                'browser' => $browser['browser'],
                'browser_version' => $browser['version'],
                'platform' => $browser['platform'],
                'protocol' => $request->secure() ? 'HTTPS' : 'HTTP',
                'accept_language' => $request->header('Accept-Language'),
                'referer' => $request->header('Referer'),
                'session_id' => session()->getId(),
                'login_time' => now()->toDateTimeString(),
                'user_type' => $user->utype,
                // 2FA 정보
                'two_factor_required' => false,
                'two_factor_used' => false,
                'two_factor_method' => 'none',
            ]);
            
            // 세션 추적
            $session = AdminUserSession::track($user, $request, false);
            if (!$session) {
                \Log::warning('Failed to track session for user: ' . $user->email);
            }

            return redirect()->intended(route('admin.dashboard'));
        }
        
        // 로그인 실패 시 비밀번호 오류 기록
        $user = User::where('email', $request->input('email'))->first();
        $passwordLog = AdminPasswordLog::recordFailedAttempt(
            $request->input('email'), 
            $request, 
            $user ? $user->id : null
        );
        
        // 로그인 실패 로그 기록 (상세 정보 포함)
        AdminUserLog::log('failed_login', null, [
            'email' => $request->input('email'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'protocol' => $request->secure() ? 'HTTPS' : 'HTTP',
            'accept_language' => $request->header('Accept-Language'),
            'attempt_time' => now()->toDateTimeString(),
            'attempt_count' => $passwordLog->attempt_count,
            'is_blocked' => $passwordLog->is_blocked,
        ]);

        // 설정값 가져오기
        $maxAttempts = config('admin.setting.password.lockout.max_attempts', 5);
        $warningAfterAttempts = config('admin.setting.password.lockout.warning_after_attempts', 3);
        
        // 차단 여부에 따른 메시지 분기
        if ($passwordLog->is_blocked) {
            session()->flash('notification', [
                'type' => 'error',
                'title' => '접근 차단',
                'message' => "{$maxAttempts}회 이상 로그인 실패로 접근이 차단되었습니다. 관리자에게 문의하세요.",
            ]);
            
            return redirect()->route('admin.login')
                ->withErrors(['email' => '접근이 차단되었습니다. 관리자에게 문의하세요.'])
                ->withInput($request->except('password'));
        } else {
            $remainingAttempts = $maxAttempts - $passwordLog->attempt_count;
            $message = '이메일 또는 비밀번호가 올바르지 않습니다.';
            
            // 설정된 경고 횟수 이상 실패 시 경고 메시지 표시
            if ($passwordLog->attempt_count >= $warningAfterAttempts && $remainingAttempts > 0) {
                $message .= " (남은 시도 횟수: {$remainingAttempts}회)";
            }
            
            session()->flash('notification', [
                'type' => 'error',
                'title' => '로그인 실패',
                'message' => $message,
            ]);
            
            return redirect()->route('admin.login')
                ->withErrors(['email' => $message])
                ->withInput($request->except('password'));
        }
    }

    /**
     * 관리자 대시보드 표시
     * 
     * 로그인 성공 후 관리자 대시보드를 표시합니다.
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        return view('jiny-admin::admin.admin_dashboard.dashboard');
    }
    
    /**
     * 브라우저 정보 파싱
     * 
     * User-Agent 문자열에서 브라우저 종류, 버전, 플랫폼 정보를 추출합니다.
     * 
     * @param string $userAgent User-Agent 헤더 값
     * @return array 브라우저, 버전, 플랫폼 정보
     */
    private function getBrowserInfo($userAgent)
    {
        $browser = 'Unknown';
        $version = '';
        $platform = 'Unknown';
        
        // 플랫폼 감지
        if (preg_match('/windows|win32/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platform = 'Mac OS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $platform = 'iOS';
        }
        
        // 브라우저 감지
        if (preg_match('/MSIE|Trident/i', $userAgent)) {
            $browser = 'Internet Explorer';
            preg_match('/MSIE (.*?);/', $userAgent, $matches);
            if (count($matches) > 1) {
                $version = $matches[1];
            }
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
            preg_match('/Firefox\/([0-9\.]+)/', $userAgent, $matches);
            if (count($matches) > 1) {
                $version = $matches[1];
            }
        } elseif (preg_match('/OPR|Opera/i', $userAgent)) {
            $browser = 'Opera';
            preg_match('/OPR\/([0-9\.]+)/', $userAgent, $matches);
            if (count($matches) > 1) {
                $version = $matches[1];
            }
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Microsoft Edge';
            preg_match('/Edge\/([0-9\.]+)/', $userAgent, $matches);
            if (count($matches) > 1) {
                $version = $matches[1];
            }
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
            preg_match('/Chrome\/([0-9\.]+)/', $userAgent, $matches);
            if (count($matches) > 1) {
                $version = $matches[1];
            }
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
            preg_match('/Version\/([0-9\.]+)/', $userAgent, $matches);
            if (count($matches) > 1) {
                $version = $matches[1];
            }
        }
        
        return [
            'browser' => $browser,
            'version' => $version,
            'platform' => $platform
        ];
    }
    
    /**
     * 로그아웃 처리
     * 
     * 사용자를 로그아웃하고 관련 세션을 종료합니다.
     * 로그아웃 로그를 기록하고 세션 추적을 종료합니다.
     * 
     * @param Request $request HTTP 요청 객체
     * @return \Illuminate\Http\RedirectResponse 로그인 페이지로 리다이렉트
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $sessionId = session()->getId();
        
        if ($user) {
            // 로그아웃 로그 기록
            AdminUserLog::log('logout', $user, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $sessionId,
                'logout_time' => now()->toDateTimeString(),
            ]);
            
            // 세션 종료
            AdminUserSession::terminate($sessionId);
        }
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with('success', '로그아웃되었습니다.');
    }
}