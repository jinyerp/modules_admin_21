<?php

namespace Jiny\Admin\App\Http\Controllers\Admin\AdminUsers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jiny\admin\App\Services\JsonConfigService;

/**
 * AdminUsersShow Controller
 */
class AdminUsersShow extends Controller
{
    private $jsonData;
    
    public function __construct()
    {
        // 서비스를 사용하여 JSON 파일 로드
        $jsonConfigService = new JsonConfigService();
        $this->jsonData = $jsonConfigService->loadFromControllerPath(__DIR__);
    }

    /**
     * Single Action __invoke method
     * 상세 정보 표시
     */
    public function __invoke(Request $request, $id)
    {
        // 데이터베이스에서 데이터 조회
        $tableName = $this->jsonData['table']['name'] ?? 'admin_usertypes';
        $query = DB::table($tableName);
        
        // 기본 where 조건 적용
        if (isset($this->jsonData['table']['where']['default'])) {
            foreach ($this->jsonData['table']['where']['default'] as $condition) {
                if (count($condition) === 3) {
                    $query->where($condition[0], $condition[1], $condition[2]);
                } elseif (count($condition) === 2) {
                    $query->where($condition[0], $condition[1]);
                }
            }
        }
        
        $item = $query->where('id', $id)->first();
        
        if (!$item) {
            $redirectUrl = isset($this->jsonData['route']['name']) 
                ? route($this->jsonData['route']['name'] . '.index')
                : '/admin/usertype';
            return redirect($redirectUrl)
                ->with('error', 'User을(를) 찾을 수 없습니다.');
        }
        
        // 객체를 배열로 변환
        $data = (array) $item;
        
        // Apply hookShowing if exists
        if (method_exists($this, 'hookShowing')) {
            $data = $this->hookShowing(null, $data);
        }
        
        // route 정보를 jsonData에 추가
        if (isset($this->jsonData['route']['name'])) {
            $this->jsonData['currentRoute'] = $this->jsonData['route']['name'];
        } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
            $this->jsonData['currentRoute'] = $this->jsonData['route'];
        }
        
        // template.show view 경로 확인
        if(!isset($this->jsonData['template']['show'])) {
            return response("Error: 화면을 출력하기 위한 template.show 설정이 필요합니다.", 500);
        }
        
        // JSON 파일 경로 추가
        $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminUsers.json';
        $settingsPath = $jsonPath; // settings drawer를 위한 경로
        
        // Set title from data or use default
        $title = $data['title'] ?? $data['name'] ?? 'User Details';
        
        // 컨트롤러 클래스를 JSON 데이터에 추가
        $this->jsonData['controllerClass'] = get_class($this);
        
        return view($this->jsonData['template']['show'], [
            'controllerClass' => static::class,  // 현재 컨트롤러 클래스 전달
            'jsonData' => $this->jsonData,
            'jsonPath' => $jsonPath,
            'settingsPath' => $settingsPath,
            'data' => $data,
            'id' => $id,
            'title' => $title,
            'subtitle' => 'User 상세 정보'
        ]);
    }

    /**
     * 상세보기 표시 전에 호출됩니다.
     */
    public function hookShowing($wire, $data)
    {
        // 날짜 형식 지정
        $dateFormat = $this->jsonData['show']['display']['datetimeFormat'] ?? 'Y-m-d H:i:s';
        
        if (isset($data['created_at'])) {
            $data['created_at_formatted'] = date($dateFormat, strtotime($data['created_at']));
        }
        
        if (isset($data['updated_at'])) {
            $data['updated_at_formatted'] = date($dateFormat, strtotime($data['updated_at']));
        }
        
        // Boolean 라벨 처리
        $booleanLabels = $this->jsonData['show']['display']['booleanLabels'] ?? [
            'true' => 'Enabled',
            'false' => 'Disabled'
        ];
        
        if (isset($data['enable'])) {
            $data['enable_label'] = $data['enable'] ? $booleanLabels['true'] : $booleanLabels['false'];
        }
        
        return $data;
    }

    /**
     * Hook: 조회 후 데이터 가공
     */
    public function hookShowed($wire, $data)
    {
        return $data;
    }
    
    /**
     * Hook: 비밀번호 재설정 및 계정 잠금 해제
     */
    public function hookCustomPasswordReset($wire, $params)
    {
        $userId = $params['id'] ?? null;
        $action = $params['action'] ?? 'reset';
        
        if (!$userId) {
            session()->flash('error', '사용자 ID가 필요합니다.');
            return;
        }
        
        try {
            switch ($action) {
                case 'reset_attempts':
                    // 비밀번호 실패 횟수 초기화
                    DB::table('users')
                        ->where('id', $userId)
                        ->update([
                            'failed_login_attempts' => 0,
                            'account_locked_until' => null,
                            'updated_at' => now()
                        ]);
                    
                    // 관련 로그 기록
                    DB::table('admin_user_logs')->insert([
                        'user_id' => $userId,
                        'action' => 'password_reset',
                        'description' => 'Admin reset failed login attempts',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'logged_at' => now(),
                        'created_at' => now()
                    ]);
                    
                    session()->flash('success', '로그인 실패 횟수가 초기화되었습니다.');
                    break;
                    
                case 'unlock_account':
                    // 계정 잠금 해제
                    DB::table('users')
                        ->where('id', $userId)
                        ->update([
                            'account_locked_until' => null,
                            'failed_login_attempts' => 0,
                            'updated_at' => now()
                        ]);
                    
                    // 관련 로그 기록
                    DB::table('admin_user_logs')->insert([
                        'user_id' => $userId,
                        'action' => 'account_unlock',
                        'description' => 'Admin unlocked user account',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'logged_at' => now(),
                        'created_at' => now()
                    ]);
                    
                    session()->flash('success', '계정 잠금이 해제되었습니다.');
                    break;
                    
                case 'force_password_change':
                    // 다음 로그인 시 비밀번호 변경 강제
                    DB::table('users')
                        ->where('id', $userId)
                        ->update([
                            'force_password_change' => true,
                            'updated_at' => now()
                        ]);
                    
                    session()->flash('success', '다음 로그인 시 비밀번호 변경이 요구됩니다.');
                    break;
                    
                default:
                    session()->flash('error', '알 수 없는 작업입니다.');
            }
            
            // Livewire 컴포넌트 새로고침
            if ($wire) {
                $wire->refreshData();
            }
            
        } catch (\Exception $e) {
            session()->flash('error', '작업 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
}
