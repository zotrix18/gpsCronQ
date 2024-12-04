<?php

namespace App\Http\Livewire\Compras;

use App\Models\Contacto;
use App\Models\Deposito;
use App\Models\Producto;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\ProductoStock;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $title = "Crear orden de compra";
    public $selectedProductos = [];
    public $compra = [
        'numero' => '',
        'contactos_id' => '',
        'estado' => '',
        'observaciones' => '',
    ];
    public $importe_subtotal = 0;
    public $importe_iva = 0;
    public $importe_descuento = 0;
    public $importe_total = 0;

    public $depositos = [];

    // Agrega este listener
    protected $listeners = ['productSelected' => 'addProduct'];

    protected $rules = [
        'compra.numero' => 'required|unique:compras,numero',
        'compra.contactos_id' => 'required|exists:contactos,id',
        'compra.estado' => 'required|in:abierta,cerrada,anulada',
        'selectedProductos' => 'required|array|min:1',
    ];

    public function render()
    {
        $contactos = Contacto::orderBy("id", "desc")->get();
        $this->depositos = Deposito::orderBy("id", "desc")->get();

        return view(
            'livewire.compras.create',
            [
                'contactos' => $contactos,
                'depositos' => $this->depositos
            ]
        );
    }

    public function addProduct($productId)
    {
        $product = Producto::find($productId);

        if ($product) {
            // Buscar si el producto ya está en el array selectedProductos
            $existingProductIndex = collect($this->selectedProductos)->search(fn($selectedProduct) => $selectedProduct['id'] == $productId);

            if ($existingProductIndex !== false) {
                $currentProduct = $this->selectedProductos[$existingProductIndex];
                // Si ya existe el producto, aumentar la cantidad
                $currentProduct['cantidad']++;
                $currentProduct['importe_subtotal'] = $currentProduct['precio'] * $currentProduct['cantidad'];
                $currentProduct['importe_iva'] = $currentProduct['importe_subtotal'] * ($currentProduct['ivaporcentaje'] / 100);
                $currentProduct['importe_total'] = $currentProduct['importe_subtotal'] + $currentProduct['importe_iva'];

                $this->selectedProductos[$existingProductIndex] = $currentProduct;
            } else {
                // Si no existe, agregarlo como un nuevo producto
                $this->selectedProductos[] = [
                    'id' => $product->id,
                    'producto' => $product->producto,
                    'cantidad' => 1,
                    'precio' => $product->precio,
                    'importe_subtotal' => $product->precio,
                    'ivaporcentaje' => 21, // Asumiendo un IVA del 21%
                    'importe_iva' => $product->precio * 0.21,
                    'importe_total' => $product->precio * 1.21,
                    'depositos_id' => $this->depositos[0]->id ?? null,
                    'porcentaje_descuento' => 0,
                    'importe_descuento' => 0
                ];
            }

            // Actualizar los totales
            $this->updateTotals();
        }
    }


    public function removeProduct($index)
    {
        unset($this->selectedProductos[$index]);
        $this->selectedProductos = array_values($this->selectedProductos);
        $this->updateTotals();
    }

    public function updateQuantity($index)
    {
        $producto = $this->selectedProductos[$index];

        $quantity = $producto['cantidad'];
        $porcDisc = $producto['porcentaje_descuento'] / 100;
        $importe_subtotal = $producto['precio'] * $quantity;
        $importe_descuento = $importe_subtotal * $porcDisc;

        // actualizando datos
        $this->selectedProductos[$index]['importe_descuento'] = $importe_descuento;
        $this->selectedProductos[$index]['importe_subtotal'] = $importe_subtotal - $importe_descuento;
        $this->selectedProductos[$index]['importe_iva'] = $importe_subtotal * ($this->selectedProductos[$index]['ivaporcentaje'] / 100);
        $this->selectedProductos[$index]['importe_total'] = $this->selectedProductos[$index]['importe_subtotal'] + $this->selectedProductos[$index]['importe_iva'];
        $this->updateTotals();
    }

    public function updateTotals()
    {
        $this->importe_subtotal = collect($this->selectedProductos)->sum('importe_subtotal');
        $this->importe_iva = collect($this->selectedProductos)->sum('importe_iva');
        $this->importe_total = collect($this->selectedProductos)->sum('importe_total');
    }

    public function save()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $compra = Compra::create([
                    'numero' => $this->compra['numero'],
                    'contactos_id' => $this->compra['contactos_id'],
                    'importe_subtotal' => $this->importe_subtotal,
                    'importe_iva' => $this->importe_iva,
                    'importe_descuento' => $this->importe_descuento,
                    'importe_total' => $this->importe_total,
                    'estado' => $this->compra['estado'],
                    'observaciones' => $this->compra['observaciones'] ?? null,
                ]);

                foreach ($this->selectedProductos as $producto) {
                    CompraDetalle::create([
                        'compras_id' => $compra->id,
                        'depositos_id' => $producto['depositos_id'],
                        'productos_id' => $producto['id'],
                        'productostocks_id' => null, // Asume que no se maneja stock por ahora
                        'cantidad' => $producto['cantidad'],
                        'producto' => $producto['producto'],
                        'importe_subtotal' => $producto['importe_subtotal'],
                        'ivaporcentaje' => $producto['ivaporcentaje'],
                        'importe_iva' => $producto['importe_iva'],
                        'importe_descuento' => 0, // Asume que no hay descuento por línea
                        'importe_total' => $producto['importe_total'],
                    ]);


                    ProductoStock::create(
                        [
                            'productos_id' => $producto['id'],
                            'depositos_id' => $producto['depositos_id'],
                            'cantidad' => $producto['cantidad'],
                        ]
                    );
                }
            });

            session()->flash('message', 'Compra creada exitosamente.');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la compra: ' . $e->getMessage());
        }
    }
}