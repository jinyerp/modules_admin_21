<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates\Create;

use Livewire\Component;

class CreateHeaderWithSettings extends Component
{
    public $title = 'Create New Template';
    public $subtitle = 'Add a new admin template to the system';
    
    public function openSettings()
    {
        $this->dispatch('openCreateSettings');
    }

    public function render()
    {
        return view('jiny-admin2::livewire.admin.admin-templates.create.create-header-with-settings');
    }
}