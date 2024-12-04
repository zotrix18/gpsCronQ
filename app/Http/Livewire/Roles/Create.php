<?php

namespace App\Http\Livewire\Roles;

use Livewire\Component;
use App\Livewire\Forms\RolesForm;

use Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use App\Models\Model_has_role;
use App\Models\Log;

class Create extends Component
{
    public $view = 'roles';
    public $slug = 'roles';
    public $route = 'roles';
    public $title = "Crear Rol";
    public $subtitle = "Creación";
    public RolesForm $form;

    public function save() {
        $this->form->store();

        session()->flash('message', 'Rol creado con éxito.');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.roles.create', [
            'title' => $this->title,
            'subtitle' => $this->subtitle
        ]);
    }
}
