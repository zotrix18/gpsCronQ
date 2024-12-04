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
                        <h3 class="card-title">Editar Usuario</h3>
                    </div>
                    <div class="d-flex order-lg-2 ms-auto header-right-icons">
                        <a href="{{ route('users.index') }}" type="button"
                            class="btn btn-secondary btn-icon active text-end"><i class="fa fa-arrow-left"></i>Volver</a>
                    </div>
                </div>
                <div class="card-body row">

                    <div class="form-group col-md-6">
                        <input type="text" value="{{ $usuario->name }}" name="name" id="name"
                            class="form-control" readonly placeholder="Nombre Completo">
                        @error('name', 'post')
                            <div class="text-xs text-red mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <input type="email" value="{{ $usuario->email }}" name="email" id="email"
                            class="form-control" readonly placeholder="Email">
                        @error('email', 'post')
                            <div class="text-xs text-red mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <input type="text" name="role"
                            value="{{ count($usuario->roles) > 0 ? $usuario->roles[0]->name : '' }}" id="role"
                            class="form-control" readonly placeholder="Rol">
                        @error('rol', 'post')
                            <div class="text-xs text-red mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <input type="password" name="password" id="password" class="form-control" readonly
                            placeholder="Nueva Contraseña">
                        @error('password', 'post')
                            <div class="text-xs text-red mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group form-switch" style="margin-left: 20px">
                        <input class="form-check-input" type="checkbox" role="switch" id="activo" disabled
                            name="activo" @if ($usuario->activo) checked @endif>
                        <label class="form-check-label" for="flexSwitchCheckChecked">Activo</label>
                    </div>
                    @can('users.destroy')
                        <div class="form-group col-md-6">
                            <a class="btn btn-danger" data-bs-target="#modaldemo1" data-bs-toggle="modal"
                                href="javascript:void(0)">Eliminar</a>
                        </div>
                    @endcan
                    

                    <!-- BASIC MODAL -->
                    <div class="modal fade" id="modaldemo1">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">Eliminar Usuario</h6><button aria-label="Close"
                                        class="btn-close" data-bs-dismiss="modal"><span
                                            aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <h6>Estas seguro que desea realizar esta accion?</h6>
                                </div>
                                <div class="modal-footer">

                                    <form 
                                    action="{{route('users.destroy', ['user' => $usuario->id])}}" 
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
@endsection
