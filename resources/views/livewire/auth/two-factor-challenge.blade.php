@extends('layouts.web.layout-web')

@section('contenido')
    <div class="contenedor_login" x-data="{
        submitting: false,
        showRecoveryInput: @js($errors->has('recovery_code')),
        code: '',
        recovery_code: '',
        toggleInput() {
            this.showRecoveryInput = !this.showRecoveryInput;
            this.code = '';
            this.recovery_code = '';
            this.$nextTick(() => {
                if (this.showRecoveryInput) {
                    this.$refs.recovery_code?.focus();
                } else {
                    this.$refs.code?.focus();
                }
            });
        },
    }">

        <div x-show="submitting">
            <x-loading-overlay message="Verificando código..." />
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

                <div x-show="!showRecoveryInput">
                    <h1 class="titulo_formulario">CÓDIGO DE AUTENTICACIÓN</h1>
                    <p class="descripcion_formulario">
                        Ingrese el código de autenticación proporcionado por su aplicación de autenticación.
                    </p>
                </div>

                <div x-show="showRecoveryInput">
                    <h1 class="titulo_formulario">CÓDIGO DE RECUPERACIÓN</h1>
                    <p class="descripcion_formulario">
                        Confirme el acceso a su cuenta ingresando uno de sus códigos de recuperación de emergencia.
                    </p>
                </div>

                <form method="POST" action="{{ route('two-factor.login.store') }}" @submit="submitting = true">
                    @csrf

                    <div class="formulario">
                        <div x-show="!showRecoveryInput">
                            <div class="g_margin_top_20">
                                <label for="code"><i class="fa-solid fa-key"></i> Código</label>
                                <input type="text" id="code" x-ref="code" name="code" placeholder="000000"
                                    x-model="code" inputmode="numeric" autofocus autocomplete="one-time-code">
                                @error('code')
                                    <div class="mensaje_error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div x-show="showRecoveryInput">
                            <div class="g_margin_top_20">
                                <label for="recovery_code"><i class="fa-solid fa-shield-heart"></i> Código de Recuperación</label>
                                <input type="text" id="recovery_code" x-ref="recovery_code" name="recovery_code"
                                    x-model="recovery_code" x-bind:required="showRecoveryInput"
                                    autocomplete="one-time-code" placeholder="abcd-efgh-...">
                                @error('recovery_code')
                                    <div class="mensaje_error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="g_margin_top_20 formulario_botones centrar">
                            <button type="submit" class="g_boton guardar" x-bind:disabled="submitting">
                                <span x-show="!submitting">Continuar</span>
                                <span x-show="submitting">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Verificando...
                                </span>
                            </button>
                        </div>

                        <div class="g_margin_top_20 text-center">
                            <button type="button" class="g_link" @click="toggleInput()"
                                style="background:none; border:none; text-decoration:underline; font-size: 14px; color: #64748b; cursor: pointer;">
                                <span x-show="!showRecoveryInput">Usar un código de recuperación</span>
                                <span x-show="showRecoveryInput">Usar un código de autenticación</span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

