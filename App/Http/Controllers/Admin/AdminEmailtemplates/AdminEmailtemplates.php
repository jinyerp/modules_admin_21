<?php
namespace Jiny\Admin\App\Http\Controllers\Admin\AdminEmailtemplates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Jiny\admin\App\Services\JsonConfigService;
use Jiny\Admin\App\Services\EmailTemplateService;

class AdminEmailtemplates extends Controller
{
    private $jsonData;
    private $templateService;

    public function __construct()
    {
        // 서비스를 사용하여 JSON 파일 로드
        $jsonConfigService = new JsonConfigService();
        $this->jsonData = $jsonConfigService->loadFromControllerPath(__DIR__);
        $this->templateService = new EmailTemplateService();
    }

    /**
     * Display a listing of the resource.
     */
    public function __invoke(Request $request)
    {
        // JSON 데이터 확인
        if (!$this->jsonData) {
            return response("Error: JSON 데이터를 로드할 수 없습니다.", 500);
        }

        // template.index view 경로 확인
        if(!isset($this->jsonData['template']['index'])) {
            return response("Error: 화면을 출력하기 위한 template.index 설정이 필요합니다.", 500);
        }

        // JSON 파일 경로 추가
        $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . 'AdminEmailtemplates.json';
        $settingsPath = $jsonPath; // settings drawer를 위한 경로

        // currentRoute 설정
        $this->jsonData['currentRoute'] = $this->jsonData['route']['name'] ?? 'admin.emailtemplates';
        
        // 컨트롤러 클래스를 JSON 데이터에 추가
        $this->jsonData['controllerClass'] = get_class($this);

        return view($this->jsonData['template']['index'], [
            'controllerClass' => static::class,  // 현재 컨트롤러 클래스 전달
            'jsonData' => $this->jsonData,
            'jsonPath' => $jsonPath,
            'settingsPath' => $settingsPath,
            'title' => $this->jsonData['title'] ?? 'Emailtemplates Management',
            'subtitle' => $this->jsonData['subtitle'] ?? 'Manage emailtemplatess'
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
            'placeholder' => 'Search emailtemplatess...',
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
     * Hook: 템플릿 미리보기 생성
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @param int $templateId 템플릿 ID
     * @return array 미리보기 데이터
     */
    public function hookCustomPreview($wire, $params)
    {
        try {
            $templateId = $params['id'] ?? null;
            if (!$templateId) {
                return ['error' => '템플릿 ID가 필요합니다.'];
            }

            $template = $this->templateService->getTemplateById($templateId);
            if (!$template) {
                return ['error' => '템플릿을 찾을 수 없습니다.'];
            }

            // 샘플 데이터로 미리보기 생성
            $preview = $this->templateService->preview($template);
            
            return [
                'success' => true,
                'preview' => $preview,
                'available_variables' => $this->templateService->getAvailableVariables($template->slug)
            ];
        } catch (\Exception $e) {
            return ['error' => '미리보기 생성 실패: ' . $e->getMessage()];
        }
    }

    /**
     * Hook: 템플릿 테스트 발송
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @param array $params 파라미터
     * @return array 결과
     */
    public function hookCustomTestSend($wire, $params)
    {
        try {
            $templateId = $params['id'] ?? null;
            $testEmail = $params['email'] ?? null;
            
            if (!$templateId || !$testEmail) {
                return ['error' => '템플릿 ID와 이메일 주소가 필요합니다.'];
            }

            $template = $this->templateService->getTemplateById($templateId);
            if (!$template) {
                return ['error' => '템플릿을 찾을 수 없습니다.'];
            }

            // 테스트 데이터로 렌더링
            $rendered = $this->templateService->render($template, [
                'recipient_name' => '테스트 수신자',
                'recipient_email' => $testEmail,
                'test_message' => '이것은 테스트 이메일입니다.'
            ]);

            // 메일 발송
            \Illuminate\Support\Facades\Mail::to($testEmail)->send(
                new \Jiny\Admin\Mail\EmailMailable(
                    $rendered['subject'],
                    $rendered['body'],
                    config('mail.from.address'),
                    config('mail.from.name'),
                    $testEmail
                )
            );

            return [
                'success' => true,
                'message' => "테스트 이메일이 {$testEmail}로 발송되었습니다."
            ];
        } catch (\Exception $e) {
            return ['error' => '테스트 발송 실패: ' . $e->getMessage()];
        }
    }

    /**
     * Hook: 사용 가능한 변수 목록 조회
     *
     * @param mixed $wire Livewire 컴포넌트 인스턴스
     * @param array $params 파라미터
     * @return array 변수 목록
     */
    public function hookCustomGetVariables($wire, $params)
    {
        $eventType = $params['event_type'] ?? null;
        $variables = $this->templateService->getAvailableVariables($eventType);
        
        return [
            'success' => true,
            'variables' => $variables,
            'description' => $this->getVariableDescriptions($variables)
        ];
    }

    /**
     * 변수 설명 생성
     */
    private function getVariableDescriptions($variables)
    {
        $descriptions = [
            'app_name' => '애플리케이션 이름',
            'app_url' => '애플리케이션 URL',
            'current_year' => '현재 연도',
            'current_date' => '현재 날짜',
            'current_time' => '현재 시간',
            'user_name' => '사용자 이름',
            'user_email' => '사용자 이메일',
            'verification_link' => '인증 링크',
            'reset_link' => '비밀번호 재설정 링크',
            'expires_at' => '만료 시간',
            'failed_attempts' => '실패 시도 횟수',
            'ip_address' => 'IP 주소',
            'user_agent' => '사용자 에이전트',
            'blocked_reason' => '차단 사유',
            'blocked_until' => '차단 종료 시간'
        ];

        $result = [];
        foreach ($variables as $variable) {
            $result[$variable] = $descriptions[$variable] ?? $variable;
        }
        return $result;
    }
}