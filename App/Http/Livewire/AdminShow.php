<?php

namespace Jiny\Admin2\App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class AdminShow extends Component
{
    // 레코드 ID
    public $itemId;
    
    // 데이터
    public $item;
    public $data = [];
    
    // 설정
    public $jsonData;
    public $controller;
    public $controllerClass;
    
    // 표시 설정
    public $sections = [];
    public $display = [];
    
    public function mount($jsonData = null, $data = [], $id = null)
    {
        $this->jsonData = $jsonData;
        $this->data = $data;
        $this->itemId = $id;
        
        // Apply display formatting if configured
        if (isset($this->jsonData['show']['display'])) {
            $this->display = $this->jsonData['show']['display'];
        }
        
        // Apply section configuration if available
        if (isset($this->jsonData['formSections'])) {
            $this->sections = $this->jsonData['formSections'];
        }
    }
    
    /**
     * 삭제 요청
     */
    public function requestDelete()
    {
        if ($this->itemId) {
            $this->dispatch('delete-single', id: $this->itemId);
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
        return view('jiny-admin2::template.livewire.admin-show');
    }
}