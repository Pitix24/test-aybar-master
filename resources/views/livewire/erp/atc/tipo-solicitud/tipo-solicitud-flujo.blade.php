<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="guardarPaso,eliminarPaso,editarPaso,resetForm"
        message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Flujo de Procesos
            <span>Tipo de Solicitud: {{ $tipoSolicitud->nombre }}</span>
        </h2>

        <div class="cabecera_titulo_botones">
            @can('tipo-solicitud.vista-lista')
                <a href="{{ route('erp.tipo-solicitud.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('tipo-solicitud.vista-agregar-usuario')
                <a href="{{ route('erp.tipo-solicitud.vista.usuarios', $tipoSolicitud->id) }}" class="g_boton secondary">
                    Usuarios <i class="fa-solid fa-users"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Pasos Configurados ({{ $pasos->count() }})</h4>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th class="g_celda_centro" style="width: 50px;">Orden</th>
                                <th>Nombre del Paso</th>
                                <th>Descripción</th>
                                <th class="g_celda_centro centro" style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pasos as $paso)
                                <tr wire:key="paso-{{ $paso->id }}">
                                    <td class="g_celda_centro">
                                        <span class="g_badge dark">{{ $paso->orden }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $paso->nombre_paso }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $paso->descripcion ?? 'Sin descripción' }}</small>
                                    </td>
                                    <td class="g_celda_acciones g_celda_centro centro">
                                        <button wire:click="editarPaso({{ $paso->id }})" class="g_boton secondary"
                                            title="Editar">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                        <button wire:click="eliminarPaso({{ $paso->id }})"
                                            wire:confirm="¿Estás seguro de eliminar este paso?" class="g_boton danger"
                                            title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="g_vacio">
                                            <p>No hay pasos configurados para este tipo de solicitud.</p>
                                            <i class="fa-regular fa-face-frown"></i>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo">{{ $editando_id ? 'Editar Paso' : 'Agregar Nuevo Paso' }}</h4>

                <form wire:submit="guardarPaso" class="formulario">
                    <div class="">
                        <div class="g_columna_12">
                            <label>Nombre del Paso <span class="text-danger">*</span></label>
                            <input type="text" wire:model="nombre_paso"
                                placeholder="Ej: Derivar a tal, Solucionar esto...">
                            @error('nombre_paso') <span class="g_error">{{ $message }}</span> @enderror
                        </div>

                        <div class="g_columna_12">
                            <label>Orden <span class="text-danger">*</span></label>
                            <input type="number" wire:model="orden" min="1">
                            @error('orden') <span class="g_error">{{ $message }}</span> @enderror
                        </div>

                        <div class="g_columna_12">
                            <label>Descripción (Opcional)</label>
                            <textarea wire:model="descripcion" rows="3"
                                placeholder="Detalles sobre este paso..."></textarea>
                            @error('descripcion') <span class="g_error">{{ $message }}</span> @enderror
                        </div>

                        <div class="g_columna_12 g_margin_top_10">
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" class="g_boton success" style="flex: 1;">
                                    {{ $editando_id ? 'Actualizar Paso' : 'Agregar Paso' }}
                                    <i class="fa-solid fa-save"></i>
                                </button>

                                @if($editando_id)
                                    <button type="button" wire:click="resetForm" class="g_boton light">
                                        Cancelar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>