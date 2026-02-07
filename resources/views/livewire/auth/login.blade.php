@extends('layouts.web.layout-web')

@section('contenido')
    <div class="contenedor_login">

        <div class="contenedor_login_imagen">
            <img src="{{ asset('assets/imagen/construccion-aybar-corp.jpg') }}" alt="" />
        </div>

        <div class="contenedor_login_formulario">

            <div class="login_formulario_centrar">

                <div class="login_formulario_arriba">
                    <span>¿No tienes una cuenta?</span>
                    <a href="{{ route('registrar.cliente') }}">Registrarme</a>
                </div>

                <div class="login_formulario_logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/imagen/logo-aybar-corp-verde.png') }}" alt="">
                    </a>
                </div>

                <h1 class="titulo_formulario">¡Hola! Bienvenido nuevamente</h1>

                <p class="descripcion_formulario">
                    Inicie sesión con sus credenciales para continuar.
                </p>

                @if (session('status'))
                    <div class="g_alerta_succes">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="g_alerta_error">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <div>
                            <strong>Por favor corrige los siguientes errores:</strong>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="formulario_flex formulario">
                    @csrf

                    <div class="g_margin_top_20">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="mensaje_error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="g_margin_top_20">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required>

                        @error('password')
                            <div class="mensaje_error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="g_margin_top_20">
                        <label for="remember">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            Recordarme
                        </label>
                    </div>

                    <div class="g_margin_top_20 formulario_botones centrar">
                        <button type="submit" class="guardar">
                            Ingresar
                        </button>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="recuperar_clave">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </form>
            </div>
        </div>

    </div>
@endsection