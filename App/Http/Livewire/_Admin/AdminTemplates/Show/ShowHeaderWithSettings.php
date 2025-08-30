<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates\Show;

use Livewire\Component;
use Jiny\Admin2\App\Models\AdminTemplate;

class ShowHeaderWithSettings extends Component
{
    public $template;
    public $title = 'Template Details';
    public $subtitle = 'View template information';
    
    public function mount($template)
    {
        $this->template = $template;
        if ($template) {
            $this->title = $template->title;
            $this->subtitle = 'View template details and settings';
        }
    }
    
    public function openSettings()
    {
        $this->dispatch('openDetailSettings');
    }

    public function render()
    {
        return view('jiny-admin2::__admin.admin-templates.show.show-header-with-settings');
    }
}