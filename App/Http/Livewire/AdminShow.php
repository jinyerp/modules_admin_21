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
        if ($message) {
            return redirect('/admin2/templates')->with('success', $message);
        }
        return redirect('/admin2/templates');
    }
    
    public function render()
    {
        return view('jiny-admin2::livewire.admin-show');
    }
}