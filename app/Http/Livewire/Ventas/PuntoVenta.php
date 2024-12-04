<?php

namespace App\Http\Livewire\Ventas;

use App\Models\CategoriaProducto;
use App\Models\Contacto;
use App\Models\Listaprecio;
use App\Models\Producto;
use App\Models\ProductoStock;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Auth;
use DB;
use Livewire\Component;

class PuntoVenta extends Component
{

    public $querySearch = ''; // Propiedad para almacenar la consulta de búsqueda

    public $filterCategory = 0;

    public $cart = [];
    public $total = 0;
    public $subtotal = 0;
    public $iva = 0;
    public $cantidadTotal = 0;

    public $clienteId = 18;

    public $listaPrecioId = 0;

    public $talonario = null;


    // Agregar producto al carrito
    public function addToCart(Producto $producto)
    {
        // Si el producto ya existe en el carrito, incrementar cantidad
        if (isset($this->cart[$producto->id])) {
            $this->cart[$producto->id]['cantidad']++;
        } else {
            // Si no existe, agregarlo con cantidad 1
            $this->cart[$producto->id] = [
                'id' => $producto->id,
                'nombre' => $producto->producto,
                'precio' => $producto->precio,
                'cantidad' => 1
            ];
        }

        $this->calculateTotals();
    }

    public function setFilterCategory($categoryId)
    {
        $this->filterCategory = $categoryId;
    }

    // Actualiza cantidad de un producto
    public function updateQuantity($productId, $quantity)
    {
        $quantity = intval($quantity);
        if ($quantity > 0) {
            $this->cart[$productId]['cantidad'] = $quantity;
        } else {
            unset($this->cart[$productId]);
        }
        $this->calculateTotals();
    }


    // Decrementar cantidad de un producto
    public function decrementQuantity($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['cantidad'] > 1) {
                $this->cart[$productId]['cantidad']--;
            } else {
                unset($this->cart[$productId]);
            }
            $this->calculateTotals();
        }
    }

    // Calcular totales
    private function calculateTotals()
    {
        $this->subtotal = 0;
        $this->cantidadTotal = 0;

        foreach ($this->cart as $item) {
            $this->subtotal += $item['precio'] * $item['cantidad'];
            $this->cantidadTotal += $item['cantidad'];
        }

        $this->iva = $this->subtotal * 0.21;
        $this->total = $this->subtotal + $this->iva;
    }

    // Limpiar carrito
    public function clearCart()
    {
        $this->cart = [];
        $this->calculateTotals();
    }

    // Registrar la venta y los detalles
    public function registrarVenta()
    {
        try {
            DB::transaction(function () {
                // Crear la venta principal
                $venta = Venta::create([
                    'numero' => '1223344',
                    'contactos_id' => $this->clienteId,
                    'importe_total' => $this->total,
                    'importe_apagar' => $this->total,
                    'estado' => 'Pendiente',
                    'fecha' => now(),
                    'voucher_number' => '00000234',
                    'email_enviado' => 0,
                    'listasprecios_id' => $this->listaPrecioId,
                    'puntosventas_id' => $this->getPuntoVenta()->id,
                    'users_id' => Auth::user()->id
                ]);

                // Registrar los detalles de la venta
                foreach ($this->cart as $producto) {
                    VentaDetalle::create([
                        'ventas_id' => $venta->id,
                        'productos_id' => $producto['id'],
                        'depositos_id' => 1,
                        'cantidad' => $producto['cantidad'],
                        'precio_unitario' => $producto['precio'],
                        'importe_subtotal' => $producto['precio'] * $producto['cantidad'],
                        'porcentaje_iva' => 21, // Asumiendo 21% de IVA por defecto
                        'importe_iva' => $producto['precio'] * $producto['cantidad'] * 0.21,
                        'subtotal' => $producto['precio'] * $producto['cantidad'], // Antes del IVA
                        'total' => $producto['precio'] * $producto['cantidad'] * 1.21, // Con IVA
                    ]);
                }
            });

            $this->dispatch('successAlert', 'Venta registrada exitosamente');
            $this->clearCart();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }

    private function getPuntoVenta(): \App\Models\PuntoVenta
    {
        $puntosVenta = \App\Models\PuntoVenta::with('talonarios.comprobante')
            ->where('empresas_id', session('empresa')->id)
            ->first('*');
        return $puntosVenta;
    }

    private function setTalonario()
    {
        $puntoVenta = $this->getPuntoVenta();
        $responsabilidad = session('empresa')->responsabilidad->ivasresponsabilidad;

        if (in_array($responsabilidad, ['Monotributista', 'Excento/No Responsable'])) {
            // Buscar talonario directamente en la relación (si es posible)
            $talonario = $puntoVenta->talonarios()
                ->whereHas('comprobante', function ($query) {
                    $query->where('comprobantestipos', 'Factura B');
                })
                ->first();

            // Asignar talonario si se encuentra
            if ($talonario) {
                $this->talonario = $talonario;
            }
        }
    }




    public function render()
    {
        $productos = Producto::when($this->querySearch, function ($query) {
            $query->where(function ($q) {
                $q->where('producto', 'like', '%' . $this->querySearch . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->querySearch . '%');
            });
        })
            ->when($this->filterCategory > 0, function ($query) {
                return $query->where('categoriasproductos_id', $this->filterCategory);
            })
            ->where('empresas_id', session('empresa')->id)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $categorias = CategoriaProducto::where('empresas_id', session('empresa')->id)
            ->limit(5)->get();
        $clientes = Contacto::all();
        $listaPrecios = Listaprecio::all();

        if ($this->listaPrecioId == 0 && count($listaPrecios) > 0) {
            $this->listaPrecioId = $listaPrecios[0]->id;
        }

        $this->setTalonario();


        return view('livewire.ventas.puntoventa', [
            'productos' => $productos,
            'categorias' => $categorias,
            'clientes' => $clientes,
            'listaPrecios' => $listaPrecios
        ]);
    }
}
