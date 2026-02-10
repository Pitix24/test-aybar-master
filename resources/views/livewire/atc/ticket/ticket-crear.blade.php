@section('tituloPagina', 'Crear Ticket')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear ticket
            @if ($ticket_padre_id)
                asociado al ticket
                <a href="{{ route('admin.ticket.vista.editar', $ticket_padre_id) }}"
                    target="_blank">#{{ $ticket_padre_id }}</a>
            @endif
        </h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.ticket.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i></a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>


    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit="store" class="formulario g_panel" x-data="{ activeTab: 'general' }">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-building"></i> Información General
                        </button>

                        <button type="button" @click="activeTab = 'cliente'"
                            :class="activeTab === 'cliente' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
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
                                @foreach($unidades_negocios as $u) <option value="{{ $u->id }}">{{ $u->nombre }}
                                    </option>
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
                            <label>Canal <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
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
                                @foreach($tipos_solicitudes as $t) <option value="{{ $t->id }}">{{ $t->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_solicitud_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Subtipo</label>
                            <select wire:model="sub_tipo_solicitud_id">
                                <option value="">Seleccione...</option>
                                @foreach($sub_tipos_solicitudes as $st) <option value="{{ $st->id }}">
                                        {{ $st->nombre }}
                                    </option>
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

                <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
                    <div class="g_margin_bottom_10 g_columna_3">
                        <label for="cliente_id">
                            Cliente <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>

                        @if ($ticketPadre)
                            <input type="text" disabled value="{{ $ticketPadre->nombres ?? 'Sin asignar' }}">
                        @else
                            <input type="text" disabled value="{{ $cliente?->nombre ?? 'Sin asignar' }}">
                            @error('cliente_id')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div class="g_margin_bottom_10 g_columna_3">
                        <label>DNI</label>
                        <input type="text" disabled value="{{ $ticket->dni ?? 'Sin asignar' }}">
                    </div>

                    <div class="g_margin_bottom_10 g_columna_3">
                        <label>Correo</label>
                        <input type="text" wire:model.live="email">
                    </div>

                    <div class="g_margin_bottom_10 g_columna_3">
                        <label>Celular</label>
                        <input type="text" wire:model.live="celular">
                    </div>
                </div>

                <div class="formulario_botones g_margin_top_20">
                    <button type="submit" class="g_boton g_boton_primary" wire:loading.attr="disabled">
                        <i class="fa-solid fa-save"></i> Generar Ticket
                    </button>
                </div>
            </form>
        </div>

        <div class="g_columna_4 formulario">
            @if ($ticketPadre)
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Ticket padre</h4>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Empresa</label>
                            <input type="text" disabled value="{{ $ticketPadre->unidadNegocio->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Proyecto</label>
                            <input type="text" disabled value="{{ $ticketPadre->proyecto->nombre ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Área origen</label>
                            <input type="text" disabled value="{{ $ticketPadre->area->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Tipo solicitud</label>
                            <input type="text" disabled value="{{ $ticketPadre->tipoSolicitud->nombre ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Sub tipo solicitud</label>
                            <input type="text" disabled
                                value="{{ $ticketPadre->subTipoSolicitud->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Canal</label>
                            <input type="text" disabled value="{{ $ticketPadre->canal->nombre ?? 'Sin asignar' }}">
                        </div>
                    </div>
                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Cliente</label>
                            <input type="text" disabled value="{{ $ticketPadre->nombres ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label for="gestor_id">
                                Asignado <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="text" disabled value="{{ $ticketPadre->gestor->name ?? 'Sin asignar' }}">
                        </div>
                    </div>
                    <div class="g_fila">
                        <div class="g_columna_12">
                            <label>Asunto </label>
                            <textarea disabled>{{ $ticketPadre->asunto_inicial ?? 'Sin asunto' }}</textarea>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_12">
                            <label>Descripción </label>
                            <textarea disabled>{{ $ticketPadre->descripcion_inicial ?? 'Sin descripción' }}</textarea>
                        </div>
                    </div>

                    @if (!empty($ticketPadre->lotes))
                        <div class="g_fila">
                            <div class="g_columna_12">
                                <label>Lotes</label>

                                <table class="tabla_eliminar">
                                    <thead>
                                        <tr>
                                            <th>Razón Social</th>
                                            <th>Proyecto</th>
                                            <th>Mz./Lt.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ticketPadre->lotes as $index => $l)
                                            <tr class="sorteable_item" wire:key="lote-{{ $index }}">
                                                <td> {{ $l['razon_social'] }} </td>
                                                <td> {{ $l['proyecto'] }} </td>
                                                <td> {{ $l['numero_lote'] }} </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="g_panel">
                    @if (session('info'))
                        <div class="g_alerta_info">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ session('info') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="g_alerta_error">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="g_alerta_succes">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    <h4 class="g_panel_titulo">Cliente</h4>

                    <div class="g_margin_bottom_10">
                        <label for="dni">DNI/CE/RUC <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" id="dni" wire:model.live="dni"
                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" required>
                        @error('dni')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="formulario_botones g_margin_bottom_10">
                        <button wire:click="buscarCliente" class="guardar" wire:loading.attr="disabled"
                            wire:target="buscarCliente">
                            <span wire:loading.remove wire:target="buscarCliente">Buscar</span>
                            <span wire:loading wire:target="buscarCliente">Buscando...</span>
                        </button>
                    </div>

                    @if ($informaciones->isNotEmpty())
                        <h4 class="g_panel_titulo">Lotes</h4>

                        <div class="g_margin_bottom_10">
                            <select wire:model.live="lote_id">
                                <option value="">Seleccionar lote</option>

                                @foreach ($informaciones as $lote)
                                    <option value="{{ $lote->id }}">
                                        {{ $lote->razon_social }} - {{ $lote->proyecto }} -
                                        {{ $lote->numero_lote }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="formulario_botones">
                            <button wire:click="agregarLote" class="guardar" wire:loading.attr="disabled"
                                wire:target="agregarLote">
                                <span wire:loading.remove wire:target="agregarLote">Agregar</span>
                                <span wire:loading wire:target="agregarLote">Agregando...</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

</div>