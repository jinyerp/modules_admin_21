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
        
        return redirect()->route('admin2.templates.index');
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
            return redirect()->route('admin2.templates.index');
        }
    }

    public function render()
    {
        return view('jiny-admin2::livewire.admin.admin-templates.admin-template-edit');
    }
}