@extends('layouts.web.layout-web')

@section('contenido')
    <div class="contenedor_login" x-data="{ submitting: false }">

        <div x-show="submitting">
            <x-loading-overlay message="Restableciendo contraseña..." />
        </div>

        <div class="contenedor_login_imagen">
            <img src="{{ asset('assets/imagen/construccion-aybar-corp.jpg') }}" alt="" />
        </div>

        <div class="contenedor_login_formulario">
            <div class="login_formulario_centrar">

                <div class="login_formulario_logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/imagen/logo-aybar-corp-verde.png') }}" alt="">
                    </a>
                </div>

                <h1 class="titulo_formulario">RESTABLECER CONTRASEÑA</h1>
                <p class="descripcion_formulario">
                    Por favor, ingresa tu nueva contraseña para recuperar el acceso a tu cuenta.
                </p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="g_alerta success g_margin_bottom_20">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="formulario_flex formulario"
                    @submit="submitting = true">
                    @csrf
                    <!-- Token -->
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">

                    <!-- Email Address -->
                    <div class="g_margin_top_20">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email', request('email')) }}" required
                            autocomplete="email">
                        @error('email')
                            <div class="mensaje_error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="g_margin_top_20">
                        <label for="password">Nueva contraseña</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password"
                            placeholder="••••••••">
                        @error('password')
                            <div class="mensaje_error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="g_margin_top_20">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            autocomplete="new-password" placeholder="••••••••">
                    </div>

                    <div class="g_margin_top_20 formulario_botones centrar">
                        <button type="submit" class="g_boton guardar" x-bind:disabled="submitting">
                            <span x-show="!submitting">Restablecer contraseña</span>
                            <span x-show="submitting">
                                <i class="fa-solid fa-spinner fa-spin"></i> Procesando...
                            </span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection