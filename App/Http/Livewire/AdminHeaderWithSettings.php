<?php

namespace Jiny\Admin2\App\Http\Livewire;

use Livewire\Component;

class AdminHeaderWithSettings extends Component
{
    public $title = '';
    public $description = '';
    public $mode = 'index'; // index, show, create, edit
    public $data = [];
    public $createRoute = '';
    public $listRoute = '';
    public $settingsPath = '';
    
    public function mount($data = [], $mode = 'index', $settingsPath = '')
    {
        $this->data = $data;
        $this->mode = $mode;
        $this->settingsPath = $settingsPath;
        
        // JSON 데이터에서 제목과 설명 추출
        if (isset($data['title'])) {
            $this->title = $data['title'];
        }
        
        if (isset($data['description'])) {
            $this->description = $data['description'];
        }
        
        // 라우트 설정
        if (isset($data['routes'])) {
            $this->createRoute = $data['routes']['create'] ?? '';
            $this->listRoute = $data['routes']['list'] ?? '';
        }
    }
    
    public function navigateToCreate()
    {
        if ($this->createRoute) {
            return redirect($this->createRoute);
        }
    }
    
    public function navigateToList()
    {
        if ($this->listRoute) {
            return redirect($this->listRoute);
        }
    }

    // 팝업 호출시
    public function openSettings()
    {
        if ($this->settingsPath) {
            // mode에 따라 다른 이벤트 발생
            if ($this->mode === 'show') {
                $this->dispatch('openShowSettings', $this->settingsPath);
            } elseif ($this->mode === 'create') {
                $this->dispatch('openCreateSettings', $this->settingsPath);
            } elseif ($this->mode === 'edit') {
                $this->dispatch('openEditSettings', $this->settingsPath);
            } else {
                $this->dispatch('openSettingsDrawer', $this->settingsPath);
            }
        } else {
            $this->dispatch('openCreateSettings');
        }
    }

    public function render()
    {
        return view('jiny-admin2::livewire.admin-header-with-settings');
    }
}
