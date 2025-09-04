<?php

namespace Jiny\Admin\App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * 데이터 생성 Livewire 컴포넌트
 *
 * 새로운 레코드를 생성하는 폼을 처리합니다.
 * JSON 설정에 따라 필드, 유효성 검사, 기본값 등을 동적으로 처리합니다.
 */
class AdminCreate extends Component
{
    /**
     * @var array JSON 설정 데이터
     */
    public $jsonData;

    /**
     * @var array 폼 데이터
     */
    public $form = [];

    /**
     * @var array 사용자 타입 목록 (특정 폼에서 사용)
     */
    public $userTypes = [];

    /**
     * @var object|null 컨트롤러 인스턴스
     */
    protected $controller = null;

    /**
     * @var string|null 컨트롤러 클래스명
     */
    public $controllerClass = null;

    /**
     * 컴포넌트 초기화
     *
     * JSON 설정을 로드하고 폼 기본값을 설정하며 hookCreating을 호출합니다.
     *
     * @param array|null $jsonData JSON 설정 데이터
     * @param array $form 초기 폼 데이터
     */
    public function mount($jsonData = null, $form = [], $controllerClass = null)
    {
        $this->jsonData = $jsonData;

        // 컨트롤러 클래스 설정
        if ($controllerClass) {
            $this->controllerClass = $controllerClass;
            $this->setupController();
        } elseif (isset($this->jsonData['controllerClass'])) {
            $this->controllerClass = $this->jsonData['controllerClass'];
            $this->setupController();
        }

        // 기본값 설정
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

        // hookCreating 호출
        if ($this->controller && method_exists($this->controller, 'hookCreating')) {
            $result = $this->controller->hookCreating($this, $this->form);
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
            \Log::info('AdminCreate: Controller loaded successfully', [
                'class' => $this->controllerClass
            ]);
        } else {
            \Log::warning('AdminCreate: Controller class not found', [
                'class' => $this->controllerClass
            ]);
        }
    }

    /**
     * 데이터 저장 처리
     *
     * 폼 데이터를 검증하고 데이터베이스에 저장합니다.
     * Hook 메서드(hookStoring, hookStored)를 호출하여 커스텀 처리를 허용합니다.
     *
     * @param bool $continueCreating true이면 저장 후 계속 생성, false면 목록으로 이동
     */
    public function save($continueCreating = false)
    {
        // 컨트롤러 재설정 (Livewire 요청마다 필요)
        if (!$this->controller) {
            $this->setupController();
        }

        // 테이블 이름 가져오기
        if (!isset($this->jsonData['table']['name']) || empty($this->jsonData['table']['name'])) {
            $this->addError('form', 'JSON 설정 오류: table.name이 설정되지 않았습니다.');
            \Log::error('AdminCreate: table.name not configured in JSON', [
                'jsonData' => $this->jsonData
            ]);
            return;
        }

        $tableName = $this->jsonData['table']['name'];

        // casts 설정 가져오기 (타입 변환용)
        $casts = $this->jsonData['table']['casts'] ?? [];

        // 저장할 데이터 준비 - form 데이터를 그대로 사용
        $insertData = [];

        // 기본값 설정 (store.defaults 또는 create.defaults에서 가져오기)
        $defaults = $this->jsonData['store']['defaults'] ??
                   $this->jsonData['create']['defaults'] ?? [];

        // 먼저 기본값을 적용
        foreach ($defaults as $key => $defaultValue) {
            $insertData[$key] = $defaultValue;
        }

        // 사용자가 폼에서 입력한 데이터로 덮어쓰기
        foreach ($this->form as $key => $value) {
            // 빈 값이 아닌 경우에만 처리
            if ($value !== '' && $value !== null) {
                // casts 설정에 따른 타입 변환
                if (isset($casts[$key]) && $casts[$key] === 'boolean') {
                    $insertData[$key] = $value ? 1 : 0;
                }
                // pos 필드는 빈 값일 때 0으로 설정
                elseif ($key === 'pos') {
                    $insertData[$key] = (int)$value;
                }
                // level 필드도 정수형으로 변환
                elseif ($key === 'level') {
                    $insertData[$key] = (int)$value;
                } else {
                    $insertData[$key] = $value;
                }
            } elseif (!isset($insertData[$key])) {
                // 기본값도 없고 입력값도 없는 경우
                if ($key === 'pos' || $key === 'level') {
                    $insertData[$key] = 0;
                } elseif (isset($casts[$key]) && $casts[$key] === 'boolean') {
                    $insertData[$key] = 0;
                } else {
                    $insertData[$key] = null;
                }
            }
        }

        // 필수 필드 검증 (UserType의 경우 code와 name 필드)
        $requiredFields = $this->jsonData['validation']['rules'] ?? [];
        $errors = [];

        foreach ($requiredFields as $field => $rules) {
            if (strpos($rules, 'required') !== false && empty($insertData[$field])) {
                $errors[] = "{$field} 필드는 필수입니다.";
            }
        }

        if (!empty($errors)) {
            session()->flash('error', implode(', ', $errors));
            return;
        }

        // hookStoring 호출 (저장 전 처리)
        if ($this->controller && method_exists($this->controller, 'hookStoring')) {
            \Log::info('AdminCreate: Calling hookStoring', [
                'controller' => get_class($this->controller),
                'data_before' => array_keys($insertData)
            ]);

            $result = $this->controller->hookStoring($this, $insertData);

            // 반환값 타입 체크
            if (is_array($result)) {
                // 성공: 배열 반환 시 삽입 데이터로 사용
                $insertData = $result;
                \Log::info('AdminCreate: hookStoring returned array', [
                    'data_after' => array_keys($insertData)
                ]);
            } elseif (is_string($result)) {
                // 실패: 문자열 반환 시 에러 메시지로 처리
                $this->addError('form', $result);
                session()->flash('error', $result);
                return;
            } elseif (is_object($result)) {
                // 실패: 객체 반환 시 에러로 처리
                $errorMessage = method_exists($result, '__toString')
                    ? (string)$result
                    : '데이터 검증 실패';
                $this->addError('form', $errorMessage);
                session()->flash('error', $errorMessage);
                return;
            } elseif ($result === false) {
                // 실패: false 반환 시 일반 에러
                $this->addError('form', '저장이 취소되었습니다.');
                return;
            }
        } else {
            // 기본 timestamps 추가 (hook이 없는 경우)
            \Log::warning('AdminCreate: hookStoring not called', [
                'has_controller' => !is_null($this->controller),
                'controller_class' => $this->controllerClass ?? 'not set',
                'has_method' => $this->controller ? method_exists($this->controller, 'hookStoring') : false
            ]);
            $insertData['created_at'] = now();
            $insertData['updated_at'] = now();
        }



        try {
            // 데이터베이스에 저장
            $id = DB::table($tableName)->insertGetId($insertData);

            // hookStored 호출 (저장 후 처리)
            if ($this->controller && method_exists($this->controller, 'hookStored')) {
                $this->controller->hookStored($this, array_merge($insertData, ['id' => $id]));
            }

            if ($continueCreating) {
                // 계속 생성 모드: 폼 데이터를 모두 유지
                // 사용자가 필요한 부분만 수정하여 계속 생성 가능
                // 성공 메시지 (계속 생성용)
                $successMessage = $this->jsonData['store']['messages']['continueSuccess'] ??
                                $this->jsonData['messages']['store']['continueSuccess'] ??
                                '성공적으로 생성되었습니다. 계속 생성할 수 있습니다.';
                session()->flash('success', $successMessage);

                // 폼 데이터를 초기화하지 않음 - 사용자가 수정하면서 계속 입력 가능
                // continueResetFields 설정이 있더라도 기본적으로는 초기화하지 않음
                if (isset($this->jsonData['create']['continueResetFields']) &&
                    !empty($this->jsonData['create']['continueResetFields']) &&
                    ($this->jsonData['create']['enableFieldReset'] ?? false)) {
                    // 명시적으로 enableFieldReset이 true인 경우에만 필드 초기화
                    $fieldsToReset = $this->jsonData['create']['continueResetFields'];
                    foreach ($fieldsToReset as $field) {
                        if (isset($this->form[$field])) {
                            $this->form[$field] = '';
                        }
                    }
                }

                // 페이지 새로고침 없이 계속 작업
                $this->dispatch('focus-first-field');
                $this->dispatch('highlight-success');
            } else {
                // 일반 저장: 목록 페이지로 리다이렉트
                $successMessage = $this->jsonData['store']['messages']['success'] ??
                                $this->jsonData['messages']['store']['success'] ??
                                '성공적으로 생성되었습니다.';
                session()->flash('success', $successMessage);

                // 목록 페이지로 리다이렉트
                // JSON 설정에서 라우트 정보 가져오기
                if (isset($this->jsonData['route']['name'])) {
                    // 라우트 이름으로 URL 생성
                    try {
                        $redirectUrl = route($this->jsonData['route']['name'] . '.index');
                    } catch (\Exception $e) {
                        // 라우트가 없는 경우 대체 방법 사용
                        $routeName = $this->jsonData['route']['name'];
                        // admin.users -> /admin/users 변환
                        $redirectUrl = '/' . str_replace('.', '/', $routeName);
                    }
                } else {
                    // JSON에 라우트 정보가 없는 경우 기존 방식 사용
                    $currentPath = request()->path();
                    if (strpos($currentPath, '/livewire') !== false) {
                        // Livewire 요청인 경우 referer에서 경로 추출
                        $referer = request()->headers->get('referer');
                        if ($referer) {
                            $parsedUrl = parse_url($referer);
                            $path = $parsedUrl['path'] ?? '';
                            // /admin/users/create -> /admin/users
                            $segments = explode('/', trim($path, '/'));
                            if (end($segments) === 'create') {
                                array_pop($segments);
                            }
                            $redirectUrl = '/' . implode('/', $segments);
                        } else {
                            // referer가 없는 경우 기본값
                            $redirectUrl = '/admin';
                        }
                    } else {
                        // 일반 요청
                        $segments = explode('/', $currentPath);
                        array_pop($segments); // 마지막 세그먼트(create) 제거
                        $redirectUrl = '/' . implode('/', $segments);
                    }
                }
                $this->dispatch('redirect-with-replace', url: $redirectUrl);
            }

        } catch (\Exception $e) {
            // 중복 키 에러인지 확인
            if (str_contains($e->getMessage(), 'UNIQUE constraint failed')) {
                // 어떤 필드가 중복인지 파악
                if (str_contains($e->getMessage(), 'users.email')) {
                    $this->addError('form.email', '이미 등록된 이메일입니다. 다른 이메일을 사용해주세요.');
                } else {
                    $this->addError('form', '중복된 데이터가 있습니다. 입력값을 확인해주세요.');
                }
            } else {
                $errorMessage = $this->jsonData['store']['messages']['error'] ??
                              $this->jsonData['messages']['store']['error'] ??
                              '저장 중 오류가 발생했습니다: ';
                $this->addError('form', $errorMessage . $e->getMessage());
            }

            // 페이지 리로드 없이 에러 메시지 유지
            return;
        }
    }

    /**
     * 저장 후 계속 생성
     *
     * 데이터를 저장하고 폼을 유지하여 계속 생성할 수 있도록 합니다.
     */
    public function saveAndContinue()
    {
        $this->save(true);
    }

    /**
     * 취소 처리
     *
     * 폼 입력을 취소하고 목록 페이지로 돌아갑니다.
     */
    public function cancel()
    {
        // 목록 페이지로 리다이렉트
        // JSON 설정에서 라우트 정보 가져오기
        if (isset($this->jsonData['route']['name'])) {
            // 라우트 이름으로 URL 생성
            try {
                $redirectUrl = route($this->jsonData['route']['name'] . '.index');
            } catch (\Exception $e) {
                // 라우트가 없는 경우 대체 방법 사용
                $routeName = $this->jsonData['route']['name'];
                // admin.users -> /admin/users 변환
                $redirectUrl = '/' . str_replace('.', '/', $routeName);
            }
        } else {
            // JSON에 라우트 정보가 없는 경우 기존 방식 사용
            $currentPath = request()->path();
            if (strpos($currentPath, '/livewire') !== false) {
                // Livewire 요청인 경우 referer에서 경로 추출
                $referer = request()->headers->get('referer');
                if ($referer) {
                    $parsedUrl = parse_url($referer);
                    $path = $parsedUrl['path'] ?? '';
                    // /admin/users/create -> /admin/users
                    $segments = explode('/', trim($path, '/'));
                    if (end($segments) === 'create') {
                        array_pop($segments);
                    }
                    $redirectUrl = '/' . implode('/', $segments);
                } else {
                    // referer가 없는 경우 기본값
                    $redirectUrl = '/admin';
                }
            } else {
                // 일반 요청
                $segments = explode('/', $currentPath);
                array_pop($segments); // 마지막 세그먼트(create) 제거
                $redirectUrl = '/' . implode('/', $segments);
            }
        }
        $this->dispatch('redirect-with-replace', url: $redirectUrl);
    }

    /**
     * 커스텀 액션 호출
     * 컨트롤러의 hookCustom{Name} 메소드를 호출합니다.
     * 
     * @param string $actionName 액션명
     * @param array $params 파라미터
     */
    public function callCustomAction($actionName, $params = [])
    {
        // 컨트롤러 확인
        if (!$this->controller) {
            $this->setupController();
        }
        
        if (!$this->controller) {
            session()->flash('error', '컨트롤러가 설정되지 않았습니다.');
            return;
        }
        
        // Hook 메소드명 생성
        $methodName = 'hookCustom' . ucfirst($actionName);
        
        // Hook 메소드 존재 확인
        if (!method_exists($this->controller, $methodName)) {
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
        } catch (\Exception $e) {
            session()->flash('error', 'Hook 실행 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    /**
     * 폼 필드가 업데이트될 때 호출되는 매직 메서드
     *
     * Livewire의 updated 훅을 활용하여 컨트롤러의 hook 메서드를 동적으로 호출합니다.
     * 예: form.email이 변경되면 hookFormEmail() 메서드를 찾아서 호출
     *
     * @param string $property 변경된 프로퍼티 이름 (예: form.email)
     * @param mixed $value 새로운 값
     */
    public function updated($property, $value)
    {
        // 컨트롤러 재설정 (Livewire 요청마다 필요)
        if (!$this->controller && $this->controllerClass) {
            $this->setupController();
        }

        // form.* 프로퍼티만 처리
        if (strpos($property, 'form.') === 0) {
            // form.email -> email 추출
            $fieldName = substr($property, 5);
            
            // 필드명을 CamelCase로 변환 (email -> Email, user_name -> UserName, password_confirmation -> PasswordConfirmation)
            $methodSuffix = str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName)));
            
            // hookFormEmail 형태의 메서드명 생성
            $hookMethod = 'hookForm' . $methodSuffix;
            
            // 디버깅용 로그
            \Log::info('AdminCreate: Field update detected', [
                'property' => $property,
                'fieldName' => $fieldName,
                'methodSuffix' => $methodSuffix,
                'hookMethod' => $hookMethod,
                'has_controller' => !is_null($this->controller),
                'method_exists' => $this->controller ? method_exists($this->controller, $hookMethod) : false
            ]);
            
            // 컨트롤러에 해당 hook 메서드가 있으면 호출
            if ($this->controller && method_exists($this->controller, $hookMethod)) {
                \Log::info("AdminCreate: Calling {$hookMethod}");
                $result = $this->controller->$hookMethod($this, $value, $fieldName);
                
                // hook이 false를 반환하면 값 복원
                if ($result === false && isset($this->form[$fieldName])) {
                    // 이전 값으로 복원이 필요한 경우
                    // 현재는 값을 그대로 유지
                }
            } else {
                \Log::warning("AdminCreate: Hook method not found", [
                    'hookMethod' => $hookMethod,
                    'controller' => $this->controller ? get_class($this->controller) : 'null'
                ]);
            }
            
            // 특별 처리: slug 자동 생성 (name 필드가 변경되고 slug가 비어있을 때)
            if ($fieldName === 'name' && isset($this->form['slug']) && empty($this->form['slug'])) {
                $this->form['slug'] = Str::slug($value);
            }
        }
    }

    /**
     * 컴포넌트 렌더링
     *
     * JSON 설정에 지정된 뷰를 렌더링합니다.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $viewPath = $this->jsonData['createLayoutPath'] ?? 'jiny-admin::template.livewire.admin-create';
        return view($viewPath);
    }
}
