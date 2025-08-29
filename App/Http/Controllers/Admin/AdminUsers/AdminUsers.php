<?php
namespace Jiny\Admin2\App\Http\Controllers\Admin\AdminUsers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminUsers extends Controller
{
    private $jsonData;

    public function __construct()
    {
        // __DIR__에서 마지막 경로명과 동일한 JSON 파일 읽어오기
        $this->jsonData = $this->loadJsonFromCurrentPath();
    }

    /**
     * __DIR__에서 마지막 경로명과 동일한 JSON 파일을 읽어오는 메소드
     *
     * @return array|null JSON 데이터를 배열로 반환, 파일이 없거나 오류시 null 반환
     */
    private function loadJsonFromCurrentPath()
    {
        try {
            // __DIR__에서 마지막 경로명 추출 (AdminUsers)
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
     * Single Action __invoke method
     * 목록 조회 처리
     */
    public function __invoke(Request $request)
    {
        // jsonData에 컨트롤러 클래스 정보 추가
        $this->jsonData['controller'] = self::class;

        return view($this->jsonData['index']['viewPath'], [
            'jsonData' => $this->jsonData,
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
        //dump("hookIndexing");
        // 예: 특정 조건으로 필터링
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
        //dump("hookIndexed");
        return $rows;
    }


}
