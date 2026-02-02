@extends('layouts.app')

@section('styles')
{{-- Style Here --}}
@vite(['resources/scss/light/assets/authentication/auth-boxed.scss'])
@vite(['resources/scss/dark/assets/authentication/auth-boxed.scss'])
@endsection

@section('content')
{{-- Content Here --}}
<div class="auth-container d-flex">

    <div class="container mx-auto align-self-center">

        <div class="row">

            <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center mx-auto">
                <div class="card mt-3 mb-3">
                    <div class="card-body">

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-4 text-center">
                                    <img src="{{Vite::asset('resources/images/Logo sin fondo.png')}}" alt="Logo" style="height: 80px; width: auto; margin-bottom: 20px;">
                                </div>
                                <div class="col-md-12 mb-3">
                                    
                                    <h2>Iniciar Sesión</h2>
                                    <p>Ingresa tu correo y contraseña para iniciar sesión</p>
                                    
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Correo Electrónico</label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                        @error('email')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-4">
                                        <label class="form-label">Contraseña</label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                        @error('password')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <div class="form-check form-check-primary form-check-inline">
                                            <input class="form-check-input me-3" type="checkbox" name="remember" id="form-check-default">
                                            <label class="form-check-label" for="form-check-default">
                                                Recuérdame
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="mb-4">
                                        <button type="submit" class="btn btn-secondary w-100">INICIAR SESIÓN</button>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="text-center">
                                        <p class="mb-0"><a href="{{ route('password.request') }}" class="text-warning">¿Olvidaste tu contraseña?</a></p>
                                    </div>
                                </div>
                                
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>

</div>
@endsection

@section('scripts')
{{-- Scripts Here --}}
@endsection