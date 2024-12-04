<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Validation\Rule;

use Spatie\Permission\Models\Role;

class RolesForm extends Form
{
    public ?Role $rol;

    #[Validate ('required|unique:roles,name|max:254')] 
    public $name = '';

    public function setRol(Role $rol) {
        $this->rol = $rol;

        $this->name = $this->rol->name;
    }


    public function store() {
        $this->validate();

        $role = Role::create([
            'name' => $this->name,
        ]);
    }

    public function update() {
        $this->validate([
            'name' => [
                'required',
                'max:254',
                Rule::unique('roles', 'name')->ignore($this->rol->id),
            ]
        ]);

        $role = Role::find($this->rol->id);
        $role->name = $this->name;

        $role->update();
    }
}
