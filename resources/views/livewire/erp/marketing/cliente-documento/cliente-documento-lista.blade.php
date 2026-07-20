<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, perPage, proyecto_id, tipo_cliente_documentos_id, activo, resetFiltros, gotoPage, nextPage, previousPage, toggleActivo"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Gestión de Documentos de Clientes</h2>

        <div class="cabecera_titulo_botones">
            @can('cliente_documento.crear')
            <a href="{{ route('erp.cliente-documento.vista.crear') }}" class="g_boton primary">
                Crear <i class="fa-solid fa-square-plus"></i>
            </a>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Buscar (Título)</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" placeholder="Ej: Plano...">
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Proyecto</label>
                    <select wire:model.live="proyecto_id">
                        <option value="">Todos</option>
                        @foreach($proyectos as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Tipo de Documento</label>
                    <select wire:model.live="tipo_cliente_documentos_id">
                        <option value="">Todos</option>
                        @foreach($tipos as $t)
                        <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Nuevo filtro heredado de Reglamentos -->
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Estado Activo</label>
                    <select wire:model.live="activo">
                        <option value="">Todos</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
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
                        <th class="g_celda_centro">Orden</th>
                        <th>Documento</th>
                        <th>Proyecto</th>
                        <th>Tipo</th>
                        <th class="g_celda_centro">Privacidad</th>
                        <th class="g_celda_centro">Clicks</th>
                        <th class="g_celda_centro">Activo</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                    <tr wire:key="doc-{{ $item->id }}">
                        <td class="g_celda_centro">
                            <span class="g_badge light">{{ $item->orden }}</span>
                        </td>
                        <td>
                            <div class="g_negrita">
                                @if($item->icono)
                                <i class="{{ $item->icono }} g_texto_secundario"></i>
                                @endif
                                {{ $item->titulo }}
                            </div>
                            <div class="g_texto_secundario g_resumir">{{ Str::limit($item->descripcion, 50) }}</div>
                        </td>
                        <td>
                            @if($item->proyecto)
                            <div class="g_badge outline primary" title="Proyecto">
                                {{ $item->proyecto->nombre }}
                            </div>
                            @endif
                        </td>
                        <td>
                            @if($item->tipoDocumento)
                            <div class="g_badge"
                                style="background-color: {{ $item->tipoDocumento->color }}; color: white; opacity: 0.9;"
                                title="Tipo">
                                {{ $item->tipoDocumento->nombre }}
                            </div>
                            @endif
                        </td>
                        <td class="g_celda_centro">
                            <!-- Mejorada la presentación del modo de lectura -->
                            <span class="g_badge {{ $item->solo_lectura ? 'warning' : 'success' }}" title="{{ $item->solo_lectura ? 'Descargas e impresión bloqueadas' : 'Archivo descargable' }}">
                                <i class="fa-solid {{ $item->solo_lectura ? 'fa-shield-halved' : 'fa-unlock' }}"></i>
                                {{ $item->solo_lectura ? 'Solo Lectura' : 'Descargable' }}
                            </span>
                        </td>
                        <td class="g_celda_centro">
                            <span class="g_badge primary"><i class="fa-regular fa-hand-pointer"></i> {{ $item->clicks }}</span>
                        </td>
                        <td class="g_celda_centro">
                            @can('cliente_documento.editar')
                            <button wire:click="toggleActivo({{ $item->id }})"
                                class="g_badge {{ $item->activo ? 'success' : 'error' }}"
                                style="cursor: pointer;"
                                title="Click para cambiar estado">
                                {{ $item->activo ? 'Activo' : 'Inactivo' }}
                            </button>
                            @else
                            <span class="g_badge {{ $item->activo ? 'success' : 'error' }}">
                                {{ $item->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                            @endcan
                        </td>
                        <td class="g_celda_centro">
                            @if($item->archivoPdf)
                            <a href="{{ $item->archivoPdf->url }}" target="_blank" class="g_accion ver" title="Ver PDF Original">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                            @endif

                            @can('cliente_documento.editar')
                            <a href="{{ route('cliente-documento.editar', $item->id) }}" class="g_accion editar"
                                title="Editar">
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
            <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay registros de documentos.' }}</p>
            <i class="fa-regular fa-folder-open"></i>
        </div>
        @else
        <div class="g_paginacion">
            Mostrando {{ $items->firstItem() ?? 0 }} – {{ $items->lastItem() ?? 0 }}
            de {{ $items->total() }} registros
        </div>
        @endif
    </div>
</div>
