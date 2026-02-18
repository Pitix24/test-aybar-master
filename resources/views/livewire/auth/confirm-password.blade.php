@extends('layouts.web.layout-web')

@section('contenido')
    <div class="contenedor_login" x-data="{ submitting: false }">

        <div x-show="submitting">
            <x-loading-overlay message="Confirmando contraseña..." />
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

                <h1 class="titulo_formulario">CONFIRMAR CONTRASEÑA</h1>
                <p class="descripcion_formulario">
                    Esta es un área segura de la aplicación. Por favor, confirme su contraseña antes de continuar.
                </p>

                <form method="POST" action="{{ route('password.confirm.store') }}" class="formulario_flex formulario"
                    @submit="submitting = true">
                    @csrf

                    <div class="g_margin_top_20">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••">
                        @error('password')
                            <div class="mensaje_error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="g_margin_top_20 formulario_botones centrar">
                        <button type="submit" class="g_boton guardar" x-bind:disabled="submitting">
                            <span x-show="!submitting">Confirmar contraseña</span>
                            <span x-show="submitting">
                                <i class="fa-solid fa-spinner fa-spin"></i> Validando...
                            </span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection