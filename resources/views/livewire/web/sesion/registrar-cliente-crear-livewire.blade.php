<div class="contenedor_login">

    <div class="contenedor_login_imagen">
        <img src="{{ asset('assets/imagen/construccion-aybar-corp.jpg') }}" alt="" />
    </div>

    <div class="contenedor_login_formulario">
        <div class="login_formulario_centrar">

            <div class="login_formulario_arriba">
                <span>¿Ya tienes una cuenta?</span>
                <a href="{{ route('ingresar.cliente') }}">Ingresar</a>
            </div>

            <div class="login_formulario_logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/imagen/logo-aybar-corp-verde.png') }}" alt="">
                </a>
            </div>

            <h1 class="titulo_formulario">¡HOLA! CREA UNA CUENTA</h1>
            <p class="descripcion_formulario">Sigue los pasos correctamente.</p>

            @if (session('error'))
                <div class="g_alerta_error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if (!$cliente_encontrado)
                <div class="formulario_flex formulario">
                    <div class="g_margin_top_20">
                        <label>Ingresa tu DNI/RUC</label>
                        <input type="text" wire:model="dni" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                            class="form-control">
                        @error('dni')
                            <div class="mensaje_error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="g_margin_top_20 formulario_botones centrar">
                        <button wire:click="buscarCliente" class="guardar">Validar DNI</button>
                    </div>
                </div>
            @endif

            @if ($cliente_encontrado)
                @if (session('status'))
                    <div class="g_alerta_succes">
                        <i class="fa-solid fa-circle-check"></i>
                        ¡{{ $this->cliente_encontrado['apellidos_nombres'] }}!, {{ session('status') }}
                    </div>
                @endif

                <form wire:submit.prevent="registrar" class="formulario_flex formulario">

                    <div class="g_margin_top_20">
                        <label>Correo electrónico</label>
                        <input type="email" wire:model="email" required>
                        @error('email')
                            <div class="mensaje_error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="g_margin_top_20">
                        <label>Contraseña</label>
                        <input type="password" wire:model="password" required>
                        @error('password')
                            <div class="mensaje_error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="g_margin_top_20">
                        <label>Repetir contraseña</label>
                        <input type="password" wire:model="password_confirmation" required>
                    </div>

                    <div class="g_margin_top_20">
                        <label>
                            <input type="checkbox" wire:model="politica_uno">
                            <span>He leído y acepto el <a href="https://aybarcorp.com/tratamiento-de-datos-personales"
                                    target="_blank" rel="noopener noreferrer"> <u>Tratamiento de mis datos
                                        personales</u>.
                                </a></span>
                        </label>
                        @error('politica_uno')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label>
                            <input type="checkbox" wire:model="politica_dos">
                            <span>He leído y acepto la <a
                                    href="https://aybarcorp.com/politica-comunicaciones-comerciales" target="_blank"
                                    rel="noopener noreferrer"> <u>Política para envío de comunicaciones
                                        comerciales</u>.
                                </a></span>
                        </label>
                        @error('politica_dos')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="g_margin_top_20 formulario_botones centrar">
                        <button type="submit" class="guardar">Crear cuenta</button>
                    </div>

                </form>
            @endif

        </div>
    </div>
</div>
