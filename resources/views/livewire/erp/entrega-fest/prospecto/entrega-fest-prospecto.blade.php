<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Prospectos: <span>{{ $evento->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            @can('entrega-fest.ver-panel')
            <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Gestión
            </a>
            @endcan

            @can('prospecto.crear')
            <a href="{{ route('erp.entrega-fest.prospecto.crear', $evento->id) }}" class="g_boton primary">
                Crear <i class="fa-solid fa-square-plus"></i>
            </a>
            @endcan

            <button wire:click="enviarPreInvitacion" class="g_boton success">
                Enviar pre invitación <i class="fa-solid fa-envelope"></i> <i class="fa-brands fa-whatsapp"></i>
            </button>

            <button wire:click="enviarInvitacion" class="g_boton success">
                Enviar invitación <i class="fa-solid fa-envelope"></i> <i class="fa-brands fa-whatsapp"></i>
            </button>

            <a href="{{ route('erp.entrega-fest.prospecto.bancarizacion', $evento->id) }}" class="g_boton light">
                Bancarización <i class="fa-solid fa-list"></i>
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_panel_dashboard_grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; width: 100%;">
            <div class="g_panel" title="Total de lotes en este evento">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Total Lotes</h2>
                        <p class="g_negrita">{{ number_format($stats['total']) }}</p>
                    </div>
                    <i class="fa-solid fa-layer-group" style="color: var(--color-primario);"></i>
                </div>
            </div>

            <div class="g_panel" title="Clientes que aceptaron la pre-invitación">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Pre-Invit. SI</h2>
                        <p class="g_negrita">{{ number_format($stats['preinvitacion']) }}</p>
                    </div>
                    <i class="fa-solid fa-envelope-circle-check" style="color: #3B82F6;"></i>
                </div>
            </div>

            <div class="g_panel" title="Lotes con estado BO CONFORME (Supervisor)">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>BO Conforme</h2>
                        <p class="g_negrita">{{ number_format($stats['backoffice']) }}</p>
                    </div>
                    <i class="fa-solid fa-user-check" style="color: #10B981;"></i>
                </div>
            </div>

            <div class="g_panel" title="Contratos preliminares emitidos CONFORME">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Contratos OK</h2>
                        <p class="g_negrita">{{ number_format($stats['contrato']) }}</p>
                    </div>
                    <i class="fa-solid fa-file-signature" style="color: #F59E0B;"></i>
                </div>
            </div>

            <div class="g_panel" title="Lotes que ya cuentan con fecha de firma">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Firmados</h2>
                        <p class="g_negrita">{{ number_format($stats['firmados']) }}</p>
                    </div>
                    <i class="fa-solid fa-pen-fancy" style="color: #8B5CF6;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Buscar (DNI, Nombres, Celular o Email)</label>
                    <input type="text" wire:model.live.debounce.400ms="buscar">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Proyecto</label>
                    <select wire:model.live="proyecto_id">
                        <option value="">Todos los proyectos del evento</option>
                        @foreach ($proyectos as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Estado Gestor BO</label>
                    <select wire:model.live="estado_gestor_backoffice">
                        <option value="">Todos</option>
                        @foreach (\App\Models\ProspectoEntregaFest::ESTADO_GESTOR_BACKOFFICE as $valor => $info)
                        <option value="{{ $valor }}">{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Estado Supervisor BO</label>
                    <select wire:model.live="estado_backoffice">
                        <option value="">Todos</option>
                        @foreach (\App\Models\ProspectoEntregaFest::ESTADO_BACKOFFICE as $valor => $info)
                        <option value="{{ $valor }}">{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Contrato Preliminar</label>
                    <select wire:model.live="estado_contrato_preeliminar_emitido">
                        <option value="">Todos</option>
                        @foreach (\App\Models\ProspectoEntregaFest::ESTADO_CONTRATO_PRELIMINAR as $valor => $info)
                        <option value="{{ $valor }}">{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Grupo</label>
                    <select wire:model.live="grupo">
                        <option value="">Todos</option>
                        <option value="A">Grupo A</option>
                        <option value="B">Grupo B</option>
                        <option value="C">Grupo C</option>
                        <option value="D">Grupo D</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Pre-invitación</label>
                    <select wire:model.live="filtro_confirmacion">
                        <option value="">Todas</option>
                        <option value="1">Aceptó</option>
                        <option value="0">No Aceptó</option>
                        <option value="pendiente">Pendiente</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Invitación</label>
                    <select wire:model.live="filtro_invitacion">
                        <option value="">Todas</option>
                        <option value="1">Aceptó</option>
                        <option value="0">No Aceptó</option>
                        <option value="pendiente">Pendiente</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Gestor BO</label>
                    <select wire:model.live="gestor_id">
                        <option value="">Todos los gestores</option>
                        @foreach ($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Estado del Cliente</label>
                    <select wire:model.live="estado_cliente_id">
                        <option value="">Todos</option>
                        @foreach ($estados_cliente as $ec)
                        <option value="{{ $ec->id }}">{{ $ec->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Abogado (Gestor Legal)</label>
                    <select wire:model.live="gestor_legal_id">
                        <option value="">Todos</option>
                        {{-- Opción especial para filtrar prospectos sin gestor asignado --}}
                        <option value="sin_asignar">Sin Asignar</option>
                        @foreach ($gestoresLegales as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Fecha Firma Desde</label>
                    <input type="date" wire:model.live="fechaFirmaDesde">
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Fecha Firma Hasta</label>
                    <input type="date" wire:model.live="fechaFirmaHasta">
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                @can('prospecto.exportar-filtro')
                <button wire:click="exportExcelFiltro" class="g_boton excel" wire:loading.attr="disabled"
                    wire:target="exportExcelFiltro">
                    <span wire:loading.remove wire:target="exportExcelFiltro">Excel Filtrados <i
                            class="fa-regular fa-file-excel"></i></span>
                    <span wire:loading wire:target="exportExcelFiltro">Generando... <i
                            class="fa-solid fa-spinner fa-spin"></i></span>
                </button>
                @endcan

                @can('prospecto.exportar-todo')
                <button wire:click="exportExcelTodo" class="g_boton dark" wire:loading.attr="disabled"
                    wire:target="exportExcelTodo">
                    <span wire:loading.remove wire:target="exportExcelTodo">Excel Todo <i
                            class="fa-solid fa-file-export"></i></span>
                    <span wire:loading wire:target="exportExcelTodo">Generando... <i
                            class="fa-solid fa-spinner fa-spin"></i></span>
                </button>
                @endcan

                <button wire:click="resetFiltros" class="g_boton danger">
                    Limpiar <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>

            <div class="g_tabla_cabecera_filtro formulario">
                <div>
                    <label>Mostrar</label>
                    <select wire:model.live="perPage">
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Estado Cliente</th>
                        <th>DNI</th>
                        <th>Cliente</th>
                        <th>Proyecto</th>
                        <th>Proyecto Reubicado</th>
                        <th>Mz-Lt</th>
                        <th>Mz-Lt Reubicado</th>
                        <th class="g_celda_centro">Pre-invitación</th>
                        <th class="g_celda_centro">Invitación</th>
                        <th class="g_celda_centro">Gestor BO</th>
                        <th class="g_celda_centro">Estado Gestor BO</th>
                        <th class="g_celda_centro">Fecha Culminación EECC</th>
                        <th class="g_celda_centro">Supervisor BO</th>
                        <th class="g_celda_centro">Abogado</th>
                        <th class="g_celda_centro">Estado Contrato Preliminar</th>
                        <th class="g_celda_centro">Fecha para Firmar</th>
                        <th class="g_celda_centro">Fecha Firmado</th>
                        <th class="g_celda_centro">Invitado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $p)
                    <tr wire:key="prospecto-{{ $p->id }}">
                        <td class="g_celda_centro">{{ $items->firstItem() + $index }}</td>
                        <td>
                            @if ($p->estadoCliente)
                            <span class="g_badge g_badge_soft"
                                style="color: {{ $p->estadoCliente->color ?? '#64748b' }};">
                                {{ $p->estadoCliente->nombre }}
                            </span>
                            @else
                            <span class="g_badge light">N/A</span>
                            @endif
                        </td>
                        <td>{{ $p->dni }}</td>
                        <td>
                            <div class="g_negrita">
                                {{ $p->nombre_completo }}
                            </div>
                            <div>{{ $p->email }}</div>
                            <div>{{ $p->celular }}</div>

                            @php
                            $etapas_com = [
                            'pre-invitacion' => 'Pre-invitación',
                            'asistencia-invitacion' => 'Asist Invitación',
                            'asistencia-confirmacion' => 'Asist Confirmación',
                            'instrucciones' => 'Instrucciones',
                            'contrato-preliminar' => 'Contrato Preliminar',
                            'cita-agendar' => 'Agendar Cita',
                            'cita-confirmacion' => 'Confirmar Cita',
                            'cita-recordatorio' => 'Recordatorio Cita',
                            ];
                            @endphp

                            <div class="g_tracker_comunicaciones">
                                <div class="g_tracker_fila">
                                    <i class="fa-solid fa-envelope" title="Canal Email"></i>
                                    @foreach ($etapas_com as $slug => $label)
                                    @php $hist = $p->historialComunicaciones->where('canal', 'email')->where('etapa',
                                    $slug)->whereIn('estado', ['enviado', 'leido'])->first(); @endphp
                                    <span class="g_tracker_punto {{ $hist ? 'activo' : '' }}"
                                        title="{{ $label }}: {{ $hist ? 'Enviado' : 'Pendiente' }}"></span>
                                    @endforeach
                                </div>
                                <div class="g_tracker_fila">
                                    <i class="fa-brands fa-whatsapp" title="Canal WhatsApp"></i>
                                    @foreach ($etapas_com as $slug => $label)
                                    @php $hist = $p->historialComunicaciones->where('canal', 'whatsapp')->where('etapa',
                                    $slug)->whereIn('estado', ['enviado', 'leido'])->first(); @endphp
                                    <span class="g_tracker_punto {{ $hist ? 'activo' : '' }}"
                                        title="{{ $label }}: {{ $hist ? 'Enviado' : 'Pendiente' }}"></span>
                                    @endforeach
                                </div>
                            </div>

                            <hr>
                            @foreach ($p->copropietarios as $c)
                            <div class="g_negrita" style="color: #666; font-size: 0.9em;">
                                Coprop: {{ $c->nombres }}
                            </div>
                            <div style="font-size: 0.85em;">{{ $c->email }}</div>
                            <div style="font-size: 0.85em;">{{ $c->celular }}</div>

                            <div class="g_tracker_comunicaciones">
                                <div class="g_tracker_fila">
                                    <i class="fa-solid fa-envelope" title="Canal Email"></i>
                                    @foreach ($etapas_com as $slug => $label)
                                    @php $hist = $c->historialComunicaciones->where('canal', 'email')->where('etapa',
                                    $slug)->whereIn('estado', ['enviado', 'leido'])->first(); @endphp
                                    <span class="g_tracker_punto {{ $hist ? 'activo' : '' }}"
                                        title="{{ $label }}: {{ $hist ? 'Enviado' : 'Pendiente' }}"></span>
                                    @endforeach
                                </div>
                                <div class="g_tracker_fila">
                                    <i class="fa-brands fa-whatsapp" title="Canal WhatsApp"></i>
                                    @foreach ($etapas_com as $slug => $label)
                                    @php $hist = $c->historialComunicaciones->where('canal', 'whatsapp')->where('etapa',
                                    $slug)->whereIn('estado', ['enviado', 'leido'])->first(); @endphp
                                    <span class="g_tracker_punto {{ $hist ? 'activo' : '' }}"
                                        title="{{ $label }}: {{ $hist ? 'Enviado' : 'Pendiente' }}"></span>
                                    @endforeach
                                </div>
                            </div>

                            @if (!$loop->last)
                            <div style="margin-bottom: 5px; border-bottom: 1px dashed #eee;"></div>
                            @endif
                            @endforeach
                        </td>
                        <td>{{ $p->proyecto->nombre ?? 'N/A' }}</td>
                        <td>{{ $p->reubicadoProyecto?->nombre }}</td>
                        <td>{{ $p->manzana }}-{{ $p->lote }}</td>
                        <td>
                            @if ($p->reubicado_manzana || $p->reubicado_lote)
                            {{ $p->reubicado_manzana }}-{{ $p->reubicado_lote }}
                            @endif
                        </td>
                        <td class="g_celda_centro">
                            @if (is_null($p->preinvitacion_confirmada))
                            <span class="g_badge info" title="Pendiente">Pendiente</span>
                            @elseif($p->preinvitacion_confirmada)
                            <span class="g_badge success" title="Aceptó">Aceptó</span>
                            @else
                            <span class="g_badge danger" title="Rechazó">Rechazó</span>
                            @endif
                        </td>
                        <td class="g_celda_centro">
                            @if (is_null($p->invitacion_confirmada))
                            <span class="g_badge info" title="Pendiente">Pendiente</span>
                            @elseif($p->invitacion_confirmada)
                            <span class="g_badge success" title="Aceptó">Aceptó</span>
                            @else
                            <span class="g_badge danger" title="Rechazó">Rechazó</span>
                            @endif
                        </td>
                        <td>
                            {{ $p->gestor->name ?? '' }}
                            <div class="g_negrita">
                                {{ $p->gestor_fecha_asignacion ? date('d/m/Y', strtotime($p->gestor_fecha_asignacion)) :
                                '' }}
                            </div>
                        </td>
                        <td class="g_celda_centro">
                            <span class="g_badge g_badge_soft" style="color: {{ $p->badgeGestorBackoffice() }}">
                                {{
                                \App\Models\ProspectoEntregaFest::ESTADO_GESTOR_BACKOFFICE[$p->estado_gestor_backoffice]['label']
                                ?? $p->estado_gestor_backoffice }}
                            </span>
                            <div>
                                @if ($p->link_carpeta_eecc)
                                <a href="{{ $p->link_carpeta_eecc }}" target="_blank" class="g_accion info"
                                    title="Abrir Carpeta EECC">
                                    <i class="fa-solid fa-folder-open"></i>
                                </a>
                                @endif

                                @if ($p->link_eecc_firmado)
                                <a href="{{ $p->link_eecc_firmado }}" target="_blank" class="g_accion ver"
                                    title="Ver EECC Firmado">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                        <td class="g_celda_centro">
                            {{ $p->fecha_culminacion_eecc ? date('d/m/Y', strtotime($p->fecha_culminacion_eecc)) : '' }}
                        </td>
                        <td class="g_celda_centro">
                            <span class="g_badge g_badge_soft" style="color: {{ $p->badgeBackoffice() }}">
                                {{ \App\Models\ProspectoEntregaFest::ESTADO_BACKOFFICE[$p->estado_backoffice]['label']
                                ?? $p->estado_backoffice }}
                            </span>
                        </td>
                        <td>
                            @if ($p->gestorLegal)
                                <span class="g_badge"
                                    style="background-color: #8e44ad; color: #ffffff;">
                                    <i class="fa-solid fa-scale-balanced"></i>
                                    {{ $p->gestorLegal->name }}
                                </span>
                            @else
                                <span class="g_badge light"></span>
                            @endif
                        </td>
                        <td class="g_celda_centro">
                            <span class="g_badge g_badge_soft" style="color: {{ $p->badgeContratoPreeliminar() }}">
                                {{
                                \App\Models\ProspectoEntregaFest::ESTADO_CONTRATO_PRELIMINAR[$p->estado_contrato_preeliminar_emitido]['label']
                                ?? $p->estado_contrato_preeliminar_emitido }}
                            </span>
                        </td>
                        <td>{{ $p->fecha_firma ? date('d/m/Y', strtotime($p->fecha_firma)) : '' }}</td>
                        <td>{{ $p->fecha_generacion_contrato ? date('d/m/Y', strtotime($p->fecha_generacion_contrato)) :
                            '' }}
                        </td>
                        <td class="g_celda_centro">
                            @if ($p->invitado)
                            <span class="g_badge success" title="{{ $p->invitado->estado_confirmacion }}">SÍ</span>
                            @else
                            <span class="g_badge danger">NO</span>
                            @endif
                        </td>
                        <td class="g_celda_acciones g_celda_centro">
                            @can('prospecto.editar')
                            <a href="{{ route('erp.entrega-fest.prospecto.editar', [$evento->id, $p->id]) }}"
                                class="g_accion editar" title="Editar / Evaluar">
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
        <div class="g_paginacion">
            {{ $items->links('vendor.pagination.default-livewire') }}
        </div>
        @endif

        @if ($items->isEmpty())
        <div class="g_vacio">
            <p>{{ $buscar ? 'No se encontraron prospectos para "' . $buscar . '"' : 'No hay prospectos registrados en
                este evento.' }}
            </p>
            <i class="fa-regular fa-face-grin-wink"></i>
        </div>
        @else
        <div class="g_paginacion">
            Mostrando {{ $items->firstItem() ?? 0 }} – {{ $items->lastItem() ?? 0 }}
            de {{ $items->total() }} registros
        </div>
        @endif
    </div>
</div>
