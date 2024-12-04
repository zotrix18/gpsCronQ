<?php

namespace App\Http\Livewire\MedioPago;

use App\Models\MedioPago;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
  public $title = "Listado de medios de pago";
  public $querySearch = '';

  public function updatingQuerySearch()
  {
    $this->resetPage();
  }

  public function showConfirmar($id)
  {
    $this->dispatch('showConfirmar', id: $id);
  }

  #[On('toggleActive')]
  public function toggleActive($id)
  {
    $medio = MedioPago::findOrFail($id);
    if ($medio) {
      $medio->activo = !$medio->activo;
      $medio->save();
      $this->dispatch('successAlert');
    }
  }

  public function render()
  {
    $mediospago = MedioPago::where('nombre', 'like', '%' . $this->querySearch . '%')
      ->orderByDesc('id')
      ->paginate(10);

    return view('livewire.mediopago.index', ["mediospago" => $mediospago]);
  }
}