@extends('layouts.web.layout-web')

@section('contenido')
    <div class="contenedor_login" x-data="{ submitting: false }">

        <div x-show="submitting">
            <x-loading-overlay message="Enviando enlace de recuperación..." />
        </div>

        <div class="contenedor_login_imagen">
            <img src="{{ asset('assets/imagen/construccion-aybar-corp.jpg') }}" alt="" />
        </div>

        <div class="contenedor_login_formulario">
            <div class="login_formulario_centrar">

                <div class="login_formulario_arriba">
                    <span>¿Recordaste tu clave?</span>
                    <a href="{{ route('login') }}">Ingresar</a>
                </div>

                <div class="login_formulario_logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/imagen/logo-aybar-corp-verde.png') }}" alt="">
                    </a>
                </div>

                <h1 class="titulo_formulario">¿OLVIDASTE TU CONTRASEÑA?</h1>
                <p class="descripcion_formulario">
                    Ingresa tu correo y te enviaremos un enlace para que puedas restablecerla.
                </p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="g_alerta success g_margin_bottom_20">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="formulario_flex formulario"
                    @submit="submitting = true">
                    @csrf

                    <div class="g_margin_top_20">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="email@ejemplo.com">
                        @error('email')
                            <div class="mensaje_error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="g_margin_top_20 formulario_botones centrar">
                        <button type="submit" class="g_boton guardar" x-bind:disabled="submitting">
                            <span x-show="!submitting">Enviar enlace de recuperación</span>
                            <span x-show="submitting">
                                <i class="fa-solid fa-spinner fa-spin"></i> Enviando...
                            </span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection