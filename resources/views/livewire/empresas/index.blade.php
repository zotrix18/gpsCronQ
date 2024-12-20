<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $title }}</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('empresas.index') }}">Empresas</a>
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
                        <a href="{{ route('empresas.create') }}" type="button" class="btn btn-primary btn-icon active text-end"
                            >
                            <i class="mx-2 fa fa-plus"></i>
                            Nueva
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-hover">
                            <thead>
                                <tr>
                                    <th>Empresa</th>
                                    <th>KEY</th>                                  
                                    <th style="width: 250px;">Logo</th>
                                    <th class="text-end">Opciones</th>
                                </tr>
                            </thead>
                            <tbody wire:loading.class="opacity-50">
                                @forelse ($empresas as $empresa)
                                    <tr>
                                        <td>{{ $empresa->empresa }}</td>
                                        <td>{{ $empresa->key }}</td>                                                                              
                                        <td><img src="{{ $empresa->logoPath ? asset('storage/' . $empresa->logoPath) : '' }}" width="150"></td>                                                                              
                                        <td class="text-end">
                                            <div aria-label="Basic example" class="my-1 btn-group btn-group-sm"
                                                role="group">

                                                <!-- Botón para cambiar el estado -->
                                                {{--<button
                                                    class="btn btn-{{ $empresa->activo ? 'danger' : 'primary' }} btn-icon active"
                                                    data-bs-placement="top" data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ $empresa->activo ? 'Desactivar' : 'Activar' }}">
                                                    <i class="fa fa-{{ $empresa->activo ? 'ban' : 'check' }}"></i>
                                                </button>--}}

                                                <a class="btn btn-primary btn-icon active"
                                                    href="{{ route('empresas.users', ['id' => $empresa->id]) }}"
                                                    data-bs-placement="top" data-bs-toggle="tooltip"
                                                    data-bs-original-title="Gestión de usuarios">
                                                    <i class="fa fa-user"></i>
                                                </a>

                                                <a class="btn btn-primary btn-icon active"
                                                    href="{{ route('empresas.update', ['id' => $empresa->id]) }}"
                                                    data-bs-placement="top" data-bs-toggle="tooltip"
                                                    data-bs-original-title="Editar" >
                                                    <i class="fa fa-edit"></i>
                                                </a>                                             

                                                <a class="btn btn-primary btn-icon active"
                                                    href="{{ route('empresas.show', $empresa->id) }}"
                                                    data-bs-placement="top" data-bs-toggle="tooltip"
                                                    data-bs-original-title="Ver">
                                                    <i class="fa fa-eye"></i>
                                                </a>
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
                    {{ $empresas->links() }}

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

        setTimeout(() => {
            loader.classList.add('fade-out');

            setTimeout(() => loader.classList.add('d-none'), 500)
        }, 250);


        $wire.on('alertaExito', () => {
            window.$.growl.notice({
                title: 'Operación exitosa!',
                message: 'Estado actualizado con éxito!'
            })
        })

        $wire.on('confirmarCambioEstado', (event) => {
            swal({
                    title: "¿Estás seguro?",
                    text: "Vas a cambiar el estado de la empresa.",
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
