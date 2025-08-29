<?php

namespace Jiny\Admin2\App\Http\Livewire\Admin\AdminTemplates;

use Livewire\Component;
use Jiny\Admin2\App\Models\AdminTemplate;

class AdminTemplateShow extends Component
{
    public AdminTemplate $template;
    public $item; // Alias for template
    
    protected $listeners = [
        'deleteConfirmed' => 'delete'
    ];

    public function mount(AdminTemplate $template)
    {
        $this->template = $template;
        $this->item = $template; // Set alias
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

    public function openSettings()
    {
        $this->dispatch('openDetailSettings');
    }

    public function render()
    {
        return view('jiny-admin2::livewire.admin.admin-templates.admin-template-show');
    }
}