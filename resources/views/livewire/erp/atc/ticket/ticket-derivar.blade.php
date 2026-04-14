<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store" message="Procesando derivación..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Derivar Ticket</h2>

        @can('ticket.vista-editar')
            <div class="cabecera_titulo_botones">
                <a href="{{ route('erp.ticket.vista.editar', $ticket->id) }}" class="g_boton dark">
                    <i class="fa-solid fa-arrow-left"></i> Regresar al ticket
                </a>
            </div>
        @endcan
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel" x-data="{ activeTab: 'informacion' }">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" @click="activeTab = 'informacion'"
                            :class="activeTab === 'informacion' ? 'g_tab_active' : 'g_tab_inactive'"
                            class="g_tab_boton">
                            <i class="fa-solid fa-share-from-square"></i> Información
                        </button>

                        <button type="button" @click="activeTab = 'derivaciones'"
                            :class="activeTab === 'derivaciones' ? 'g_tab_active' : 'g_tab_inactive'"
                            class="g_tab_boton">
                            <i class="fa-solid fa-route"></i> Derivaciones
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'informacion'" x-transition class="g_tab_content formulario">
                    <form wire:submit="store">
                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_10">
                                <label>Área inicial</label>
                                <input type="text" disabled value="{{ $ticket->area->nombre ?? 'Sin asignar' }}">
                            </div>

                            <div class="g_columna_6 g_margin_bottom_10">
                                <label>Gestor inicial</label>
                                <input type="text" disabled value="{{ $ticket->gestor->name ?? 'Sin asignar' }}">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="a_area_id">Área destino <span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <select id="a_area_id" wire:model.live="a_area_id"
                                    class="@error('a_area_id') input-error @enderror">
                                    <option value="" selected disabled>Seleccionar área destino</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('a_area_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="gestor_id">Gestor destino<span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <select id="gestor_id" wire:model.live="gestor_id"
                                    class="@error('gestor_id') input-error @enderror">
                                    <option value="" selected disabled>Sin asignar</option>
                                    @foreach ($gestores as $usuario)
                                        <option value="{{ $usuario->id }}">
                                            {{ $usuario->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_columna_12 g_margin_bottom_10">
                                <label for="motivo">Motivo<span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <textarea id="motivo" wire:model.live="motivo" rows="4"
                                    class="@error('motivo') input-error @enderror"></textarea>
                                @error('motivo') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="formulario_botones">
                            @can('ticket.accion-derivar')
                                <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="store">
                                        <i class="fa-solid fa-route"></i> Derivar
                                    </span>
                                    <span wire:loading wire:target="store">
                                        <i class="fa-solid fa-spinner fa-spin"></i> Derivando...
                                    </span>
                                </button>
                            @endcan

                            <button type="button" class="g_boton cancelar" onclick="history.back()">
                                <i class="fa-solid fa-times"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <div x-show="activeTab === 'derivaciones'" x-transition class="g_tab_content">
                    @livewire('erp.atc.ticket.ticket-derivados', ['ticket' => $ticket])
                </div>
            </div>
        </div>

        <div class="g_columna_4 formulario">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Ticket padre</h4>

                <div class="g_margin_bottom_10">
                    @can('ticket.vista-ver')
                        <a href="{{ route('erp.ticket.vista.ver', $ticket->id) }}" class="g_boton warning">
                            <i class="fa-solid fa-eye"></i> Ver ticket
                        </a>
                    @endcan

                    @can('ticket.vista-editar')
                        <a href="{{ route('erp.ticket.vista.editar', $ticket->id) }}" class="g_boton info">
                            <i class="fa-solid fa-pencil"></i> Editar ticket
                        </a>
                    @endcan
                </div>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Empresa</label>
                        <input type="text" disabled value="{{ $ticket->unidadNegocio->nombre ?? 'Sin asignar' }}">
                    </div>

                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Proyecto</label>
                        <input type="text" disabled value="{{ $ticket->proyecto->nombre ?? 'Sin asignar' }}">
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Área origen</label>
                        <input type="text" disabled value="{{ $ticket->area->nombre ?? 'Sin asignar' }}">
                    </div>

                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Tipo solicitud</label>
                        <input type="text" disabled value="{{ $ticket->tipoSolicitud->nombre ?? 'Sin asignar' }}">
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Sub tipo solicitud</label>
                        <input type="text" disabled value="{{ $ticket->subTipoSolicitud->nombre ?? 'Sin asignar' }}">
                    </div>

                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Canal</label>
                        <input type="text" disabled value="{{ $ticket->canal->nombre ?? 'Sin asignar' }}">
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Cliente</label>
                        <input type="text" disabled value="{{ $ticket->nombres ?? 'Sin asignar' }}">
                    </div>

                    <div class="g_margin_bottom_10 g_columna_6">
                        <label for="gestor_id">
                            Asignado <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>
                        <input type="text" disabled value="{{ $ticket->gestor->name ?? 'Sin asignar' }}">
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_columna_12 g_margin_bottom_10">
                        <label>Asunto </label>
                        <textarea disabled>{{ $ticket->asunto_inicial ?? 'Sin asunto' }}</textarea>
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_columna_12 g_margin_bottom_10">
                        <label>Descripción </label>
                        <textarea disabled>{{ $ticket->descripcion_inicial ?? 'Sin descripción' }}</textarea>
                    </div>
                </div>

                @if (!empty($ticket->lotes))
                    <div class="g_fila">
                        <div class="g_columna_12">
                            <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Lotes vinculados</h4>

                            <div class="g_contenedor_tabla">
                                <table class="g_tabla">
                                    <thead>
                                        <tr>
                                            <th>Razón Social</th>
                                            <th>Proyecto</th>
                                            <th>Mz./Lt.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ticket->lotes as $index => $l)
                                            <tr wire:key="lote-parent-{{ $index }}">
                                                <td> {{ $l['razon_social'] }} </td>
                                                <td> {{ $l['proyecto'] }} </td>
                                                <td> {{ $l['numero_lote'] }} </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>