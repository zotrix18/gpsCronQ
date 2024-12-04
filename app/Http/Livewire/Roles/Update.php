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

class Update extends Component
{
    public $title = 'Actualizar Rol';
    public $subtitle = 'Actualización';
    public RolesForm $form;

    public function mount($id)
    {
        $rol = Role::findOrFail($id);
        $this->form->setRol($rol);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('message', 'Rol actualizado con éxito.');
    }

    public function render()
    {
        return view('livewire.roles.create', [
            'title' => $this->title,
            'subtitle' => $this->subtitle
        ]);
    }
}
