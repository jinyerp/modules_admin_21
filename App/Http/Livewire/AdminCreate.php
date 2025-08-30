<?php

namespace Jiny\Admin2\App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminCreate extends Component
{
    public $jsonData;
    public $form = [];

    public function mount($jsonData = null, $form = [])
    {
        $this->jsonData = $jsonData;

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
    }

    /**
     * 데이터 저장 처리
     * Save data and redirect to list or continue creating
     * 
     * @param bool $continueCreating 계속 생성 여부
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
        foreach ($this->form as $key => $value) {
            // 허용된 컬럼만 저장
            if (in_array($key, $allowedColumns)) {
                // casts 설정에 따른 타입 변환
                if (isset($casts[$key]) && $casts[$key] === 'boolean') {
                    $insertData[$key] = $value ? 1 : 0;
                } else {
                    $insertData[$key] = $value ?: null;
                }
            }
        }

        // 필수 필드 검증
        if (empty($insertData['name'])) {
            session()->flash('error', 'Name 필드는 필수입니다.');
            return;
        }

        // slug 자동 생성
        if (empty($insertData['slug'])) {
            $insertData['slug'] = Str::slug($insertData['name']);
        }

        // timestamps 추가
        $insertData['created_at'] = now();
        $insertData['updated_at'] = now();

        try {
            // 데이터베이스에 저장
            $id = DB::table($tableName)->insertGetId($insertData);

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
                $redirectUrl = '/admin2/templates';
                if (isset($this->jsonData['route']['name'])) {
                    $redirectUrl = route($this->jsonData['route']['name'] . '.index');
                } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
                    $redirectUrl = route($this->jsonData['route'] . '.index');
                }
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
     * Save and continue creating with similar data
     */
    public function saveAndContinue()
    {
        $this->save(true);
    }

    public function cancel()
    {
        // 목록 페이지로 리다이렉트
        $redirectUrl = '/admin2/templates';
        if (isset($this->jsonData['route']['name'])) {
            $redirectUrl = route($this->jsonData['route']['name'] . '.index');
        } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
            $redirectUrl = route($this->jsonData['route'] . '.index');
        }
        $this->dispatch('redirect-with-replace', url: $redirectUrl);
    }

    // 동적 필드 처리
    public function updatedFormName($value)
    {
        // slug 자동 생성
        if (empty($this->form['slug'])) {
            $this->form['slug'] = Str::slug($value);
        }
    }

    public function render()
    {
        $viewPath = $this->jsonData['createLayoutPath'] ?? 'jiny-admin2::template.livewire.admin-create';
        return view($viewPath);
    }
}
