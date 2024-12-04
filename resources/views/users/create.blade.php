@extends('layouts.app')

@section('styles')
@endsection

@section('content')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Usuario</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Index</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <!-- Row -->
    @include('modals.alerts')
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-header border-bottom d-flex">

                    <div class="main-header-center d-none d-xl-block">
                        <h3 class="card-title">Crear Usuario</h3>
                    </div>
                    <div class="d-flex order-lg-2 ms-auto header-right-icons">
                        <a href="{{ route('users.index') }}" type="button"
                        class="btn btn-secondary btn-icon active text-end"><i class="fa fa-arrow-left"></i>Volver</a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal row row-sm" method="POST" action="{{ route('users.store') }}">

                        @csrf

                        <div class="form-group col-md-6">
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Nombre Completo" value="{{ old('name')}}">
                            @error('name', 'post')
                                <div class="text-xs text-red mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="{{ old('email')}}">
                            @error('email', 'post')
                                <div class="text-xs text-red mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <input type="text" name="role" id="role" class="form-control" placeholder="Rol" value="{{ old('role')}}">
                            <input type="hidden" name="rol" id="rol" value="{{ old('rol')}}">
                            @error('rol', 'post')
                                <div class="text-xs text-red mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Contraseña">
                            @error('password', 'post')
                                <div class="text-xs text-red mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group form-switch" style="margin-left: 20px">
                            <input class="form-check-input" type="checkbox" role="switch" id="activo" name="activo" checked>
                            <label class="form-check-label" for="flexSwitchCheckChecked">Activo</label>
                        </div>

                        <div class="form-group">
                            <div>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $("#role").autocomplete({
            source: "{{ route('roles.search') }}",
            select: function(event, ui) {
                if (ui.item.id != 0) {
                    $('#role').val(ui.item.value);
                    $('#rol').val(ui.item.id);
                }
            },
            minLength: 0
        }).click(function() {
            $(this).autocomplete("search");
        });
    </script>
@endsection
