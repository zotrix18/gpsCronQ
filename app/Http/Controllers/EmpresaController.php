<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\IvaCategoria;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public $view = 'empresas';

    /**
     * Muestra todas las empresas.
     */
    public function index(Request $request)
    {
        $title = "Lista de empresas";

        if ($request->buscar) {
            $title = "Empresa: buscando " . $request->buscar;
            $empresas = Empresa::where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->buscar . '%')
                    ->Orwhere('email', 'LIKE', '%' . $request->buscar . '%');
            })->paginate(15);
        } else {
            $title = "Lista de empresas";
            $empresas = Empresa::orderByDesc('id')->paginate(10);
        }

        $links = true;
        return view($this->view . '.index', compact('empresas', 'title', 'links'));
    }

    public function create()
    {
        $ivascategorias = IvaCategoria::get();

        return view($this->view . '.create', compact('ivascategorias'));
    }


    /**
     * Almacena una nueva empresa.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa' => 'required|string|max:254',
            'cuit' => 'required|string|max:11|unique:empresas,cuit',
            'direccion' => 'required|string|max:254',
            'ciudad' => 'required|string|max:125',
            'ingresosbrutos' => 'nullable|string|max:254',
            'activo' => 'boolean',
            'empresa_id' => 'required|exists:empresa,id',
            'wee_key' => 'nullable|string|max:45',
            'ivasresponsabilidads_id' => 'required|exists:ivasresponsabilidades,id',
            'ivascategorias_id' => 'required|exists:ivascategorias,id',
        ]);

        $empresa = Empresa::create($validated);
        return response()->json($empresa, 201);
    }

    /**
     * Muestra una empresa específica.
     */
    public function show(Empresa $empresa)
    {
        return response()->json($empresa->load(['user', 'ivaResponsabilidad', 'ivaCategoria']));
    }

    /**
     * Actualiza una empresa.
     */
    public function update(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'empresa' => 'required|string|max:254',
            'cuit' => 'required|string|max:11|unique:empresas,cuit,' . $empresa->id,
            'direccion' => 'required|string|max:254',
            'ciudad' => 'required|string|max:125',
            'ingresosbrutos' => 'nullable|string|max:254',
            'activo' => 'boolean',
            'empresa_id' => 'required|exists:empresa,id',
            'wee_key' => 'nullable|string|max:45',
            'ivasresponsabilidads_id' => 'required|exists:ivasresponsabilidades,id',
            'ivascategorias_id' => 'required|exists:ivascategorias,id',
        ]);

        $empresa->update($validated);
        return response()->json($empresa);
    }

    /**
     * Elimina una empresa.
     */
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return response()->json(null, 204);
    }
}
