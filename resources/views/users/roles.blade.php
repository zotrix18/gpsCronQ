@extends('layouts.app')

@section('styles')
@endsection

@section('content')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $title }}</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Roles</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <!-- Row -->
    @include('modals.alerts')
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-body">

                    <form 
                    action="{{route('users.roles', ['user' => $users->id])}}" 
                    method="POST">
                    <div class="row">
                        <div class="col-md-12 row">
                            <div class="col-md-3 align-self-center">
                                <input value="" class="form-control form-control-sm" placeholder="Buscar Rol"
                                    type="text" name="role" id="role">
                            </div>

                            <div class="col-md-1 align-self-center">
                                <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fa fa-plus"></i>
                                </button>
                            </div>
                            {!! Form::hidden('roles_id', '', ['id' => 'roles_id']) !!}
                        </div>
                    </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-hover">
                            <thead>
                                <tr>
                                    <th>Rol</th>
                                    <th class="text-end">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users->getRoleNames() as $role)
                                    <tr>
                                        <td>{{ $role }}</td>
                                        <td class="text-end">
                                            <div aria-label="Basic example" class="btn-group btn-group-sm my-1"
                                                role="group">
                                                <a class="btn btn-primary btn-icon active" 
                                                    href="{{route('users.rolesremove', ['user' => $users->id, 'role' => $role])}}"
                                                    data-bs-placement="top" data-bs-toggle="tooltip" title=""
                                                    data-bs-original-title="Quitar Rol">
                                                    <i class="fa fa-trash"></i>
                                                </a>
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
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->
@endsection

@section('scripts')
    <script>
        $("#role").autocomplete({
            source: "{{ route('roles.search') }}",
            select: function(event, ui) {
                if (ui.item.id != 0) {
                    $('#role').val(ui.item.value);
                    $('#roles_id').val(ui.item.id);
                }
            },
            minLength: 0
        }).click(function() {
            $(this).autocomplete("search");
        });
    </script>
@endsection
