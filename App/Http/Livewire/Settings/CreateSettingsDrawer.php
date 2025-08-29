<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class CreateSettingsDrawer extends Component
{
    public $isOpen = false;
    public $settings = [];
    public $jsonPath;
    
    // Form settings
    public $formLayout = 'vertical';
    public $enableContinueCreate = true;
    public $enableListButton = true;
    public $defaultEnable = true;
    public $requiredFields = ['title'];
    public $visibleSections = ['basic', 'settings'];
    
    protected $listeners = [
        'openDrawer' => 'open',
        'openCreateSettings' => 'open'
    ];

    public function mount($jsonPath = null)
    {
        $this->jsonPath = $jsonPath ?: base_path('jiny/Admin2/App/Http/Controllers/Admin/AdminTemplates/AdminTemplate.json');
        $this->loadSettings();
        
        // Ensure isOpen is false on mount
        $this->isOpen = false;
    }

    public function loadSettings()
    {
        if (File::exists($this->jsonPath)) {
            $jsonContent = File::get($this->jsonPath);
            $this->settings = json_decode($jsonContent, true);
            
            // Load create settings
            $createSettings = $this->settings['create'] ?? [];
            $this->formLayout = $createSettings['form']['layout'] ?? 'vertical';
            $this->enableContinueCreate = $createSettings['features']['enableContinueCreate'] ?? true;
            $this->enableListButton = $createSettings['features']['enableListButton'] ?? true;
            $this->defaultEnable = $createSettings['defaults']['enable'] ?? true;
            
            // Load visible sections
            $sections = $createSettings['form']['sections'] ?? [];
            $this->visibleSections = array_keys($sections);
            
            // Load required fields from validation
            $validation = $this->settings['validation']['store']['rules'] ?? [];
            foreach ($validation as $field => $rules) {
                if (str_contains($rules, 'required')) {
                    $this->requiredFields[] = $field;
                }
            }
        }
    }

    public function open()
    {
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function save()
    {
        // Update settings in the JSON
        $this->settings['create']['form']['layout'] = $this->formLayout;
        $this->settings['create']['features']['enableContinueCreate'] = $this->enableContinueCreate;
        $this->settings['create']['features']['enableListButton'] = $this->enableListButton;
        $this->settings['create']['defaults']['enable'] = $this->defaultEnable;
        
        // Update visible sections
        $sections = [];
        if (in_array('basic', $this->visibleSections)) {
            $sections['basic'] = [
                'title' => 'Basic Information',
                'fields' => ['title', 'description']
            ];
        }
        if (in_array('settings', $this->visibleSections)) {
            $sections['settings'] = [
                'title' => 'Settings',
                'fields' => ['enable']
            ];
        }
        $this->settings['create']['form']['sections'] = $sections;
        
        // Update timestamp
        $this->settings['create']['lastUpdated'] = now()->toIso8601String();
        
        // Save to file
        File::put($this->jsonPath, json_encode($this->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->dispatch('settingsUpdated');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Create form settings updated successfully!'
        ]);
        
        $this->close();
    }

    public function resetToDefaults()
    {
        $this->formLayout = 'vertical';
        $this->enableContinueCreate = true;
        $this->enableListButton = true;
        $this->defaultEnable = true;
        $this->requiredFields = ['title'];
        $this->visibleSections = ['basic', 'settings'];
    }

    public function render()
    {
        return view('jiny-admin2::livewire.admin.admin-templates.settings.create-settings-drawer');
    }
}