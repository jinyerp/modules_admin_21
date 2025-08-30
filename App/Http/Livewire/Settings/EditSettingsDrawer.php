<?php

namespace Jiny\Admin2\App\Http\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class EditSettingsDrawer extends Component
{
    public $isOpen = false;
    public $settings = [];
    public $jsonPath;
    
    // Edit form settings
    public $enableDelete = true;
    public $enableListButton = true;
    public $enableDetailButton = true;
    public $enableSettingsDrawer = true;
    public $formLayout = 'vertical';
    public $includeTimestamps = true;
    public $enableFieldToggle = true;
    public $enableValidationRules = true;
    public $enableChangeTracking = true;
    
    protected $listeners = ['openEditSettings' => 'openWithPath'];
    
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
                
                // Load edit settings
                $editSettings = $this->settings['edit'] ?? [];
                
                $this->enableDelete = $editSettings['enableDelete'] ?? true;
                $this->enableListButton = $editSettings['enableListButton'] ?? true;
                $this->enableDetailButton = $editSettings['enableDetailButton'] ?? true;
                $this->enableSettingsDrawer = $editSettings['enableSettingsDrawer'] ?? true;
                $this->formLayout = $editSettings['formLayout'] ?? 'vertical';
                $this->includeTimestamps = $editSettings['includeTimestamps'] ?? true;
                
                // Settings drawer options
                $settingsDrawer = $editSettings['settingsDrawer'] ?? [];
                $this->enableFieldToggle = $settingsDrawer['enableFieldToggle'] ?? true;
                $this->enableValidationRules = $settingsDrawer['enableValidationRules'] ?? true;
                $this->enableChangeTracking = $settingsDrawer['enableChangeTracking'] ?? true;
            } else {
                $this->setDefaults();
            }
        } catch (\Exception $e) {
            $this->setDefaults();
        }
    }
    
    private function setDefaults()
    {
        $this->enableDelete = true;
        $this->enableListButton = true;
        $this->enableDetailButton = true;
        $this->enableSettingsDrawer = true;
        $this->formLayout = 'vertical';
        $this->includeTimestamps = true;
        $this->enableFieldToggle = true;
        $this->enableValidationRules = true;
        $this->enableChangeTracking = true;
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
        $this->settings['edit']['enableDelete'] = $this->enableDelete;
        $this->settings['edit']['enableListButton'] = $this->enableListButton;
        $this->settings['edit']['enableDetailButton'] = $this->enableDetailButton;
        $this->settings['edit']['enableSettingsDrawer'] = $this->enableSettingsDrawer;
        $this->settings['edit']['formLayout'] = $this->formLayout;
        $this->settings['edit']['includeTimestamps'] = $this->includeTimestamps;
        
        $this->settings['edit']['settingsDrawer']['enableFieldToggle'] = $this->enableFieldToggle;
        $this->settings['edit']['settingsDrawer']['enableValidationRules'] = $this->enableValidationRules;
        $this->settings['edit']['settingsDrawer']['enableChangeTracking'] = $this->enableChangeTracking;
        
        // Save to file
        File::put($this->jsonPath, json_encode($this->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->dispatch('settingsUpdated');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Edit settings updated successfully!'
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
        return view('jiny-admin2::livewire.settings.edit-settings-drawer');
    }
}