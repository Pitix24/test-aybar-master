<div class="g_gap_pagina">

    <!-- CABECERA -->
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Movimientos del Cliente: {{ $dni }}</h2>

        <div class="cabecera_titulo_botones">
            @can('cliente.lista')
                <a href="{{ route('erp.cliente.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            <a href="{{ route('erp.cliente.vista.consultar', $dni) }}" class="g_boton secondary">
                Portal Cliente <i class="fa-solid fa-user"></i></a>

            @if ($user_model)
                @can('cliente.editar')
                    <a href="{{ route('erp.cliente.vista.editar', $user_model->id) }}" class="g_boton primary">
                        Editar Perfil <i class="fa-solid fa-user-pen"></i></a>
                @endcan
            @endif

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <!-- COLUMNA IZQUIERDA: PERFIL -->
        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Perfil del Cliente</h4>
                @if ($user_model)
                    <div class="informacion_resumen_grid">
                        <div class="informacion_resumen_item g_columna_8">
                            <span class="informacion_resumen_label">Nombre Completo</span>
                            <span class="informacion_resumen_valor">{{ $user_model->name }}</span>
                        </div>

                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">DNI</span>
                            <span class="informacion_resumen_valor">{{ $user_model->perfilCliente?->dni }}</span>
                        </div>

                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Celular</span>
                            <span
                                class="informacion_resumen_valor">{{ $user_model->perfilCliente?->telefono_principal ?: 'No registrado' }}</span>
                        </div>

                        <div class="informacion_resumen_item g_columna_8">
                            <span class="informacion_resumen_label">Email</span>
                            <span class="informacion_resumen_valor">{{ $user_model->email }}</span>
                        </div>

                        <div class="informacion_resumen_item g_columna_8">
                            <span class="informacion_resumen_label">Estado</span>
                            <div class="g_switch-wrapper">
                                <label class="g_switch">
                                    <input id="estado_activo" type="checkbox" value="{{ $activo }}" @disabled(true)>
                                    <span class="g_switch-slider"></span>
                                </label>
                                <span class="g_switch-label">
                                    {{ $activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>

                        <div class="informacion_resumen_item g_columna_8">
                            <span class="informacion_resumen_label">Consentimientos</span>
                            <div>
                                <span class="g_badge g_badge_soft {{ $user_model->politica_uno ? 'success' : 'danger' }}"
                                    title="Tratamiento de datos personales">
                                    <i
                                        class="fa-solid {{ $user_model->politica_uno ? 'fa-check-circle' : 'fa-circle-xmark' }}"></i>
                                    Datos Personales
                                </span>

                                <span class="g_badge g_badge_soft {{ $user_model->politica_dos ? 'success' : 'danger' }}"
                                    title="Políticas comerciales">
                                    <i
                                        class="fa-solid {{ $user_model->politica_dos ? 'fa-check-circle' : 'fa-circle-xmark' }}"></i>
                                    Políticas Comerciales
                                </span>
                            </div>
                        </div>
                    </div>

                    @if ($user_model->direccion)
                        <hr>
                        <h4 class="g_panel_titulo">Dirección</h4>

                        <div class="informacion_resumen_grid">
                            <div class="informacion_resumen_item g_columna_8">
                                <span class="informacion_resumen_label">Avenida / Calle / Jirón</span>
                                <span class="informacion_resumen_valor">
                                    {{ $user_model->direccion->direccion }} {{ $user_model->direccion->direccion_numero }}
                                </span>
                            </div>

                            <div class="informacion_resumen_item g_columna_8">
                                <span class="informacion_resumen_label">Ubicación</span>
                                <span class="informacion_resumen_valor">
                                    {{ $user_model->direccion->distrito?->nombre }},
                                    {{ $user_model->direccion->provincia?->nombre }},
                                    {{ $user_model->direccion->region?->nombre }}
                                </span>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="g_resaltado_caja info">
                        <span class="g_resaltado_caja_titulo">Información</span>
                        <p>El cliente no tiene una cuenta de usuario en el portal creada aún.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- COLUMNA DERECHA: MOVIMIENTOS -->
        <div class="g_columna_8 g_gap_pagina">

            <!-- TICKETS -->
            <div class="g_panel">
                <div class="g_tabla_cabecera">
                    <h4 class="g_panel_titulo"><i class="fa-solid fa-ticket-alt"></i> Tickets de Atención</h4>

                    <div class="g_tabla_cabecera_filtro formulario">
                        <div>
                            <label>Mostrar</label>
                            <select wire:model.live="perPageTickets">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Asunto</th>
                                <th>Área</th>
                                <th>Estado</th>
                                <th class="g_celda_centro">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span>{{ $ticket->asunto_inicial }}</span>
                                        <p>{{ $ticket->unidadNegocio?->nombre }}</p>
                                    </td>
                                    <td>{{ $ticket->area?->nombre }}</td>
                                    <td>
                                        <span class="g_badge g_badge_soft" style="color: {{ $ticket->estado?->color }};">
                                            {{ strtoupper($ticket->estado?->nombre) }}
                                        </span>
                                    </td>
                                    <td class="g_celda_centro">
                                        <a href="{{ route('erp.ticket.vista.ver', $ticket->id) }}" class="g_accion ver"
                                            title="Ver Ticket">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($tickets->hasPages())
                    <div class="g_paginacion">
                        {{ $tickets->links('vendor.pagination.default-livewire') }}
                    </div>
                @endif

                @if ($tickets->isEmpty())
                    <div class="g_vacio">
                        <p>No tiene tickets registrados.</p>
                        <i class="fa-regular fa-face-meh"></i>
                    </div>
                @else
                    <div class="g_paginacion">
                        Mostrando {{ $tickets->firstItem() ?? 0 }} – {{ $tickets->lastItem() ?? 0 }}
                        de {{ $tickets->total() }} registros
                    </div>
                @endif
            </div>

            <!-- SOLICITUD DIGITALIZAR -->
            <div class="g_panel">
                <div class="g_tabla_cabecera">
                    <h4 class="g_panel_titulo"><i class="fa-solid fa-file-contract"></i> Solicitudes Digitalizar</h4>

                    <div class="g_tabla_cabecera_filtro formulario">
                        <div>
                            <label>Mostrar</label>
                            <select wire:model.live="perPageDigitalizar">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Lote / Cuota</th>
                                <th>Proyecto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitudes_digitalizar as $solicitud)
                                <tr>
                                    <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span>{{ $solicitud->lote_completo }}</span>
                                        <p>Cuota: {{ $solicitud->numero_cuota }}</p>
                                    </td>
                                    <td>{{ $solicitud->nombre_proyecto }}</td>
                                    <td>
                                        <span class="g_badge g_badge_soft" style="color: {{ $solicitud->estado?->color }};">
                                            {{ strtoupper($solicitud->estado?->nombre) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($solicitudes_digitalizar->hasPages())
                    <div class="g_paginacion">
                        {{ $solicitudes_digitalizar->links('vendor.pagination.default-livewire') }}
                    </div>
                @endif

                @if ($solicitudes_digitalizar->isEmpty())
                    <div class="g_vacio">
                        <p>No tiene solicitudes de digitalizar.</p>
                        <i class="fa-regular fa-face-meh"></i>
                    </div>
                @else
                    <div class="g_paginacion">
                        Mostrando {{ $solicitudes_digitalizar->firstItem() ?? 0 }} –
                        {{ $solicitudes_digitalizar->lastItem() ?? 0 }}
                        de {{ $solicitudes_digitalizar->total() }} registros
                    </div>
                @endif
            </div>

            <!-- CITAS -->
            <div class="g_panel">
                <div class="g_tabla_cabecera">
                    <h4 class="g_panel_titulo"><i class="fa-solid fa-calendar-check"></i> Citas Agendadas</h4>

                    <div class="g_tabla_cabecera_filtro formulario">
                        <div>
                            <label>Mostrar</label>
                            <select wire:model.live="perPageCitas">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Fecha Cita</th>
                                <th>Motivo</th>
                                <th>Sede</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($citas as $cita)
                                <tr>
                                    <td>{{ $cita->fecha_inicio->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span>{{ $cita->motivo?->nombre }}</span>
                                        <p>{{ $cita->asunto_solicitud }}</p>
                                    </td>
                                    <td>{{ $cita->sede?->nombre }}</td>
                                    <td>
                                        <span class="g_badge g_badge_soft" style="color: {{ $cita->estado?->color }};">
                                            {{ strtoupper($cita->estado?->nombre) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($citas->hasPages())
                    <div class="g_paginacion">
                        {{ $citas->links('vendor.pagination.default-livewire') }}
                    </div>
                @endif

                @if ($citas->isEmpty())
                    <div class="g_vacio">
                        <p>No tiene citas registradas.</p>
                        <i class="fa-regular fa-face-meh"></i>
                    </div>
                @else
                    <div class="g_paginacion">
                        Mostrando {{ $citas->firstItem() ?? 0 }} – {{ $citas->lastItem() ?? 0 }}
                        de {{ $citas->total() }} registros
                    </div>
                @endif
            </div>

            <!-- EVIDENCIAS DE PAGO -->
            <div class="g_panel">
                <div class="g_tabla_cabecera">
                    <h4 class="g_panel_titulo"><i class="fa-solid fa-receipt"></i> Evidencias de Pago</h4>

                    <div class="g_tabla_cabecera_filtro formulario">
                        <div>
                            <label>Mostrar</label>
                            <select wire:model.live="perPageEvidencias">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Cuota / Monto</th>
                                <th>Estado</th>
                                <th class="g_celda_centro">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitudes_evidencia as $sol_evid)
                                <tr>
                                    <td>{{ $sol_evid->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span>Cuota: {{ $sol_evid->numero_cuota }}</span>
                                        <p>S/ {{ number_format($sol_evid->monto_operacion, 2) }}</p>
                                    </td>
                                    <td>
                                        <span class="g_badge g_badge_soft" style="color: {{ $sol_evid->estado?->color }};">
                                            {{ strtoupper($sol_evid->estado?->nombre) }}
                                        </span>
                                    </td>
                                    <td class="g_celda_centro">
                                        <a href="{{ route('erp.solicitud-evidencia-pago.vista.editar', $sol_evid->id) }}"
                                            class="g_accion ver" title="Ver Evidencia">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($solicitudes_evidencia->hasPages())
                    <div class="g_paginacion">
                        {{ $solicitudes_evidencia->links('vendor.pagination.default-livewire') }}
                    </div>
                @endif

                @if ($solicitudes_evidencia->isEmpty())
                    <div class="g_vacio">
                        <p>No tiene evidencias de pago enviadas.</p>
                        <i class="fa-regular fa-face-meh"></i>
                    </div>
                @else
                    <div class="g_paginacion">
                        Mostrando {{ $solicitudes_evidencia->firstItem() ?? 0 }} –
                        {{ $solicitudes_evidencia->lastItem() ?? 0 }}
                        de {{ $solicitudes_evidencia->total() }} registros
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>