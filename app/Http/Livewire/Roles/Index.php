<?php

namespace App\Http\Livewire\Roles;

use Livewire\Component;
use Livewire\WithPagination;

use Auth;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use App\Models\Model_has_role;
use App\Models\Log;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $view = 'roles';
    public $slug = 'roles';
    public $route = 'roles';
    public $title = "Lista de roles";
    public $querySearch = '';

    private function permisos($permiso)
    {
        $user = Auth::user();
        // if ($permiso != null && !$user->hasPermissionTo($permiso)) {
        //     abort(401);
        // }
    }

    public function render()
    {
        $roles = Role::where('name', 'LIKE', '%' . $this->querySearch . '%')
                    ->orderBy('id', 'desc')
                    ->paginate(10);

        return view('livewire.roles.index', [
            'title' => $this->title,
            'roles' => $roles
        ]);
    }
}
