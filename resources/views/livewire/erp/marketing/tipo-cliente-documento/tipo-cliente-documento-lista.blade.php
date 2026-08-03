<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="buscar, activo, perPage, resetFiltros, gotoPage, nextPage, previousPage, toggleActivo"
        message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Gestión de Tipos de Documento</h2>

        <div class="cabecera_titulo_botones">
            @can('tipo_cliente_documento.crear')
            <a href="{{ route('erp.tipo-cliente-documento.vista.crear') }}" class="g_boton primary">
                Crear <i class="fa-solid fa-square-plus"></i>
            </a>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_8">
                    <label>Buscar (Nombre / Descripción)</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" placeholder="Ej: Manuales, Planos...">
                </div>

                <div class="g_margin_bottom_10 g_columna_4">
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
                        <th class="g_celda_centro">Color</th>
                        <th>Tipo de Documento</th>
                        <th class="g_celda_centro">Activo</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                    <tr wire:key="tipo-{{ $item->id }}">
                        <td class="g_celda_centro">
                            <span class="g_badge light">{{ $item->orden }}</span>
                        </td>
                        <td class="g_celda_centro">
                            <div style="width: 28px; height: 28px; border-radius: 8px; background-color: {{ $item->color ?? '#3b82f6' }}; margin: 0 auto; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" title="{{ $item->color }}"></div>
                        </td>
                        <td>
                            <div class="g_negrita">
                                @if($item->icono)
                                <i class="{{ $item->icono }}" style="color: {{ $item->color ?? '#3b82f6' }}; margin-right: 5px;"></i>
                                @endif
                                {{ $item->nombre }}
                            </div>
                            <div class="g_texto_secundario g_resumir">{{ Str::limit($item->descripcion, 60) }}</div>
                        </td>
                        <td class="g_celda_centro">
                            @can('tipo_cliente_documento.editar')
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
                            @can('tipo_cliente_documento.editar')
                            <a href="{{ route('erp.tipo-cliente-documento.vista.editar', $item->id) }}" class="g_accion editar"
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
            <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay tipos de documentos registrados.' }}</p>
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
