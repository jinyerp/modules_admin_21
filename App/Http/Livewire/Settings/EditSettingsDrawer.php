<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class EditSettingsDrawer extends Component
{
    public $isOpen = false;
    public $settings = [];
    public $jsonPath;
    
    // Edit form settings
    public $formLayout = 'vertical';
    public $enableDelete = true;
    public $enableListButton = true;
    public $enableDetailButton = true;
    public $trackChanges = true;
    public $visibleSections = ['basic', 'settings', 'metadata'];
    public $requiredFields = ['title'];
    public $readonlyFields = ['created_at', 'updated_at'];
    
    protected $listeners = [
        'openDrawer' => 'open',
        'openEditSettings' => 'open'
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
            
            // Load edit settings
            $editSettings = $this->settings['edit'] ?? [];
            $this->formLayout = $editSettings['form']['layout'] ?? 'vertical';
            
            // Load features
            $features = $editSettings['features'] ?? [];
            $this->enableDelete = $features['enableDelete'] ?? true;
            $this->enableListButton = $features['enableListButton'] ?? true;
            $this->enableDetailButton = $features['enableDetailButton'] ?? true;
            
            // Load update features
            $updateFeatures = $this->settings['update']['features'] ?? [];
            $this->trackChanges = $updateFeatures['trackChanges'] ?? true;
            
            // Load visible sections
            $sections = $editSettings['form']['sections'] ?? [];
            $this->visibleSections = array_keys($sections);
            
            // Load required fields from validation
            $validation = $this->settings['validation']['update']['rules'] ?? [];
            $this->requiredFields = [];
            foreach ($validation as $field => $rules) {
                if (str_contains($rules, 'required')) {
                    $this->requiredFields[] = $field;
                }
            }
            
            // Load readonly fields
            foreach ($sections as $section) {
                if (($section['readonly'] ?? false) && isset($section['fields'])) {
                    $this->readonlyFields = array_merge($this->readonlyFields, $section['fields']);
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
        $this->settings['edit']['form']['layout'] = $this->formLayout;
        
        // Update features
        $this->settings['edit']['features']['enableDelete'] = $this->enableDelete;
        $this->settings['edit']['features']['enableListButton'] = $this->enableListButton;
        $this->settings['edit']['features']['enableDetailButton'] = $this->enableDetailButton;
        
        // Update update features
        $this->settings['update']['features']['trackChanges'] = $this->trackChanges;
        
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
        if (in_array('metadata', $this->visibleSections)) {
            $sections['metadata'] = [
                'title' => 'Information',
                'readonly' => true,
                'fields' => ['created_at', 'updated_at']
            ];
        }
        $this->settings['edit']['form']['sections'] = $sections;
        
        // Update timestamp
        $this->settings['edit']['lastUpdated'] = now()->toIso8601String();
        
        // Save to file
        File::put($this->jsonPath, json_encode($this->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->dispatch('settingsUpdated');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Edit form settings updated successfully!'
        ]);
        
        $this->close();
    }

    public function resetToDefaults()
    {
        $this->formLayout = 'vertical';
        $this->enableDelete = true;
        $this->enableListButton = true;
        $this->enableDetailButton = true;
        $this->trackChanges = true;
        $this->visibleSections = ['basic', 'settings', 'metadata'];
        $this->requiredFields = ['title'];
        $this->readonlyFields = ['created_at', 'updated_at'];
    }

    public function render()
    {
        return view('jiny-admin2::livewire.admin.admin-templates.settings.edit-settings-drawer');
    }
}