<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class DetailSettingsDrawer extends Component
{
    public $isOpen = false;
    public $settings = [];
    public $jsonPath;
    
    // Display settings
    public $dateFormat = 'Y-m-d H:i:s';
    public $enableEdit = true;
    public $enableDelete = true;
    public $enableCreate = true;
    public $enableListButton = true;
    public $visibleSections = ['information', 'timestamps'];
    public $visibleFields = [];
    
    protected $listeners = [
        'openDrawer' => 'open',
        'openDetailSettings' => 'open'
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
            
            // Load show settings
            $showSettings = $this->settings['show'] ?? [];
            $this->dateFormat = $showSettings['display']['dateFormat'] ?? 'Y-m-d H:i:s';
            
            // Load features
            $features = $showSettings['features'] ?? [];
            $this->enableEdit = $features['enableEdit'] ?? true;
            $this->enableDelete = $features['enableDelete'] ?? true;
            $this->enableCreate = $features['enableCreate'] ?? true;
            $this->enableListButton = $features['enableListButton'] ?? true;
            
            // Load visible sections and fields
            $sections = $showSettings['sections'] ?? [];
            $this->visibleSections = array_keys($sections);
            
            foreach ($sections as $section) {
                if (isset($section['fields'])) {
                    $this->visibleFields = array_merge($this->visibleFields, $section['fields']);
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
        $this->settings['show']['display']['dateFormat'] = $this->dateFormat;
        
        // Update features
        $this->settings['show']['features']['enableEdit'] = $this->enableEdit;
        $this->settings['show']['features']['enableDelete'] = $this->enableDelete;
        $this->settings['show']['features']['enableCreate'] = $this->enableCreate;
        $this->settings['show']['features']['enableListButton'] = $this->enableListButton;
        
        // Update visible sections
        $sections = [];
        if (in_array('information', $this->visibleSections)) {
            $sections['information'] = [
                'title' => 'Template Information',
                'fields' => ['id', 'title', 'description', 'enable']
            ];
        }
        if (in_array('timestamps', $this->visibleSections)) {
            $sections['timestamps'] = [
                'title' => 'Timestamps',
                'fields' => ['created_at', 'updated_at']
            ];
        }
        $this->settings['show']['sections'] = $sections;
        
        // Update timestamp
        $this->settings['show']['lastUpdated'] = now()->toIso8601String();
        
        // Save to file
        File::put($this->jsonPath, json_encode($this->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->dispatch('settingsUpdated');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Detail view settings updated successfully!'
        ]);
        
        $this->close();
    }

    public function resetToDefaults()
    {
        $this->dateFormat = 'Y-m-d H:i:s';
        $this->enableEdit = true;
        $this->enableDelete = true;
        $this->enableCreate = true;
        $this->enableListButton = true;
        $this->visibleSections = ['information', 'timestamps'];
        $this->visibleFields = ['id', 'title', 'description', 'enable', 'created_at', 'updated_at'];
    }

    public function render()
    {
        return view('jiny-admin2::livewire.admin.admin-templates.settings.detail-settings-drawer');
    }
}