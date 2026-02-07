@extends('layouts.web.layout-web')

@section('contenido')
    <div class="contenedor_login">
        <div class="contenedor_login_imagen">
            <img src="{{ asset('assets/imagen/construccion-aybar-corp.jpg') }}" alt="" />
        </div>

        <div class="contenedor_login_formulario">
            <!--FORMULARIO CENTRAR-->
            <div class="login_formulario_centrar">

                <!--LOGO-->
                <div class="login_formulario_logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/imagen/logo-aybar-corp-verde.png') }}" alt="">
                    </a>
                </div>

                <!--TITULO-->
                <h1 class="titulo_formulario">¡HOLA! BIENVENIDO DE NUEVO </h1>

                <!--PÁRRAFO-->
                <p class="descripcion_formulario">Inicie sesión con los datos correctos.
                </p>

                @if ($errors->any())
                    <div class="g_alerta_error">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <div>
                            <strong>Por favor corrige los siguientes errores:</strong>
                        </div>
                    </div>
                @endif

                <form action="{{ route('ingresar.admin') }}" method="POST" class="formulario_flex formulario">
                    @csrf

                    <div class="g_margin_top_20">
                        <label for="email">Correo electrónico</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="g_margin_top_20">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" id="password" required>
                        @error('password')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="g_margin_top_20">
                        <label for="recordarme">
                            <input type="checkbox" name="recordarme" id="recordarme" />
                            Recordarme
                        </label>
                    </div>

                    <div class="g_margin_top_20 formulario_botones centrar">
                        <button type="submit" class="guardar">
                            Ingresar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
