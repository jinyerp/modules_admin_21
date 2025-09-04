<?php

namespace Jiny\Admin\App\Http\Controllers\Web\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Jiny\Admin\App\Models\AdminUserLog;
use Jiny\Admin\App\Models\AdminPasswordLog;

/**
 * 관리자 비밀번호 변경 컨트롤러
 * 
 * 이 컨트롤러는 관리자 사용자의 비밀번호 변경 기능을 담당합니다.
 * 비밀번호 만료, 관리자의 강제 변경 요청, 사용자의 자발적 변경 등
 * 다양한 시나리오에서 비밀번호 변경을 처리합니다.
 * 
 * @package Jiny\Admin
 * @author  JinyPHP Team
 * @since   2025.09.04
 */
class AdminPasswordChangeController extends Controller
{
    /**
     * 비밀번호 변경 폼 표시
     * 
     * 사용자에게 비밀번호 변경 화면을 보여줍니다.
     * 로그인하지 않은 사용자는 로그인 페이지로 리다이렉트됩니다.
     * 
     * 화면에 표시되는 정보:
     * - 현재 로그인한 사용자의 이메일
     * - 마지막 비밀번호 변경 시간
     * - 비밀번호 만료일 (설정된 경우)
     * - 변경 필수 여부 (강제 변경인지 자발적 변경인지)
     * 
     * @param  void
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showChangeForm()
    {
        // 로그인 상태 확인
        // 비밀번호 변경은 반드시 인증된 사용자만 접근 가능
        if (!Auth::check()) {
            // 로그인하지 않은 사용자에게 알림 메시지 표시
            session()->flash('notification', [
                'type' => 'error',
                'title' => '로그인 필요',
                'message' => '비밀번호를 변경하려면 먼저 로그인해주세요.',
            ]);
            
            // 로그인 페이지로 리다이렉트
            return redirect()->route('admin.login');
        }
        
        // 비밀번호 변경이 강제인지 확인
        // 세션에 password_change_required 플래그가 있으면 강제 변경
        $passwordChangeRequired = session('password_change_required', false);
        
        // 비밀번호 변경 뷰 반환
        // required: 강제 변경 여부 (true면 "나중에 변경" 버튼 숨김)
        // user: 현재 로그인한 사용자 정보
        return view('jiny-admin::Site.Login.password_change', [
            'required' => $passwordChangeRequired,
            'user' => Auth::user()
        ]);
    }
    
    /**
     * 비밀번호 변경 처리
     * 
     * 사용자가 제출한 새 비밀번호를 검증하고 변경합니다.
     * 
     * 처리 과정:
     * 1. 현재 비밀번호 확인
     * 2. 새 비밀번호 복잡도 검증 (8자 이상, 대소문자, 숫자, 특수문자 포함)
     * 3. 새 비밀번호가 현재 비밀번호와 다른지 확인
     * 4. 최근 3개 비밀번호 재사용 방지 체크
     * 5. 비밀번호 변경 로그 기록
     * 6. 새 비밀번호 저장 및 만료일 설정
     * 7. 강제 변경 플래그 초기화
     * 8. 성공 메시지 표시 및 대시보드로 리다이렉트
     * 
     * @param  Request $request HTTP 요청 객체
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        // 로그인 상태 재확인 (보안을 위한 이중 체크)
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }
        
        // 입력 데이터 검증
        // Laravel의 Password 규칙을 사용하여 강력한 비밀번호 정책 적용
        $validated = $request->validate([
            // 현재 비밀번호 검증 (current_password 규칙 사용)
            'current_password' => ['required', 'current_password'],
            
            // 새 비밀번호 검증
            'password' => [
                'required',                    // 필수 입력
                'confirmed',                   // password_confirmation 필드와 일치 확인
                Password::min(8)               // 최소 8자
                    ->mixedCase()              // 대소문자 혼합
                    ->numbers()                // 숫자 포함
                    ->symbols()                // 특수문자 포함
                    ->uncompromised(),         // 유출된 비밀번호 데이터베이스 체크
            ],
        ], [
            // 사용자 친화적인 한글 에러 메시지
            'current_password.current_password' => '현재 비밀번호가 일치하지 않습니다.',
            'password.required' => '새 비밀번호를 입력해주세요.',
            'password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',
            'password.min' => '비밀번호는 최소 8자 이상이어야 합니다.',
            'password.mixed' => '비밀번호는 대소문자를 포함해야 합니다.',
            'password.numbers' => '비밀번호는 숫자를 포함해야 합니다.',
            'password.symbols' => '비밀번호는 특수문자를 포함해야 합니다.',
            'password.uncompromised' => '이 비밀번호는 데이터 유출에 노출된 적이 있습니다. 다른 비밀번호를 선택해주세요.',
        ]);
        
        // 현재 로그인한 사용자 정보 가져오기
        $user = Auth::user();
        
        // 새 비밀번호가 현재 비밀번호와 동일한지 체크
        // 보안을 위해 반드시 다른 비밀번호를 사용하도록 강제
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => '새 비밀번호는 현재 비밀번호와 다르게 설정해주세요.'
            ])->withInput();
        }
        
        // 이전 비밀번호 히스토리 체크
        // 최근 3개의 비밀번호는 재사용할 수 없도록 제한
        $recentPasswords = AdminPasswordLog::where('user_id', $user->id)
            ->where('action', 'password_changed')    // 비밀번호 변경 액션만 조회
            ->orderBy('created_at', 'desc')          // 최신순 정렬
            ->limit(3)                               // 최근 3개만
            ->pluck('old_password_hash')             // 이전 비밀번호 해시값만 추출
            ->toArray();
            
        // 각 이전 비밀번호와 새 비밀번호 비교
        foreach ($recentPasswords as $oldPasswordHash) {
            if (Hash::check($request->password, $oldPasswordHash)) {
                return back()->withErrors([
                    'password' => '최근에 사용한 비밀번호는 재사용할 수 없습니다.'
                ])->withInput();
            }
        }
        
        // 비밀번호 변경 로그 기록 (감사 추적용)
        // 변경 전 비밀번호 해시값과 변경 정보를 저장
        AdminPasswordLog::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'action' => 'password_changed',                    // 액션 타입: 비밀번호 변경
            'old_password_hash' => $user->password,           // 변경 전 비밀번호 해시 저장
            'ip_address' => $request->ip(),                   // 변경 요청 IP
            'user_agent' => $request->userAgent(),            // 브라우저 정보
            'metadata' => [
                // 강제 변경인지 자발적 변경인지 구분
                'forced_change' => session('password_change_required', false),
                'change_reason' => session('password_change_required', false) 
                    ? 'password_expired'      // 비밀번호 만료로 인한 강제 변경
                    : 'user_initiated'        // 사용자의 자발적 변경
            ]
        ]);
        
        // 사용자 정보 업데이트
        // 새 비밀번호를 해시화하여 저장
        $user->password = Hash::make($request->password);
        
        // 비밀번호 변경 시간 기록
        $user->password_changed_at = now();
        
        // 비밀번호 만료일 설정
        // 설정 파일에서 만료 기간을 가져오고, 기본값은 90일
        $passwordExpiryDays = config('admin.setting.password.expiry_days', 90);
        if ($passwordExpiryDays > 0) {
            // 만료 기간이 설정된 경우: 현재 시간 + 만료 기간
            $user->password_expires_at = now()->addDays($passwordExpiryDays);
        } else {
            // 0으로 설정된 경우: 만료 없음 (null)
            $user->password_expires_at = null;
        }
        
        // 비밀번호 강제 변경 플래그 초기화
        // 관리자가 설정한 강제 변경 플래그들을 모두 해제
        if (isset($user->force_password_change)) {
            $user->force_password_change = false;     // 관리자 강제 변경 플래그
        }
        if (isset($user->password_must_change)) {
            $user->password_must_change = false;      // 시스템 강제 변경 플래그
        }
        
        // 데이터베이스에 변경사항 저장
        $user->save();
        
        // 관리자 활동 로그 기록
        // 보안 감사를 위한 상세 로그
        AdminUserLog::log('password_changed', $user, [
            'ip_address' => $request->ip(),
            'forced_change' => session('password_change_required', false),
        ]);
        
        // 세션 플래그 제거
        // 비밀번호 변경이 완료되었으므로 강제 변경 관련 세션 데이터 삭제
        session()->forget([
            'password_change_required',      // 강제 변경 필요 플래그
            'password_change_user_id'        // 대상 사용자 ID
        ]);
        
        // 성공 메시지 설정
        session()->flash('notification', [
            'type' => 'success',
            'title' => '비밀번호 변경 완료',
            'message' => '비밀번호가 성공적으로 변경되었습니다.',
        ]);
        
        // 관리자 대시보드로 리다이렉트
        // 비밀번호 변경 완료 후 정상 업무 진행
        return redirect()->route('admin.dashboard');
    }
}