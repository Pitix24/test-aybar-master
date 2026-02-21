<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update" message="Guardando cambios..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Solicitud de Letra Digital</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.solicitar-letra-digital.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>

            @can('solicitud-digitalizar-letra.ejecutar-cron-letra')
                @if($solicitud->estado_solicitud_digitalizar_letra_id === \App\Models\EstadoSolicitudDigitalizarLetra::id(\App\Models\EstadoSolicitudDigitalizarLetra::PENDIENTE))
                    <button wire:click="enviarIndividual" class="g_boton primary" wire:loading.attr="disabled"
                        wire:target="enviarIndividual">
                        <span wire:loading.remove wire:target="enviarIndividual">Enviar a Digitalizar <i
                                class="fa-solid fa-file-export"></i></span>
                        <span wire:loading wire:target="enviarIndividual">Enviando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                @endif
            @endcan

            @can('solicitud-digitalizar-letra.validar-cron-letra')
                @if($solicitud->estado_solicitud_digitalizar_letra_id === \App\Models\EstadoSolicitudDigitalizarLetra::id(\App\Models\EstadoSolicitudDigitalizarLetra::ENVIADO))
                    <button wire:click="validarIndividual" class="g_boton warning" wire:loading.attr="disabled"
                        wire:target="validarIndividual">
                        <span wire:loading.remove wire:target="validarIndividual">Validar en Cavali <i
                                class="fa-solid fa-circle-check"></i></span>
                        <span wire:loading wire:target="validarIndividual">Validando... <i
                                class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                @endif
            @endcan
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_12">
            <form wire:submit="update" class="formulario g_panel" x-data="{ activeTab: 'general' }">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-file-invoice"></i> Información General
                        </button>

                        <button type="button" @click="activeTab = 'cliente'"
                            :class="activeTab === 'cliente' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-user"></i> Cliente
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Empresa</label>
                            <input type="text" disabled value="{{ $solicitud->unidadNegocio->razon_social ?? '—' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Proyecto</label>
                            <input type="text" disabled value="{{ $solicitud->proyecto->nombre ?? '—' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Estado Solicitud <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="estado_solicitud_digitalizar_letra_id"
                                class="@error('estado_solicitud_digitalizar_letra_id') input-error @enderror">
                                <option value="">Seleccionar...</option>
                                @foreach($estados as $es)
                                    <option value="{{ $es->id }}">{{ $es->nombre }}</option>
                                @endforeach
                            </select>
                            @error('estado_solicitud_digitalizar_letra_id') <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_3">
                            <label>Etapa</label>
                            <input type="text" disabled value="{{ $solicitud->etapa }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_3">
                            <label>Mz. / Lt.</label>
                            <input type="text" disabled value="{{ $solicitud->manzana }} / {{ $solicitud->lote }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_3">
                            <label>Código Cuota</label>
                            <input type="text" disabled value="{{ $solicitud->codigo_cuota }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_3">
                            <label>Importe Cuota</label>
                            <input type="text" disabled value="{{ number_format($solicitud->importe_cuota, 2) }}">
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Nombre Cliente</label>
                            <input type="text" disabled value="{{ $solicitud->userCliente->name ?? '—' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>DNI</label>
                            <input type="text" disabled
                                value="{{ $solicitud->userCliente->perfilCliente?->dni ?? '—' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Correo</label>
                            <input type="text" disabled value="{{ $solicitud->userCliente->email ?? '—' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Código Cliente</label>
                            <input type="text" disabled value="{{ $solicitud->codigo_cliente }}">
                        </div>
                    </div>
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled"
                        wire:target="update">
                        <span wire:loading.remove wire:target="update">
                            <i class="fa-solid fa-pencil"></i> Guardar Cambios
                        </span>
                        <span wire:loading wire:target="update">
                            <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                        </span>
                    </button>

                    <a href="{{ route('erp.solicitar-letra-digital.vista.todo') }}" class="g_boton g_boton_cancelar">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>