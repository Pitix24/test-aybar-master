<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscarCliente, agregarLote, quitarLote, addParticipant, removeParticipant, store"
        message="Guardando cambios..." />
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear ticket
            @can('ticket.vista-ver')
                @if ($ticket_padre_id)
                    <span>Asociado al ticket: <a href="{{ route('erp.ticket.vista.editar', $ticket_padre_id) }}" target="_blank"
                            class="g_negrita">#{{ $ticket_padre_id }}</a></span>
                @endif
            @endcan
        </h2>

        <div class="cabecera_titulo_botones">
            @can('ticket.vista-lista')
                <a href="{{ route('erp.ticket.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
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
                            <label>Unidad de Negocio <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="unidad_negocio_id"
                                class="@error('unidad_negocio_id') input-error @enderror">
                                <option value="">Seleccione...</option>
                                @foreach($unidades_negocios as $u)
                                    <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                                @endforeach
                            </select>
                            @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Proyecto <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="proyecto_id" class="@error('proyecto_id') input-error @enderror">
                                <option value="">Seleccione...</option>
                                @foreach($proyectos as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                @endforeach
                            </select>
                            @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Área Destino <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="area_id" class="@error('area_id') input-error @enderror">
                                <option value="">Seleccione...</option>
                                @foreach($areas as $ar)
                                    <option value="{{ $ar->id }}">{{ $ar->nombre }}</option>
                                @endforeach
                            </select>
                            @error('area_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Tipo Solicitud <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="tipo_solicitud_id"
                                class="@error('tipo_solicitud_id') input-error @enderror">
                                <option value="">Seleccione...</option>
                                @foreach($tipos_solicitudes as $t)
                                    <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                            @error('tipo_solicitud_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Subtipo</label>
                            <select wire:model.live="sub_tipo_solicitud_id">
                                <option value="">Seleccione...</option>
                                @foreach($sub_tipos_solicitudes as $st)
                                    <option value="{{ $st->id }}">{{ $st->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Canal <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="canal_id" class="@error('canal_id') input-error @enderror">
                                <option value="">Seleccione...</option>
                                @foreach($canales as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                            @error('canal_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Prioridad <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="prioridad_ticket_id"
                                class="@error('prioridad_ticket_id') input-error @enderror">
                                <option value="">Seleccionar...</option>
                                @foreach($prioridades as $pr)
                                    <option value="{{ $pr->id }}">{{ $pr->nombre }}</option>
                                @endforeach
                            </select>
                            @error('prioridad_ticket_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Gestor Asignado <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="gestor_id" class="@error('gestor_id') input-error @enderror">
                                <option value="">Sin asignar</option>
                                @foreach($gestores as $ge)
                                    <option value="{{ $ge->id }}">{{ $ge->name }}</option>
                                @endforeach
                            </select>
                            @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Estado</label>
                            <input type="text" value="Nuevo" disabled>
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
                        <div class="g_margin_bottom_10">
                            <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Lotes vinculados</h4>

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
                                                        class="g_boton danger" title="Quitar">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Nombre del Cliente</label>
                            <input type="text" wire:model.blur="nombres">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>DNI/CE/RUC</label>
                            <input type="text" disabled value="{{ $dni ?? 'No identificado' }}"
                                class="g_input_disabled">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Email</label>
                            <input type="text" wire:model.blur="email">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Celular</label>
                            <input type="text" wire:model.blur="celular">
                        </div>
                    </div>
                </div>

                <div class="formulario_botones">
                    @can('ticket.accion-crear')
                        <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="store">
                                <i class="fa-solid fa-save"></i> Crear
                            </span>
                            <span wire:loading wire:target="store">
                                <i class="fa-solid fa-spinner fa-spin"></i> Creando...
                            </span>
                        </button>
                    @endcan

                    <button type="button" class="g_boton cancelar" onclick="history.back()">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>

        <div class="g_columna_4 formulario">
            @if ($ticketPadre)
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Ticket padre</h4>

                    <div class="g_margin_bottom_10">
                        @can('ticket.vista-ver')
                            <a href="{{ route('erp.ticket.vista.ver', $ticketPadre->id) }}" class="g_boton warning">
                                <i class="fa-solid fa-eye"></i> Ver ticket
                            </a>
                        @endcan

                        @can('ticket.vista-editar')
                            <a href="{{ route('erp.ticket.vista.editar', $ticketPadre->id) }}" class="g_boton info">
                                <i class="fa-solid fa-pencil"></i> Editar ticket
                            </a>
                        @endcan
                    </div>

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
                            <textarea disabled
                                rows="5">{{ $ticketPadre->descripcion_inicial ?? 'Sin descripción' }}</textarea>
                        </div>
                    </div>

                    @if (!empty($ticketPadre->lotes))
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
                                            @foreach ($ticketPadre->lotes as $index => $l)
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
            @else
                <div class="g_panel">
                    @if (session('info'))
                        <div class="g_alerta info">
                            <i class="fa-solid fa-circle-info"></i>
                            {{ session('info') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="g_alerta danger">
                            <i class="fa-solid fa-circle-exclamation"></i>
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
                        <input type="text" id="dni" wire:model.live="dni"
                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" required
                            class="@error('dni') input-error @enderror">
                        @error('dni')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="formulario_botones g_margin_bottom_10">
                        <button wire:click="buscarCliente" class="g_boton guardar" wire:loading.attr="disabled"
                            wire:target="buscarCliente">
                            <span wire:loading.remove wire:target="buscarCliente"><i class="fa-solid fa-search"></i>
                                Buscar</span>
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
                            <button wire:click="agregarLote" class="g_boton guardar" wire:loading.attr="disabled"
                                wire:target="agregarLote">
                                <span wire:loading.remove wire:target="agregarLote"><i class="fa-solid fa-plus"></i>
                                    Agregar</span>
                                <span wire:loading wire:target="agregarLote">Agregando...</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        $wire.on('confirmarTicketSinDatos', () => {
            Swal.fire({
                title: '¿Continuar sin datos?',
                text: "Estás creando un ticket sin email o celular, ¿deseas continuar?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'No, completar datos'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.store(true);
                }
            });
        });
    </script>
    @endscript
</div>