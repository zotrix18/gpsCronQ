<div>
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Roles</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}" >Roles</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $subtitle }}</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <!-- Row -->
    @include('modals.alerts')
    <div class="row row-sm">
        <div class="col-lg-12">
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
            <div class="card custom-card">
                <div class="card-header border-bottom d-flex">

                    <div class="main-header-center d-none d-xl-block">
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <div class="d-flex order-lg-2 ms-auto header-right-icons">
                        <a href="{{ route('roles.index') }}" type="button"
                            class="btn btn-secondary btn-icon active text-end" ><i class="fe fe-arrow-left"></i>Volver</a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" wire:submit="save">
                        @csrf
                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Rol" wire:model="form.name">
                            @error('form.name')
                                <div class="text-xs text-red mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div>
                                <button type="submit" class="btn btn-primary" style="min-width: 150px; min-height: 32px">
                                    <span wire:loading.remove>
                                        Guardar
                                    </span>

                                    <div class="d-flex justify-content-center">
                                        <div class="spinner-border spinner-border-sm" role="status" wire:loading>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->
</div>

{{-- para solucinar el error de cargando infinito --}}
@script
    <script>
        const loader = document.getElementById('global-loader')

        // Aplica la transición de opacidad

        setTimeout(() => {
            loader.classList.add('fade-out');

            setTimeout(() => loader.classList.add('d-none'), 500)
        }, 200);
    </script>
@endscript