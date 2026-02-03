@section('tituloPagina', 'Unidad Negocio')

@section('anchoPantalla', '100%')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Unidad Negocio</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.unidad-negocio.vista.todo') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i></a>

            <a href="{{ route('erp.unidad-negocio.vista.crear') }}" class="g_boton g_boton_primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>
        </div>
    </div>

    <div class="g_panel">
        <div class="tabla_cabecera">
            <div class="tabla_cabecera_buscar">
                <form action="" class="formulario">
                    <label for="buscar">Nombre</label>
                    <input type="text" wire:model.live.debounce.1300ms="buscar" id="buscar" name="buscar">
                </form>
            </div>

            <div class="tabla_cabecera_botones">
                <button class="g_boton g_boton_primary">
                    <i class="fa-solid fa-download"></i> Descargar
                </button>
            </div>
        </div>

        <div class="tabla_contenido">
            <div class="contenedor_tabla">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Razón Social</th>
                            <th></th>
                        </tr>
                    </thead>

                    @if ($items->count())
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr>
                                    <td> {{ $item->id }} </td>
                                    <td class="g_resaltar">{{ $item->nombre }}</td>
                                    <td class="g_inferior g_resumir">{{ $item->razon_social }}</td>

                                    <td class="centrar_iconos">
                                        <a href="{{ route('erp.unidad-negocio.vista.editar', $item->id) }}"
                                            class="g_accion_editar">
                                            <span><i class="fa-solid fa-pencil"></i></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endif
                </table>
            </div>
        </div>

        @if ($items->hasPages())
            <div class="g_paginacion">
                {{ $items->links('vendor.pagination.default-livewire') }}
            </div>
        @endif

        @if ($items->count() == 0)
            <div class="g_vacio">
                <p>No hay items disponibles.</p>
                <i class="fa-regular fa-face-grin-wink"></i>
            </div>
        @endif
    </div>
</div>