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
                <li class="breadcrumb-item active" aria-current="page">Logs</li>
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

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Log</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at }}</td>
                                        <td style="word-wrap: break-word; text-wrap: balance;">{{ $log->log  }}</td>
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

                    {{ $logs->links('vendor.pagination.bootstrap-4') }}

                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->
@endsection

@section('scripts')
@endsection
