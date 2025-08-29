<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates;

use Livewire\Component;
use Jiny\Admin2\App\Models\AdminTemplate;

class AdminTemplateCreate extends Component
{
    public $enable = true;
    public $title = '';
    public $description = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'enable' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        AdminTemplate::create([
            'enable' => $this->enable,
            'title' => $this->title,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Template created successfully.');
        
        return redirect()->route('admin2.templates.index');
    }

    public function render()
    {
        return view('jiny-admin2::livewire.admin.admin-templates.admin-template-create');
    }
}