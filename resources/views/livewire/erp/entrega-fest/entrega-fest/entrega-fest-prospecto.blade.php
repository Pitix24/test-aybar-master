<div class="g_gap_pagina">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Prospectos: <span>{{ $evento->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <button wire:click="enviarCorreos" class="g_boton dark" wire:loading.attr="disabled"
                wire:target="enviarCorreos">
                <span wire:loading.remove wire:target="enviarCorreos">Enviar correos <i
                        class="fa-solid fa-envelope"></i></span>
                <span wire:loading wire:target="enviarCorreos">Enviando... <i
                        class="fa-solid fa-spinner fa-spin"></i></span>
            </button>

            <button wire:click="enviarWhatsapp" class="g_boton warning" wire:loading.attr="disabled"
                wire:target="enviarWhatsapp">
                <span wire:loading.remove wire:target="enviarWhatsapp">Enviar whatsapp <i
                        class="fa-brands fa-whatsapp"></i></span>
                <span wire:loading wire:target="enviarWhatsapp">Enviando... <i
                        class="fa-solid fa-spinner fa-spin"></i></span>
            </button>

            <a href="{{ route('erp.entrega-fest.vista.prospectos.crear', $evento->id) }}" class="g_boton primary">
                Añadir Prospecto <i class="fa-solid fa-user-plus"></i>
            </a>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Buscar (Nombres, Celular, Email)</label>
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
                    <label>Estado Prospecto</label>
                    <select wire:model.live="estado">
                        <option value="">Todos</option>
                        <option value="pendiente">PENDIENTE</option>
                        <option value="observado">OBSERVADO</option>
                        <option value="aprobado">APROBADO</option>
                        <option value="rechazado">RECHAZADO</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_2">
                    <label>Firma Contrato</label>
                    <select wire:model.live="estado_firma_contrato_firmado">
                        <option value="">Todos</option>
                        <option value="pendiente">PENDIENTE</option>
                        <option value="observado">OBSERVADO</option>
                        <option value="aprobado">APROBADO</option>
                        <option value="rechazado">RECHAZADO</option>
                    </select>
                </div>

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
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                <button wire:click="resetFiltros" class="g_boton danger">
                    Limpiar Filtros <i class="fa-solid fa-rotate-left"></i>
                </button>
                <button wire:click="exportExcelFiltro" class="g_boton success">
                    Exportar Filtro <i class="fa-solid fa-file-excel"></i>
                </button>
                <button wire:click="exportExcelTodo" class="g_boton info">
                    Exportar Todo <i class="fa-solid fa-file-excel"></i>
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
                        <th>DNI</th>
                        <th>Cliente</th>
                        <th>Proyecto</th>
                        <th>Lote/Mz</th>
                        <th class="g_celda_centro">Estado Prospecto</th>
                        <th class="g_celda_centro">BackOffice</th>
                        <th class="g_celda_centro">Estado Contrato Preliminar</th>
                        <th class="g_celda_centro">Fecha Firma</th>
                        <th class="g_celda_centro">Invitado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $p)
                        <tr wire:key="prospecto-{{ $p->id }}">
                            <td class="g_celda_centro">{{ $items->firstItem() + $index }}</td>
                            <td>{{ $p->dni }}</td>
                            <td class="g_negrita">{{ $p->nombre_completo }}</td>
                            <td>{{ $p->proyecto->nombre ?? 'N/A' }}</td>
                            <td>{{ $p->lote }}{{ $p->manzana }}</td>
                            <td class="g_celda_centro">
                                @php
                                    $claseEstado = match ($p->estado) {
                                        'pendiente' => 'primary',
                                        'observado' => 'warning',
                                        'aprobado' => 'success',
                                        'rechazado' => 'danger',
                                        default => 'light',
                                    };
                                @endphp
                                <span class="g_badge {{ $claseEstado }}">{{ strtoupper($p->estado) }}</span>
                            </td>
                            <td class="g_celda_centro">
                                @php
                                    $claseBO = match ($p->estado_backoffice) {
                                        'pendiente' => 'primary',
                                        'observado' => 'warning',
                                        'aprobado' => 'success',
                                        'rechazado' => 'danger',
                                        default => 'light',
                                    };
                                @endphp
                                <span class="g_badge {{ $claseBO }}">{{ strtoupper($p->estado_backoffice) }}</span>
                            </td>
                            <td class="g_celda_centro">
                                @php
                                    $claseEstado = match ($p->estado_firma_contrato_firmado) {
                                        'pendiente' => 'primary',
                                        'observado' => 'warning',
                                        'aprobado' => 'success',
                                        'rechazado' => 'danger',
                                        default => 'light',
                                    };
                                @endphp
                                <span class="g_badge {{ $claseEstado }}">{{ strtoupper($p->estado_firma_contrato_firmado) }}</span>
                            </td>
                            <td>{{ $p->fecha_firma ? date('d/m/Y', strtotime($p->fecha_firma)) : 'N/A' }}</td>
                            <td class="g_celda_centro">
                                @if ($p->invitado)
                                    <span class="g_badge success" title="{{ $p->invitado->estado_confirmacion }}">SÍ</span>
                                @else
                                    <span class="g_badge danger">NO</span>
                                @endif
                            </td>
                            <td class="g_celda_acciones g_celda_centro">
                                @can('entrega-fest.prospectos')
                                    <a href="{{ route('erp.entrega-fest.vista.prospectos.editar', [$evento->id, $p->id]) }}"
                                        class="g_accion editar" title="Editar / Evaluar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>

                                    @if ($p->estado_backoffice === 'aprobado' && !$p->invitado)
                                         <a href="{{ route('public.entrega-fest.asistencia', [$evento->slug, $p->id]) }}" target="_blank"
                                             class="g_accion info" title="Ver Link de Asistencia">
                                             <i class="fa-solid fa-link"></i>
                                         </a>
                                         <a href="https://wa.me/{{ preg_replace('/\D/', '', $p->celular) }}?text={{ urlencode('Hola ' . $p->nombres . ', confirma tu asistencia al evento ' . $evento->nombre . ' aquí: ' . route('public.entrega-fest.asistencia', [$evento->slug, $p->id])) }}"
                                             target="_blank" class="g_accion success" title="Enviar por WhatsApp">
                                             <i class="fa-brands fa-whatsapp"></i>
                                         </a>
                                     @endif
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
                <p>{{ $buscar ? 'No se encontraron prospectos para "' . $buscar . '"' : 'No hay prospectos registrados en este evento.' }}
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