<?php

namespace Jiny\Admin2\App\Http\Livewire;

use Livewire\Component;

class AdminTableSetting extends Component
{
    public $jsonData;
    public $isOpen = false;
    
    public function mount($jsonData = null)
    {
        $this->jsonData = $jsonData;
    }
    
    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
    }
    
    public function render()
    {
        return view('jiny-admin2::livewire.admin-table-setting');
    }
}