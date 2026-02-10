@section('tituloPagina', 'Editar Ticket')

<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, adjuntar, eliminarTicketOn, eliminarArchivo"
        message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <div>
            <h2>Ticket #{{ $ticket->id }}</h2>
            <p style="margin: 0; color: #64748b;">Creado por: <span class="g_negrita">{{ $ticket->creadoPor?->name ?? 'Sistema' }}</span> el
                {{ $ticket->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="cabecera_titulo_botones">
            <button type="button" class="g_boton g_boton_primary" wire:click="$dispatch('toggleChat')">
                Mensajes <i class="fa-solid fa-comments"></i>
            </button>

            <a href="{{ route('erp.ticket.vista.crear', ['ticketPadre' => $ticket->id]) }}" class="g_boton g_boton_success">
                Nuevo Hijo <i class="fa-solid fa-square-plus"></i>
            </a>

            @if ($ticket->padre)
                <a href="{{ route('erp.ticket.vista.editar', $ticket->padre->id) }}" class="g_boton g_boton_secondary">
                    Ir al Padre #{{ $ticket->padre->id }} <i class="fa-solid fa-arrow-up"></i>
                </a>
            @endif

            <a href="{{ route('erp.ticket.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i></a>

            <button type="button" class="g_boton g_boton_danger" onclick="alertaEliminarTicket()">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <!-- COLUMNA IZQUIERDA: FORMULARIO Y TABS -->
        <div class="g_columna_8">
            <div class="g_panel" x-data="{ mainTab: '{{ $tab_activa }}' }">
                <!-- NAVEGACIÓN PRINCIPAL (TABS) -->
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" @click="mainTab = 'ticket'; $wire.set('tab_activa', 'ticket')"
                            :class="mainTab === 'ticket' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-circle-info"></i> Información
                        </button>
                        <button type="button" @click="mainTab = 'historial'; $wire.set('tab_activa', 'historial')"
                            :class="mainTab === 'historial' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-clock-rotate-left"></i> Historial
                        </button>
                        <button type="button" @click="mainTab = 'adjuntos'; $wire.set('tab_activa', 'adjuntos')"
                            :class="mainTab === 'adjuntos' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-paperclip"></i> Adjuntos ({{ $archivos_existentes->count() }})
                        </button>
                    </div>
                </div>

                <div class="g_tab_content">
                    <!-- TAB: INFORMACIÓN -->
                    <div x-show="mainTab === 'ticket'" x-transition>
                        <div x-data="{ subTab: 'general' }">
                            <!-- SUB-TABS (Estilo Crear) -->
                            <div class="g_tab_navegacion" style="margin-top: 0; background: #f8fafc; border-radius: 8px 8px 0 0;">
                                <div class="g_tab_botones">
                                    <button type="button" @click="subTab = 'general'"
                                        :class="subTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                                        <i class="fa-solid fa-file-lines"></i> Requerimiento y Respuesta
                                    </button>
                                    <button type="button" @click="subTab = 'cliente'"
                                        :class="subTab === 'cliente' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                                        <i class="fa-solid fa-user"></i> Datos del Solicitante
                                    </button>
                                </div>
                            </div>

                            <form wire:submit="update" class="formulario" style="padding: 20px;">
                                <!-- SUB-TAB: GENERAL -->
                                <div x-show="subTab === 'general'" x-transition>
                                    <div class="g_margin_bottom_15">
                                        <label for="asunto_inicial">Asunto Inicial</label>
                                        <input type="text" id="asunto_inicial" wire:model.blur="asunto_inicial"
                                            class="@error('asunto_inicial') input-error @enderror">
                                        @error('asunto_inicial') <p class="mensaje_error">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="g_margin_bottom_15">
                                        <label for="descripcion_inicial">Descripción Inicial</label>
                                        <textarea id="descripcion_inicial" wire:model.blur="descripcion_inicial" rows="5"
                                            class="@error('descripcion_inicial') input-error @enderror"></textarea>
                                        @error('descripcion_inicial') <p class="mensaje_error">{{ $message }}</p> @enderror
                                    </div>

                                    <hr class="g_hr">

                                    <div class="g_margin_bottom_15">
                                        <label for="asunto_respuesta" style="color: var(--primary);">Asunto Respuesta (Cierre/Avance)</label>
                                        <input type="text" id="asunto_respuesta" wire:model.blur="asunto_respuesta"
                                            placeholder="Resumen de la solución o estado actual...">
                                    </div>

                                    <div class="g_margin_bottom_15">
                                        <label for="descripcion_respuesta" style="color: var(--primary);">Descripción Detallada de Respuesta</label>
                                        <textarea id="descripcion_respuesta" wire:model.blur="descripcion_respuesta" rows="6"
                                            placeholder="Explique aquí la solución brindada o respuesta al cliente..."></textarea>
                                    </div>

                                    <div class="g_fila">
                                        <div class="g_columna_6 g_margin_bottom_15">
                                            <label>Unidad de Negocio</label>
                                            <select wire:model.live="unidad_negocio_id">
                                                @foreach($unidades as $u) <option value="{{ $u->id }}">{{ $u->nombre }}</option> @endforeach
                                            </select>
                                        </div>
                                        <div class="g_columna_6 g_margin_bottom_15">
                                            <label>Proyecto</label>
                                            <select wire:model.live="proyecto_id">
                                                @foreach($proyectos as $p) <option value="{{ $p->id }}">{{ $p->nombre }}</option> @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="g_fila">
                                        <div class="g_columna_4 g_margin_bottom_15">
                                            <label>Tipo Solicitud</label>
                                            <select wire:model.live="tipo_solicitud_id">
                                                @foreach($tipos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option> @endforeach
                                            </select>
                                        </div>
                                        <div class="g_columna_4 g_margin_bottom_15">
                                            <label>Subtipo</label>
                                            <select wire:model="sub_tipo_solicitud_id">
                                                <option value="">General</option>
                                                @foreach($subtipos as $st) <option value="{{ $st->id }}">{{ $st->nombre }}</option> @endforeach
                                            </select>
                                        </div>
                                        <div class="g_columna_4 g_margin_bottom_15">
                                            <label>Canal de Origen</label>
                                            <select wire:model="canal_id">
                                                @foreach($canales as $c) <option value="{{ $c->id }}">{{ $c->nombre }}</option> @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    @if (!empty($lotes))
                                        <div class="g_margin_top_10">
                                            <label>Lotes Vinculados</label>
                                            <div class="g_contenedor_tabla">
                                                <table class="g_tabla g_tabla_pequena">
                                                    <thead>
                                                        <tr>
                                                            <th>Razón Social</th>
                                                            <th>Proyecto</th>
                                                            <th>Mz./Lt.</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($lotes as $index => $l)
                                                            <tr wire:key="lote-edit-{{ $index }}">
                                                                <td>{{ $l['razon_social'] }}</td>
                                                                <td>{{ $l['proyecto'] }}</td>
                                                                <td>{{ $l['numero_lote'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- SUB-TAB: CLIENTE -->
                                <div x-show="subTab === 'cliente'" x-transition>
                                    <div class="g_fila">
                                        <div class="g_columna_6 g_margin_bottom_15">
                                            <label>DNI / RUC / CE</label>
                                            <div style="display: flex; gap: 10px;">
                                                <input type="text" wire:model="dni" disabled class="g_input_disabled" style="flex: 1;">
                                                <a href="https://aybarcorp.com/slin/cliente/{{ $dni }}" target="_blank" class="g_boton g_boton_dark" title="Ver en SLIN">
                                                    <i class="fa-solid fa-external-link"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="g_columna_6 g_margin_bottom_15">
                                            <label>Origen Datos</label>
                                            <span class="g_badge g_badge_light">{{ strtoupper($origen ?? 'N/A') }}</span>
                                        </div>
                                    </div>

                                    <div class="g_margin_bottom_15">
                                        <label>Nombre Completo / Razón Social</label>
                                        <input type="text" wire:model="nombres" class="g_input">
                                    </div>

                                    <div class="g_fila">
                                        <div class="g_columna_6 g_margin_bottom_15">
                                            <label>Correo Electrónico</label>
                                            <input type="email" wire:model="email" class="g_input" placeholder="ejemplo@correo.com">
                                        </div>
                                        <div class="g_columna_6 g_margin_bottom_15">
                                            <label>Celular / Teléfono</label>
                                            <input type="text" wire:model="celular" class="g_input" placeholder="999888777">
                                        </div>
                                    </div>

                                    <div class="g_alerta g_alerta_info">
                                        <i class="fa-solid fa-circle-info"></i> Estos datos son específicos para el contacto de este ticket.
                                        @if($ticket->cliente)
                                            <br>Propietario de la cuenta: <strong>{{ $ticket->cliente->name }}</strong>
                                        @endif
                                    </div>
                                </div>

                                <div class="formulario_botones g_margin_top_20">
                                    <button type="submit" class="g_boton g_boton_primary">
                                        <i class="fa-solid fa-save"></i> Guardar Todos los Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- TAB: HISTORIAL -->
                    <div x-show="mainTab === 'historial'" x-transition style="padding: 20px;">
                        <div class="historial_lista">
                            @foreach($historialFull as $h)
                                <div class="historial_item">
                                    <div class="historial_meta">
                                        <span class="historial_accion">{{ $h->accion }}</span>
                                        <span class="historial_fecha">{{ $h->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="historial_detalle">
                                        @foreach (explode(' | ', $h->detalle) as $linea)
                                            <div>{{ $linea }}</div>
                                        @endforeach
                                    </div>
                                    <div class="historial_usuario">
                                        <i class="fa-solid fa-user-tag"></i> {{ $h->user?->name ?? 'Sistema' }}
                                    </div>
                                </div>
                            @endforeach
                            @if($historialFull->isEmpty())
                                <div class="g_vacio">
                                    <p>No hay historial registrado.</p>
                                    <i class="fa-solid fa-timeline"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- TAB: ADJUNTOS -->
                    <div x-show="mainTab === 'adjuntos'" x-transition style="padding: 20px;">
                        <div class="g_fila">
                            <!-- SUBIDOR -->
                            <div class="g_columna_5">
                                <div class="g_panel" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                                    <h5 style="margin-top: 0; margin-bottom: 15px;">Añadir Adjunto</h5>

                                    <input type="file" id="fileUpload" wire:model="archivo"
                                        accept=".pdf,.docx,.xlsx,.pptx,.jpg,.jpeg,.png" style="display: none;">

                                    <div class="contenedor_dropzone"
                                        onclick="document.getElementById('fileUpload').click()">
                                        @if ($archivo)
                                            <div class="dropzone_item">
                                                @php
                                                    $ext = strtolower($archivo->getClientOriginalExtension());
                                                    $icons = [
                                                        'pdf' => 'fa-file-pdf text-red-600',
                                                        'docx' => 'fa-file-word text-blue-600',
                                                        'xlsx' => 'fa-file-excel text-green-600',
                                                        'jpg' => 'fa-file-image text-purple-600',
                                                        'jpeg' => 'fa-file-image text-purple-600',
                                                        'png' => 'fa-file-image text-purple-600',
                                                    ];
                                                @endphp
                                                <i class="fa-solid {{ $icons[$ext] ?? 'fa-file text-gray-500' }}"
                                                    style="font-size: 2rem;"></i>
                                                <span
                                                    style="font-size: 0.8rem; display: block; margin-top: 5px;">{{ $archivo->getClientOriginalName() }}</span>
                                            </div>
                                        @else
                                            <div class="g_vacio" style="padding: 20px 0;">
                                                <i class="fa-solid fa-cloud-arrow-up"
                                                    style="font-size: 2.5rem; color: #94a3b8; margin-bottom: 10px;"></i>
                                                <p style="font-size: 0.9rem;">Haz clic para subir archivo</p>
                                            </div>
                                        @endif
                                    </div>

                                    @error('archivo') <p class="mensaje_error">{{ $message }}</p> @enderror

                                    @if ($archivo)
                                        <div class="g_margin_top_15">
                                            <label>Descripción del adjunto</label>
                                            <textarea wire:model="descripcion_archivo" class="g_input" style="height: 80px;"
                                                placeholder="¿Qué contiene este archivo?"></textarea>
                                            @error('descripcion_archivo') <p class="mensaje_error">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                                            <button wire:click="adjuntar" class="g_boton g_boton_primary" style="flex: 1;">
                                                Subir <i class="fa-solid fa-upload"></i>
                                            </button>
                                            <button wire:click="cancelarAdjunto" class="g_boton g_boton_dark">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- LISTA DE ARCHIVOS -->
                            <div class="g_columna_7">
                                <div class="archivos_lista">
                                    @foreach($archivos_existentes as $file)
                                        <div class="archivo_item">
                                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                                @php
                                                    $ext = strtolower($file->extension);
                                                    $iconClass = match ($ext) {
                                                        'pdf' => 'fa-file-pdf text-red-600',
                                                        'docx', 'doc' => 'fa-file-word text-blue-600',
                                                        'xlsx', 'xls' => 'fa-file-excel text-green-600',
                                                        'jpg', 'jpeg', 'png' => 'fa-file-image text-purple-600',
                                                        default => 'fa-file text-gray-400'
                                                    };
                                                @endphp
                                                <i class="fa-solid {{ $iconClass }}" style="font-size: 1.5rem;"></i>
                                                <div style="display: flex; flex-direction: column;">
                                                    <a href="{{ $file->url }}" target="_blank"
                                                        class="archivo_nombre">
                                                        {{ $file->descripcion ?? $file->nombre_original }}
                                                    </a>
                                                    <span class="archivo_meta">Subido por {{ $file->user?->name }} el
                                                        {{ $file->created_at->format('d/m/Y') }}</span>
                                                </div>
                                            </div>
                                            <button type="button" class="archivo_eliminar"
                                                onclick="alertaEliminarArchivo({{ $file->id }})" title="Eliminar archivo">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                    @if($archivos_existentes->isEmpty())
                                        <div class="g_vacio">
                                            <p>No hay documentos adjuntos.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TICKETS HIJOS (Si existen) -->
            @if (!$ticket->hijos->isEmpty())
                <div class="g_panel g_margin_top_20">
                    <h4 class="g_panel_titulo">Tickets Derivados / Hijos</h4>
                    <div class="g_contenedor_tabla">
                        <table class="g_tabla">
                            <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Asunto</th>
                                    <th>Área</th>
                                    <th>Gestor</th>
                                    <th>Estado</th>
                                    <th class="g_celda_centro">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ticket->hijos as $hijo)
                                    <tr>
                                        <td class="g_negrita">#{{ $hijo->id }}</td>
                                        <td>{{ $hijo->asunto_inicial }}</td>
                                        <td>{{ $hijo->area?->nombre }}</td>
                                        <td>{{ $hijo->gestor?->name }}</td>
                                        <td>{!! $hijo->estado?->badge !!}</td>
                                        <td class="g_celda_acciones g_celda_centro">
                                            <a href="{{ route('erp.ticket.vista.editar', $hijo->id) }}"
                                                class="g_accion_editar" title="Ver Ticket">
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

        <!-- COLUMNA DERECHA -->
        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Estado y Asignación</h4>

                <div class="g_margin_bottom_15">
                    <label>Estado Actual</label>
                    <select wire:model="estado_ticket_id">
                        @foreach($estados as $es) <option value="{{ $es->id }}">{{ $es->nombre }}</option> @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_15">
                    <label>Prioridad</label>
                    <select wire:model="prioridad_ticket_id">
                        @foreach($prioridades as $pr) <option value="{{ $pr->id }}">{{ $pr->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_15">
                    <label>Área Responsable</label>
                    <select wire:model.live="area_id">
                        @foreach($areas as $ar) <option value="{{ $ar->id }}">{{ $ar->nombre }}</option> @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_15">
                    <label>Gestor Asignado</label>
                    <select wire:model="gestor_id">
                        <option value="">Sin asignar</option>
                        @foreach($gestores as $ge) <option value="{{ $ge->id }}">{{ $ge->name }}</option> @endforeach
                    </select>
                </div>
            </div>

            <!-- PARTICIPANTES -->
            <div class="g_panel g_margin_top_20">
                <h4 class="g_panel_titulo">Participantes (CC)</h4>

                <div class="g_select_search">
                    <input type="text" wire:model.live.debounce.300ms="searchUser" class="g_select_search_input"
                        placeholder="Buscar para agregar...">

                    @if(!empty($participantesDisponibles))
                        <div class="g_select_search_results">
                            @foreach($participantesDisponibles as $du)
                                <div class="g_select_search_item" wire:click="addParticipant({{ $du->id }})">
                                    <div class="g_select_search_avatar">{{ $du->initials() }}</div>
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
                                <div class="g_select_search_selected_avatar">{{ $su->initials() }}</div>
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
                    $wire.dispatch('eliminarArchivoOn', { archivoId: id });
                }
            });
        }
    </script>
    @endscript

    @livewire('atc.ticket.ticket-chat', ['ticket' => $ticket])
</div>

<style>
    /* Estilos específicos para tabs y diseño premium */
    .g_tab_navegacion {
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 0;
    }

    .g_tab_botones {
        display: flex;
        gap: 5px;
    }

    .g_tab_boton {
        padding: 12px 20px;
        border: none;
        background: none;
        cursor: pointer;
        font-weight: 600;
        color: #64748b;
        transition: all 0.2s;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .g_tab_boton:hover {
        color: var(--primary);
    }

    .g_tab_active {
        color: var(--primary) !important;
        border-bottom-color: var(--primary) !important;
        background: rgba(var(--primary-rgb), 0.05); /* Suponiendo que existe la variable rgb */
    }

    .g_tab_inactive {
        opacity: 0.7;
    }

    .g_tab_content {
        /* min-height: 400px; */
    }

    /* Dropzone */
    .contenedor_dropzone {
        padding: 20px;
        text-align: center;
        border-radius: 8px;
        background: white;
        cursor: pointer;
        transition: background 0.2s;
    }

    .contenedor_dropzone:hover {
        background: #f1f5f9;
    }

    /* Archivos List */
    .archivos_lista {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .archivo_item {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 10px 15px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }

    .archivo_item:hover {
        border-color: var(--primary);
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .archivo_nombre {
        font-weight: 600;
        color: #334155;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .archivo_meta {
        font-size: 0.7rem;
        color: #94a3b8;
    }

    .archivo_eliminar {
        color: #ef4444;
        background: none;
        border: none;
        cursor: pointer;
        padding: 6px;
        border-radius: 50%;
        transition: background 0.2s;
    }

    .archivo_eliminar:hover {
        background: #fee2e2;
    }

    /* Historial Premium */
    .historial_lista {
        display: flex;
        flex-direction: column;
        gap: 15px;
        padding-left: 10px;
    }

    .historial_item {
        background: #fdfdfd;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #edf2f7;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .historial_meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }

    .historial_accion {
        font-weight: 700;
        color: #2d3748;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .historial_fecha {
        font-size: 0.7rem;
        color: #a0aec0;
    }

    .historial_detalle {
        font-size: 0.85rem;
        color: #4a5568;
        margin: 5px 0 10px 0;
        line-height: 1.4;
    }

    .historial_usuario {
        font-size: 0.7rem;
        font-weight: 600;
        color: #718096;
        background: #edf2f7;
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
    }

    /* Helpers */
    .g_hr {
        border: none;
        border-top: 1px solid #e2e8f0;
        margin: 20px 0;
    }

    .text-red-600 { color: #dc2626; }
    .text-blue-600 { color: #2563eb; }
    .text-green-600 { color: #16a34a; }
    .text-purple-600 { color: #9333ea; }
</style>