<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    // UsersController,
    // RoleController,
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

    // // Empresas
    // Empresas\Index as EmpresasIndex,
    // Empresas\Create as EmpresasCreate,
    // Empresas\Update as EmpresasUpdate,
    // Empresas\SelectUser as EmpresasSelectUser,
    // Empresas\Show as EmpresasShow,

    // // Usuarios
    // Usuarios\Index as UsuariosIndex,
    // Usuarios\Create as UsuariosCrate,
    // Usuarios\Update as UsuariosUpdate,

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

    // Roles\Index as RolesIndex,
    // Roles\Create as RolesCreate,
    // Roles\Update as RolesUpdate,

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

    // // Compras
    // Route::get('/compras', ComprasIndex::class)->name('compras.index');
    // Route::get('/compras/create', ComprasCreate::class)->name('compras.create');
    // Route::get('/compras/{id}/detalle', ComprasDetalle::class)->name('compras.detalle');

    // // Ventas
    // Route::get('/ventas/create', VentasPV::class)->name('ventas.create');

    // // Depositos
    // Route::get('/depositos', DepositosIndex::class)->name('depositos.index');
    // Route::get('/depositos/create', DepositosForm::class)->name('depositos.create');
    // Route::get('/depositos/{id}/update', DepositosForm::class)->name('depositos.update');

    // // Chatbot
    // Route::get('/chatbot/nuevo', ChatBotCreate::class)->name('chatbot.assistant.new');
    // Route::get('/chatbot/{assistants_id}', ChatBotIndex::class)->name('chatbot.assistant.assistant');
    // Route::get('/chatbot/{assistants_id}/funciones', ChatBotFunction::class)->name('chatbot.assistant.funciones');
    // Route::get('/chatbot/{assistants_id}/archivos', ChatBotFiles::class)->name('chatbot.assistant.files');

    // // Empresas
    // Route::get('/empresas', EmpresasIndex::class)->name('empresas.index');
    // Route::get('/empresas/{id}/usuarios', EmpresasSelectUser::class)->name('empresas.users');
    // Route::get('/empresas/create', EmpresasCreate::class)->name('empresas.create');
    // Route::get('/empresas/{id}/update', EmpresasUpdate::class)->name('empresas.update');
    // Route::get('/empresas/{id}/ver', EmpresasShow::class)->name('empresas.show');

    // // Confifuracion 
    // Route::prefix('/configuraciones')->group(function () {
    //     //Iva 
    //     Route::get('/ivacategoria', IvaCategoriaIndex::class)->name('conf.ivacategoria.index');
    //     Route::get('/ivacategoria/create', IvaCategoriaForm::class)->name('conf.ivacategoria.create');
    //     Route::get('/ivacategoria/{id}/update', IvaCategoriaForm::class)->name('conf.ivacategoria.update');

    //     //Medio de pago 
    //     Route::get('/mediopago', MedioPagoIndex::class)->name('conf.mediopago.index');
    //     Route::get('/mediopago/create', MedioPagoForm::class)->name('conf.mediopago.create');
    //     Route::get('/mediopago/{id}/update', MedioPagoForm::class)->name('conf.mediopago.update');

    //     // Usuarios
    //     Route::get('/usuarios', UsuariosIndex::class)->name('conf.usuarios.index');
    //     Route::get('/usuarios/formulario', UsuariosCrate::class)->name('conf.usuarios.create');
    //     Route::get('/usuarios/formulario/{id}', UsuariosUpdate::class)->name('conf.usuarios.update');
    // });


    // //Users
    // Route::get('/users/search', [UsersController::class, 'search'])->name('users.search');
    // Route::post('/users/finder', [UsersController::class, 'finder'])->name('users.finder');
    // Route::get('/users/{user}/roles', [UsersController::class, 'roles'])->name('users.roles');
    // Route::post('/users/{user}/roles', [UsersController::class, 'rolesadd'])->name('users.roles');
    // Route::get('/users/{user}/roles/{role}', [UsersController::class, 'rolesremove'])->name('users.rolesremove');
    // Route::resource('/users', UsersController::class);

    // //Roles
    // // Route::get('/roles', [RoleController::class, 'search'])->name('roles.index');
    // // Route::get('/roles/search', [RoleController::class, 'search'])->name('roles.search');
    // // Route::post('/roles/finder', [RoleController::class, 'finder'])->name('roles.finder');
    // // Route::resource('/roles', RoleController::class);

    // //Roles-Permissions
    // Route::get('/permission/role/{id}', [PermissionController::class, 'role'])->name('permissions.role');
    // Route::get('/permission/activate/{permissions_id}/{roles_id}', [PermissionController::class, 'activate'])->name('permissions.activate');
    // Route::post('/permission/role/{id}/finder', [PermissionController::class, 'rolefinder'])->name('permissions.rolefinder');

    // //Configuraciones
    // Route::post('/configuracions/finder', [ConfiguracionsController::class, 'finder'])->name('configuracions.finder');
    // Route::get('/configuracions', [ConfiguracionsController::class, 'index'])->name('configuracions.index');
    // Route::get('/configuracions/create', [ConfiguracionsController::class, 'create'])->name('configuracions.create');
    // Route::post('/configuraciones/store', [ConfiguracionsController::class, 'store'])->name('configuracions.store');
    // Route::get('/configuracions/edit/{configuracion}', [ConfiguracionsController::class, 'edit'])->name('configuracions.edit');
    // Route::put('/configuracions/edit/{configuracion}', [ConfiguracionsController::class, 'update'])->name('configuracions.update');


    // //-------Roles-Permisos-------
    // Route::get('/permissions/search', [PermissionController::class, 'search'])->name('permissions.search');
    // Route::post('/permissions/finder', [PermissionController::class, 'finder'])->name('permissions.finder');
    // Route::resource('/permissions', PermissionController::class);
    // //-------turnosciudadanos-PDF-----------
    // Route::post('/turnosciudadanos/{turnos_id}/pdf', [PdfController::class, 'ciudadanos_turnos'])->name('turnosciudadanos.ciudadanos_turnos');
    // //-------CiudadanosExcluidos--------------
    // Route::get('/ciudadanosexcluidos/search', [CiudadanosExcluidoController::class, 'search'])->name('ciudadanosexcluidos.search');
    // Route::post('/ciudadanosexcluidos/finder', [CiudadanosExcluidoController::class, 'finder'])->name('ciudadanosexcluidos.finder');
    // Route::resource('/ciudadanosexcluidos', CiudadanosExcluidoController::class);
    // //End ceci


    // // PRODUCTOS
    // Route::get('/productos-simple', [ProductosController::class, 'index'])->name('productos.index-simple');
    // Route::get('/productos', ProductosIndex::class)->name('productos.index');
    // Route::get('/productos/create', ProductosCreate::class)->name('productos.create');
    // Route::get('/productos/{id}/update', ProductosUpdate::class)->name('productos.update');

    // // CATEGORIAS
    // Route::get('/categorias', CategoriasIndex::class)->name('categorias.index');
    // Route::get('/categorias/create', CategoriasCreate::class)->name('categorias.create');
    // Route::get('/categorias/{id}/update', CategoriasUpdate::class)->name('categorias.update');

    // // ROLES
    // Route::get('/roles', RolesIndex::class)->name('roles.index');
    // Route::get('/roles/create', RolesCreate::class)->name('roles.create');
    // Route::get('/roles/{id}/update', RolesUpdate::class)->name('roles.update');

    // // EMPRESAS-MEDIOS PAGO
    // Route::get('/empresa/{id}/mediospago', EmpresasMediosPagoIndex::class)->name('empresas.mediopago.index');
    // Route::get('/empresa/{id}/mediospago/create', EmpresasMediosPagoCreate::class)->name('empresas.mediopago.create');
    // Route::get('/empresa/{emp_id}/mediospago/{empresamediopago_id}/update', EmpresasMediosPagoUpdate::class)->name('empresas.mediopago.update');

    // // PRODUCTOS STOCKS
    // Route::get('/producto/{id}/depositos', ProductosStocksCreate::class)->name('productosstocks.create');

    // // VENTAS
    // Route::get('/ventas', VentasIndex::class)->name('ventas.index');

    // // PUNTOS DE VENTA
    // Route::get('/puntosventa', PuntosVentaIndex::class)->name('puntosventa.index');
    // Route::get('/puntosventa/create', PuntosVentaCreate::class)->name('puntosventa.create');
    // Route::get('/puntosventa/{id}/update', PuntosVentaUpdate::class)->name('puntosventa.update');
});


require __DIR__ . '/auth.php';


