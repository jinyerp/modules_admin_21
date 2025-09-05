<?php

namespace Jiny\Admin\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jiny\Admin\App\Models\AdminUserLog;
use Jiny\Admin\App\Models\AdminUsertype;
use Symfony\Component\HttpFoundation\Response;

/**
 * 관리자 접근 권한 미들웨어
 *
 * 관리자 페이지 접근 시 인증과 권한을 확인합니다.
 * 다음 조건을 모두 만족해야 접근 가능:
 * 1. 로그인 상태
 * 2. isAdmin이 true
 * 3. utype이 관리자 권한 타입
 * 4. 차단되지 않은 상태
 */
class AdminMiddleware
{
    /**
     * 허용된 관리자 타입 (더 이상 하드코딩하지 않음)
     * admin_user_types 테이블에서 동적으로 확인
     *
     * @deprecated
     */
    // protected $allowedTypes = ['super', 'admin'];

    /**
     * 요청 처리
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $requiredType  특정 타입 요구 (선택사항)
     */
    public function handle(Request $request, Closure $next, ?string $requiredType = null): Response
    {
        // 로그인 페이지는 미들웨어 체크 제외 (무한 루프 방지)
        if ($request->routeIs('admin.login', 'admin.login.post')) {
            return $next($request);
        }

        // 1. 로그인 확인
        if (! Auth::check()) {
            // 로그인 안됨 - 로그인 페이지로 리다이렉트
            session()->flash('notification', [
                'type' => 'error',
                'title' => '인증 필요',
                'message' => '로그인이 필요한 서비스입니다.',
            ]);

            // AJAX 요청인 경우 JSON 응답
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => '로그인이 필요합니다.',
                ], 401);
            }

            // 접근하려던 URL을 세션에 저장 (로그인 후 리다이렉트용)
            session()->put('url.intended', $request->url());

            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // 2. isAdmin 확인
        if (! $user->isAdmin) {
            $this->logUnauthorizedAccess($request, 'Not an admin user');

            return $this->denyAccess($request, '관리자 권한이 없습니다.');
        }

        // 3. utype 확인
        if (! $user->utype) {
            $this->logUnauthorizedAccess($request, 'User type not set');

            return $this->denyAccess($request, '사용자 타입이 설정되지 않았습니다.');
        }

        // 4. admin_user_types 테이블에서 사용자 타입 확인
        $adminUserType = AdminUsertype::where('code', $user->utype)->first();
        if (! $adminUserType) {
            $this->logUnauthorizedAccess($request, "User type not found in admin_user_types: {$user->utype}");

            return $this->denyAccess($request, '등록되지 않은 사용자 타입입니다.');
        }

        // 5. 특정 타입이 요구되는 경우
        if ($requiredType) {
            if ($user->utype !== $requiredType) {
                // 요청된 타입이 admin_user_types에 있는지도 확인
                $requiredAdminType = AdminUsertype::where('code', $requiredType)->first();
                if (! $requiredAdminType) {
                    $this->logUnauthorizedAccess($request, "Invalid required type: {$requiredType}");

                    return $this->denyAccess($request, "잘못된 권한 타입입니다: {$requiredType}");
                }

                $this->logUnauthorizedAccess($request, "Required type: {$requiredType}, User type: {$user->utype}");

                return $this->denyAccess($request, "이 기능은 {$requiredAdminType->title} 권한이 필요합니다.");
            }
        }

        // 6. 사용자 타입이 활성화되어 있는지 확인 (enable 컬럼 체크)
        if (isset($adminUserType->enable) && ! $adminUserType->enable) {
            $this->logUnauthorizedAccess($request, "User type is disabled: {$user->utype}");

            return $this->denyAccess($request, '비활성화된 사용자 타입입니다.');
        }

        // 7. 계정이 차단되었는지 확인 (is_blocked 컬럼이 있는 경우)
        if (isset($user->is_blocked) && $user->is_blocked) {
            $this->logUnauthorizedAccess($request, 'User account is blocked');

            // 로그아웃 처리
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->denyAccess($request, '계정이 차단되었습니다. 관리자에게 문의하세요.');
        }

        // 8. 마지막 활동 시간 업데이트
        $this->updateLastActivity($user);

        // 모든 검증 통과 - 요청 계속 진행
        return $next($request);
    }

    /**
     * 접근 거부 처리
     */
    protected function denyAccess(Request $request, string $message): Response
    {
        session()->flash('notification', [
            'type' => 'error',
            'title' => '접근 거부',
            'message' => $message,
        ]);

        // AJAX 요청인 경우 JSON 응답
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => $message,
            ], 403);
        }

        // 일반 요청인 경우 로그인 페이지로 리다이렉트
        return redirect()->route('admin.login');
    }

    /**
     * 권한 없는 접근 시도 로그 기록
     */
    protected function logUnauthorizedAccess(Request $request, string $reason): void
    {
        $user = Auth::user();

        AdminUserLog::log('unauthorized_access', $user, [
            'url' => $request->url(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'reason' => $reason,
            'user_id' => $user ? $user->id : null,
            'email' => $user ? $user->email : 'Guest',
            'utype' => $user ? $user->utype : null,
        ]);
    }

    /**
     * 마지막 활동 시간 업데이트
     *
     * @param  \App\Models\User  $user
     */
    protected function updateLastActivity($user): void
    {
        // 마지막 활동 시간이 1분 이상 지났을 때만 업데이트 (DB 부하 감소)
        if (! $user->last_activity_at || $user->last_activity_at->diffInMinutes(now()) >= 1) {
            $user->last_activity_at = now();
            $user->save();
        }
    }
}
