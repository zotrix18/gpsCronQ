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

                    <div class="form-group col-md-6">
                        <label for="imgId">Imagen Primaria</label>
                        <input  id="imgIdHidden" type="hidden" wire:model="currentStore.imgPrimary">
                        <select id="imgId"
                            class="form-control @error('form.imgId')
                            is-invalid @enderror"
                            wire:model.defer="form.imgId"
                            data-dynamic-select
                            >
                            <option value="">Seleccione una imagen</option>
                            @foreach ($imgs as $img)
                                <option value="{{$img['path']}}" {{$form->imgId == $img['path'] ? 'selected' : ''}} data-img="{{ asset( $img['path']) }}">{{ $img['nombre']}}</option>
                            @endforeach
                        </select>
                        @error('form.imgId')
                            <small class="error text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        <label for="formFileDisabled" class="">Imagen Personalizada</label>
                        <input class="form-control @error('form.imgId') is-invalid @enderror" 
                            type="file" 
                            id="formFileDisabled" 
                            accept=".jpg,.jpeg,.png"
                            x-on:change="
                                let file = $event.target.files[0];
                                $wire.upload('form.imgCustomPath', file, () => {
                                    $wire.call('handleFileUpload')
                                })
                            ">
                        @error('form.imgCustomPath')
                            <small class="error text-danger">{{ $message }}</small>
                        @enderror                        
                    </div>


                    <div class="form-group col-md-6 mb-3">
                        <label for="formFileDisabled" class="">Observación</label>
                        <textarea rows="10" class="form-control @error('form.observacion') is-invalid @enderror" name="observacion" wire:model.defer="form.observacion" id=""></textarea>                    
                        @error('form.observacion')
                            <small class="error text-danger">{{ $message }}</small>
                        @enderror 
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        @if($form->imgCustomPath && $form->imgCustomPath != null)
                            <img src="{{ $form->imgCustomPath ? asset('storage/' . $form->imgCustomPath) : '' }}" class="img-thumbnail mx-auto mt-1" width="450px" alt="">
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

{{-- Solucionar el error de cargando infinito --}}
@script
    <script>
        const loader = document.getElementById('global-loader');

        setTimeout(() => {
            loader.classList.add('fade-out');
            setTimeout(() => loader.classList.add('d-none'), 500);
        }, 200);

        // document.getElementById('formFileDisabled').addEventListener('change', (e) => {              
        //     setTimeout(() => {
        //         $wire.resetJs();
        //     }, 500);        
        // })

        const dinamicSelectCart = () => {
            new DynamicSelect('#imgId', {
                // width: '200px',
                placeholder: 'Seleccione',
                name: 'imgId',
                value: '{{ $form->imgId }}',
                onChange: function(value, text, option) {
                    @this.set('form.imgId', value);
                    $wire.resetJs();
                }
            });        
        }

        dinamicSelectCart();

        $wire.on('resetJs', (data) => {
            offset = data[0] || 0;
            console.log(offset);
            
            
            //Eventos js que se pierden deben reiniciarse en cada actualización de componente
            setTimeout(() => {
                dinamicSelectCart();
            }, offset)
        });


    </script>
@endscript
