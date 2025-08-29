<?php

namespace Jiny\Admin2\App\Http\Controllers\Admin\AdminTemplates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AdminTemplates Show Controller
 * 
 * 관리자 템플릿 상세보기 전용 컨트롤러
 * Single Action 방식으로 구현
 *
 * @package Jiny\Admin2
 * @author JinyPHP Team
 */
class AdminTemplatesShow extends Controller
{
    private $jsonData;
    
    public function __construct()
    {
        // JSON 설정 파일 로드
        $this->jsonData = $this->loadJsonFromCurrentPath();
    }

    /**
     * __DIR__에서 AdminTemplates.json 파일을 읽어오는 메소드
     */
    private function loadJsonFromCurrentPath()
    {
        try {
            $jsonFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminTemplates.json';
            
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
     * 템플릿 상세 정보 표시
     */
    public function __invoke(Request $request, $id)
    {
        // 데이터베이스에서 템플릿 조회
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        $template = DB::table($tableName)
            ->where('id', $id)
            ->first();
        
        if (!$template) {
            return redirect('/admin2/templates')
                ->with('error', '템플릿을 찾을 수 없습니다.');
        }
        
        // 객체를 배열로 변환
        $data = (array) $template;
        
        // 뷰 경로
        $viewPath = 'jiny-admin2::admin.admin_templates.show';
        
        return view($viewPath, [
            'jsonData' => $this->jsonData,
            'data' => $data,
            'id' => $id,
            'title' => 'Template Details',
            'subtitle' => '템플릿 상세 정보'
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
        
        if (isset($data['is_default'])) {
            $data['is_default_label'] = $data['is_default'] ? 'Yes' : 'No';
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