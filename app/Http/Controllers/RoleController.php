<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Model_has_role;
use App\Models\Log;

class RoleController extends Controller
{
    public $view = 'roles';
    public $slug = 'roles';
    public $route = 'roles';

    private function permisos($permiso)
    {
        $user = Auth::user();
        // if ($permiso != null && !$user->hasPermissionTo($permiso)) {
        //     abort(401);
        // }
    }

    public function index(Request $request)
    {
        $this->permisos($this->slug . '.index');


        if ($request->buscar) {
            $title = "Rol: buscando " . $request->buscar;
            $roles = Role::where('name', 'LIKE', '%' . $request->buscar . '%')->paginate(15);
        } else {
            $title = "Lista de roles";
            $roles = Role::paginate(15);
        }

        $links = true;
        return view($this->view . '.index', compact('roles', 'title', 'links'));
    }


    public function create()
    {
        $this->permisos($this->slug . '.create');

        return view($this->view . '.create');
    }

    public function store(Request $request)
    {
        //$this->permisos($this->slug . '.create');

        $validated = $request->validateWithBag('post', [
            'name' => [
                'required',
                'max:255',
                Rule::unique('roles', 'name'),
            ],
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);

        Log::info('Creo rol: ' . json_encode($role->toArray()));

        return redirect(route('roles.index'))->with('success', 'El rol ' . $request->name . ' ha sido creado correctamente');
    }

    public function show($id)
    {
        $this->permisos($this->slug . '.show');

        $route = $this->route;
        $role = Role::find($id);
        $id = $role->id;
        return view($this->view . '.show', compact('id', 'role', 'route'));
    }

    public function edit($id)
    {
        $this->permisos($this->slug . '.edit');

        $role = Role::find($id);
        return view($this->view . '.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $this->permisos($this->slug . '.edit');

        $validated = $request->validateWithBag('post', [
            'name' => [
                'required',
                'max:255',
                Rule::unique('roles', 'name')->ignore($id),
            ],
        ]);

        $role = Role::find($id);
        $role->name = $request->name;
        $role->save();
        Log::info('Editó rol: ' . json_encode(Role::find($id)->toArray()));

        return redirect(route('roles.index'))->with('success', 'El rol ' . $request->name . ' ha sido modificado correctamente');
    }


    public function destroy($id)
    {
        $this->permisos($this->slug . '.destroy');
        $role = Role::find($id);
        $name = $role->name;
        $existe = Model_has_role::where('role_id', $id)->count();

        if ($existe == 0) {
            $role->delete();
            Log::info('Eliminó rol: ' . $name);

            return redirect(route('roles.index'))->with('success', 'El rol ' . $name . ' ha sido eliminado correctamente');
        } else {
            return redirect(route('roles.index'))->with('error', 'El rol ' . $name . ' no se puede eliminar porque existen dependencias');
        }
    }

    public function search(Request $request)
    {
        $term = $request->term;

        $datos = Role::where('name', 'like', '%' . $request->term . '%')->get();
        $adevol = array();
        if (count($datos) > 0) {
            foreach ($datos as $dato) {
                $adevol[] = array(
                    'id' => $dato->id,
                    'value' => $dato->name,
                );
            }
        } else {
            $adevol[] = array(
                'id' => 0,
                'value' => 'No hay coincidencias para ' . $term
            );
        }
        return json_encode($adevol);
    }

    public function finder(Request $request)
    {
        $this->permisos($this->slug . '.index');
        $roles = Role::where('name', 'LIKE', '%' . $request->buscar . '%')->get();
        $links = false;
        $title = "Rol: buscando " . $request->buscar;
        return view($this->view . '.index', compact('roles', 'title', 'links'));
    }
}