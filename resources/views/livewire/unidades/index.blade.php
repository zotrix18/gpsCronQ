<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $title }}</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('unidades.index') }}">Unidades</a>
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
                        <form class="form-horizontal">
                            <input class="form-control" wire:model.live="querySearch" placeholder="Buscar" />
                        </form>
                    </div>
                    <div class="d-flex order-lg-2 ms-auto header-right-icons">
                        <a href="{{Route('unidades.create')}}" type="button" class="btn btn-primary btn-icon active text-end">
                            <i class="mx-2 fa fa-plus"></i>
                            Nueva
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table border text-nowrap text-md-nowrap table-hover">
                        <thead>
                            <tr>
                                <th>Unidad</th>
                                <th>Código</th>
                                <th>Activo</th>
                                <th class="text-end">Opciones</th>
                            </tr>
                        </thead>
                        <tbody wire:loading.class="opacity-50">                            
                            @forelse ($unidades as $unidad)
                                <tr>
                                    <td>{{ $unidad->unidad }}</td>
                                    <td>{{ $unidad->codigo }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ $unidad->activo ? 'success' : 'danger' }} my-1">
                                            {{ $unidad->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div aria-label="Basic example" class="my-1 btn-group btn-group-sm" role="group">
                                            <button 
                                                class="btn btn-{{ $unidad->activo ? 'danger' : 'primary' }} btn-icon active"
                                                wire:click="confirmarCambioEstado({{ $unidad->id }})"
                                                data-bs-placement="top" data-bs-toggle="tooltip"
                                                data-bs-original-title="{{ $unidad->activo ? 'Desactivar' : 'Activar' }}">
                                                <i class="fa fa-{{ $unidad->activo ? 'ban' : 'check' }}"></i>
                                            </button>

                                            <a class="btn btn-primary btn-icon active"
                                                href="{{ route('unidades.update', ['id' => $unidad->id]) }}"
                                                data-bs-placement="top" data-bs-toggle="tooltip"
                                                data-bs-original-title="Editar">
                                                <i class="fa fa-edit"></i>
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

                    {{-- $unidades->links() --}}
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script src="{{ asset('assets/plugins/notify/js/rainbow.js') }}"></script>
    <script src="{{ asset('assets/plugins/notify/js/sample.js') }}"></script>
    <script src="{{ asset('assets/plugins/notify/js/jquery.growl.js') }}"></script>
    <script src="{{ asset('assets/plugins/notify/js/notifIt.js') }}"></script>
@endsection

@script
    <script>
        const loader = document.getElementById('global-loader')

        setTimeout(() => {
            loader.classList.add('fade-out');
            setTimeout(() => loader.classList.add('d-none'), 500)
        }, 250);

        $wire.on('alertaExito', () => {
            window.$.growl.notice({
                title: 'Operación exitosa!',                
            })
        })

        $wire.on('confirmarCambioEstado', (event) => {
            swal({
                title: "¿Estás seguro?",
                text: "Vas a cambiar el estado de la unidad.",
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
                    $wire.cambiarEstado(event[0]?.id);                    
                }
            });
        });
    </script>
@endscript