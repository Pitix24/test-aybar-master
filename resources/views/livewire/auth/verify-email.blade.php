@extends('layouts.web.layout-web')

@section('contenido')
    <div class="contenedor_login" x-data="{ submitting: false }">

        <div x-show="submitting">
            <x-loading-overlay message="Enviando correo de verificación..." />
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

                <h1 class="titulo_formulario">VERIFICA TU CORREO</h1>
                <p class="descripcion_formulario">
                    ¡Gracias por registrarte! Por favor verifique su dirección de correo electrónico haciendo clic en el
                    enlace que acabamos de enviar a su bandeja de entrada.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="g_alerta success g_margin_bottom_20">
                        <i class="fa-solid fa-circle-check"></i>
                        Se ha enviado un nuevo enlace de verificación a la dirección de correo proporcionada.
                    </div>
                @endif

                <div class="g_fila centrar">
                    <form method="POST" action="{{ route('verification.send') }}" @submit="submitting = true">
                        @csrf
                        <div class="g_margin_top_20 formulario_botones centrar">
                            <button type="submit" class="g_boton guardar" x-bind:disabled="submitting">
                                <span x-show="!submitting">Reenviar correo de verificación</span>
                                <span x-show="submitting">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Enviando...
                                </span>
                            </button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <div class="g_margin_top_20">
                            <button type="submit" class="g_boton"
                                style="background: none; color: #64748b; border: 1px solid #e2e8f0;">
                                <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection