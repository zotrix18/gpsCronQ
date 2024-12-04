<?php

namespace App\Http\Livewire\Ventas;

use App\Models\Contacto;
use Livewire\Component;

class SelectContact extends Component
{
  public $query = '';
  public $contacts = [];
  public $highlightIndex = 0;
  public $showDropdown = false;

  protected $listeners = ['clearSearch' => 'clear'];

  public function updatedQuery()
  {
    $this->contacts = [];

    if (strlen($this->query) >= 2) {
      $this->contacts = Contacto::where('contacto', 'like', '%' . $this->query . '%')
        ->orWhere('razon_social', 'like', '%' . $this->query . '%')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get();
      $this->showDropdown = true;
    } else {
      $this->showDropdown = false;
    }

    $this->highlightIndex = 0;
  }

  public function selectContact($contactId)
  {
    // Emite el evento al componente padre
    $this->dispatch('contactselected', $contactId);
    $this->clear();
  }

  public function clear()
  {
    $this->query = $this->contacts[$this->highlightIndex]->contacto;
    $this->contacts = [];
    $this->highlightIndex = 0;
    $this->showDropdown = false;
  }

  public function incrementHighlight()
  {
    if ($this->highlightIndex === count($this->contacts) - 1) {
      $this->highlightIndex = 0;
      return;
    }
    $this->highlightIndex++;
  }

  public function decrementHighlight()
  {
    if ($this->highlightIndex === 0) {
      $this->highlightIndex = count($this->contacts) - 1;
      return;
    }
    $this->highlightIndex--;
  }

  public function enterContact()
  {

    if (count($this->contacts) > 0 && isset($this->contacts[$this->highlightIndex])) {
      $this->selectContact($this->contacts[$this->highlightIndex]->id);
    }
  }

  public function render()
  {
    return view('livewire.ventas.select-contact');
  }
}