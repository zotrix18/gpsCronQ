<div>
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Formulario de usuario</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('conf.usuarios.index') }}" >Usuarios</a></li>
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

                <a href="{{ route('conf.usuarios.index') }}" type="button" 
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
                        <label for="name">Nombre completo*</label>
                        <input type="text" id="name" wire:model="form.name"
                            class="form-control @error('form.name') is-invalid @enderror">
                        @error('form.name')
                            <small class="error text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="email">Correo electrónico *</label>
                        <input type="email" id="email" wire:model="form.email"
                            class="form-control @error('form.email') is-invalid @enderror">
                        @error('form.email')
                            <small class="error text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="telefono">Telefono</label>
                        <input type="number" id="telefono" wire:model="form.telefono"
                            class="form-control @error('form.telefono') is-invalid @enderror">
                        @error('form.telefono')
                            <small class="error text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    @if (empty($form->email))
                        <div class="form-group col-md-6">
                            <label for="password">Contraseña *</label>
                            <input type="password" id="password" wire:model="form.password"
                                class="form-control @error('form.password') is-invalid @enderror">
                            @error('form.password')
                                <small class="error text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    @endif

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
