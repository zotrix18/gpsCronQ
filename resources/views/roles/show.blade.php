@extends('layouts.app')

@section('styles')
@endsection

@section('content')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Roles</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ver</li>
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
                        <h3 class="card-title">Ver Rol</h3>
                    </div>
                    <div class="d-flex order-lg-2 ms-auto header-right-icons">
                        <a href="{{ route('roles.index') }}" type="button"
                            class="btn btn-secondary btn-icon active text-end"><i class="fe fe-arrow-left"></i>Volver</a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <input value="{{ $role->name }}" type="text" name="name" id="name"
                            class="form-control readonly" placeholder="Rol">
                        @error('name', 'post')
                            <div class="text-xs text-red mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <a class="btn btn-danger" data-bs-target="#modaldemo1" data-bs-toggle="modal"
                        href="javascript:void(0)">Eliminar</a>

                    <!-- BASIC MODAL -->
                    <div class="modal fade" id="modaldemo1">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">Eliminar Rol</h6><button aria-label="Close" class="btn-close"
                                        data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <h6>Estas seguro que desea realizar esta accion?</h6>
                                </div>
                                <div class="modal-footer">

                                    <form 
                                    action="{{route('roles.destroy', ['role' => $role->id])}}" 
                                    class="form-horizontal"
                                    method="DELETE">

                                    @csrf
                                    <button type="submit" class="btn btn-danger">Si, Eliminar</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>

                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <!-- End Row -->
@endsection

@section('scripts')
@endsection
