<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates\Edit;

use Livewire\Component;
use Jiny\Admin2\App\Models\AdminTemplate;

class EditHeaderWithSettings extends Component
{
    public $template;
    public $title = 'Edit Template';
    public $subtitle = 'Update template information';
    
    public function mount($template)
    {
        $this->template = $template;
        if ($template) {
            $this->title = 'Edit: ' . $template->title;
            $this->subtitle = 'Update template information and settings';
        }
    }
    
    public function openSettings()
    {
        $this->dispatch('openEditSettings');
    }

    public function render()
    {
        return view('jiny-admin2::__admin.admin-templates.edit.edit-header-with-settings');
    }
}