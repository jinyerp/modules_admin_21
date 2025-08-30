<?php

namespace Jiny\Admin2\App\Http\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class ShowSettingsDrawer extends Component
{
    public $isOpen = false;
    public $settings = [];
    public $jsonPath;
    
    // Display settings
    public $dateFormat = 'Y-m-d H:i:s';
    public $booleanTrueLabel = 'Enabled';
    public $booleanFalseLabel = 'Disabled';
    public $enableFieldToggle = true;
    public $enableDateFormat = true;
    public $enableSectionToggle = true;
    
    protected $listeners = ['openShowSettings' => 'openWithPath'];
    
    public function mount($jsonPath = null)
    {
        $this->isOpen = false;
        $this->jsonPath = $jsonPath ?: base_path('jiny/admin2/App/Http/Controllers/Admin/AdminTemplates/AdminTemplates.json');
        $this->loadSettings();
    }
    
    public function openWithPath($jsonPath)
    {
        $this->jsonPath = $jsonPath;
        $this->loadSettings();
        $this->isOpen = true;
    }
    
    public function loadSettings()
    {
        try {
            if (File::exists($this->jsonPath)) {
                $jsonContent = File::get($this->jsonPath);
                $this->settings = json_decode($jsonContent, true);
                
                // Load show settings
                $showSettings = $this->settings['show'] ?? [];
                
                // Display settings
                $display = $showSettings['display'] ?? [];
                $this->dateFormat = $display['dateFormat'] ?? 'Y-m-d H:i:s';
                
                $booleanLabels = $display['booleanLabels'] ?? [];
                $this->booleanTrueLabel = $booleanLabels['true'] ?? 'Enabled';
                $this->booleanFalseLabel = $booleanLabels['false'] ?? 'Disabled';
                
                // Settings drawer options
                $settingsDrawer = $showSettings['settingsDrawer'] ?? [];
                $this->enableFieldToggle = $settingsDrawer['enableFieldToggle'] ?? true;
                $this->enableDateFormat = $settingsDrawer['enableDateFormat'] ?? true;
                $this->enableSectionToggle = $settingsDrawer['enableSectionToggle'] ?? true;
            } else {
                $this->setDefaults();
            }
        } catch (\Exception $e) {
            $this->setDefaults();
        }
    }
    
    private function setDefaults()
    {
        $this->dateFormat = 'Y-m-d H:i:s';
        $this->booleanTrueLabel = 'Enabled';
        $this->booleanFalseLabel = 'Disabled';
        $this->enableFieldToggle = true;
        $this->enableDateFormat = true;
        $this->enableSectionToggle = true;
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
        $this->settings['show']['display']['dateFormat'] = $this->dateFormat;
        $this->settings['show']['display']['booleanLabels']['true'] = $this->booleanTrueLabel;
        $this->settings['show']['display']['booleanLabels']['false'] = $this->booleanFalseLabel;
        
        $this->settings['show']['settingsDrawer']['enableFieldToggle'] = $this->enableFieldToggle;
        $this->settings['show']['settingsDrawer']['enableDateFormat'] = $this->enableDateFormat;
        $this->settings['show']['settingsDrawer']['enableSectionToggle'] = $this->enableSectionToggle;
        
        // Save to file
        File::put($this->jsonPath, json_encode($this->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->dispatch('settingsUpdated');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Display settings updated successfully!'
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
        return view('jiny-admin2::livewire.settings.show-settings-drawer');
    }
}