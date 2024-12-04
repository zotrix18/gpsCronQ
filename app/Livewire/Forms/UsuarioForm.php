<?php

namespace App\Livewire\Forms;

use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\User;

class UsuarioForm extends Form
{
    public ?User $usuario;

    #[Validate('required|string|max:254')]
    public $name = '';

    #[Validate('required|string|max:254')]
    public $user = '';

    #[Validate('required|email|max:254')]
    public $email = '';

    // #[Validate('required|exists:roles,id')]
    // public $rol = '';

    #[Validate('required|string|max:254')]
    public $password = '';

    #[Validate('boolean')]
    public $activo = false;

    #[Validate('string|max:20')]
    public $telefono = '';


    public function setUser(User $user)
    {
        $this->usuario = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->user = $user->user;
        $this->telefono = $user->telefono ?? '';
        //$this->password = $user->password;
        $this->activo = $user->activo;
    }


    public function store()
    {
        $this->activo = true;
        $this->user = $this->email;

        if (strlen($this->password) > 0) {
            $this->password = Hash::make($this->password);
        }
        $this->validate();
        User::create($this->all());

        $this->reset();
    }

    public function update()
    {
        // Validamos todos los campos excepto la contraseña
        $this->validate([
            'name' => 'required|string|max:254',
            'email' => 'required|email|max:254',
            'telefono' => 'nullable|string|max:20',
        ]);

        $this->usuario->update(
            $this->only(['name', 'email', 'telefono'])
        );


    }
}
