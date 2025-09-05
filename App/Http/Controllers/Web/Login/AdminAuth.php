<?php

namespace Jiny\Admin\App\Http\Controllers\Web\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Validation\ValidationException; // Not needed anymore
use Jiny\Admin\App\Models\AdminPasswordLog;
use Jiny\Admin\App\Models\AdminUserLog;
use Jiny\Admin\App\Models\AdminUserSession;
use Jiny\Admin\App\Models\AdminUsertype;
use Jiny\Admin\App\Models\User;

/**
 * 관리자 인증 컨트롤러
 *
 * 관리자 로그인, 권한 검증 및 세션 관리를 담당합니다.
 * IP 차단, 비밀번호 실패 횟수 추적, 2FA 인증 등의 보안 기능을 포함합니다.
 */
class AdminAuth extends Controller
{
    /**
     * 컨트롤러 생성자
     */
    public function __construct()
    {
        // 설정은 config('admin.setting')에서 직접 읽음
    }

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
     * @param  Request  $request  HTTP 요청 객체
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Step 1: 입력 유효성 검증
        $validationResponse = $this->validateLoginRequest($request);
        if ($validationResponse) {
            return $validationResponse;
        }

        $credentials = $request->only('email', 'password');

        // Step 2: IP 차단 확인
        $blockResponse = $this->checkIpBlocking($credentials['email'], $request);
        if ($blockResponse) {
            return $blockResponse;
        }

        // Step 3: 자격증명 확인
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return $this->handleFailedLogin($request);
        }

        $user = Auth::user();

        // Step 4: 관리자 권한 검증
        $authorizationResponse = $this->verifyAdminAuthorization($user, $request);
        if ($authorizationResponse) {
            return $authorizationResponse;
        }

        // Step 5: 로그인 성공 시 실패 카운트 초기화
        AdminPasswordLog::resetFailedAttempts($request->input('email'), $request->ip());

        // Step 6: 비밀번호 변경 필요 여부 확인
        $passwordChangeResponse = $this->checkPasswordChangeRequired($user, $request);
        if ($passwordChangeResponse) {
            return $passwordChangeResponse;
        }

        // Step 7: 2FA 확인
        if (Admin2FA::check2FARequired($user, $request)) {
            return redirect()->route('admin.2fa.challenge');
        }

        // Step 8: 로그인 완료 처리
        return $this->completeLogin($user, $request);
    }

    /**
     * Step 1: 로그인 요청 유효성 검증
     */
    private function validateLoginRequest(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.login')
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        return null;
    }

    /**
     * Step 2: IP 차단 확인
     */
    private function checkIpBlocking($email, Request $request)
    {
        if (AdminPasswordLog::isBlocked($email, $request->ip())) {
            session()->flash('notification', [
                'type' => 'error',
                'title' => '접근 차단',
                'message' => '너무 많은 로그인 시도로 인해 접근이 차단되었습니다. 관리자에게 문의하세요.',
            ]);

            return redirect()->route('admin.login')
                ->withErrors(['email' => '접근이 차단되었습니다. 관리자에게 문의하세요.'])
                ->withInput($request->except('password'));
        }

        return null;
    }

    /**
     * Step 4: 관리자 권한 검증
     */
    private function verifyAdminAuthorization($user, Request $request)
    {
        // isAdmin 플래그 확인
        if (! $user->isAdmin) {
            Auth::logout();

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

        // 사용자 타입 검증
        if (! $user->utype) {
            Auth::logout();

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

        // admin_user_types 테이블에서 유효성 확인
        $adminUserType = AdminUsertype::where('code', $user->utype)
            ->where('enable', true)
            ->first();

        if (! $adminUserType) {
            Auth::logout();

            AdminUserLog::log('unauthorized_login', null, [
                'email' => $request->input('email'),
                'reason' => 'Invalid or inactive user type: '.$user->utype,
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

        return null;
    }

    /**
     * Step 6: 비밀번호 변경 필요 여부 확인
     */
    private function checkPasswordChangeRequired($user, Request $request)
    {
        $passwordChangeRequired = false;
        $changeReason = '';

        // 강제 변경 플래그 확인
        if (isset($user->force_password_change) && $user->force_password_change) {
            $passwordChangeRequired = true;
            $changeReason = '관리자가 비밀번호 변경을 요청했습니다.';
        }
        // 필수 변경 플래그 확인
        elseif (isset($user->password_must_change) && $user->password_must_change) {
            $passwordChangeRequired = true;
            $changeReason = '비밀번호 변경이 필요합니다.';
        }
        // 비밀번호 만료 확인
        elseif ($user->password_changed_at && $user->password_expires_at) {
            if (now()->greaterThan($user->password_expires_at)) {
                $passwordChangeRequired = true;
                $changeReason = '비밀번호가 만료되었습니다.';

                AdminUserLog::log('password_expired', $user, [
                    'ip_address' => $request->ip(),
                    'password_changed_at' => $user->password_changed_at,
                    'password_expires_at' => $user->password_expires_at,
                    'current_time' => now()->toDateTimeString(),
                ]);
            }
        }

        if ($passwordChangeRequired) {
            session()->put('password_change_required', true);
            session()->put('password_change_user_id', $user->id);

            session()->flash('notification', [
                'type' => 'warning',
                'title' => '비밀번호 변경 필요',
                'message' => $changeReason.' 새 비밀번호를 설정해주세요.',
            ]);

            return redirect()->route('admin.password.change');
        }

        return null;
    }

    /**
     * Step 8: 로그인 완료 처리
     */
    private function completeLogin($user, Request $request)
    {
        $request->session()->regenerate();

        // 사용자 정보 업데이트
        $user->last_login_at = now();
        $user->login_count = ($user->login_count ?? 0) + 1;
        $user->save();

        // 브라우저 정보 파싱
        $userAgent = $request->header('User-Agent');
        $browser = $this->getBrowserInfo($userAgent);

        // 로그인 로그 기록
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
            'two_factor_required' => false,
            'two_factor_used' => false,
            'two_factor_method' => 'none',
        ]);

        // 세션 추적
        $session = AdminUserSession::track($user, $request, false);
        if (! $session) {
            \Log::warning('Failed to track session for user: '.$user->email);
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * 로그인 실패 처리
     */
    private function handleFailedLogin(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        // 실패 시도 기록
        $passwordLog = AdminPasswordLog::recordFailedAttempt(
            $request->input('email'),
            $request,
            $user ? $user->id : null
        );

        // 실패 로그 기록
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
     * @param  string  $userAgent  User-Agent 헤더 값
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
            'platform' => $platform,
        ];
    }
}
