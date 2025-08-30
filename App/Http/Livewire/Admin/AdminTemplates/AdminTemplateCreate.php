<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates;

use Livewire\Component;
use Jiny\Admin2\App\Models\AdminTemplate;

class AdminTemplateCreate extends Component
{
    public $enable = true;
    public $title = '';
    public $description = '';
    public $jsonData;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'enable' => 'boolean',
    ];
    
    public function mount()
    {
        // JSON 데이터 로드
        $jsonPath = dirname(dirname(dirname(dirname(__DIR__)))) . '/Http/Controllers/Admin/AdminTemplates/AdminTemplates.json';
        if (file_exists($jsonPath)) {
            $this->jsonData = json_decode(file_get_contents($jsonPath), true);
        }
    }

    public function save()
    {
        $this->validate();

        AdminTemplate::create([
            'enable' => $this->enable,
            'title' => $this->title,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Template created successfully.');
        
        $routeName = isset($this->jsonData['route']) 
            ? $this->jsonData['route'] . '.index'
            : 'admin2.templates.index';
        return redirect()->route($routeName);
    }

    public function render()
    {
        return view('jiny-admin2::livewire.admin.admin-templates.admin-template-create');
    }
}