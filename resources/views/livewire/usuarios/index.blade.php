<div>
    <div>
        <div class="page-header">
            <div>
                <h1 class="page-title">{{ $title }}</h1>
            </div>
            <div class="ms-auto pageheader-btn">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('conf.usuarios.index') }}">Usuarios</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Listado
                    </li>
                </ol>
            </div>
        </div>

        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card custom-card">
                    <div class="card-header border-bottom d-flex">
                        <div class="main-header-center d-none d-xl-block">
                            <!-- Campo de búsqueda enlazado a la propiedad Livewire -->
                            <form class="form-horizontal">
                                <input class="form-control" wire:model.live="querySearch" placeholder="Buscar" />

                            </form>
                        </div>
                        <div class="d-flex order-lg-2 ms-auto header-right-icons">
                            <a href="usuarios/formulario" type="button"
                                class="btn btn-primary btn-icon active text-end">
                                <i class="mx-2 fa fa-plus"></i>
                                Nuevo
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table border text-nowrap text-md-nowrap table-hover">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Telefono</th>
                                        <th>Estado</th>
                                        <th class="text-end">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody wire:loading.class="opacity-50">
                                    @forelse ($usuarios as $usuario)
                                        <tr>
                                            <td>{{ $usuario->name }}</td>
                                            <td>{{ $usuario->email }}</td>
                                            <td>{{ $usuario->telefono ? $usuario->telefono : '-' }}</td>
                                            <td>
                                                <span
                                                    class="badge rounded-pill bg-{{ $usuario->activo ? 'success' : 'danger' }} my-1">
                                                    {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div aria-label="Basic example" class="my-1 btn-group btn-group-sm"
                                                    role="group">

                                                    <!-- Botón para cambiar el estado -->
                                                    <button
                                                        class="btn btn-{{ $usuario->activo ? 'danger' : 'primary' }} btn-icon active"
                                                        wire:click="confirmarCambioEstado({{ $usuario->id }})"
                                                        data-bs-placement="top" data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}">
                                                        <i class="fa fa-{{ $usuario->activo ? 'ban' : 'check' }}"></i>
                                                    </button>

                                                    <a class="btn btn-primary btn-icon active"
                                                        href="{{ route('conf.usuarios.update', ['id' => $usuario->id]) }}"
                                                        data-bs-placement="top" data-bs-toggle="tooltip"
                                                        data-bs-original-title="Editar">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    {{-- <a class="btn btn-primary btn-icon active"
                                                        href="{{ route('empresas.show', ['empresa' => $usuario->id]) }}"
                                                        data-bs-placement="top" data-bs-toggle="tooltip"
                                                        data-bs-original-title="Ver">
                                                        <i class="fa fa-eye"></i>
                                                    </a> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Sin registros para mostrar...</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>


                        <!-- Enlaces de paginación -->
                        {{ $usuarios->links() }}

                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <!-- INTERNAL Notifications js -->
        <script src="{{ asset('assets/plugins/notify/js/rainbow.js') }}"></script>
        <script src="{{ asset('assets/plugins/notify/js/sample.js') }}"></script>
        <script src="{{ asset('assets/plugins/notify/js/jquery.growl.js') }}"></script>
        <script src="{{ asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    @endsection


    {{-- para solucinar el error de cargando infinito --}}
    @script
        <script>
            const loader = document.getElementById('global-loader')

            // Aplica la transición de opacidad

            document.addEventListener('lived', () => {
                setTimeout(() => {
                    loader.classList.add('fade-out');

                    setTimeout(() => loader.classList.add('d-none'), 500)
                }, 350);
            })



            $wire.on('alertaExito', () => {
                window.$.growl.notice({
                    title: 'Operación exitosa!',
                    message: 'Estado actualizado con éxito!'
                })
            })

            $wire.on('confirmarCambioEstado', (event) => {
                swal({
                        title: "¿Estás seguro?",
                        text: "Vas a cambiar el estado del usuario.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Si continuar",
                        cancelButtonText: "Cancelar",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            $wire.dispatch('cambiarEstado', {
                                id: event?.id
                            });
                        }
                    });

            });
        </script>
    @endscript

</div>
