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
					<div class="col col-login mx-auto text-center">
						<a href="{{route('home')}}">
							<img src="{{asset('assets/images/brand/logo-3.png')}}" class="header-brand-img" alt="">
						</a>
					</div>
					<div class="container-login100">
						<div class="wrap-login100 p-0">
							<div class="card-body">

                                <form class="login100-form validate-form" method="POST" action="{{ route('register') }}">
                                @csrf
									<span class="login100-form-title">
										Registro
									</span>

									<div class="wrap-input100 validate-input" data-bs-validate = "Valid email is required: ex@abc.xyz">
                                        <input class="input100" placeholder="Nombre Completo" id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
										<span class="symbol-input100">
											<i class="mdi mdi-account" aria-hidden="true"></i>
										</span>
									</div>

                                    @error('name')
                                        <span class="text-red">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

									<div class="wrap-input100">
										<input class="input100 form-control @error('email') is-invalid @enderror" type="text" name="email" placeholder="Email">
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
										<input class="input100 form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" placeholder="Contraseña">
										<span class="symbol-input100">
											<i class="zmdi zmdi-lock" aria-hidden="true"></i>
										</span>
									</div>
										
                                    @error('password')
                                        <div class="mb-1 text-red">
                                            {{ $message }}
                                        </div>
                                    @enderror
										

									<div class="wrap-input100">
										<input class="input100 form-control @error('password_confirmation') is-invalid @enderror" type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirmar Contraseña">
										<span class="symbol-input100">
											<i class="zmdi zmdi-lock" aria-hidden="true"></i>
										</span>
									</div>

									<label class="custom-control custom-checkbox mt-4">
										<input type="checkbox" class="custom-control-input">
										<span class="custom-control-label">Acepto los <a href="{{url('terms')}}">términos y condiciones</a></span>
									</label>
									<div class="container-login100-form-btn">
                                        <button type="submit" class="login100-form-btn btn-primary">
                                            Registrarme
										</button>
									</div>
									<div class="text-center pt-3">
										<p class="text-dark mb-0">¿Ya tienes cuenta?<a href="{{url('login')}}" class="text-primary ms-1">Iniciar sesión</a></p>
									</div>
								</form>
							</div>
							<div class="card-footer">
								<div class="d-flex justify-content-center my-3">
									<a href="javascript:void(0)" class="social-login  text-center me-4">
										<i class="fa fa-google"></i>
									</a>
									<a href="javascript:void(0)" class="social-login  text-center me-4">
										<i class="fa fa-facebook"></i>
									</a>
									<a href="javascript:void(0)" class="social-login  text-center">
										<i class="fa fa-twitter"></i>
									</a>
								</div>
							</div>
						</div>
					</div>
					<!-- CONTAINER CLOSED -->
				</div>
			</div>
			<!-- END PAGE -->

            @endsection

        @section('scripts')

        @endsection

{{-- 

    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>

    --}}