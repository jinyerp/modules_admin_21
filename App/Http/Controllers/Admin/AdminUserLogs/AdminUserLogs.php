<?php

namespace Jiny\Admin\App\Http\Controllers\Admin\AdminUserLogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jiny\Admin\App\Models\AdminUserLog;

/**
 * AdminUserLogs Main Controller
 * 
 * 사용자 로그인/로그아웃 활동 로그 조회
 */
class AdminUserLogs extends Controller
{
    private $jsonData;
    
    public function __construct()
    {
        $this->jsonData = $this->loadJsonFromCurrentPath();
    }

    private function loadJsonFromCurrentPath()
    {
        try {
            $jsonFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminUserLogs.json';
            
            if (!file_exists($jsonFilePath)) {
                return $this->getDefaultJsonData();
            }

            $jsonContent = file_get_contents($jsonFilePath);
            $jsonData = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->getDefaultJsonData();
            }

            return $jsonData;

        } catch (\Exception $e) {
            return $this->getDefaultJsonData();
        }
    }
    
    private function getDefaultJsonData()
    {
        return [
            'title' => 'User Activity Logs',
            'subtitle' => 'Monitor user authentication activities',
            'route' => [
                'name' => 'admin.user.logs'
            ],
            'table' => [
                'name' => 'admin_user_logs',
                'model' => '\\Jiny\\Admin\\App\\Models\\AdminUserLog'
            ],
            'template' => [
                'layout' => 'jiny-admin::layouts.admin',
                'index' => 'jiny-admin::template.index'
            ],
            'index' => [
                'features' => [
                    'enableCreate' => false,
                    'enableDelete' => true,
                    'enableEdit' => false,
                    'enableSearch' => true,
                    'enableSort' => true,
                    'enablePagination' => true
                ]
            ]
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function __invoke(Request $request)
    {
        if (!$this->jsonData) {
            return response("Error: JSON 데이터를 로드할 수 없습니다.", 500);
        }
        
        // JSON 파일 경로 추가
        $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminUserLogs.json';
        $settingsPath = $jsonPath;
        
        // currentRoute 설정
        $this->jsonData['currentRoute'] = 'admin.user.logs';
        
        return view($this->jsonData['template']['index'], [
            'jsonData' => $this->jsonData,
            'jsonPath' => $jsonPath,
            'settingsPath' => $settingsPath,
            'title' => $this->jsonData['title'] ?? 'User Activity Logs',
            'subtitle' => $this->jsonData['subtitle'] ?? 'Monitor user authentication activities'
        ]);
    }
}