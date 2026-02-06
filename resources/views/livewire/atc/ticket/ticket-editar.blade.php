@section('tituloPagina', 'Editar Ticket')

<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, adjuntar, eliminarTicketOn, eliminarArchivo"
        message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <div>
            <h2>Ticket #{{ $ticket->id }}</h2>
            <p style="margin: 0; color: #64748b;">Creado por: {{ $ticket->creadoPor?->name ?? 'Sistema' }} el
                {{ $ticket->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="cabecera_titulo_botones">
            <button type="button" class="g_boton g_boton_primary" wire:click="$dispatch('toggleChat')">
                Mensajes <i class="fa-solid fa-comments"></i>
            </button>

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
            <div class="g_panel" style="padding: 0;">
                <div class="tabs_contenedor">
                    <button wire:click="$set('tab_activa', 'ticket')"
                        class="tab_boton {{ $tab_activa == 'ticket' ? 'activa' : '' }}">
                        <i class="fa-solid fa-circle-info"></i> Información
                    </button>
                    <button wire:click="$set('tab_activa', 'historial')"
                        class="tab_boton {{ $tab_activa == 'historial' ? 'activa' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left"></i> Historial
                    </button>
                    <button wire:click="$set('tab_activa', 'adjuntos')"
                        class="tab_boton {{ $tab_activa == 'adjuntos' ? 'activa' : '' }}">
                        <i class="fa-solid fa-paperclip"></i> Adjuntos ({{ $archivos_existentes->count() }})
                    </button>
                </div>

                <div style="padding: 20px;">
                    <!-- TAB: INFORMACIÓN -->
                    <div class="{{ $tab_activa == 'ticket' ? '' : 'g_oculto' }}">
                        <form wire:submit="update" class="formulario">
                            <div class="g_margin_bottom_15">
                                <label for="asunto_inicial">Asunto <span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <input type="text" id="asunto_inicial" wire:model.blur="asunto_inicial"
                                    class="@error('asunto_inicial') input-error @enderror">
                                @error('asunto_inicial') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_15">
                                <label for="descripcion_inicial">Descripción <span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span></label>
                                <textarea id="descripcion_inicial" wire:model.blur="descripcion_inicial" rows="8"
                                    class="@error('descripcion_inicial') input-error @enderror"></textarea>
                                @error('descripcion_inicial') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_fila">
                                <div class="g_columna_6 g_margin_bottom_15">
                                    <label>Unidad de Negocio</label>
                                    <select wire:model.live="unidad_negocio_id">
                                        @foreach($unidades as $u) <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="g_columna_6 g_margin_bottom_15">
                                    <label>Proyecto</label>
                                    <select wire:model.live="proyecto_id">
                                        @foreach($proyectos as $p) <option value="{{ $p->id }}">{{ $p->nombre }}
                                        </option> @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="g_fila">
                                <div class="g_columna_4 g_margin_bottom_15">
                                    <label>Tipo Solicitud</label>
                                    <select wire:model.live="tipo_solicitud_id">
                                        @foreach($tipos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="g_columna_4 g_margin_bottom_15">
                                    <label>Subtipo</label>
                                    <select wire:model="sub_tipo_solicitud_id">
                                        <option value="">General</option>
                                        @foreach($subtipos as $st) <option value="{{ $st->id }}">{{ $st->nombre }}
                                        </option> @endforeach
                                    </select>
                                </div>
                                <div class="g_columna_4 g_margin_bottom_15">
                                    <label>Canal</label>
                                    <select wire:model="canal_id">
                                        @foreach($canales as $c) <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="formulario_botones">
                                <button type="submit" class="g_boton g_boton_guardar">
                                    <i class="fa-solid fa-pencil"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB: HISTORIAL -->
                    <div class="{{ $tab_activa == 'historial' ? '' : 'g_oculto' }}">
                        <div class="historial_lista">
                            @foreach($historial as $h)
                                <div class="historial_item">
                                    <div class="historial_meta">
                                        <span class="historial_accion">{{ $h->accion }}</span>
                                        <span class="historial_fecha">{{ $h->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <p class="historial_detalle">{{ $h->detalle }}</p>
                                    <div class="historial_usuario">
                                        <i class="fa-solid fa-user-tag"></i> {{ $h->user?->name ?? 'Sistema' }}
                                    </div>
                                </div>
                            @endforeach
                            @if($historial->isEmpty())
                                <div class="g_vacio">
                                    <p>No hay historial registrado.</p>
                                    <i class="fa-solid fa-timeline"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- TAB: ADJUNTOS -->
                    <div class="{{ $tab_activa == 'adjuntos' ? '' : 'g_oculto' }}">
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
                                                    <a href="{{ Storage::url($file->path) }}" target="_blank"
                                                        class="archivo_nombre">
                                                        {{ $file->nombre_original }}
                                                    </a>
                                                    <span class="archivo_meta">Subido por {{ $file->user?->name }} el
                                                        {{ $file->created_at->format('d/m/Y') }}</span>
                                                </div>
                                            </div>
                                            <button type="button" class="archivo_eliminar"
                                                wire:click="eliminarArchivo({{ $file->id }})" title="Eliminar archivo">
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
                    <label>Gestor Asignado</label>
                    <select wire:model="gestor_id">
                        <option value="">Sin asignar</option>
                        @foreach($gestores as $ge) <option value="{{ $ge->id }}">{{ $ge->name }}</option> @endforeach
                    </select>
                </div>

                <hr style="margin: 20px 0; border: none; border-top: 1px solid #e2e8f0;">

                <div class="g_margin_bottom_15">
                    <label>Cliente / Solicitante</label>
                    <select wire:model="cliente_id">
                        @foreach($clientes as $cl) <option value="{{ $cl->id }}">{{ $cl->name }}</option> @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_15">
                    <label>Área Responsable</label>
                    <select wire:model="area_id">
                        @foreach($areas as $ar) <option value="{{ $ar->id }}">{{ $ar->nombre }}</option> @endforeach
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
    </script>
    @endscript

    @livewire('atc.ticket.ticket-chat', ['ticket' => $ticket])
</div>

<style>
    /* Tabs System */
    .tabs_contenedor {
        display: flex;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .tab_boton {
        padding: 15px 25px;
        border: none;
        background: none;
        cursor: pointer;
        font-weight: 600;
        color: #64748b;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
    }

    .tab_boton:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .tab_boton.activa {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
        background: white;
    }

    /* Dropzone */
    .contenedor_dropzone {
        padding: 30px;
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
        gap: 10px;
    }

    .archivo_item {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.2s;
    }

    .archivo_item:hover {
        transform: translateX(5px);
        border-color: #cbd5e1;
    }

    .archivo_nombre {
        font-weight: 600;
        color: #334155;
        text-decoration: none;
        font-size: 0.95rem;
    }

    .archivo_nombre:hover {
        text-decoration: underline;
        color: #3b82f6;
    }

    .archivo_meta {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .archivo_eliminar {
        color: #ef4444;
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: background 0.2s;
    }

    .archivo_eliminar:hover {
        background: #fee2e2;
    }

    /* Historial */
    .historial_lista {
        display: flex;
        flex-direction: column;
        gap: 15px;
        border-left: 2px solid #e2e8f0;
        padding-left: 20px;
        margin-left: 10px;
    }

    .historial_item {
        position: relative;
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .historial_item::before {
        content: '';
        position: absolute;
        left: -27px;
        top: 20px;
        width: 12px;
        height: 12px;
        background: #3b82f6;
        border-radius: 50%;
        box-shadow: 0 0 0 4px #dbeafe;
    }

    .historial_meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .historial_accion {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.9rem;
    }

    .historial_fecha {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .historial_detalle {
        font-size: 0.85rem;
        color: #475569;
        margin: 0 0 10px 0;
    }

    .historial_usuario {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
    }

    .g_oculto {
        display: none;
    }

    /* Helpers Colores Extensiones */
    .text-red-600 {
        color: #dc2626;
    }

    .text-blue-600 {
        color: #2563eb;
    }

    .text-green-600 {
        color: #16a34a;
    }

    .text-purple-600 {
        color: #9333ea;
    }

    .text-orange-500 {
        color: #f97316;
    }
</style>