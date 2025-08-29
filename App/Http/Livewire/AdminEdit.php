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
    
    public function mount($jsonData = null, $form = [], $id = null)
    {
        $this->jsonData = $jsonData;
        $this->id = $id;
        
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
        
        // 실제 테이블 컬럼 목록
        $allowedColumns = ['enable', 'name', 'slug', 'description', 'category', 'version', 'author', 'settings'];
        
        // 업데이트할 데이터 준비
        $updateData = [];
        foreach ($this->form as $key => $value) {
            // 허용된 컬럼만 업데이트 (id와 created_at 제외)
            if (in_array($key, $allowedColumns)) {
                // 체크박스 필드 처리 (boolean을 0/1로 변환)
                if ($key === 'enable') {
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
            session()->flash('success', '성공적으로 수정되었습니다.');
            
            // 이전 페이지의 페이지네이션 정보를 가져오기
            $previousUrl = url()->previous();
            
            // 만약 이전 URL이 목록 페이지라면 그대로 사용, 아니면 기본 목록 페이지로
            if (strpos($previousUrl, '/admin2/templates') !== false && strpos($previousUrl, '/edit') === false) {
                $redirectUrl = $previousUrl;
            } else {
                $redirectUrl = '/admin2/templates';
            }
            
            // JavaScript로 브라우저 히스토리를 조작하여 뒤로가기 방지
            $this->dispatch('redirect-with-replace', url: $redirectUrl);
            
        } catch (\Exception $e) {
            session()->flash('error', '수정 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
    
    public function cancel()
    {
        // 이전 페이지의 페이지네이션 정보를 가져오기
        $previousUrl = url()->previous();
        
        // 만약 이전 URL이 목록 페이지라면 그대로 사용, 아니면 기본 목록 페이지로
        if (strpos($previousUrl, '/admin2/templates') !== false && strpos($previousUrl, '/edit') === false) {
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
     * 삭제 완료 처리
     */
    #[On('delete-completed')]
    public function handleDeleteCompleted($message = null)
    {
        // 목록 페이지로 리다이렉트 (메시지 포함)
        if ($message) {
            return redirect('/admin2/templates')->with('success', $message);
        }
        return redirect('/admin2/templates');
    }
    
    public function render()
    {
        return view('jiny-admin2::livewire.admin-edit');
    }
}