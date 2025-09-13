<?php

namespace Jiny\Admin\App\Http\Controllers\Admin\AdminEmailtemplates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jiny\admin\App\Services\JsonConfigService;

/**
 * settings 생성 컨트롤러
 * 
 * 새로운 settings를 생성하는 폼 표시 및 처리를 담당합니다.
 * Livewire 컴포넌트(AdminCreate)와 Hook 패턴을 통해 동작합니다.
 * 
 * @package Jiny\Admin
 * @since   1.0.0
 */
class AdminEmailtemplatesCreate extends Controller
{
    /**
     * JSON 설정 데이터
     *
     * @var array|null
     */
    private $jsonData;

    /**
     * 컨트롤러 생성자
     */
    public function __construct()
    {
        // 서비스를 사용하여 JSON 파일 로드
        $jsonConfigService = new JsonConfigService;
        $this->jsonData = $jsonConfigService->loadFromControllerPath(__DIR__);
    }

    /**
     * Single Action __invoke method
     * 생성 폼 표시
     *
     * @param  Request  $request  HTTP 요청 객체
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // JSON 데이터 확인
        if (!$this->jsonData) {
            return response('Error: JSON 데이터를 로드할 수 없습니다.', 500);
        }

        // 기본값 설정
        $form = [];

        // route 정보를 jsonData에 추가
        if (isset($this->jsonData['route']['name'])) {
            $this->jsonData['currentRoute'] = $this->jsonData['route']['name'];
        }

        // template.create view 경로 확인
        if (!isset($this->jsonData['template']['create'])) {
            return response('Error: 화면을 출력하기 위한 template.create 설정이 필요합니다.', 500);
        }

        // JSON 파일 경로 추가
        $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . '{{class}}.json';
        $settingsPath = $jsonPath;

        // 현재 컨트롤러 클래스를 JSON 데이터에 추가
        $this->jsonData['controllerClass'] = get_class($this);

        return view($this->jsonData['template']['create'], [
            'jsonData' => $this->jsonData,
            'jsonPath' => $jsonPath,
            'settingsPath' => $settingsPath,
            'controllerClass' => static::class,
            'form' => $form,
        ]);
    }

    /**
     * Hook: 폼 초기화
     *
     * @param  mixed  $wire  Livewire 컴포넌트
     * @param  array  $form  폼 데이터
     * @return array
     */
    public function hookCreating($wire, $form)
    {
        // 기본값 설정
        return $form;
    }

    /**
     * Hook: 저장 전 처리
     *
     * @param  mixed  $wire  Livewire 컴포넌트
     * @param  array  $form  폼 데이터
     * @return array|string 성공시 배열, 실패시 에러 메시지
     */
    public function hookStoring($wire, $form)
    {
        // 데이터 가공 및 검증
        return $form;
    }

    /**
     * Hook: 저장 후 처리
     *
     * @param  mixed  $wire  Livewire 컴포넌트
     * @param  array  $form  저장된 데이터
     * @return void
     */
    public function hookStored($wire, $form)
    {
        // 후처리 로직
    }
}