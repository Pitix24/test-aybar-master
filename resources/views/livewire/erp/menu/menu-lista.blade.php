<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="buscar, activo, resetFiltros, gotoPage" message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Gestión de Menú ERP</h2>

        <div class="cabecera_titulo_botones">
            @can('menu-crear')
                <a href="{{ route('erp.menu.vista.crear') }}" class="g_boton g_boton_primary">
                    Crear <i class="fa-solid fa-square-plus"></i></a>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_6">
                    <label>Nombre del Ítem</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" placeholder="Buscar por nombre o ID...">
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Estado</label>
                    <select wire:model.live="activo">
                        <option value="">Todos</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>

                <div class="g_columna_3 g_fila_final">
                    <button wire:click="resetFiltros" class="g_boton g_boton_danger">
                        Limpiar <i class="fa-solid fa-rotate-left"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th width="350">Nombre / Estructura</th>
                        <th>Ruta / URL</th>
                        <th class="g_celda_centro">Nivel</th>
                        <th class="g_celda_centro">Orden</th>
                        <th class="g_celda_centro">Estado</th>
                        <th class="g_celda_centro">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $item)
                        @include('livewire.erp.menu.item-recursivo', ['item' => $item, 'espacio' => 0])
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
                <p>{{ $buscar ? 'No se encontraron resultados para "' . $buscar . '"' : 'No hay ítems registrados en el menú.' }}
                </p>
                <i class="fa-regular fa-face-grin-wink"></i>
            </div>
        @endif
    </div>
</div>