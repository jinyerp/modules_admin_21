<?php

namespace Jiny\Admin\App\Http\Controllers\Admin\AdminSessions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AdminSessions Show Controller
 * 
 * Sessions 상세보기 전용 컨트롤러
 * Single Action 방식으로 구현
 *
 * @package Jiny\Admin
 */
class AdminSessionsShow extends Controller
{
    private $jsonData;
    
    public function __construct()
    {
        // JSON 설정 파일 로드
        $this->jsonData = $this->loadJsonFromCurrentPath();
    }

    /**
     * __DIR__에서 AdminSessions.json 파일을 읽어오는 메소드
     */
    private function loadJsonFromCurrentPath()
    {
        try {
            $jsonFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminSessions.json';
            
            if (!file_exists($jsonFilePath)) {
                return null;
            }

            $jsonContent = file_get_contents($jsonFilePath);
            $jsonData = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            return $jsonData;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Single Action __invoke method
     * 상세 정보 표시
     */
    public function __invoke(Request $request, $id)
    {
        // Eloquent 모델로 데이터 조회 (관계 포함)
        $session = \Jiny\Admin\App\Models\AdminUserSession::with('user')->find($id);
        
        if (!$session) {
            $redirectUrl = isset($this->jsonData['route']['name']) 
                ? route($this->jsonData['route']['name'])
                : '/admin/user/sessions';
            return redirect($redirectUrl)
                ->with('error', '세션을 찾을 수 없습니다.');
        }
        
        // 모델을 배열로 변환
        $data = $session->toArray();
        
        // 추가 정보 계산
        if ($session->last_activity) {
            $lastActivity = \Carbon\Carbon::parse($session->last_activity);
            $data['last_activity_human'] = $lastActivity->diffForHumans();
            $data['session_duration'] = $lastActivity->diffInMinutes(\Carbon\Carbon::parse($session->login_at));
        }
        
        // 현재 세션인지 확인
        $data['is_current_session'] = ($session->session_id === session()->getId());
        
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
        $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminSessions.json';
        $settingsPath = $jsonPath; // settings drawer를 위한 경로
        
        // Set title from data or use default
        $title = '세션 상세 정보';
        if (isset($data['user']['name'])) {
            $subtitle = $data['user']['name'] . '의 세션';
        } else {
            $subtitle = '세션 ID: ' . substr($data['session_id'], 0, 8) . '...';
        }
        
        return view($this->jsonData['template']['show'], [
            'jsonData' => $this->jsonData,
            'jsonPath' => $jsonPath,
            'settingsPath' => $settingsPath,
            'data' => $data,
            'id' => $id,
            'title' => $title,
            'subtitle' => $subtitle,
            'session' => $session
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
}