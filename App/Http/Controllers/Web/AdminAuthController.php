<?php

namespace Jiny\Admin\App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Jiny\Admin\App\Models\AdminUserLog;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // 로그인 성공 로그 기록
            AdminUserLog::log('login', Auth::user(), [
                'remember' => $request->boolean('remember')
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }
        
        // 로그인 실패 로그 기록
        AdminUserLog::log('failed_login', null, [
            'email' => $request->input('email')
        ]);

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        // 로그아웃 로그 기록 (로그아웃 전에 기록)
        if (Auth::check()) {
            AdminUserLog::log('logout', Auth::user());
        }
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        return view('jiny-admin::web.dashboard');
    }
}