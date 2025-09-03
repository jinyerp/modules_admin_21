<?php
namespace Jiny\Admin\App\Http\Controllers\Admin\AdminHello;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * AdminHello 컨트롤러
 * 
 * @jiny/admin 패키지의 샘플 CRUD 컨트롤러입니다.
 * JSON 설정 파일을 기반으로 동적으로 CRUD 기능을 제공합니다.
 */
class AdminHello extends Controller
{
    /**
     * JSON 설정 데이터
     * 
     * @var array|null
     */
    private $jsonData;

    /**
     * 컨트롤러 생성자
     * 
     * 컨트롤러명과 동일한 이름의 JSON 설정 파일을 로드합니다.
     */
    public function __construct()
    {
        // __DIR__에서 마지막 경로명과 동일한 JSON 파일 읽어오기
        $this->jsonData = $this->loadJsonFromCurrentPath();
    }

    /**
     * JSON 설정 파일 로드
     * 
     * 컨트롤러명과 동일한 이름의 JSON 파일을 현재 디렉토리에서 읽어옵니다.
     * 예: AdminHello 컨트롤러는 AdminHello.json 파일을 로드
     *
     * @return array|null JSON 데이터를 배열로 반환, 파일이 없거나 오류시 null 반환
     */
    private function loadJsonFromCurrentPath()
    {
        try {
            // __DIR__에서 마지막 경로명 추출 (AdminHello)
            $pathParts = explode(DIRECTORY_SEPARATOR, __DIR__);
            $lastPathName = end($pathParts);

            // JSON 파일 경로 생성
            $jsonFilePath = __DIR__ . DIRECTORY_SEPARATOR . $lastPathName . '.json';

            // 파일 존재 여부 확인
            if (!file_exists($jsonFilePath)) {
                return null;
            }

            // JSON 파일 읽기
            $jsonContent = file_get_contents($jsonFilePath);

            // JSON 디코딩
            $jsonData = json_decode($jsonContent, true);

            // JSON 오류 확인
            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            return $jsonData;

        } catch (\Exception $e) {
            // 오류 발생시 null 반환
            return null;
        }
    }

    /**
     * 메인 인덱스 페이지 처리
     * 
     * CRUD 목록 페이지를 표시합니다.
     * JSON 설정 파일의 template.index에 지정된 뷰를 렌더링합니다.
     * 
     * @param Request $request HTTP 요청 객체
     * @return \Illuminate\View\View|\Illuminate\Http\Response 목록 뷰 또는 에러 응답
     */
    public function __invoke(Request $request)
    {
        try {
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
            $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminHello.json';
            $settingsPath = $jsonPath; // settings drawer를 위한 경로

            return view($this->jsonData['template']['index'], [
                'jsonData' => $this->jsonData,
                'jsonPath' => $jsonPath,
                'settingsPath' => $settingsPath
            ]);
            
        } catch (\Exception $e) {
            \Log::error('AdminHello error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response("Error: " . $e->getMessage(), 500);
        }
    }

    /**
     * Hook: 데이터 조회 전 실행
     * 
     * Livewire 컴포넌트가 데이터를 조회하기 전에 호출됩니다.
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
     * 
     * Livewire 컴포넌트가 데이터 조회를 완료한 후 호출됩니다.
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
     * 목록 테이블의 헤더 컬럼을 설정합니다.
     * JSON 설정의 index.table.columns 값을 반환합니다.
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
     * 한 페이지에 표시할 항목 수와 옵션을 설정합니다.
     * JSON 설정의 index.pagination 값을 반환합니다.
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
     * 기본 정렬 컬럼과 방향을 설정합니다.
     * JSON 설정의 index.sorting 값을 반환합니다.
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
     * 검색 필드의 placeholder와 디바운스 시간을 설정합니다.
     * JSON 설정의 index.search 값을 반환합니다.
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @return array 검색 설정
     */
    public function hookSearch($wire)
    {
        return $this->jsonData['index']['search'] ?? [
            'placeholder' => 'Search hellos...',
            'debounce' => 300
        ];
    }

    /**
     * Hook: 필터 설정
     * 
     * 목록 페이지에서 사용할 필터 옵션을 설정합니다.
     * JSON 설정의 index.filters 값을 반환합니다.
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @return array 필터 설정
     */
    public function hookFilters($wire)
    {
        return $this->jsonData['index']['filters'] ?? [];
    }
}