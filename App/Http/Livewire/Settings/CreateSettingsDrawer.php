<?php

namespace Jiny\Admin2\App\Http\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class CreateSettingsDrawer extends Component
{
    public $isOpen = false;
    public $settings = [];
    public $jsonPath;
    
    // Create form settings
    public $enableContinueCreate = true;
    public $enableListButton = true;
    public $enableSettingsDrawer = true;
    public $formLayout = 'vertical';
    public $enableDefaultValues = true;
    public $enableFieldToggle = true;
    public $enableValidationRules = true;
    
    protected $listeners = ['openCreateSettings' => 'openWithPath'];
    
    public function mount($jsonPath = null)
    {
        $this->isOpen = false;
        $this->jsonPath = $jsonPath ?: base_path('jiny/admin2/App/Http/Controllers/Admin/AdminTemplates/AdminTemplates.json');
        $this->loadSettings();
    }
    
    public function openWithPath($jsonPath = null)
    {
        if ($jsonPath) {
            $this->jsonPath = $jsonPath;
        }
        $this->loadSettings();
        $this->isOpen = true;
    }
    
    public function loadSettings()
    {
        try {
            if (File::exists($this->jsonPath)) {
                $jsonContent = File::get($this->jsonPath);
                $this->settings = json_decode($jsonContent, true);
                
                // Load create settings
                $createSettings = $this->settings['create'] ?? [];
                
                $this->enableContinueCreate = $createSettings['enableContinueCreate'] ?? true;
                $this->enableListButton = $createSettings['enableListButton'] ?? true;
                $this->enableSettingsDrawer = $createSettings['enableSettingsDrawer'] ?? true;
                $this->formLayout = $createSettings['formLayout'] ?? 'vertical';
                
                // Settings drawer options
                $settingsDrawer = $createSettings['settingsDrawer'] ?? [];
                $this->enableDefaultValues = $settingsDrawer['enableDefaultValues'] ?? true;
                $this->enableFieldToggle = $settingsDrawer['enableFieldToggle'] ?? true;
                $this->enableValidationRules = $settingsDrawer['enableValidationRules'] ?? true;
            } else {
                $this->setDefaults();
            }
        } catch (\Exception $e) {
            $this->setDefaults();
        }
    }
    
    private function setDefaults()
    {
        $this->enableContinueCreate = true;
        $this->enableListButton = true;
        $this->enableSettingsDrawer = true;
        $this->formLayout = 'vertical';
        $this->enableDefaultValues = true;
        $this->enableFieldToggle = true;
        $this->enableValidationRules = true;
    }
    
    public function open()
    {
        $this->loadSettings();
        $this->isOpen = true;
    }
    
    public function close()
    {
        $this->isOpen = false;
    }
    
    public function save()
    {
        // Update settings in the JSON
        $this->settings['create']['enableContinueCreate'] = $this->enableContinueCreate;
        $this->settings['create']['enableListButton'] = $this->enableListButton;
        $this->settings['create']['enableSettingsDrawer'] = $this->enableSettingsDrawer;
        $this->settings['create']['formLayout'] = $this->formLayout;
        
        $this->settings['create']['settingsDrawer']['enableDefaultValues'] = $this->enableDefaultValues;
        $this->settings['create']['settingsDrawer']['enableFieldToggle'] = $this->enableFieldToggle;
        $this->settings['create']['settingsDrawer']['enableValidationRules'] = $this->enableValidationRules;
        
        // Save to file
        File::put($this->jsonPath, json_encode($this->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->dispatch('settingsUpdated');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Form settings updated successfully!'
        ]);
        
        $this->close();
        
        // 페이지 새로고침으로 변경사항 즉시 반영
        $this->dispatch('refresh-page');
    }
    
    public function resetToDefaults()
    {
        $this->setDefaults();
    }
    
    public function render()
    {
        return view('jiny-admin2::livewire.settings.create-settings-drawer');
    }
}