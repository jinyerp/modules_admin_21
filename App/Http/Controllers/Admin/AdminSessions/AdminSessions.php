<?php
namespace Jiny\Admin\App\Http\Controllers\Admin\AdminSessions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Jiny\admin\App\Services\JsonConfigService;

class AdminSessions extends Controller
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
     * 목록 조회 처리
     */
    public function __invoke(Request $request)
    {
        // JSON 데이터 확인
        if (!$this->jsonData) {
            return response("Error: JSON configuration file not found or invalid.", 500);
        }

        // template.index view 경로 확인
        if(!isset($this->jsonData['template']['index'])) {
            return response("Error: 화면을 출력하기 위한 template.index 설정이 필요합니다.", 500);
        }

        // route 정보를 jsonData에 추가
        if (isset($this->jsonData['route']['name'])) {
            $this->jsonData['currentRoute'] = $this->jsonData['route']['name'];
        } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
            // 이전 버전 호환성
            $this->jsonData['currentRoute'] = $this->jsonData['route'];
        }

        // JSON 파일 경로 추가
        $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminSessions.json';
        $settingsPath = $jsonPath; // settings drawer를 위한 경로

        return view($this->jsonData['template']['index'], [
            'jsonData' => $this->jsonData,
            'jsonPath' => $jsonPath,
            'settingsPath' => $settingsPath
        ]);
    }

    /**
     * Hook: Livewire 컴포넌트의 데이터 조회 전 실행
     * 데이터베이스 쿼리 조건을 수정하거나 추가 로직을 실행할 수 있습니다.
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @return false|mixed false 반환시 정상 진행, 다른 값 반환시 해당 값이 출력됨
     */
    public function hookIndexing($wire)
    {
        return false;
    }

    /**
     * Hook: 데이터 조회 후 실행
     * 조회된 데이터를 가공하거나 추가 처리를 할 수 있습니다.
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @param mixed $rows 조회된 데이터
     * @return mixed 가공된 데이터
     */
    public function hookIndexed($wire, $rows)
    {
        return $rows;
    }

    /**
     * Hook: 테이블 헤더 커스터마이징
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @return array 커스터마이징된 헤더 설정
     */
    public function hookTableHeader($wire)
    {
        return $this->jsonData['index']['table']['columns'] ?? [];
    }

    /**
     * Hook: 페이지네이션 설정
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @return array 페이지네이션 설정
     */
    public function hookPagination($wire)
    {
        return $this->jsonData['index']['pagination'] ?? [
            'perPage' => 10,
            'perPageOptions' => [10, 25, 50, 100]
        ];
    }

    /**
     * Hook: 정렬 설정
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @return array 정렬 설정
     */
    public function hookSorting($wire)
    {
        return $this->jsonData['index']['sorting'] ?? [
            'default' => 'created_at',
            'direction' => 'desc'
        ];
    }

    /**
     * Hook: 검색 설정
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @return array 검색 설정
     */
    public function hookSearch($wire)
    {
        return $this->jsonData['index']['search'] ?? [
            'placeholder' => 'Search sessionss...',
            'debounce' => 300
        ];
    }

    /**
     * Hook: 필터 설정
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @return array 필터 설정
     */
    public function hookFilters($wire)
    {
        return $this->jsonData['index']['filters'] ?? [];
    }
    
    /**
     * Hook: 세션 종료
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @param int $id 세션 ID
     * @return void
     */
    public function hookTerminateSession($wire, $id)
    {
        try {
            $session = \Jiny\Admin\App\Models\AdminUserSession::find($id);
            
            if (!$session) {
                session()->flash('error', '세션을 찾을 수 없습니다.');
                return;
            }
            
            // 자기 자신의 세션은 종료할 수 없음
            if ($session->session_id === session()->getId()) {
                session()->flash('warning', '현재 사용 중인 세션은 종료할 수 없습니다.');
                return;
            }
            
            if ($session->is_active) {
                $session->is_active = false;
                $session->save();
                
                // 로그 기록
                \Jiny\Admin\App\Models\AdminUserLog::log('session_terminated', auth()->user(), [
                    'terminated_session_id' => $session->session_id,
                    'terminated_user_id' => $session->user_id,
                    'terminated_user_email' => $session->user->email ?? 'Unknown',
                    'ip_address' => request()->ip()
                ]);
                
                session()->flash('success', '세션이 성공적으로 종료되었습니다.');
            } else {
                session()->flash('info', '이미 종료된 세션입니다.');
            }
        } catch (\Exception $e) {
            \Log::error('Session termination failed: ' . $e->getMessage());
            session()->flash('error', '세션 종료 중 오류가 발생했습니다.');
        }
        
        $wire->resetPage();
    }
    
    /**
     * Hook: 세션 재발급
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @param int $id 세션 ID
     * @return void
     */
    public function hookRegenerateSession($wire, $id)
    {
        try {
            $session = \Jiny\Admin\App\Models\AdminUserSession::find($id);
            
            if (!$session) {
                session()->flash('error', '세션을 찾을 수 없습니다.');
                return;
            }
            
            // 현재 사용 중인 세션만 재발급 가능
            if ($session->session_id === session()->getId()) {
                // 새로운 세션 ID 생성
                request()->session()->regenerate();
                
                // 데이터베이스 업데이트
                $newSessionId = session()->getId();
                $session->session_id = $newSessionId;
                $session->last_activity = now();
                $session->save();
                
                // 로그 기록
                \Jiny\Admin\App\Models\AdminUserLog::log('session_regenerated', auth()->user(), [
                    'old_session_id' => $session->getOriginal('session_id'),
                    'new_session_id' => $newSessionId,
                    'ip_address' => request()->ip()
                ]);
                
                session()->flash('success', '세션이 재발급되었습니다. 새 세션 ID: ' . substr($newSessionId, 0, 8) . '...');
            } else {
                session()->flash('warning', '현재 사용 중인 세션만 재발급할 수 있습니다.');
            }
        } catch (\Exception $e) {
            \Log::error('Session regeneration failed: ' . $e->getMessage());
            session()->flash('error', '세션 재발급 중 오류가 발생했습니다.');
        }
        
        $wire->resetPage();
    }
}