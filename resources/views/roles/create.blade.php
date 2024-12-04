@extends('layouts.app')

@section('styles')
@endsection

@section('content')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">roles</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">roles</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
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
                        <h3 class="card-title">Crear Rol</h3>
                    </div>
                    <div class="d-flex order-lg-2 ms-auto header-right-icons">
                        <a href="{{ route('roles.index') }}" type="button"
                            class="btn btn-secondary btn-icon active text-end"><i class="fe fe-arrow-left"></i>Volver</a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" method="POST" action="{{ route('roles.store') }}">

                        @csrf

                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Rol" value="{{ old('name')}}">
                            @error('name', 'post')
                                <div class="text-xs text-red mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <div>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->
@endsection

@section('scripts')
@endsection
