<?php

namespace Jiny\Admin\App\Http\Controllers\Web\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Jiny\Admin\App\Models\AdminUsertype;

/**
 * 관리자 로그인 컨트롤러
 * 
 * 관리자 로그인 페이지를 표시하고 인증 처리를 담당합니다.
 */
class AdminLoginController extends Controller
{
    /**
     * 로그인 폼 표시
     * 
     * 이미 로그인된 사용자는 관리자 권한에 따라 리다이렉트하고,
     * 로그인되지 않은 사용자에게는 로그인 폼을 표시합니다.
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLoginForm()
    {
        // 로그인 상태 확인
        if (auth()->check()) {
            $user = auth()->user();
            
            // 관리자 권한 확인 (isAdmin이 존재하는지 먼저 체크)
            if (isset($user->isAdmin) && $user->isAdmin) {
                // utype이 admin_user_types 테이블에 존재하는지 확인
                if (isset($user->utype) && $user->utype) {
                    $adminType = AdminUsertype::where('code', $user->utype)
                        ->where('enable', true)
                        ->first();
                    
                    if ($adminType) {
                        return redirect()->route('admin.dashboard');
                    }
                }
            }
            
            // 관리자가 아닌 경우 로그아웃하고 로그인 폼 표시
            auth()->logout();
            session()->flash('notification', [
                'type' => 'info',
                'title' => '관리자 로그인',
                'message' => '관리자 계정으로 로그인해주세요.',
            ]);
        }
        
        return view('jiny-admin::Site.Login.login');
    }
}