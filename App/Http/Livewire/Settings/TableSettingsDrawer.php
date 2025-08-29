<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class TableSettingsDrawer extends Component
{
    public $isOpen = false;
    public $settings = [];
    public $jsonPath;

    // Table display settings
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $visibleColumns = [];
    public $enableSearch = true;
    public $enableBulkActions = true;
    public $enablePagination = true;
    public $enableStatusToggle = true;

    protected $listeners = ['openTableSettings' => 'open'];

    public function mount($jsonPath = null)
    {
        // isOpen must be false on mount
        $this->isOpen = false;

        $this->jsonPath = $jsonPath ?: base_path('jiny/Admin2/App/Http/Controllers/Admin/AdminTemplates/AdminTemplate.json');
        $this->loadSettings();
    }

    public function loadSettings()
    {
        try {
            if (File::exists($this->jsonPath)) {
                $jsonContent = File::get($this->jsonPath);
                $this->settings = json_decode($jsonContent, true);

                // Load index settings
                $indexSettings = $this->settings['index'] ?? [];
                $this->perPage = $indexSettings['pagination']['perPage'] ?? 10;
                $this->sortField = $indexSettings['sorting']['default'] ?? 'created_at';
                $this->sortDirection = $indexSettings['sorting']['direction'] ?? 'desc';

                // Load feature settings
                $features = $indexSettings['features'] ?? [];
                $this->enableSearch = $features['enableSearch'] ?? true;
                $this->enableBulkActions = $features['enableBulkActions'] ?? true;
                $this->enablePagination = $features['enablePagination'] ?? true;
                $this->enableStatusToggle = $features['enableStatusToggle'] ?? true;

                // Load visible columns
                $this->visibleColumns = [];
                $columns = $indexSettings['table']['columns'] ?? [];
                foreach ($columns as $key => $column) {
                    if ($column['visible'] ?? false) {
                        $this->visibleColumns[] = $key;
                    }
                }

                // If no visible columns found, set defaults
                if (empty($this->visibleColumns)) {
                    $this->visibleColumns = ['checkbox', 'id', 'title', 'description', 'enable', 'created_at', 'actions'];
                }
            } else {
                // Set default values if JSON doesn't exist
                $this->setDefaults();
            }
        } catch (\Exception $e) {
            // Set defaults on error
            $this->setDefaults();
        }
    }

    private function setDefaults()
    {
        $this->perPage = 10;
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->enableSearch = true;
        $this->enableBulkActions = true;
        $this->enablePagination = true;
        $this->enableStatusToggle = true;
        $this->visibleColumns = ['checkbox', 'id', 'title', 'description', 'enable', 'created_at', 'actions'];
    }

    public function open()
    {
        //dd("open settings2");
        $this->loadSettings(); // Reload settings when opening
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function save()
    {
        // Update settings in the JSON
        $this->settings['index']['pagination']['perPage'] = $this->perPage;
        $this->settings['index']['sorting']['default'] = $this->sortField;
        $this->settings['index']['sorting']['direction'] = $this->sortDirection;

        // Update features
        $this->settings['index']['features']['enableSearch'] = $this->enableSearch;
        $this->settings['index']['features']['enableBulkActions'] = $this->enableBulkActions;
        $this->settings['index']['features']['enablePagination'] = $this->enablePagination;
        $this->settings['index']['features']['enableStatusToggle'] = $this->enableStatusToggle;

        // Update visible columns
        foreach ($this->settings['index']['table']['columns'] as $key => &$column) {
            $column['visible'] = in_array($key, $this->visibleColumns);
        }

        // Save to file
        File::put($this->jsonPath, json_encode($this->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->dispatch('settingsUpdated');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Table settings updated successfully!'
        ]);

        $this->close();
    }

    public function resetToDefaults()
    {
        $this->setDefaults();
    }

    public function render()
    {
        return view('jiny-admin2::livewire.admin.admin-templates.settings.table-settings-drawer');
    }
}
