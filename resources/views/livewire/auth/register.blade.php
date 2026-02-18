@extends('layouts.web.layout-web')

@section('contenido')
    <div class="contenedor_login" x-data="{ submitting: false }">

        <div x-show="submitting">
            <x-loading-overlay message="Creando tu cuenta..." />
        </div>

        <div class="contenedor_login_imagen">
            <img src="{{ asset('assets/imagen/construccion-aybar-corp.jpg') }}" alt="" />
        </div>

        <div class="contenedor_login_formulario">
            <div class="login_formulario_centrar">

                <div class="login_formulario_arriba">
                    <span>¿Ya tienes una cuenta?</span>
                    <a href="{{ route('login') }}">Ingresar</a>
                </div>

                <div class="login_formulario_logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/imagen/logo-aybar-corp-verde.png') }}" alt="">
                    </a>
                </div>

                <h1 class="titulo_formulario">CREAR UNA CUENTA</h1>
                <p class="descripcion_formulario">
                    Completa tus datos para empezar a utilizar nuestra plataforma.
                </p>

                <form method="POST" action="{{ route('register.store') }}" class="formulario_flex formulario"
                    @submit="submitting = true">
                    @csrf

                    <!-- Name -->
                    <div class="g_margin_top_20">
                        <label for="name">Nombre completo</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                            autocomplete="name" placeholder="Ej: Juan Pérez">
                        @error('name')
                            <div class="mensaje_error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="g_margin_top_20">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                            placeholder="email@ejemplo.com">
                        @error('email')
                            <div class="mensaje_error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="g_margin_top_20">
                        <label for="password">Contraseña</label>
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
                            <span x-show="!submitting">Crear cuenta</span>
                            <span x-show="submitting">
                                <i class="fa-solid fa-spinner fa-spin"></i> Registrando...
                            </span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection