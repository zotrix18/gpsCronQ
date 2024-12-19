<div>
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Formulario de empresa</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('empresas.index') }}">Empresas</a></li>
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


        <div class=" card custom-card">
            <div class="card-header border-bottom d-flex">
                <a href="{{ route('empresas.index') }}" type="button" class="btn btn-outline-secondary btn-icon">
                    <i class="fa fa-arrow-left"></i>
                </a>

                <div class="main-header-center d-none d-xl-block mx-3">
                    <h3 class="card-title">{{ $title }}</h3>
                </div>
            </div>

            <div class="card-body">
                <form class="row row-sm form-horizontal" wire:submit.prevent="save">
                    <div class="form-group col-md-6">
                        <label for="empresa">Empresa *</label>
                        <input type="text" id="empresa" wire:model.defer="empresa.empresa"
                            class="form-control @error('empresa.empresa') is-invalid @enderror">
                        @error('empresa.empresa')
                            <small class="error text-danger">{{ $message }}</small>
                        @enderror
                    </div>                    

                    <div class="form-group col-md-6">
                        <label for="key">KEY *</label>
                        <input type="text" id="key" wire:model.defer="empresa.key"
                            class="form-control @error('empresa.key') is-invalid @enderror">
                        @error('empresa.key')
                            <small class="error text-danger">{{ $message }}</small>
                        @enderror
                    </div>                               

                    <div class="form-group col-md-6">
                        <label for="key">Logo</label>
                        <input class="form-control @error('empresa.logo') is-invalid @enderror" 
                            type="file" 
                            id="formFileDisabled" 
                            accept=".jpg,.jpeg,.png"
                            wire:model.defer="empresa.logo"
                            >
                        @error('empresa.logo')
                            <small class="error text-danger">{{ $message }}</small>
                        @enderror
                    
                            @if ($empresa->logo && $empresa->logo != null)
                            <div class="mx-auto w-fc">
                                <img src="{{ $empresa->logo ? asset('storage/' . $empresa->logo) : '' }}" class="img-thumbnail mx-auto mt-1" width="450px" alt="">
                            </div>
                            @endif
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
