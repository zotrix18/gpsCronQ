<?php

namespace App\Http\Livewire\Usuarios;
use App\Livewire\Forms\UsuarioForm;
use Livewire\Component;

class Create extends Component
{

    public UsuarioForm $form;
    public $title = "Crear usuario";


    public function save()
    {
        // Almacenar el usuario en la base de datos
        $this->form->store();

        // Mensaje de éxito
        session()->flash('message', 'Usuario creado con éxito.');
    }



    public function render()
    {
        return view('livewire.usuarios.form');
    }
}
