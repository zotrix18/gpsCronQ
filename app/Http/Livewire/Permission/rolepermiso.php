<?php

namespace App\Http\Livewire\Permission;

use App\Models\Log;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Rolepermiso extends Component
{

    public $roleId;
    public $role = [];
    public $permissions = [];
    public $title = "";

    public function render()
    {

        $this->role = Role::find($this->roleId);
        $this->permissions = Permission::orderBy('descripcion', 'asc')->orderBy('name', 'asc')->get();
        $this->title = 'Permisos del rol: '.$this->role->name;

        return view('livewire.permission.rolepermiso');
    }

    public function changePermiso($permissions_id) {
        
        $permissions = Permission::find($permissions_id);
        
        $role = Role::find($this->roleId);
        
        if($role->hasPermissionTo($permissions->name)){
            $role->revokePermissionTo($permissions->name);
            Log::info('Revocó permiso: '.$permissions->id.' al rol: '.$role->id);
        } else {
            $role->givePermissionTo($permissions->name);
            Log::info('Asignó permiso: '.$permissions->id.' al rol: '.$role->id);
        }
    }
}
