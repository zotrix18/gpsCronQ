<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    // UsersController,
    RoleController,
    // PermissionController,
    // ConfiguracionesController,
    // ChatbotController,


    // CategoriasController,
    // EmpresaController,
    // ProductosController,

    // TurnosController,
    // VacunasController,
    // VacunatoriosController,
    // ObrasocialsController,

    // ConfiguracionsController,
    // CiudadanosController,
    // CiudadanosExcluidoController,
    // CiudadsController,
    // ContactosController,
    // InformeController,
    // PdfController,
    // TurnosciudadanosController,
    // UsersvacunatoriosController
};

use App\Http\Livewire\{
    Dashboard,

    // // Compras
    // Compras\Index as ComprasIndex,
    // Compras\Create as ComprasCreate,
    // Compras\Detalle as ComprasDetalle,

    // //Ventas
    // Ventas\PuntoVenta as VentasPV,

    // Empresas
    Empresas\Index as EmpresasIndex,
    Empresas\Create as EmpresasCreate,
    Empresas\Update as EmpresasUpdate,
    Empresas\SelectUser as EmpresasSelectUser,
    Empresas\Show as EmpresasShow,

    // Usuarios
    Usuarios\Index as UsuariosIndex,
    Usuarios\Create as UsuariosCrate,
    Usuarios\Update as UsuariosUpdate,

    // // Productos
    // Productos\Update as ProductosUpdate,
    // Productos\Index as ProductosIndex,
    // Productos\Create as ProductosCreate,

    // // Productos stock
    // ProductosStocks\Create as ProductosStocksCreate,

    // // Depositos
    // Depositos\Index as DepositosIndex,
    // Depositos\Form as DepositosForm,


    // // Categorias
    // Categorias\Index as CategoriasIndex,
    // Categorias\Create as CategoriasCreate,
    // Categorias\Update as CategoriasUpdate,

    Roles\Index as RolesIndex,
    Roles\Create as RolesCreate,
    Roles\Update as RolesUpdate,

    // // Iva
    // IvaCategoria\Index as IvaCategoriaIndex,
    // IvaCategoria\Form as IvaCategoriaForm,

    // // Medio pago
    // MedioPago\Index as MedioPagoIndex,
    // MedioPago\Form as MedioPagoForm,

    // //Empresa-medios pago
    // EmpresasMediosPago\Index as EmpresasMediosPagoIndex,
    // EmpresasMediosPago\Create as EmpresasMediosPagoCreate,

    // //Chatbot
    // Chatbot\create as ChatBotCreate,
    // Chatbot\Assistant as ChatBotIndex,
    // Chatbot\Functions as ChatBotFunction,
    // Chatbot\Files as ChatBotFiles,
    // EmpresasMediosPago\Update as EmpresasMediosPagoUpdate,

    // // Ventas
    // Ventas\Index as VentasIndex,

    // // Puntos de venta
    // Puntosventa\Index as PuntosVentaIndex,
    // Puntosventa\Create as PuntosVentaCreate,
    // Puntosventa\Update as PuntosVentaUpdate
};


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('livewire.index');
});*/


Auth::routes();

/* 
|-----------------------------------------------------------------------------------------
| Creados
|Route::view('/categorias', '/categorias/index')->name('categorias.index');
|-----------------------------------------------------------------------------------------
*/


Route::get('/users', function () {
    return view('users.index');
})->name('users.index');

Route::get('/terminos', function () {
    return view('terminos');
})->name('terminos');

Route::middleware(['auth'])->group(function () {
    //Dashboard
    Route::get('/', Dashboard::class)
        ->middleware('check.empresa')
        ->name('home');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Confifuracion 
    Route::prefix('/configuraciones')->group(function () {
        // Usuarios
        Route::get('/usuarios', UsuariosIndex::class)->name('conf.usuarios.index');
        Route::get('/usuarios/formulario', UsuariosCrate::class)->name('conf.usuarios.create');
        Route::get('/usuarios/formulario/{id}', UsuariosUpdate::class)->name('conf.usuarios.update');

        //Empresas
        Route::get('/empresas', EmpresasIndex::class)->name('empresas.index');
        Route::get('/empresas/{id}/usuarios', EmpresasSelectUser::class)->name('empresas.users');
        Route::get('/empresas/create', EmpresasCreate::class)->name('empresas.create');
        Route::get('/empresas/{id}/update', EmpresasUpdate::class)->name('empresas.update');
        Route::get('/empresas/{id}/ver', EmpresasShow::class)->name('empresas.show');

        //Roles
        Route::get('/roles', [RoleController::class, 'search'])->name('roles.index');
        Route::get('/roles/search', [RoleController::class, 'search'])->name('roles.search');
        Route::post('/roles/finder', [RoleController::class, 'finder'])->name('roles.finder');
        Route::resource('/roles', RoleController::class);

        //Roles-Permissions
        Route::get('/permission/role/{id}', [PermissionController::class, 'role'])->name('permissions.role');
        Route::get('/permission/activate/{permissions_id}/{roles_id}', [PermissionController::class, 'activate'])->name('permissions.activate');
        Route::post('/permission/role/{id}/finder', [PermissionController::class, 'rolefinder'])->name('permissions.rolefinder');
    });   

    




});


require __DIR__ . '/auth.php';


