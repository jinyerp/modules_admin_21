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
    protected $controllerClass = null;

    /**
     * 컴포넌트 초기화
     * 
     * JSON 설정을 로드하고 폼 기본값을 설정하며 hookCreating을 호출합니다.
     * 
     * @param array|null $jsonData JSON 설정 데이터
     * @param array $form 초기 폼 데이터
     */
    public function mount($jsonData = null, $form = [])
    {
        $this->jsonData = $jsonData;
        
        // 컨트롤러 클래스 설정
        $this->setupController();

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
     * 
     * 현재 URL에 따라 적절한 Create 컨트롤러를 결정하고 인스턴스를 생성합니다.
     */
    protected function setupController()
    {
        // URL에서 현재 경로 확인
        $currentUrl = request()->url();
        
        // 컨트롤러 클래스 결정
        if (strpos($currentUrl, '/admin/users/create') !== false) {
            $this->controllerClass = \Jiny\Admin\App\Http\Controllers\Admin\AdminUsers\AdminUsersCreate::class;
        } elseif (strpos($currentUrl, '/admin/user/type/create') !== false) {
            $this->controllerClass = \Jiny\Admin\App\Http\Controllers\Admin\AdminUsertype\AdminUsertypeCreate::class;
        } elseif (strpos($currentUrl, '/admin/hello/create') !== false) {
            $this->controllerClass = \Jiny\Admin\App\Http\Controllers\Admin\AdminHello\AdminHelloCreate::class;
        } elseif (strpos($currentUrl, '/admin/templates/create') !== false) {
            $this->controllerClass = \Jiny\Admin\App\Http\Controllers\Admin\AdminTemplates\AdminTemplatesCreate::class;
        } elseif (strpos($currentUrl, '/admin/test/create') !== false) {
            $this->controllerClass = \Jiny\Admin\App\Http\Controllers\Admin\AdminTest\AdminTestCreate::class;
        }
        
        // 컨트롤러 인스턴스 생성
        if ($this->controllerClass && class_exists($this->controllerClass)) {
            $this->controller = new $this->controllerClass();
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
        // 테이블 이름 가져오기
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';

        // fillable 필드 가져오기 (이전 버전 호환성 포함)
        $allowedColumns = $this->jsonData['store']['fillable'] ??
                         $this->jsonData['fields']['fillable'] ??
                         ['enable', 'name', 'slug', 'description', 'category', 'version', 'author', 'settings'];

        // casts 설정 가져오기
        $casts = $this->jsonData['table']['casts'] ??
                 $this->jsonData['fields']['casts'] ??
                 [];

        // 저장할 데이터 준비
        $insertData = [];
        
        // 기본값 설정 (store.defaults 또는 create.defaults에서 가져오기)
        $defaults = $this->jsonData['store']['defaults'] ?? 
                   $this->jsonData['create']['defaults'] ?? [];
        
        // 먼저 기본값을 적용
        foreach ($defaults as $key => $defaultValue) {
            if (in_array($key, $allowedColumns)) {
                $insertData[$key] = $defaultValue;
            }
        }
        
        // 사용자 입력값으로 덮어쓰기
        foreach ($this->form as $key => $value) {
            // 허용된 컬럼만 저장
            if (in_array($key, $allowedColumns)) {
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
            $result = $this->controller->hookStoring($this, $insertData);
            
            // 반환값 타입 체크
            if (is_array($result)) {
                // 성공: 배열 반환 시 삽입 데이터로 사용
                $insertData = $result;
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
                $redirectUrl = '/admin/user/type';
                $this->dispatch('redirect-with-replace', url: $redirectUrl);
            }

        } catch (\Exception $e) {
            $errorMessage = $this->jsonData['store']['messages']['error'] ??
                          $this->jsonData['messages']['store']['error'] ??
                          '저장 중 오류가 발생했습니다: ';
            session()->flash('error', $errorMessage . $e->getMessage());
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
        $redirectUrl = '/admin/user/type';
        $this->dispatch('redirect-with-replace', url: $redirectUrl);
    }

    /**
     * name 필드 업데이트 처리
     * 
     * name 필드가 변경될 때 slug를 자동으로 생성합니다.
     * 
     * @param string $value 입력된 name 값
     */
    public function updatedFormName($value)
    {
        // slug 자동 생성
        if (empty($this->form['slug'])) {
            $this->form['slug'] = Str::slug($value);
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
