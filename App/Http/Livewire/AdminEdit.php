<?php

namespace Jiny\Admin\App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class AdminEdit extends Component
{
    public $jsonData;
    public $form = [];
    public $id;
    public $settings = [];
    public $userTypes = [];  // 사용자 타입 목록
    public $controllerClass = null;  // Livewire가 상태를 유지하기 위해 public으로 변경
    protected $controller = null;

    public function mount($controllerClass = null, $jsonData = null, $form = [], $id = null)
    {
        $this->jsonData = $jsonData;
        $this->id = $id;

        // 전달받은 컨트롤러 클래스 저장
        if ($controllerClass) {
            $this->controllerClass = $controllerClass;
            // 컨트롤러 인스턴스 생성
            if (class_exists($this->controllerClass)) {
                $this->controller = new $this->controllerClass();
            }
        }

        // JSON 설정에서 edit 설정 추출
        $this->settings = [
            'enableDelete' => $this->jsonData['edit']['enableDelete'] ?? true,
            'enableListButton' => $this->jsonData['edit']['enableListButton'] ?? true,
            'enableDetailButton' => $this->jsonData['edit']['enableDetailButton'] ?? false,
            'enableSettingsDrawer' => $this->jsonData['edit']['enableSettingsDrawer'] ?? true,
            'includeTimestamps' => $this->jsonData['edit']['includeTimestamps'] ?? false,
            'formLayout' => $this->jsonData['edit']['formLayout'] ?? 'vertical',
        ];

        // form.컬럼명 형태로 데이터 설정
        if (!empty($form)) {
            foreach ($form as $key => $value) {
                // 체크박스 필드는 boolean으로 변환
                if ($key === 'enable') {
                    $this->form[$key] = (bool) $value;
                } else {
                    $this->form[$key] = $value;
                }
            }
        }

        // hookEditing 호출
        if ($this->controller && method_exists($this->controller, 'hookEditing')) {
            $result = $this->controller->hookEditing($this, $this->form);
            if (is_array($result)) {
                $this->form = $result;
            }
        }
    }

    /**
     * 컨트롤러 설정
     */
    protected function setupController()
    {
        // 컨트롤러 인스턴스 생성
        if ($this->controllerClass && class_exists($this->controllerClass)) {
            $this->controller = new $this->controllerClass();
            \Log::info('AdminEdit - Controller created: ' . get_class($this->controller));
        } else {
            \Log::error('AdminEdit - Failed to create controller. Class: ' . ($this->controllerClass ?? 'null'));
        }
    }

    public function save()
    {
        // 컨트롤러 항상 재설정 (Livewire는 PHP 객체를 직렬화하지 못함)
        $this->setupController();
        \Log::info('AdminEdit save() - Controller after setup: ' . ($this->controller ? get_class($this->controller) : 'null'));

        // 테이블 이름 가져오기
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';

        // 업데이트할 데이터 준비 - form 데이터를 그대로 사용하되 id만 제외
        $updateData = $this->form;
        
        // id는 업데이트 대상에서 제외 (WHERE 조건으로만 사용)
        unset($updateData['id']);
        
        // casts 설정 가져오기 (타입 변환이 필요한 경우를 위해)
        $casts = $this->jsonData['table']['casts'] ??
                 $this->jsonData['fields']['casts'] ??
                 [];
        
        // casts 설정에 따른 타입 변환 적용
        foreach ($updateData as $key => $value) {
            if (isset($casts[$key]) && $casts[$key] === 'boolean') {
                $updateData[$key] = $value ? 1 : 0;
            }
            // pos 필드는 빈 값일 때 0으로 설정
            elseif ($key === 'pos') {
                $updateData[$key] = !empty($value) ? (int)$value : 0;
            }
            // level 필드도 정수형으로 변환
            elseif ($key === 'level') {
                $updateData[$key] = !empty($value) ? (int)$value : 0;
            }
        }

        // hookUpdating 호출 (업데이트 전 처리)
        if ($this->controller && method_exists($this->controller, 'hookUpdating')) {
            \Log::info('AdminEdit - Controller: ' . get_class($this->controller));
            \Log::info('AdminEdit - Calling hookUpdating with data:', $updateData);
            $result = $this->controller->hookUpdating($this, $updateData);
            \Log::info('AdminEdit - hookUpdating result type: ' . gettype($result));
            if (is_string($result)) {
                \Log::info('AdminEdit - hookUpdating returned error: ' . $result);
            }

            // 반환값 타입 체크
            if (is_array($result)) {
                // 성공: 배열 반환 시 업데이트 데이터로 사용
                $updateData = $result;
            } elseif (is_string($result)) {
                // 실패: 문자열 반환 시 에러 메시지로 처리
                \Log::error('AdminEdit - Stopping update due to validation error');
                $this->addError('form', $result);
                return;
            } elseif (is_object($result)) {
                // 실패: 객체 반환 시 에러로 처리
                $errorMessage = method_exists($result, '__toString')
                    ? (string)$result
                    : '업데이트 검증 실패';
                $this->addError('form', $errorMessage);
                return;
            } elseif ($result === false) {
                // 실패: false 반환 시 일반 에러
                $this->addError('form', '업데이트가 취소되었습니다.');
                return;
            }
        } else {
            // 컨트롤러나 메서드가 없는 경우
            \Log::warning('AdminEdit - No controller or hookUpdating method found');
            \Log::warning('AdminEdit - Controller: ' . ($this->controller ? get_class($this->controller) : 'null'));
            // 기본 updated_at 추가 (hook이 없는 경우)
            $updateData['updated_at'] = now();
        }

        try {
            // 데이터베이스 업데이트
            DB::table($tableName)
                ->where('id', $this->id)
                ->update($updateData);

            // hookUpdated 호출 (업데이트 후 처리)
            if ($this->controller && method_exists($this->controller, 'hookUpdated')) {
                $this->controller->hookUpdated($this, array_merge($updateData, ['id' => $this->id]));
            }

            // 성공 메시지
            $successMessage = $this->jsonData['update']['messages']['success'] ??
                            $this->jsonData['messages']['update']['success'] ??
                            '성공적으로 수정되었습니다.';
            session()->flash('success', $successMessage);

            // 이전 페이지의 페이지네이션 정보를 가져오기
            $previousUrl = url()->previous();

            // JSON 설정에서 라우트 가져오기
            if (isset($this->jsonData['route']['name'])) {
                $redirectUrl = route($this->jsonData['route']['name']);
            } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
                $redirectUrl = route($this->jsonData['route']);
            } else {
                // URL에서 경로 추출
                $currentUrl = request()->url();
                if (strpos($currentUrl, '/admin/users/') !== false) {
                    $redirectUrl = '/admin/users';
                } elseif (strpos($currentUrl, '/admin/user/type/') !== false) {
                    $redirectUrl = '/admin/user/type';
                } else {
                    $redirectUrl = '/admin';
                }
            }

            // JavaScript로 브라우저 히스토리를 조작하여 뒤로가기 방지
            $this->dispatch('redirect-with-replace', url: $redirectUrl);

        } catch (\Exception $e) {
            $errorMessage = $this->jsonData['update']['messages']['error'] ??
                          $this->jsonData['messages']['update']['error'] ??
                          '수정 중 오류가 발생했습니다: ';
            session()->flash('error', $errorMessage . $e->getMessage());
        }
    }

    public function cancel()
    {
        // 이전 페이지의 페이지네이션 정보를 가져오기
        $previousUrl = url()->previous();

        // JSON 설정에서 라우트 가져오기
        if (isset($this->jsonData['route']['name'])) {
            $redirectUrl = route($this->jsonData['route']['name']);
        } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
            $redirectUrl = route($this->jsonData['route']);
        } else {
            // URL에서 경로 추출
            $currentUrl = request()->url();
            if (strpos($currentUrl, '/admin/users/') !== false) {
                $redirectUrl = '/admin/users';
            } elseif (strpos($currentUrl, '/admin/user/type/') !== false) {
                $redirectUrl = '/admin/user/type';
            } else {
                $redirectUrl = '/admin';
            }
        }

        // 목록 페이지로 리다이렉트 (페이지네이션 정보 유지)
        $this->dispatch('redirect-with-replace', url: $redirectUrl);
    }

    /**
     * 삭제 요청
     */
    public function requestDelete()
    {
        if ($this->id) {
            $this->dispatch('delete-single', id: $this->id);
        }
    }

    /**
     * 설정 업데이트 시 리프레시
     */
    #[On('settingsUpdated')]
    public function handleSettingsUpdate()
    {
        // JSON 설정 다시 로드
        if ($this->jsonData) {
            $this->settings = [
                'enableDelete' => $this->jsonData['edit']['enableDelete'] ?? true,
                'enableListButton' => $this->jsonData['edit']['enableListButton'] ?? true,
                'enableDetailButton' => $this->jsonData['edit']['enableDetailButton'] ?? false,
                'enableSettingsDrawer' => $this->jsonData['edit']['enableSettingsDrawer'] ?? true,
                'includeTimestamps' => $this->jsonData['edit']['includeTimestamps'] ?? false,
                'formLayout' => $this->jsonData['edit']['formLayout'] ?? 'vertical',
            ];
        }
    }

    /**
     * 삭제 완료 처리
     */
    #[On('delete-completed')]
    public function handleDeleteCompleted($message = null)
    {
        // 목록 페이지로 리다이렉트 (메시지 포함)
        if (isset($this->jsonData['route']['name'])) {
            $redirectUrl = route($this->jsonData['route']['name']);
        } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
            $redirectUrl = route($this->jsonData['route']);
        } else {
            // URL에서 경로 추출
            $currentUrl = request()->url();
            if (strpos($currentUrl, '/admin/users/') !== false) {
                $redirectUrl = '/admin/users';
            } elseif (strpos($currentUrl, '/admin/user/type/') !== false) {
                $redirectUrl = '/admin/user/type';
            } else {
                $redirectUrl = '/admin';
            }
        }

        if ($message) {
            return redirect($redirectUrl)->with('success', $message);
        }
        return redirect($redirectUrl);
    }

    public function render()
    {
        $viewPath = $this->jsonData['edit']['editLayoutPath'] ?? 'jiny-admin::template.livewire.admin-edit';
        return view($viewPath);
    }
}
