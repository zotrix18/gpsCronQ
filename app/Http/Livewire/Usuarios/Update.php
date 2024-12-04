<?php

namespace App\Http\Livewire\Usuarios;
use App\Livewire\Forms\UsuarioForm;
use Livewire\Component;
use App\Models\User;

class Update extends Component
{

  public UsuarioForm $form;
  public $title = "Actualizar usuario";


  public function mount($id)
  {
    $user = User::findOrFail($id);
    $this->form->setUser($user);
  }


  public function save()
  {
    // Almacenar el usuario en la base de datos
    $this->form->update();

    // Mensaje de éxito
    session()->flash('message', 'Usuario actualizado con éxito.');
  }



  public function render()
  {
    return view('livewire.usuarios.form');
  }
}
