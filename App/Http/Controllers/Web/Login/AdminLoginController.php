<?php

namespace Jiny\Admin\App\Http\Controllers\Web\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
     * 이미 로그인된 사용자는 대시보드로 리다이렉트하고,
     * 로그인되지 않은 사용자에게는 로그인 폼을 표시합니다.
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('jiny-admin::Site.Login.login');
    }
}