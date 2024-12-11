<div>
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Formulario de Unidad</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('unidades.index') }}">Unidades</a></li>
                <li class="breadcrumb-item active" aria-current="page">Formulario</li>
            </ol>
        </div>
    </div>

    {{-- CONTENT --}}
    <div>
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <div class="card custom-card">
            <div class="card-header border-bottom d-flex">
                <a href="{{ route('unidades.index') }}" type="button" 
                    class="btn btn-outline-secondary btn-icon">
                    <i class="fa fa-arrow-left mx-2"></i>
                </a>
                <div class="main-header-center d-none d-xl-block mx-3">
                    <h3 class="card-title">{{ $title }}</h3>
                </div>
            </div>

            <div class="card-body">
                <form class="row row-sm form-horizontal" wire:submit.prevent="save">
                    <div class="form-group col-md-6">
                        <label for="unidad_nombre">Unidad *</label>
                        <input type="text" id="unidad_nombre" wire:model="form.unidad_nombre"
                            class="form-control @error('form.unidad_nombre') is-invalid @enderror">
                        @error('form.unidad_nombre')
                            <small class="error text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="codigo">Código *</label>
                        <input type="text" id="codigo" wire:model="form.codigo"
                            class="form-control @error('form.codigo') is-invalid @enderror">
                        @error('form.codigo')
                            <small class="error text-danger">{{ $message }}</small>
                        @enderror
                    </div>                                        

                    <div class="form-group">
                        <p class="mt-2 mb-6">Los campos marcados con (*) son obligatorios</p>
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
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Solucionar el error de cargando infinito --}}
@script
    <script>
        const loader = document.getElementById('global-loader');

        setTimeout(() => {
            loader.classList.add('fade-out');
            setTimeout(() => loader.classList.add('d-none'), 500);
        }, 200);
    </script>
@endscript
