<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates;

use Livewire\Component;
use Jiny\Admin2\App\Models\AdminTemplate;

class AdminTemplateEdit extends Component
{
    public AdminTemplate $template;
    public $item; // Alias for template
    public $enable;
    public $title;
    public $description;
    public $jsonData;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'enable' => 'boolean',
    ];
    
    protected $listeners = [
        'deleteConfirmed' => 'delete'
    ];

    public function mount(AdminTemplate $template)
    {
        $this->template = $template;
        $this->item = $template; // Set alias
        $this->enable = $template->enable;
        $this->title = $template->title;
        $this->description = $template->description;
        
        // JSON 데이터 로드
        $jsonPath = dirname(dirname(dirname(dirname(__DIR__)))) . '/Http/Controllers/Admin/AdminTemplates/AdminTemplates.json';
        if (file_exists($jsonPath)) {
            $this->jsonData = json_decode(file_get_contents($jsonPath), true);
        }
    }

    public function update()
    {
        $this->validate();

        $this->template->update([
            'enable' => $this->enable,
            'title' => $this->title,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Template updated successfully.');
        
        $routeName = isset($this->jsonData['route']) 
            ? $this->jsonData['route'] . '.index'
            : 'admin2.templates.index';
        return redirect()->route($routeName);
    }

    public function openSettings()
    {
        $this->dispatch('openEditSettings');
    }
    
    public function confirmDelete()
    {
        $this->dispatch('confirmDelete', $this->template->id, $this->template->title, 'template', 'deleteConfirmed', true);
    }
    
    public function delete($id)
    {
        if ($this->template->id == $id) {
            $this->template->delete();
            session()->flash('message', 'Template deleted successfully.');
            // Notify the delete confirmation that deletion is complete
            $this->dispatch('deleteCompleted');
            
            $routeName = isset($this->jsonData['route']) 
                ? $this->jsonData['route'] . '.index'
                : 'admin2.templates.index';
            return redirect()->route($routeName);
        }
    }

    public function render()
    {
        return view('jiny-admin2::__admin.admin-templates.admin-template-edit');
    }
}