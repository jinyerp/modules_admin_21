<?php

namespace Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * AdminTemplates Create Controller
 * 
 * 관리자 템플릿 생성 전용 컨트롤러
 * Single Action 방식으로 구현
 *
 * @package Jiny\Admin
 * @author JinyPHP Team
 */
class AdminTemplatesCreate extends Controller
{
    private $jsonData;
    
    public function __construct()
    {
        // JSON 설정 파일 로드
        $this->jsonData = $this->loadJsonFromCurrentPath();
        
        // 기본 리다이렉트 경로 설정 방식 변경 - 직접 route 정보를 사용
    }

    /**
     * __DIR__에서 AdminTemplates.json 파일을 읽어오는 메소드
     */
    private function loadJsonFromCurrentPath()
    {
        try {
            $jsonFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminTemplates.json';
            
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
            'enable' => false,
            'category' => '',
            'version' => '1.0.0',
            'author' => ''
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
            // 디버깅 정보 추가
            $debugInfo = "JSON template section: " . json_encode($this->jsonData['template'] ?? 'not found');
            return response("Error: 화면을 출력하기 위한 template.create 설정이 필요합니다. " . $debugInfo, 500);
        }
        
        // JSON 파일 경로 추가
        $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminTemplates.json';
        
        return view($this->jsonData['template']['create'], [
            'jsonData' => $this->jsonData,
            'jsonPath' => $jsonPath,
            'form' => $form,
            'title' => 'Create New Template',
            'subtitle' => '새로운 템플릿을 생성합니다.'
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
            'category' => 'admin',
            'version' => '1.0.0',
            'enable' => true
        ], $defaults);

        return $form;
    }

    /**
     * 신규 데이터 DB 삽입전에 호출됩니다.
     */
    public function hookStoring($wire, $form)
    {
        
        // slug이 없으면 name을 기반으로 생성
        if (!isset($form['slug']) && isset($form['name'])) {
            $form['slug'] = Str::slug($form['name']);
        }
        
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
        // 예: 템플릿 파일 생성, 캐시 클리어 등
        // settings 필드가 문자열로 들어온 경우 JSON으로 변환
        if (isset($form['settings']) && is_string($form['settings'])) {
            $form['settings'] = json_decode($form['settings'], true);
        }
        
        return $form;
    }
}