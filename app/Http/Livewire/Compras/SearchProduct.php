<?php

namespace App\Http\Livewire\Compras;

use App\Models\Producto;
use Livewire\Component;

class SearchProduct extends Component
{
  public $query = '';
  public $products = [];
  public $highlightIndex = 0;
  public $showDropdown = false;

  protected $listeners = ['clearSearch' => 'clear'];

  public function updatedQuery()
  {
    $this->products = [];

    if (strlen($this->query) >= 2) {
      $this->products = Producto::where('producto', 'like', '%' . $this->query . '%')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get();
      $this->showDropdown = true;
    } else {
      $this->showDropdown = false;
    }

    $this->highlightIndex = 0;
  }

  public function selectProduct($productId)
  {
    // Emite el evento al componente padre
    $this->dispatch('productSelected', $productId);
    $this->clear();
  }

  public function clear()
  {
    $this->query = '';
    $this->products = [];
    $this->highlightIndex = 0;
    $this->showDropdown = false;
  }

  public function incrementHighlight()
  {
    if ($this->highlightIndex === count($this->products) - 1) {
      $this->highlightIndex = 0;
      return;
    }
    $this->highlightIndex++;
  }

  public function decrementHighlight()
  {
    if ($this->highlightIndex === 0) {
      $this->highlightIndex = count($this->products) - 1;
      return;
    }
    $this->highlightIndex--;
  }

  public function enterProduct()
  {
    if (count($this->products) > 0 && isset($this->products[$this->highlightIndex])) {
      $this->selectProduct($this->products[$this->highlightIndex]->id);
    }
  }

  public function render()
  {
    return view('livewire.compras.search-product');
  }
}