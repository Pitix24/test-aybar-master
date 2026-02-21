<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Consultar clientes portal</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.cliente.vista.todo') }}" class="g_boton light">
                Inicio <i class="fa-solid fa-house"></i>
            </a>

            <a href="{{ route('erp.cliente.vista.todo') }}" class="g_boton dark">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </a>

            <a href="{{ route('erp.cliente.vista.ver', $dni) }}" class="g_boton secondary">
                Ver Movimientos <i class="fa-solid fa-right-left"></i></a>

            <button wire:click="resetFiltros" class="g_boton danger">
                Refresh Campos <i class="fa-solid fa-rotate-left"></i>
            </button>
        </div>
    </div>

    <div class="formulario g_gap_pagina">
        <div class="g_fila">
            <div class="g_columna_8 ">
                <div class="g_panel">
                    @if (session('info'))
                        <div class="g_alerta info">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ session('info') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="g_alerta error">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="g_alerta success">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    <h4 class="g_panel_titulo">Cliente</h4>

                    <div class="g_margin_bottom_10">
                        <label for="dni">DNI/CE/RUC <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" id="dni" wire:model.defer="dni"
                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" required>

                        @error('dni')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="formulario_botones g_margin_bottom_10">
                        <button wire:click="buscarCliente" class="g_boton guardar" wire:loading.attr="disabled"
                            wire:target="buscarCliente">
                            <span wire:loading.remove wire:target="buscarCliente">Buscar</span>
                            <span wire:loading wire:target="buscarCliente">Buscando...</span>
                        </button>
                    </div>
                </div>
            </div>
            @if ($cliente_encontrado)
                <div class="g_columna_4 g_gap_pagina">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo">Registrar cliente</h4>

                        <div class="g_margin_bottom_10">
                            <label>Email</label>
                            <input type="text" disabled value="{{ $cliente_encontrado['correo'] }}">
                        </div>

                        <div class="g_margin_bottom_10">
                            <label>Celular</label>
                            <input type="text" disabled value="{{ $cliente_encontrado['telefono'] }}">
                        </div>

                        {{--<div class="g_margin_bottom_10">
                            <label for="email">Email <span class="obligatorio">*</span></label>
                            <input type="email" id="email" wire:model="email" required>
                            @error('email')
                            <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>--}}

                        @if ($mostrar_form_email)
                            <div class="formulario_botones">
                                <button wire:click="store" class="g_boton guardar" wire:loading.attr="disabled"
                                    wire:target="store">
                                    <span wire:loading.remove wire:target="store">Registrar cliente</span>
                                    <span wire:loading wire:target="store">Registrando...</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        @if ($cliente_encontrado)
            @livewire(
                'cliente.lote.lote-todo',
                [
                    'clienteEncontradoCrear' => $cliente_encontrado,
                    'razonesSocialesCrear' => $razones_sociales,
                ],
                key($dni)
            )
        @endif
    </div>

</div>