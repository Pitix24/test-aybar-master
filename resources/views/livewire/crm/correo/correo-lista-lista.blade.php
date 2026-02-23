<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Listas / Segmentos de Contactos</h2>
        <div class="cabecera_titulo_botones">
            <button class="g_boton primary">
                <i class="fa-solid fa-folder-plus"></i> Crear Nueva Lista
            </button>
            <a href="{{ route('erp.correo.vista.todo') }}" class="g_boton dark">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>s

    <div class="g_panel">
        <div class="g_buscador_input">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" wire:model.live="search" placeholder="Buscar lista por nombre...">
        </div>
    </div>

    <div class="g_panel">
        <div class="tabla_contenido">
            <div class="contenedor_tabla">
                <table class="g_tabla">
                    <thead>
                        <tr>
                            <th>Nombre de la Lista</th>
                            <th>Descripción</th>
                            <th class="g_celda_centro">Miembros</th>
                            <th>Fecha Creación</th>
                            <th class="g_celda_centro">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listas as $lista)
                            <tr>
                                <td class="g_negrita">{{ $lista->nombre }}</td>
                                <td>{{ $lista->descripcion ?? '-' }}</td>
                                <td class="g_celda_centro">
                                    <span class="g_badge g_badge_success">{{ $lista->contactos_count }}</span>
                                </td>
                                <td>{{ $lista->created_at->format('d/m/Y') }}</td>
                                <td class="g_celda_centro">
                                    <a href="#" class="g_accion editar" title="Gestionar miembros">
                                        <i class="fa-solid fa-users-gear"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="g_celda_centro">No has creado ninguna lista aún.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="g_margin_top_10">
                {{ $listas->links() }}
            </div>
        </div>
    </div>
</div>