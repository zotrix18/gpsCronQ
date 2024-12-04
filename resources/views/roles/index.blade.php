@extends('layouts.app')

@section('styles')
@endsection

@section('content')

    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">{{$title}}</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('roles.index')}}">Roles</a></li>
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

                        <form 
                        action="{{route('roles.index')}}" 
                        class="form-horizontal"
                        method="GET">
                        <input class="form-control" id="buscar" name="buscar" placeholder="Buscar" type="buscar">
                        </form>
                    </div>
                    <div class="d-flex order-lg-2 ms-auto header-right-icons">
                        <a href="{{route('roles.create')}}" type="button" class="btn btn-primary btn-icon active text-end"><i
                                class="fa fa-plus"></i>Nuevo</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Rol</th>
                                    <th class="text-end">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $rol)
                                    <tr>
                                        <td>{{ $rol->id }}</td>
                                        <td>{{ $rol->name }}</td>
                                        <td class="text-end">
                                            <div aria-label="Basic example" class="btn-group btn-group-sm my-1 " role="group">
                                                @can('roles.edit')
                                                    <a class="btn  btn-primary btn-icon active"
                                                        href="{{ route('roles.edit', ['role' => $rol->id]) }}" data-bs-placement="top" data-bs-toggle="tooltip" title=""
                                                        data-bs-original-title="Editar">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('roles.show')
                                                    <a class="btn btn-primary btn-icon active"
                                                        href="{{ route('roles.show', ['role' => $rol->id]) }}" data-bs-placement="top" data-bs-toggle="tooltip" title=""
                                                        data-bs-original-title="Ver">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('permissions.role')
                                                    <a class="btn btn-primary btn-icon active"
                                                        href="{{ route('permissions.role', ['id' => $rol->id]) }}"
                                                        data-bs-placement="top" data-bs-toggle="tooltip" title=""
                                                        data-bs-original-title="Permisos">
                                                        <i class="fa fa-key"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="8"
                                            class="px-6 py-4 whitespace-no-wrap border-b text-black border-gray-300 text-sm leading-5">
                                            Sin registros para mostrar...</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($links == true)
                        {{ $roles->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
                        @endif
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->
@endsection

@section('scripts')
@endsection
