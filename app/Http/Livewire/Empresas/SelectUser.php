<?php

namespace App\Http\Livewire\Empresas;

use App\Models\Empresa;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SelectUser extends Component
{
  use WithPagination;
  protected $paginationTheme = 'bootstrap';

  public $title;
  public $querySearch = ''; // Propiedad para almacenar la consulta de búsqueda
  public $empresa_id;

  public function mount($id)
  {
    $this->title = "Listado de usuarios";
    $this->empresa_id = $id;
  }

  public function updatingQuerySearch()
  {
    // Reinicia la paginación cada vez que cambia el término de búsqueda
    $this->resetPage();
  }

  #[On('assignUsers')]
  public function assignUsers($selectedUsers)
  {
    // Obtener la empresa
    $empresa = Empresa::find($this->empresa_id);

    // Asignar los usuarios seleccionados a la empresa
    $empresa->users()->syncWithoutDetaching($selectedUsers);

    // Mostrar una notificación de éxito y resetear selección
    $this->dispatch('successAlert', message: "Usuario asignado correctamente");
  }

  #[On('removeUser')]
  public function removeUser($userId)
  {
    $empresa = Empresa::find($this->empresa_id);
    $empresa->users()->detach($userId);
    $this->dispatch('successAlert', message: 'Usuario removido correctamente');
  }

  public function showConfirm($id)
  {
    $this->dispatch('showConfirm', id: $id);
  }

  public function render()
  {
    $empresa = Empresa::where("id", $this->empresa_id)
      ->with("users")->first();



    // Obtener los usuarios que NO están asociados a esta empresa
    $users = User::whereDoesntHave('empresas', function ($query) {
      $query->where('empresas.id', $this->empresa_id);
    })
      ->orderBy("id", "desc")
      ->limit(5)
      ->get();


    return view('livewire.empresas.select-user', [
      'empresa' => $empresa,
      'users' => $users,
    ]);
  }
}
