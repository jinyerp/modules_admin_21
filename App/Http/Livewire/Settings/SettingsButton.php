<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates\Settings;

use Livewire\Component;

class SettingsButton extends Component
{
    public function openSettings()
    {
        $this->dispatch('openTableSettings');
    }

    public function render()
    {
        return view('jiny-admin2::livewire.admin.admin-templates.settings.settings-button');
    }
}