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

    public function save()
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

            // 성공 메시지
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

        } catch (\Exception $e) {
            $errorMessage = $this->jsonData['store']['messages']['error'] ??
                          $this->jsonData['messages']['store']['error'] ??
                          '저장 중 오류가 발생했습니다: ';
            session()->flash('error', $errorMessage . $e->getMessage());
        }
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
