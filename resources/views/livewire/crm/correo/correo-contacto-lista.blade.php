<div class="g_gap_pagina">
    {{-- Cabecera --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Gestión de Contactos (Audiencia)</h2>
        <div class="cabecera_titulo_botones">
            <button class="g_boton primary" wire:click="$dispatch('abrirModalImportacion')">
                <i class="fa-solid fa-file-import"></i> Importar Excel
            </button>
            <a href="{{ route('erp.correo.vista.todo') }}" class="g_boton dark">
                <i class="fa-solid fa-arrow-left"></i> Volver al CRM
            </a>
        </div>
    </div>

    {{-- Filtros y Búsqueda --}}
    <div class="g_panel">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_buscador_input">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" wire:model.live="search" placeholder="Buscar por nombres, apellidos o email...">
                </div>
            </div>
            <div class="g_columna_4">
                <select wire:model.live="lista_id" class="g_columna_12">
                    <option value="">Todas las Listas / Grupos</option>
                    @foreach($listas as $lista)
                        <option value="{{ $lista->id }}">{{ $lista->nombre }} ({{ $lista->contactos_count ?? 0 }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Tabla de Contactos --}}
    <div class="g_panel">
        <div class="tabla_contenido">
            <div class="contenedor_tabla">
                <table class="g_tabla">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombres y Apellidos</th>
                            <th>Email</th>
                            <th>Listas Asignadas</th>
                            <th>Estado</th>
                            <th class="g_celda_centro">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contactos as $contacto)
                            <tr>
                                <td>{{ $contacto->id }}</td>
                                <td class="g_negrita">{{ $contacto->nombres }} {{ $contacto->apellidos }}</td>
                                <td>{{ $contacto->email }}</td>
                                <td>
                                    @forelse($contacto->listas as $l)
                                        <span class="g_badge g_badge_light">{{ $l->nombre }}</span>
                                    @empty
                                        <small class="g_texto_muted">Ninguna</small>
                                    @endforelse
                                </td>
                                <td>
                                    @if($contacto->activo)
                                        <span class="g_badge g_badge_success">Suscrito</span>
                                    @else
                                        <span class="g_badge g_badge_danger">Desuscrito</span>
                                    @endif
                                </td>
                                <td class="g_celda_centro">
                                    <button class="g_accion editar" title="Editar contacto">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="g_celda_centro">No se encontraron contactos en esta selección.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="g_margin_top_10">
                {{ $contactos->links() }}
            </div>
        </div>
    </div>
</div>