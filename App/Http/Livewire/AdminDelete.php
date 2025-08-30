<?php

namespace Jiny\Admin2\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class AdminDelete extends Component
{
    public $jsonData;
    
    // 삭제 모달 상태
    public $showDeleteModal = false;
    public $deleteType = ''; // 'single' or 'multiple'
    public $deleteIds = [];
    public $deleteCount = 0;
    
    // 확인 키
    public $deleteConfirmKey = '';
    public $deleteConfirmInput = '';
    public $deleteButtonEnabled = false;
    
    public function mount($jsonData = null)
    {
        $this->jsonData = $jsonData;
    }
    
    // 단일 항목 삭제 이벤트 처리
    #[On('delete-single')]
    public function handleDeleteSingle($id)
    {
        $this->deleteType = 'single';
        $this->deleteIds = [$id];
        $this->deleteCount = 1;
        $this->openDeleteModal();
    }
    
    // 다중 항목 삭제 이벤트 처리
    #[On('delete-multiple')]
    public function handleDeleteMultiple($ids)
    {
        if (empty($ids)) {
            return;
        }
        
        $this->deleteType = 'multiple';
        $this->deleteIds = $ids;
        $this->deleteCount = count($ids);
        $this->openDeleteModal();
    }
    
    // 삭제 모달 열기
    private function openDeleteModal()
    {
        // 4자리 랜덤 키 생성
        $this->deleteConfirmKey = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $this->deleteConfirmInput = '';
        $this->deleteButtonEnabled = false;
        $this->showDeleteModal = true;
    }
    
    // 삭제 모달 닫기
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteType = '';
        $this->deleteIds = [];
        $this->deleteCount = 0;
        $this->deleteConfirmKey = '';
        $this->deleteConfirmInput = '';
        $this->deleteButtonEnabled = false;
    }
    
    // 확인 키 복사
    public function copyConfirmKey()
    {
        $this->deleteConfirmInput = $this->deleteConfirmKey;
        $this->validateConfirmKey();
    }
    
    // 확인 키 입력 처리
    public function updatedDeleteConfirmInput($value)
    {
        $this->validateConfirmKey();
    }
    
    private function validateConfirmKey()
    {
        $this->deleteButtonEnabled = ($this->deleteConfirmInput === $this->deleteConfirmKey);
    }
    
    // 삭제 실행
    public function executeDelete()
    {
        if (!$this->deleteButtonEnabled || empty($this->deleteIds)) {
            return;
        }
        
        // 테이블 이름 가져오기
        $tableName = $this->jsonData['table']['name'] ?? 'admin_templates';
        
        try {
            // 데이터베이스에서 삭제
            DB::table($tableName)
                ->whereIn('id', $this->deleteIds)
                ->delete();
            
            // 모달 닫기
            $this->closeDeleteModal();
            
            // 성공 메시지
            $message = $this->deleteType === 'single' 
                ? '템플릿이 삭제되었습니다.'
                : "{$this->deleteCount}개 항목이 삭제되었습니다.";
            
            // 완료 이벤트 발송 (항상 메시지 포함)
            $this->dispatch('delete-completed', message: $message);
            
        } catch (\Exception $e) {
            session()->flash('error', '삭제 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('jiny-admin2::template.livewire.admin-delete');
    }
}