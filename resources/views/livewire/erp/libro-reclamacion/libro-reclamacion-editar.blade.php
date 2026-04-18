<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="buscarCliente,agregarLote,quitarLote,update,eliminarLibroTicketOn" message="Guardando cambios..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Ticket Reclamacion #{{ $ticket_model->ticket }}</h2>

        <div class="cabecera_titulo_botones">
            @can('ticket-libro-reclamacion.ver')
                <a href="{{ route('erp.libro-reclamacion.vista.ver', $ticket_model->ticket) }}" class="g_boton warning">
                    Ver <i class="fa-solid fa-eye"></i>
                </a>
            @endcan

            @can('ticket-libro-reclamacion.lista')
                <a href="{{ route('erp.libro-reclamacion.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            @can('ticket.ver')
                @if ($ticket_model->ticketRelacionado)
                    <a href="{{ route('erp.ticket.vista.ver', $ticket_model->ticketRelacionado->id) }}" class="g_boton warning">
                        Ver Ticket <i class="fa-solid fa-ticket"></i>
                    </a>
                @endif
            @endcan
        </div>
    </div>

    <form wire:submit.prevent="update" class="formulario g_panel" x-data="{ activeTab: 'general' }">
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

                <button type="button" @click="activeTab = 'asunto'"
                    :class="activeTab === 'asunto' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                    <i class="fa-solid fa-comment-dots"></i> Asunto y Lotes
                </button>

                <button type="button" @click="activeTab = 'nota'"
                    :class="activeTab === 'nota' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                    <i class="fa-solid fa-note-sticky"></i> Nota y Observaciones
                </button>

                <button type="button" @click="activeTab = 'auditoria'"
                    :class="activeTab === 'auditoria' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                    <i class="fa-solid fa-shield-halved"></i> Auditoría
                </button>
            </div>
        </div>

        <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
            <div class="g_fila">
                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Código</label>
                    <input type="text" value="{{ $ticket_model->codigo }}" disabled>
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Estado Legal <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <select wire:model.live="estado_libro_reclamaciones_id" class="@error('estado_libro_reclamaciones_id') input-error @enderror">
                        <option value="">Seleccione...</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->id }}">{{ str_replace('_', ' ', $estado->nombre) }}</option>
                        @endforeach
                    </select>
                    @error('estado_libro_reclamaciones_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Clasificación</label>
                    <input type="text" value="{{ $clasificacion === 'PENDIENTE_REVISION' ? 'PENDIENTE VERIFICACION' : str_replace('_', ' ', $clasificacion) }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Ticket</label>
                    <input type="text" value="{{ $ticket_model->ticket ?: 'N/D' }}" disabled>
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Unidad de negocio <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <select wire:model.live="unidad_negocio_id" class="@error('unidad_negocio_id') input-error @enderror">
                        <option value="">Seleccione...</option>
                        @foreach ($unidades as $unidad)
                            <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                        @endforeach
                    </select>
                    @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Proyecto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <select wire:model.live="proyecto_id" class="@error('proyecto_id') input-error @enderror">
                        <option value="">Seleccione...</option>
                        @foreach ($proyectos as $proyecto)
                            <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                        @endforeach
                    </select>
                    @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Origen</label>
                    <input type="text" value="{{ $ticket_model->esOrigenErp() ? 'ERP - Registro Interno' : 'Formulario web' }}" disabled>
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Gestor</label>
                    <select wire:model.live="gestor_id" class="@error('gestor_id') input-error @enderror">
                        <option value="">Sin asignar</option>
                        @foreach ($gestores as $gestor)
                            <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                        @endforeach
                    </select>
                    @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Subtipo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <select wire:model.live="tipo_pedido" class="@error('tipo_pedido') input-error @enderror">
                        <option value="">Seleccione...</option>
                        <option value="RECLAMO">RECLAMO</option>
                        <option value="QUEJA">QUEJA</option>
                    </select>
                    @error('tipo_pedido') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_12 g_margin_bottom_10">
                    <label>Asignado desde</label>
                    <input type="text" value="{{ optional($ticket_model->assigned_at)->format('d/m/Y H:i') ?: 'Sin asignación' }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_12 g_margin_bottom_10">
                    <label>Ticket vinculado</label>
                    <input type="text" value="{{ $ticket_model->ticketRelacionado ? ('#' . $ticket_model->ticketRelacionado->id) : 'Sin vincular' }}" disabled>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
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

            <div class="g_fila">
                <div class="g_columna_8 g_margin_bottom_10">
                    <label>DNI / CE / RUC (opcional)</label>
                    <input type="text" wire:model.live="dni" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                        class="@error('dni') input-error @enderror">
                    @error('dni') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>&nbsp;</label>
                    <button type="button" wire:click="buscarCliente" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="buscarCliente"><i class="fa-solid fa-search"></i> Buscar</span>
                        <span wire:loading wire:target="buscarCliente">Buscando...</span>
                    </button>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Nombre del cliente <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" wire:model.blur="cliente_nombre" class="@error('cliente_nombre') input-error @enderror">
                    @error('cliente_nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Email</label>
                    <input type="email" wire:model.blur="cliente_email" class="@error('cliente_email') input-error @enderror">
                    @error('cliente_email') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Celular</label>
                    <input type="text" wire:model.blur="cliente_celular" class="@error('cliente_celular') input-error @enderror">
                    @error('cliente_celular') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Dirección</label>
                    <input type="text" wire:model.blur="cliente_direccion" class="@error('cliente_direccion') input-error @enderror">
                    @error('cliente_direccion') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Tipo de documento</label>
                    <input type="text" value="{{ $cliente_tipo_documento ?: 'N/D' }}" disabled>
                </div>

                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Documento del cliente</label>
                    <input type="text" value="{{ $cliente_documento ?: 'N/D' }}" disabled>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'asunto'" x-transition class="g_tab_content">
            <div class="g_margin_bottom_10">
                <label>Asunto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <textarea wire:model.blur="asunto" rows="5" class="@error('asunto') input-error @enderror"></textarea>
                @error('asunto') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

            @if ($informaciones->isNotEmpty())
                <div class="g_margin_bottom_10">
                    <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Lotes disponibles</h4>

                    <div class="g_fila">
                        <div class="g_columna_8 g_margin_bottom_10">
                            <select wire:model.live="lote_id">
                                <option value="">Seleccionar lote</option>
                                @foreach ($informaciones as $lote)
                                    <option value="{{ $lote->id }}">
                                        {{ $lote->razon_social }} - {{ $lote->proyecto }} - {{ $lote->numero_lote }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="g_columna_4 g_margin_bottom_10">
                            <button type="button" wire:click="agregarLote" class="g_boton guardar" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="agregarLote"><i class="fa-solid fa-plus"></i> Agregar</span>
                                <span wire:loading wire:target="agregarLote">Agregando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if (! empty($lotes_agregados))
                <div class="g_margin_bottom_10">
                    <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Lotes seleccionados</h4>

                    <div class="g_contenedor_tabla">
                        <table class="g_tabla">
                            <thead>
                                <tr>
                                    <th>Razón Social</th>
                                    <th>Proyecto</th>
                                    <th>Mz./Lt.</th>
                                    <th>Estado</th>
                                    <th class="g_celda_centro">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lotes_agregados as $lote)
                                    <tr wire:key="lote-{{ $lote['id'] }}">
                                        <td>{{ $lote['razon_social'] ?? 'N/D' }}</td>
                                        <td>{{ $lote['proyecto'] ?? 'N/D' }}</td>
                                        <td>{{ $lote['numero_lote'] ?? 'N/D' }}</td>
                                        <td>{{ $lote['estado_lote'] ?? 'N/D' }}</td>
                                        <td class="g_celda_acciones g_celda_centro">
                                            <button type="button" wire:click="quitarLote('{{ $lote['id'] }}')" class="g_boton danger" title="Quitar">
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

        <div x-show="activeTab === 'nota'" x-transition class="g_tab_content">
            <div class="g_margin_bottom_10">
                <label>Observaciones internas</label>
                <textarea wire:model.blur="observaciones_internas" rows="5" class="@error('observaciones_internas') input-error @enderror"></textarea>
                @error('observaciones_internas') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

            <div class="g_fila">
                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Titulo de nota</label>
                    <input type="text" wire:model.blur="nota_fuente_titulo" class="@error('nota_fuente_titulo') input-error @enderror">
                    @error('nota_fuente_titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_columna_6 g_margin_bottom_10">
                    <label>Fecha de nota</label>
                    <input type="text" wire:model.blur="nota_fuente_fecha" class="@error('nota_fuente_fecha') input-error @enderror">
                    @error('nota_fuente_fecha') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_margin_bottom_10">
                <label>Nota fuente</label>
                <textarea rows="8" disabled>{{ $nota_fuente ?: 'Sin nota fuente.' }}</textarea>
            </div>
        </div>

        <div x-show="activeTab === 'auditoria'" x-transition class="g_tab_content">
            <div class="g_fila">
                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Creado</label>
                    <input type="text" value="{{ optional($ticket_model->created_at)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Actualizado</label>
                    <input type="text" value="{{ optional($ticket_model->updated_at)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Asignado</label>
                    <input type="text" value="{{ optional($ticket_model->assigned_at)->format('d/m/Y H:i') ?: 'N/D' }}" disabled>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Creado por</label>
                    <input type="text" value="{{ $ticket_model->creador?->name ?: 'N/D' }}" disabled>
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Actualizado por</label>
                    <input type="text" value="{{ $ticket_model->actualizador?->name ?: 'N/D' }}" disabled>
                </div>

                <div class="g_columna_4 g_margin_bottom_10">
                    <label>Eliminado por</label>
                    <input type="text" value="{{ $ticket_model->eliminador?->name ?: 'N/D' }}" disabled>
                </div>
            </div>
        </div>

        <div class="formulario_botones">
            @can('ticket-libro-reclamacion.editar')
                <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="update"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</span>
                    <span wire:loading wire:target="update"><i class="fa-solid fa-spinner fa-spin"></i> Guardando...</span>
                </button>
            @endcan

            @can('ticket-libro-reclamacion.eliminar')
                <button type="button" class="g_boton danger" onclick="alertaEliminarLibroTicket()">
                    Eliminar <i class="fa-solid fa-trash"></i>
                </button>
            @endcan

            <button type="button" class="g_boton cancelar" onclick="history.back()">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        </div>
    </form>

    @script
    <script>
        window.alertaEliminarLibroTicket = function () {
            Swal.fire({
                title: '¿Eliminar ticket?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarLibroTicketOn();
                }
            });
        }
    </script>
    @endscript
</div>
