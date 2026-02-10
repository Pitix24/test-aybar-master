@section('tituloPagina', 'Crear Ticket')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Nuevo Ticket</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.ticket.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i></a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="store" class="formulario">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel" x-data="{ activeTab: 'general' }">

                    <div class="g_tab_navegacion">
                        <div class="g_tab_botones">
                            <button type="button" @click="activeTab = 'general'"
                                :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'"
                                class="g_tab_boton">
                                <i class="fa-solid fa-building"></i> Información General
                            </button>

                            <button type="button" @click="activeTab = 'cliente'"
                                :class="activeTab === 'cliente' ? 'g_tab_active' : 'g_tab_inactive'"
                                class="g_tab_boton">
                                <i class="fa-solid fa-user-tie"></i> Cliente
                            </button>
                        </div>
                    </div>


                    <div x-show="activeTab === 'general'" x-transition class="g_tab_content">

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Unidad de Negocio <span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <select wire:model.live="unidad_negocio_id">
                                    <option value="">Seleccione...</option>
                                    @foreach($unidades as $u) <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Proyecto <span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <select wire:model.live="proyecto_id">
                                    <option value="">Seleccione...</option>
                                    @foreach($proyectos as $p) <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Canal <span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <select wire:model="canal_id">
                                    <option value="">Seleccione...</option>
                                    @foreach($canales as $c) <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('canal_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Tipo Solicitud <span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <select wire:model.live="tipo_solicitud_id">
                                    <option value="">Seleccione...</option>
                                    @foreach($tipos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_solicitud_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Subtipo</label>
                                <select wire:model="sub_tipo_solicitud_id">
                                    <option value="">General</option>
                                    @foreach($subtipos as $st) <option value="{{ $st->id }}">{{ $st->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Área Destino <span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <select wire:model.live="area_id">
                                    <option value="">Seleccione...</option>
                                    @foreach($areas as $ar) <option value="{{ $ar->id }}">{{ $ar->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('area_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Prioridad</label>
                                <select wire:model="prioridad_ticket_id">
                                    @foreach($prioridades as $pr) <option value="{{ $pr->id }}">{{ $pr->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Gestor Asignado</label>
                                <select wire:model="gestor_id">
                                    <option value="">Sin asignar</option>
                                    @foreach($gestores as $ge) <option value="{{ $ge->id }}">{{ $ge->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Estado Inicial</label>
                                <select wire:model="estado_ticket_id">
                                    @foreach($estados as $es) <option value="{{ $es->id }}">{{ $es->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="g_margin_bottom_10">
                            <label for="asunto_inicial">Asunto <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" id="asunto_inicial" wire:model.blur="asunto_inicial"
                                class="@error('asunto_inicial') input-error @enderror">
                            @error('asunto_inicial') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10">
                            <label for="descripcion_inicial">Descripción Detallada <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <textarea id="descripcion_inicial" wire:model.blur="descripcion_inicial" rows="6"
                                class="@error('descripcion_inicial') input-error @enderror"></textarea>
                            @error('descripcion_inicial') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        @if (!empty($lotes_agregados))
                            <h4 class="g_panel_titulo">Lotes vinculados</h4>
                            <div class="g_contenedor_tabla">
                                <table class="g_tabla">
                                    <thead>
                                        <tr>
                                            <th>Razón Social</th>
                                            <th>Proyecto</th>
                                            <th>Mz./Lt.</th>
                                            <th class="g_celda_centro">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lotes_agregados as $index => $l)
                                            <tr wire:key="lote-{{ $index }}">
                                                <td>{{ $l['razon_social'] }}</td>
                                                <td>{{ $l['proyecto'] }}</td>
                                                <td>{{ $l['numero_lote'] }}</td>
                                                <td class="g_celda_acciones g_celda_centro">
                                                    <button type="button" wire:click="quitarLote('{{ $l['id'] }}')"
                                                        class="g_accion_eliminar" title="Quitar">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    <div x-show="activeTab === 'slin'" x-transition class="g_tab_content">

                    </div>
                    <div class="formulario_botones g_margin_top_20">
                        <button type="submit" class="g_boton g_boton_primary" wire:loading.attr="disabled">
                            <i class="fa-solid fa-save"></i> Generar Ticket
                        </button>
                    </div>
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Cliente / Solicitante</h4>

                    @if (session('info'))
                    <div class="g_alerta g_alerta_info g_margin_bottom_10">{{ session('info') }}</div> @endif
                    @if (session('error'))
                    <div class="g_alerta g_alerta_danger g_margin_bottom_10">{{ session('error') }}</div> @endif
                    @if (session('success'))
                    <div class="g_alerta g_alerta_success g_margin_bottom_10">{{ session('success') }}</div> @endif

                    <div class="g_margin_bottom_15">
                        <label for="dni">DNI / RUC / CE <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <div class="g_fila_flex">
                            <input type="text" id="dni" wire:model.live="dni" placeholder="Ingrese documento..."
                                style="flex: 1;" {{ $ticket_padre_id ? 'disabled' : '' }}>
                            @if(!$ticket_padre_id)
                                <button type="button" wire:click="buscarCliente" class="g_boton g_boton_dark"
                                    wire:loading.attr="disabled" style="margin-left: 10px;">
                                    <i class="fa-solid fa-search" wire:loading.remove wire:target="buscarCliente"></i>
                                    <i class="fa-solid fa-spinner fa-spin" wire:loading wire:target="buscarCliente"></i>
                                </button>
                            @endif
                        </div>
                        @error('dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    @if($nombres)
                        <div class="g_margin_bottom_15">
                            <label>Nombre / Razón Social</label>
                            <p class="g_negrita" style="color: var(--primary);">{{ $nombres }}</p>
                            @if($origen)
                                <span class="g_badge g_badge_soft">{{ strtoupper($origen) }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- SELECCIÓN DE LOTE (MUESTRA SI HAY BÚSQUEDA) -->
                @if ($informaciones && $informaciones->isNotEmpty())
                    <div class="g_panel g_margin_top_20">
                        <h4 class="g_panel_titulo">Vincular Lote</h4>
                        <div class="g_margin_bottom_15">
                            <select wire:model.live="lote_id">
                                <option value="">Seleccione lote...</option>
                                @foreach ($informaciones as $lote)
                                    <option value="{{ $lote->id }}">
                                        {{ $lote->proyecto }} - {{ $lote->numero_lote }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" wire:click="agregarLote" class="g_boton g_boton_success g_full_width">
                            <i class="fa-solid fa-plus-circle"></i> Agregar Lote
                        </button>
                    </div>
                @endif

                <!-- PARTICIPANTES -->
                <div class="g_panel g_margin_top_20">
                    <h4 class="g_panel_titulo">Participantes (CC)</h4>

                    <div class="g_select_search">
                        <input type="text" wire:model.live.debounce.300ms="searchUser" class="g_select_search_input"
                            placeholder="Buscar usuario para copiar...">

                        @if(!empty($participantesDisponibles))
                            <div class="g_select_search_results">
                                @foreach($participantesDisponibles as $du)
                                    <div class="g_select_search_item" wire:click="addParticipant({{ $du->id }})">
                                        <div class="g_select_search_avatar">
                                            {{ $du->initials() }}
                                        </div>
                                        <div class="g_select_search_info">
                                            <span class="g_select_search_name">{{ $du->name }}</span>
                                            <span class="g_select_search_sub">{{ $du->email }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="g_select_search_selected_list">
                        @foreach($participantesSeleccionados as $su)
                            <div class="g_select_search_selected_item">
                                <div class="g_select_search_selected_info">
                                    <div class="g_select_search_selected_avatar">
                                        {{ $su->initials() }}
                                    </div>
                                    <span class="g_select_search_selected_name">{{ $su->name }}</span>
                                </div>
                                <button type="button" class="g_select_search_remove"
                                    wire:click="removeParticipant({{ $su->id }})" title="Quitar">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($ticketPadre)
                    <div class="g_panel g_margin_top_20">
                        <h4 class="g_panel_titulo">Ticket Padre</h4>
                        <p class="g_inferior">Asociado al ticket: <span
                                class="g_badge g_badge_light">#{{ $ticketPadre->id }}</span></p>
                        <p class="g_resumir_2 g_margin_top_10">{{ $ticketPadre->asunto_inicial }}</p>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>