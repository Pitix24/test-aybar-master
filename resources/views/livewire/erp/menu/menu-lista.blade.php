<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="buscar, toggleActivo, eliminar" message="Cargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Administrar Menú ERP</h2>

        <div class="cabecera_titulo_botones">
            @can('menu-crear')
                <a href="{{ route('erp.menu.vista.crear') }}" class="g_boton g_boton_primary">
                    Crear Ítem <i class="fa-solid fa-square-plus"></i></a>
            @endcan
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_12">
                    <label>Buscar Ítem</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" placeholder="Nombre del menú...">
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