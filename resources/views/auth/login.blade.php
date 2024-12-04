@extends('layouts.custom-app')

@section('styles')
@endsection

@section('body')

    <body class="ltr login-img">
    @endsection

    @section('content')
        <!-- PAGE -->
        <div class="page">
            <div>
                <!-- CONTAINER OPEN -->
                <div class="container-login100">
                    <div class="wrap-login100 p-0">
                        <div class="card-body">
                            <form method="POST" action="{{ route('login') }}" class="login100-form validate-form">
                                @csrf
                                <div class="col col-login mx-auto text-center" style="margin-bottom: 20px; margin-top: 10px">
                                    <h3 class="text-primary" style="font-weight: bold">Facturador</h3>
                                </div>
                                <br>

                                {{-- <div style="display: flex; justify-content: center; margin-bottom: 30px">

                                    <img src="{{ asset('assets/images/brand/cronq.png') }}" class="header-brand-img"
                                        alt="logo" style="border: 1 px solid red">
                                </div> --}}

                                @error('empresa')
                                    <div class="alert alert-danger" style="font-size: 13px">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div class="wrap-input100 mb-5">
                                    <input class="input100 form-control @error('email') is-invalid @enderror" type="text"
                                        name="email" placeholder="Correo electrónico">
                                    <span class="symbol-input100">
                                        <i class="zmdi zmdi-email" aria-hidden="true"></i>
                                    </span>
                                </div>
                                @error('email')
                                    <div class="mb-1 text-red">
                                        {{ $message }}
                                    </div>
                                @enderror


                                <div class="wrap-input100">
                                    <input class="input100 form-control @error('password') is-invalid @enderror"
                                        type="password" id="password" name="password" placeholder="Contraseña">
                                    <span class="symbol-input100">
                                        <i class="zmdi zmdi-lock" aria-hidden="true"></i>
                                    </span>
                                </div>

                                @error('password')
                                    <div class="mb-1 text-red">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div class="container-login100-form-btn">
                                    <button type="submit" class="login100-form-btn btn-primary">
                                        Iniciar Sesión
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- CONTAINER CLOSED -->
            </div>

            @if (session('empresas'))
                <script>
                    // Código para mostrar el modal automáticamente
                    document.addEventListener('DOMContentLoaded', function() {
                        var myModal = new bootstrap.Modal(document.getElementById('seleccionarEmpresa'));
                        myModal.show();
                    });
                </script>
            @endif

        </div>
        <!-- End PAGE -->
    @endsection

    @section('scripts')
    @endsection
