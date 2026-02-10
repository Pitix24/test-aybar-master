<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store, adjuntar, eliminarTicketOn, eliminarArchivo"
        message="Guardando cambios..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar ticket</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.ticket.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <a href="{{ route('erp.ticket.vista.crear', $ticket->id) }}" class="g_boton g_boton_primary">
                Ticket asociado <i class="fa-solid fa-square-plus"></i></a>

            <a href="{{ route('erp.ticket.vista.derivar', $ticket->id) }}" class="g_boton g_boton_secondary">
                Derivar <i class="fa-solid fa-route"></i>
            </a>

            <button type="button" class="g_boton g_boton_danger" onclick="alertaEliminarTicket()">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>

            <button type="button" class="g_boton g_boton_info" wire:click="$dispatch('toggleChat')">
                Chat <i class="fa-solid fa-comments"></i>
            </button>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
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

                        <button type="button" @click="activeTab = 'participantes'"
                            :class="activeTab === 'participantes' ? 'g_tab_active' : 'g_tab_inactive'"
                            class="g_tab_boton">
                            <i class="fa-solid fa-users"></i> Participantes
                        </button>

                        <button type="button" @click="activeTab = 'derivaciones'"
                            :class="activeTab === 'derivaciones' ? 'g_tab_active' : 'g_tab_inactive'"
                            class="g_tab_boton">
                            <i class="fa-solid fa-route"></i> Derivaciones
                        </button>

                        <button type="button" @click="activeTab = 'historial'"
                            :class="activeTab === 'historial' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-clock-rotate-left"></i> Historial
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Empresa</label>
                            <input type="text" disabled value="{{ $ticket->unidadNegocio->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Proyecto</label>
                            <input type="text" disabled value="{{ $ticket->proyecto->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Área origen</label>
                            <input type="text" disabled value="{{ $ticket->area->nombre ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Tipo solicitud</label>
                            <input type="text" disabled value="{{ $ticket->tipoSolicitud->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Sub tipo solicitud</label>
                            <input type="text" disabled
                                value="{{ $ticket->subTipoSolicitud->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Canal</label>
                            <input type="text" disabled value="{{ $ticket->canal->nombre ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Prioridad</label>
                            <input type="text" disabled value="{{ $ticket->prioridad->nombre ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Gestor Asignado</label>
                            <input type="text" disabled value="{{ $ticket->gestor->name ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_4">
                            <label>Estado <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model.live="estado_ticket_id"
                                class="@error('estado_ticket_id') input-error @enderror">
                                <option value="">Seleccionar...</option>
                                @foreach($estados as $es)
                                    <option value="{{ $es->id }}">{{ $es->nombre }}</option>
                                @endforeach
                            </select>
                            @error('estado_ticket_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Asunto inicial </label>
                        <textarea disabled>{{ $ticket->asunto_inicial ?? 'Sin asunto' }}</textarea>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Descripción inicial </label>
                        <textarea disabled rows="6">{{ $ticket->descripcion_inicial ?? 'Sin descripción' }}</textarea>
                    </div>

                    @if (!empty($ticket->lotes))
                        <div class="g_margin_bottom_10">
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

                <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Cliente</label>
                            <input type="text" disabled value="{{ $ticket->nombres ?? 'Sin asignar' }}">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>DNI</label>
                            <input type="text" disabled value="{{ $ticket->dni ?? 'Sin asignar' }}">
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Correo</label>
                            <input type="text" wire:model.live="email">
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Celular</label>
                            <input type="text" wire:model.live="celular">
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'participantes'" x-transition class="g_tab_content">
                    <div class="g_margin_bottom_10">
                        <label for="searchUser">Buscar y agregar participantes</label>
                        <div class="g_select_search">
                            <div class="g_posicion_relativa">
                                <input type="text" id="searchUser" wire:model.live="searchUser" autocomplete="off"
                                    class="g_select_search_input">
                            </div>

                            @if(!empty($participantesDisponibles))
                                <div class="g_select_search_results">
                                    @foreach($participantesDisponibles as $user)
                                        <div class="g_select_search_item" wire:click="addParticipant({{ $user->id }})">
                                            <span>{{ $user->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <h4 class="g_panel_titulo"><i class="fa-solid fa-users-gear"></i> Lista de participantes</h4>
                        <div class="g_contenedor_tabla">
                            <table class="g_tabla">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th class="g_celda_centro">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($participantesSeleccionados as $part)
                                        <tr wire:key="part-{{ $part->id }}">
                                            <td>
                                                <strong>{{ $part->name }}</strong>
                                            </td>
                                            <td>{{ $part->email }}</td>
                                            <td class="g_celda_acciones g_celda_centro">
                                                <button type="button" wire:click="removeParticipant({{ $part->id }})"
                                                    class="g_accion_eliminar" title="Quitar participante">
                                                    <i class="fa-solid fa-user-minus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="g_celda_vacia">No hay participantes agregados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'derivaciones'" x-transition class="g_tab_content">
                    <div class="g_margin_bottom_10 g_contenedor_tabla">
                        <table class="g_tabla">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>De Área</th>
                                    <th>A Área</th>
                                    <th>Deriva</th>
                                    <th>Recibe</th>
                                    <th>Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($derivados as $der)
                                    <tr wire:key="der-{{ $der->id }}">
                                        <td class="g_negrita">{{ $der->created_at->format('d/m H:i') }}</td>
                                        <td>{{ $der->deArea->nombre ?? 'N/A' }}</td>
                                        <td><span class="g_badge g_badge_primary">{{ $der->aArea->nombre ?? 'N/A' }}</span>
                                        </td>
                                        <td><small>{{ $der->usuarioDeriva->name ?? 'N/A' }}</small></td>
                                        <td><small>{{ $der->usuarioRecibe->name ?? 'N/A' }}</small></td>
                                        <td>{{ $der->motivo ?? 'Sin motivo' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="g_celda_vacia">No hay derivaciones registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="activeTab === 'historial'" x-transition class="g_tab_content">
                    <div class="g_margin_bottom_10 g_contenedor_tabla">
                        <table class="g_tabla">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Detalle</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($historial as $item)
                                    <tr wire:key="hist-{{ $item->id }}">
                                        <td class="g_negrita">{{ $item->created_at->format('d/m H:i') }}</td>
                                        <td>{{ $item->usuarioHistorial->name ?? 'Sistema' }}</td>
                                        <td><span class="g_badge g_badge_light">{{ $item->accion }}</span></td>
                                        <td style="font-size: 0.85rem;">
                                            @foreach (explode(' | ', $item->detalle) as $linea)
                                                <div>{{ $linea }}</div>
                                            @endforeach
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="g_celda_vacia">Sin movimientos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled"
                        wire:target="update">
                        <span wire:loading.remove wire:target="update">
                            <i class="fa-solid fa-pencil"></i> Actualizar
                        </span>
                        <span wire:loading wire:target="update">
                            <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                        </span>
                    </button>

                    <a href="{{ route('erp.ticket.vista.todo') }}" class="g_boton g_boton_cancelar">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>

        <div class="g_columna_4 g_gap_pagina">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-cloud-arrow-up"></i> Nuevo Adjunto</h4>

                <div class="formulario">
                    <input type="file" id="fileUpload" wire:model="archivo"
                        accept=".pdf,.docx,.xlsx,.pptx,.jpg,.jpeg,.png" style="display: none;">

                    <div class="contenedor_dropzone" onclick="document.getElementById('fileUpload').click()">
                        @if ($archivo)
                            <div class="dropzone_item">
                                @php
                                    $ext = strtolower($archivo->getClientOriginalExtension());
                                    $icons = [
                                        'pdf' => 'fa-file-pdf',
                                        'docx' => 'fa-file-word',
                                        'xlsx' => 'fa-file-excel',
                                        'pptx' => 'fa-file-powerpoin',
                                    ];
                                @endphp

                                <i class="fa-solid {{ $icons[$ext] ?? 'fa-file' }}"></i>
                                <span>{{ $archivo->getClientOriginalName() }}</span>

                                <button type="button" wire:click="$set('archivo', null)" class="remove_button">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @else
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <p>Haz clic para subir archivo</p>
                        @endif
                    </div>

                    @error('archivo')
                        <p class="mensaje_error">{{ $message }}</p>
                    @enderror

                    @if ($archivo)
                        <div class="g_margin_bottom_10">
                            <label for="descripcion_archivo">Descripción del archivo <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <textarea wire:model="descripcion_archivo" id="descripcion_archivo" class="g_input"
                                rows="2"></textarea>
                            @error('descripcion_archivo')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="formulario_botones g_margin_bottom_10">
                            <button wire:click="adjuntar" class="g_boton g_boton_guardar" wire:loading.attr="disabled"
                                wire:target="adjuntar">
                                <span wire:loading.remove wire:target="adjuntar">Adjuntar <i
                                        class="fa-solid fa-paperclip"></i></span>
                                <span wire:loading wire:target="adjuntar">Adjuntando... <i
                                        class="fa-solid fa-spinner fa-spin"></i></span>
                            </button>
                        </div>
                    @endif
                </div>

                <h4 class="g_panel_titulo">Documentos Adjuntos</h4>
                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th class="g_celda_centro">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($archivos_existentes as $file)
                                <tr wire:key="file-{{ $file->id }}">
                                    <td>
                                        <div class="g_negrita">{{ $file->descripcion }}</div>
                                        <div>{{ $file->nombre_original }}</div>
                                    </td>
                                    <td class="g_celda_acciones g_celda_centro">
                                        <a href="{{ $file->url }}" target="_blank" class="g_accion_editar" title="Ver">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <button type="button" onclick="alertaEliminarArchivo({{ $file->id }})"
                                            class="g_accion_eliminar" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan=" 2" class="g_celda_vacia">No hay archivos.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($ticket->padre)
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Ticket Principal (Padre)</h4>
                    <div class="g_contenedor_tabla">
                        <table class="g_tabla g_tabla_pequena">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Gestor</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="g_negrita">#{{ $ticket->padre->id }}</td>
                                    <td>{{ $ticket->padre->gestor->name ?? 'N/A' }}</td>
                                    <td class="g_celda_centro">
                                        <a href="{{ route('erp.ticket.vista.editar', $ticket->padre->id) }}"
                                            class="g_accion_editar" title="Ver Ticket Padre">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if (!$ticket->hijos->isEmpty())
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Tickets Asociados (Hijos)</h4>
                    <div class="g_contenedor_tabla">
                        <table class="g_tabla g_tabla_pequena">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Gestor</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ticket->hijos as $hijo)
                                    <tr>
                                        <td class="g_negrita">#{{ $hijo->id }}</td>
                                        <td>{{ $hijo->gestor->name ?? 'N/A' }}</td>
                                        <td class="g_celda_centro">
                                            <a href="{{ route('erp.ticket.vista.editar', $hijo->id) }}" class="g_accion_editar"
                                                title="Ver Ticket Hijo">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        window.alertaEliminarTicket = function () {
            Swal.fire({
                title: '¿Eliminar Ticket?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarTicketOn();
                }
            });
        }

        window.alertaEliminarArchivo = function (id) {
            Swal.fire({
                title: '¿Eliminar Adjunto?',
                text: "El archivo será borrado permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.dispatch('eliminarArchivoOn', {
                        archivoId: id
                    });
                }
            });
        }
    </script>
    @endscript

    @livewire('atc.ticket.ticket-chat', ['ticket' => $ticket])
</div>