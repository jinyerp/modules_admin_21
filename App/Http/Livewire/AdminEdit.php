<?php

namespace Jiny\Admin2\App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class AdminEdit extends Component
{
    public $jsonData;
    public $form = [];
    public $id;
    public $settings = [];
    
    public function mount($jsonData = null, $form = [], $id = null)
    {
        $this->jsonData = $jsonData;
        $this->id = $id;
        
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
    }
    
    public function save()
    {
        // 테이블 이름 가져오기
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        
        // fillable 필드 가져오기 (이전 버전 호환성 포함)
        $allowedColumns = $this->jsonData['update']['fillable'] ?? 
                         $this->jsonData['fields']['fillable'] ?? 
                         ['enable', 'name', 'slug', 'description', 'category', 'version', 'author', 'settings'];
        
        // casts 설정 가져오기
        $casts = $this->jsonData['table']['casts'] ?? 
                 $this->jsonData['fields']['casts'] ?? 
                 [];
        
        // 업데이트할 데이터 준비
        $updateData = [];
        foreach ($this->form as $key => $value) {
            // 허용된 컬럼만 업데이트 (id와 created_at 제외)
            if (in_array($key, $allowedColumns)) {
                // casts 설정에 따른 타입 변환
                if (isset($casts[$key]) && $casts[$key] === 'boolean') {
                    $updateData[$key] = $value ? 1 : 0;
                } else {
                    $updateData[$key] = $value ?: null;
                }
            }
        }
        
        // updated_at 추가
        $updateData['updated_at'] = now();
        
        try {
            // 데이터베이스 업데이트
            DB::table($tableName)
                ->where('id', $this->id)
                ->update($updateData);
            
            // 성공 메시지
            $successMessage = $this->jsonData['update']['messages']['success'] ?? 
                            $this->jsonData['messages']['update']['success'] ?? 
                            '성공적으로 수정되었습니다.';
            session()->flash('success', $successMessage);
            
            // 이전 페이지의 페이지네이션 정보를 가져오기
            $previousUrl = url()->previous();
            
            // route 정보가 있으면 사용
            if (isset($this->jsonData['route']['name'])) {
                $redirectUrl = route($this->jsonData['route']['name'] . '.index');
            } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
                $redirectUrl = route($this->jsonData['route'] . '.index');
            } elseif (strpos($previousUrl, '/admin2/templates') !== false && strpos($previousUrl, '/edit') === false) {
                $redirectUrl = $previousUrl;
            } else {
                $redirectUrl = '/admin2/templates';
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
        
        // route 정보가 있으면 사용
        if (isset($this->jsonData['route']['name'])) {
            $redirectUrl = route($this->jsonData['route']['name'] . '.index');
        } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
            $redirectUrl = route($this->jsonData['route'] . '.index');
        } elseif (strpos($previousUrl, '/admin2/templates') !== false && strpos($previousUrl, '/edit') === false) {
            $redirectUrl = $previousUrl;
        } else {
            $redirectUrl = '/admin2/templates';
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
        $redirectUrl = '/admin2/templates';
        if (isset($this->jsonData['route']['name'])) {
            $redirectUrl = route($this->jsonData['route']['name'] . '.index');
        } elseif (isset($this->jsonData['route']) && is_string($this->jsonData['route'])) {
            $redirectUrl = route($this->jsonData['route'] . '.index');
        }
        
        if ($message) {
            return redirect($redirectUrl)->with('success', $message);
        }
        return redirect($redirectUrl);
    }
    
    public function render()
    {
        return view('jiny-admin2::template.livewire.admin-edit');
    }
}