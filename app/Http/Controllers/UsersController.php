<?php

namespace App\Http\Controllers;
use Auth;
use DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public $view = 'users';
    public $slug = 'users';
    public $route = 'users';

    private function permisos($permiso)
    {
        $user = Auth::user();
        if ($permiso != null && !$user->hasPermissionTo($permiso)) {
            abort(401);
        }
    }

    public function index(Request $request)
    {
        $this->permisos($this->slug . '.index');


        if ($request->buscar) {
            $title = "Usuario: buscando " . $request->buscar;
            $users = User::where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->buscar . '%')
                    ->Orwhere('email', 'LIKE', '%' . $request->buscar . '%');
            })->paginate(15);
        } else {
            $title = "Lista de usuarios";
            $users = User::paginate(15);
        }

        $links = true;
        return view($this->view . '.index', compact('users', 'title', 'links'));
    }

    public function logs($id)
    {
        $this->permisos($this->slug . '.logs');

        $title = "Lista de usuarios";
        $users = User::find($id);
        $logs = Log::where('users_id', $id)->orderBy('created_at', 'desc')->paginate(15);

        return view($this->view . '.logs', compact('users', 'title', 'logs'));
    }


    public function create()
    {
        //$this->permisos($this->slug.'.create');

        return view($this->view . '.create');
    }

    public function store(Request $request)
    {
        $this->permisos($this->slug . '.create');

        $validation = $request->validateWithBag('post', [
            'name' => 'required|max:254',
            'email' => [
                'required',
                'max:254',
                Rule::unique('users')->whereNull('deleted_at'),
            ],
            'rol' => 'required|exists:roles,id',
            'password' => 'required|max:254',
        ]);

        $activo = 1;
        if ($request->activo == "") {
            $activo = 0;
        }
        ;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'activo' => $activo
        ]);

        if ($request->rol) {
            $user->assignRole(Role::find($request->rol)->name);
        }

        Log::info('Creó el usuario: ' . json_encode($user->toArray()));

        return redirect(route($this->view . '.index'))->with('success', 'El Usuario ' . $request->name . ' ha sido agregado correctamente');
    }

    public function show($id)
    {
        $this->permisos($this->slug . '.show');

        $route = $this->route;
        $usuario = User::find($id);
        $id = $usuario->id;
        return view($this->view . '.show', compact('id', 'usuario', 'route'));
    }

    public function edit($id)
    {
        $this->permisos($this->slug . '.edit');

        $usuario = User::find($id);
        return view($this->view . '.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $this->permisos($this->slug . '.create');

        $validation = $request->validateWithBag('post', [
            'name' => 'required|max:254',
            'email' => [
                'required',
                'max:254',
                Rule::unique('users')->ignore($id)->whereNull('deleted_at'),
            ],
            'rol' => 'required|exists:roles,id',
        ]);

        $activo = 1;
        if ($request->activo == "") {
            $activo = 0;
        }
        ;

        $usuario = User::find($id);

        $role = $usuario->roles[0]->name;

        if ($request->password != null) {
            $user = User::where('id', $id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'activo' => $activo,
            ]);
        } else {
            $user = User::where('id', $id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'activo' => $activo,
            ]);
        }

        if ($request->rol) {
            $usuario->removeRole($role);
            $user->assignRole(Role::find($request->rol)->name);
        }

        Log::info('Editó el usuario: ' . json_encode(User::find($id)->toArray()));

        return redirect(route($this->view . '.index'))->with('success', 'El Usuario ' . $usuario->name . ' ha sido actualizado correctamente');
    }

    public function destroy($id)
    {
        $this->permisos($this->slug . '.destroy');
        $user = User::find($id);
        $name = $user->name;

        try {
            $user = User::where('id', $id)->delete();

            Log::info('Eliminó el usuario: ' . $name);
            return redirect(route('users.index'))->with('success', 'El usuario ' . $name . ' ha sido eliminado correctamente');
        } catch (\Throwable $th) {
            return redirect(route('users.index'))->with('error', 'El usuario ' . $name . ' no se puede eliminar porque existen dependencias');
        }
    }

    public function search(Request $request)
    {
        $term = $request->term;

        $datos = User::where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('email', 'LIKE', '%' . $term . '%')
            ->where('activo', 1)->get();

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

        $buscar = $request->buscar;
        $users = User::where(function ($query) use ($buscar) {
            $query->where('name', 'LIKE', '%' . $buscar . '%')
                ->Orwhere('email', 'LIKE', '%' . $buscar . '%');
        })->get();
        $links = false;
        $title = "Usuario: buscando " . $request->buscar;
        return view($this->view . '.index', compact('users', 'title', 'links'));
    }

    function roles($id)
    {

        $this->permisos($this->slug . '.index');

        $users = User::find($id);

        $title = "Roles del usuario: " . $users->name;

        return view($this->view . '.roles', compact('users', 'title'));
    }

    function rolesadd($id, Request $request)
    {

        $this->permisos($this->slug . '.index');

        $users = User::find($id);

        $users->assignRole($request->role);

        return redirect()->back()->with('success', 'Rol asignado correctamente');
    }

    function rolesremove($id, $role)
    {

        $this->permisos($this->slug . '.index');

        $users = User::find($id);

        $users->removeRole($role);

        return redirect()->back()->with('success', 'Rol quitado correctamente');
    }

}