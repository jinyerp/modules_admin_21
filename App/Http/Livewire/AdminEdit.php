<?php

namespace Jiny\Admin\App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class AdminEdit extends Component
{
    public $jsonData;

    public $form = [];

    public $id;

    public $settings = [];

    public $userTypes = [];  // 사용자 타입 목록

    public $controllerClass = null;  // Livewire가 상태를 유지하기 위해 public으로 변경

    protected $controller = null;

    public function mount($jsonData = null, $form = [], $id = null, $controllerClass = null)
    {
        $this->jsonData = $jsonData;
        $this->id = $id;

        // 컨트롤러 클래스 설정
        if ($controllerClass) {
            $this->controllerClass = $controllerClass;
            $this->setupController();
        } elseif (isset($this->jsonData['controllerClass'])) {
            $this->controllerClass = $this->jsonData['controllerClass'];
            $this->setupController();
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
        if (! empty($form)) {
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
            $this->controller = new $this->controllerClass;
            \Log::info('AdminEdit - Controller created: '.get_class($this->controller));
        } else {
            \Log::error('AdminEdit - Failed to create controller. Class: '.($this->controllerClass ?? 'null'));
        }
    }

    public function save()
    {
        // 컨트롤러 항상 재설정 (Livewire는 PHP 객체를 직렬화하지 못함)
        $this->setupController();
        \Log::info('AdminEdit save() - Controller after setup: '.($this->controller ? get_class($this->controller) : 'null'));

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
                $updateData[$key] = ! empty($value) ? (int) $value : 0;
            }
            // level 필드도 정수형으로 변환
            elseif ($key === 'level') {
                $updateData[$key] = ! empty($value) ? (int) $value : 0;
            }
        }

        // hookUpdating 호출 (업데이트 전 처리)
        if ($this->controller && method_exists($this->controller, 'hookUpdating')) {
            \Log::info('AdminEdit - Controller: '.get_class($this->controller));
            \Log::info('AdminEdit - Calling hookUpdating with data:', $updateData);
            $result = $this->controller->hookUpdating($this, $updateData);
            \Log::info('AdminEdit - hookUpdating result type: '.gettype($result));
            if (is_string($result)) {
                \Log::info('AdminEdit - hookUpdating returned error: '.$result);
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
                    ? (string) $result
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
            \Log::warning('AdminEdit - Controller: '.($this->controller ? get_class($this->controller) : 'null'));
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
            session()->flash('error', $errorMessage.$e->getMessage());
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
     * 커스텀 액션 호출
     * 컨트롤러의 hookCustom{Name} 메소드를 호출합니다.
     *
     * @param  string  $actionName  액션명
     * @param  array  $params  파라미터
     */
    public function callCustomAction($actionName, $params = [])
    {
        // 컨트롤러 확인
        if (! $this->controller) {
            $this->setupController();
        }

        if (! $this->controller) {
            session()->flash('error', '컨트롤러가 설정되지 않았습니다.');

            return;
        }

        // Hook 메소드명 생성
        $methodName = 'hookCustom'.ucfirst($actionName);

        // Hook 메소드 존재 확인
        if (! method_exists($this->controller, $methodName)) {
            session()->flash('error', "Hook 메소드 '{$methodName}'를 찾을 수 없습니다.");

            return;
        }

        // Hook 호출
        try {
            $result = $this->controller->$methodName($this, $params);

            // 결과 처리
            if (isset($result['redirect'])) {
                return redirect($result['redirect']);
            }

            // 데이터 새로고침 (필요한 경우)
            if ($this->id && isset($this->jsonData['table']['name'])) {
                $tableName = $this->jsonData['table']['name'];
                $item = DB::table($tableName)->where('id', $this->id)->first();
                if ($item) {
                    $this->form = (array) $item;
                    unset($this->form['id']); // ID는 form에서 제외
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Hook 실행 중 오류가 발생했습니다: '.$e->getMessage());
        }
    }

    /**
     * 폼 필드가 업데이트될 때 호출되는 매직 메서드
     *
     * Livewire의 updated 훅을 활용하여 컨트롤러의 hook 메서드를 동적으로 호출합니다.
     * 예: form.email이 변경되면 hookFormEmail() 메서드를 찾아서 호출
     *
     * @param  string  $property  변경된 프로퍼티 이름 (예: form.email)
     * @param  mixed  $value  새로운 값
     */
    public function updated($property, $value)
    {
        // 컨트롤러 재설정 (Livewire 요청마다 필요)
        if (! $this->controller && $this->controllerClass) {
            $this->setupController();
        }

        // form.* 프로퍼티만 처리
        if (strpos($property, 'form.') === 0) {
            // form.email -> email 추출
            $fieldName = substr($property, 5);

            // 필드명을 CamelCase로 변환 (email -> Email, user_name -> UserName, password_confirmation -> PasswordConfirmation)
            $methodSuffix = str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName)));

            // hookFormEmail 형태의 메서드명 생성
            $hookMethod = 'hookForm'.$methodSuffix;

            // 디버깅용 로그
            \Log::info('AdminEdit: Field update detected', [
                'property' => $property,
                'fieldName' => $fieldName,
                'methodSuffix' => $methodSuffix,
                'hookMethod' => $hookMethod,
                'has_controller' => ! is_null($this->controller),
                'method_exists' => $this->controller ? method_exists($this->controller, $hookMethod) : false,
            ]);

            // 컨트롤러에 해당 hook 메서드가 있으면 호출
            if ($this->controller && method_exists($this->controller, $hookMethod)) {
                \Log::info("AdminEdit: Calling {$hookMethod}");
                $result = $this->controller->$hookMethod($this, $value, $fieldName);

                // hook이 false를 반환하면 값 복원
                if ($result === false && isset($this->form[$fieldName])) {
                    // 이전 값으로 복원이 필요한 경우
                    // 현재는 값을 그대로 유지
                }
            } else {
                \Log::warning('AdminEdit: Hook method not found', [
                    'hookMethod' => $hookMethod,
                    'controller' => $this->controller ? get_class($this->controller) : 'null',
                ]);
            }

            // 특별 처리: slug 자동 생성 (name 필드가 변경되고 slug가 비어있을 때)
            if ($fieldName === 'name' && isset($this->form['slug']) && empty($this->form['slug'])) {
                $this->form['slug'] = Str::slug($value);
            }
        }
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
