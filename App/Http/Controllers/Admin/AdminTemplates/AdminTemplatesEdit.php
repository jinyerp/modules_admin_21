<?php

namespace Jiny\Admin2\App\Http\Controllers\Admin\AdminTemplates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * AdminTemplates Edit Controller
 * 
 * 관리자 템플릿 수정 전용 컨트롤러
 * Single Action 방식으로 구현
 *
 * @package Jiny\Admin2
 * @author JinyPHP Team
 */
class AdminTemplatesEdit extends Controller
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
     * 수정 폼 표시
     */
    public function __invoke(Request $request, $id)
    {
        // 데이터베이스에서 템플릿 조회
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        $template = DB::table($tableName)
            ->where('id', $id)
            ->first();
        
        if (!$template) {
            if (isset($this->jsonData['route']['name'])) {
                $redirectUrl = route($this->jsonData['route']['name'] . '.index');
            } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
                $redirectUrl = route($this->jsonData['route'] . '.index');
            } else {
                $redirectUrl = '/admin2/templates';
            }
            return redirect($redirectUrl)
                ->with('error', '템플릿을 찾을 수 없습니다.');
        }
        
        // 객체를 배열로 변환
        $form = (array) $template;
        
        // route 정보를 jsonData에 추가
        if (isset($this->jsonData['route']['name'])) {
            $this->jsonData['currentRoute'] = $this->jsonData['route']['name'];
        } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
            // 이전 버전 호환성
            $this->jsonData['currentRoute'] = $this->jsonData['route'];
        }
        
        // template.edit view 경로 확인
        if(!isset($this->jsonData['template']['edit'])) {
            return response("Error: 화면을 출력하기 위한 template.edit 설정이 필요합니다.", 500);
        }
        
        // JSON 파일 경로 추가
        $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminTemplates.json';
        
        return view($this->jsonData['template']['edit'], [
            'jsonData' => $this->jsonData,
            'jsonPath' => $jsonPath,
            'form' => $form,
            'id' => $id,
            'title' => 'Edit Template',
            'subtitle' => '템플릿을 수정합니다.'
        ]);
    }


    /**
     * 수정폼이 실행될때 호출됩니다.
     */
    public function hookEditing($wire, $form)
    {
        // enable, is_default 필드를 boolean으로 변환
        $form['enable'] = (bool) ($form['enable'] ?? false);
        $form['is_default'] = (bool) ($form['is_default'] ?? false);
        
        return $form;
    }

    /**
     * 데이터 업데이트 전에 호출됩니다.
     */
    public function hookUpdating($wire, $form)
    {
        // slug 업데이트 (name이 변경된 경우)
        if (isset($form['name']) && !isset($form['slug'])) {
            $form['slug'] = Str::slug($form['name']);
        }
        
        // title이 없으면 name을 사용
        if (!isset($form['title']) && isset($form['name'])) {
            $form['title'] = $form['name'];
        }
        
        // enable 필드 처리 (체크박스)
        $form['enable'] = isset($form['enable']) ? 1 : 0;
        $form['is_default'] = isset($form['is_default']) ? 1 : 0;

        // ID 제거 (업데이트 시 필요 없음)
        unset($form['id']);
        unset($form['_token']);
        unset($form['_method']);
        
        // updated_at 타임스탬프 업데이트
        $form['updated_at'] = now();

        return $form;
    }

    /**
     * 데이터 업데이트 후에 호출됩니다.
     */
    public function hookUpdated($wire, $form)
    {
        // 필요시 추가 작업 수행
        // 예: 캐시 클리어, 관련 파일 업데이트 등
        
        // 기본 템플릿으로 설정된 경우 다른 템플릿의 is_default를 false로 변경
        if ($form['is_default'] ?? false) {
            $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
            DB::table($tableName)
                ->where('id', '!=', $form['id'])
                ->update(['is_default' => false]);
        }
        
        return $form;
    }
}