<?php

namespace Jiny\Admin2\App\Http\Controllers\Admin\AdminTest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * AdminTest Create Controller
 * 
 * Test 생성 전용 컨트롤러
 * Single Action 방식으로 구현
 *
 * @package Jiny\Admin2
 */
class AdminTestCreate extends Controller
{
    private $jsonData;
    
    public function __construct()
    {
        // JSON 설정 파일 로드
        $this->jsonData = $this->loadJsonFromCurrentPath();
    }

    /**
     * __DIR__에서 AdminTest.json 파일을 읽어오는 메소드
     */
    private function loadJsonFromCurrentPath()
    {
        try {
            $jsonFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminTest.json';
            
            if (!file_exists($jsonFilePath)) {
                error_log("JSON file not found: " . $jsonFilePath);
                return null;
            }

            $jsonContent = file_get_contents($jsonFilePath);
            $jsonData = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON decode error: " . json_last_error_msg());
                return null;
            }

            return $jsonData;

        } catch (\Exception $e) {
            error_log("Exception loading JSON: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Single Action __invoke method
     * 생성 폼 표시
     */
    public function __invoke(Request $request)
    {
        // JSON 데이터 확인
        if (!$this->jsonData) {
            return response("Error: JSON 데이터를 로드할 수 없습니다.", 500);
        }
        
        // 기본값 설정
        $form = [
            'enable' => false
        ];
        
        // route 정보를 jsonData에 추가
        if (isset($this->jsonData['route']['name'])) {
            $this->jsonData['currentRoute'] = $this->jsonData['route']['name'];
        } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
            // 이전 버전 호환성
            $this->jsonData['currentRoute'] = $this->jsonData['route'];
        }
        
        // template.create view 경로 확인
        if(!isset($this->jsonData['template']['create'])) {
            $debugInfo = "JSON template section: " . json_encode($this->jsonData['template'] ?? 'not found');
            return response("Error: 화면을 출력하기 위한 template.create 설정이 필요합니다. " . $debugInfo, 500);
        }
        
        // JSON 파일 경로 추가
        $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminTest.json';
        $settingsPath = $jsonPath; // settings drawer를 위한 경로
        
        return view($this->jsonData['template']['create'], [
            'jsonData' => $this->jsonData,
            'jsonPath' => $jsonPath,
            'settingsPath' => $settingsPath,
            'form' => $form,
            'title' => 'Create New Test',
            'subtitle' => '새로운 Test을(를) 생성합니다.'
        ]);
    }

    /**
     * 생성폼이 실행될때 호출됩니다.
     */
    public function hookCreating($wire, $value)
    {
        // 기본값 설정
        $defaults = $this->jsonData['create']['defaults'] ?? 
                   $this->jsonData['store']['defaults'] ?? [];
        
        $form = array_merge([
            'enable' => true
        ], $defaults);

        return $form;
    }

    /**
     * 신규 데이터 DB 삽입전에 호출됩니다.
     */
    public function hookStoring($wire, $form)
    {
        // enable 필드 처리 (체크박스)
        $form['enable'] = isset($form['enable']) ? 1 : 0;
        
        // 불필요한 필드 제거
        unset($form['_token']);
        unset($form['continue_creating']);

        // timestamps 추가
        $form['created_at'] = now();
        $form['updated_at'] = now();

        return $form;
    }

    /**
     * 신규 데이터 DB 삽입후에 호출됩니다.
     */
    public function hookStored($wire, $form)
    {
        // 필요시 추가 작업 수행
        return $form;
    }
}