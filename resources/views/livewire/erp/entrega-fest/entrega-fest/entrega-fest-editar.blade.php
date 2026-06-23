<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, agregarProyecto, quitarProyecto, eliminarEntregaFestOn"
        message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Entrega Fest</h2>

        <div class="cabecera_titulo_botones">
            @can('entrega-fest.lista')
            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('entrega-fest.ver-panel')
            <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Gestión
            </a>
            @endcan

            @if($activo)
            @can('entrega-fest.cancelar')
            <button type="button" onclick="confirmarCancelarEntregaFest()" class="g_boton warning" title="Cancelar Evento">
                Cancelar <i class="fas fa-ban"></i>
            </button>
            @endcan
            @can('entrega-fest.eliminar')
            <button type="button" class="g_boton danger" onclick="confirmarEliminarEntregaFest()">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>
            @endcan
            @else
            <button type="button" class="g_boton warning" title="Evento Cancelado" style="opacity: 0.7; cursor: not-allowed;">
                Cancelado
            </button>
            @endif


            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_fila" x-data="{ activeTab: 'general' }">
        <div class="g_columna_8">
            <div class="g_panel" style="padding: 0;">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-circle-info"></i> Gral.
                        </button>
                        <button type="button" @click="activeTab = 'mensajeria'"
                            :class="activeTab === 'mensajeria' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-envelope-open-text"></i> Mensajería
                        </button>
                        <button type="button" @click="activeTab = 'prospectos'"
                            :class="activeTab === 'prospectos' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-users"></i> Prospectos
                        </button>
                        <button type="button" @click="activeTab = 'itinerario'"
                            :class="activeTab === 'itinerario' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-clock"></i> Cronograma
                        </button>
                        <button type="button" @click="activeTab = 'mop'"
                            :class="activeTab === 'mop' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-tasks"></i> Tareas MOP
                        </button>
                    </div>
                </div>

                <!-- TAB: GENERAL -->
                <div x-show="activeTab === 'general'" x-transition class="g_tab_content" style="padding: 20px;">

                    @if(!$activo)
                    <div class="g_margin_bottom_20" style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; color: #991b1b; border-radius: 4px;">
                        <i class="fa-solid fa-lock"></i> <b>Evento Cancelado:</b> Modo de solo lectura. No se pueden realizar modificaciones.
                    </div>
                    @endif

                    <form wire:submit.prevent="update" class="formulario">
                        <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Información del Evento</h4>

                        <div class="g_margin_bottom_10">
                            <label for="estado_activo">Estado</label>
                            <div class="g_switch-wrapper">
                                <label class="g_switch">
                                    <input id="estado_activo" type="checkbox" wire:model.live="activo" {{ !$activo ? 'disabled' : '' }}>
                                    <span class="g_switch-slider" style="{{ !$activo ? 'opacity: 0.5; cursor: not-allowed;' : '' }}"></span>
                                </label>
                                <span class="g_switch-label">{{ $activo ? 'Activo' : 'Inactivo' }}</span>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Nombre del Evento</label>
                                <input type="text" wire:model="nombre" {{ !$activo ? 'disabled' : '' }}>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Código único</label>
                                <input type="text" wire:model="codigo" {{ !$activo ? 'disabled' : '' }}>
                            </div>
                        </div>

                        <div class="g_margin_bottom_10">
                            <label>Descripción General</label>
                            <textarea wire:model="descripcion" rows="3" {{ !$activo ? 'disabled' : '' }}></textarea>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Fecha de Entrega</label>
                                <input type="date" wire:model="fecha_entrega" {{ !$activo ? 'disabled' : '' }}>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Responsable</label>
                                <select wire:model="gestor_id" {{ !$activo ? 'disabled' : '' }}>
                                    <option value="">Seleccione...</option>
                                    @foreach ($gestores as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($activo)
                        <div class="formulario_botones g_margin_top_10">
                            <button type="submit" class="g_boton guardar">
                                <i class="fa-solid fa-save"></i> Actualizar Información
                            </button>
                        </div>
                        @endif
                    </form>

                    @if (!empty($proyectos_agregados))
                    <div class="g_margin_top_20">
                        <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Proyectos vinculados</h4>
                        <div class="g_contenedor_tabla">
                            <table class="g_tabla">
                                <thead>
                                    <tr>
                                        <th>Empresa</th>
                                        <th>Proyecto</th>
                                        @if($activo) <th class="g_celda_centro">Acciones</th> @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proyectos_agregados as $p)
                                    <tr wire:key="p-agregado-{{ $p['id'] }}">
                                        <td class="g_negrita">{{ $p['unidad_negocio_nombre'] }}</td>
                                        <td>ID: {{ $p['id'] }} - {{ $p['nombre'] }} </td>
                                        @if($activo)
                                        <td class="g_celda_centro">
                                            <button type="button" wire:click="quitarProyecto({{ $p['id'] }})"
                                                class="g_boton danger small">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- TAB: MENSAJERÍA -->
                <div x-show="activeTab === 'mensajeria'">
                    @livewire('erp.entrega-fest.entrega-fest.entrega-fest-mensaje', ['evento' => $evento])
                </div>

                <!-- TAB: PROSPECTOS -->
                <div x-show="activeTab === 'prospectos'">
                    @livewire('erp.entrega-fest.entrega-fest.entrega-fest-importar-prospecto', ['evento' => $evento])
                </div>

                <!-- TAB: ITINERARIO -->
                <div x-show="activeTab === 'itinerario'">
                    @livewire('erp.entrega-fest.entrega-fest.entrega-fest-importar-itinerario', ['evento' => $evento])
                </div>

                <!-- TAB: TAREAS MOP -->
                <div x-show="activeTab === 'mop'">
                    @livewire('erp.entrega-fest.entrega-fest.entrega-fest-importar-mop', ['evento' => $evento])
                </div>

            </div>
        </div>

        <!-- BARRA LATERAL -->
        <div class="g_columna_4">
            <div class="g_panel formulario" x-show="activeTab === 'general'">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-diagram-project"></i> Gestión de Proyectos</h4>
                @if($activo)
                <div class="g_margin_bottom_10">
                    <label>Unidad de Negocio</label>
                    <select wire:model.live="unidad_negocio_id">
                        <option value="">Seleccione...</option>
                        @foreach ($unidades_negocios as $u)
                        <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="g_margin_bottom_10">
                    <label>Proyecto</label>
                    <select wire:model="proyecto_id" {{ !$unidad_negocio_id ? 'disabled' : '' }}>
                        <option value="">Seleccione proyecto...</option>
                        @foreach ($proyectos as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button wire:click="agregarProyecto" class="g_boton guardar g_ancho_completo">
                    <i class="fa-solid fa-plus"></i> Vincular Proyecto
                </button>
                @else
                <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 4px;">
                    <p style="color: #b45309; margin: 0; font-size: 0.9em;">
                        <i class="fa-solid fa-ban"></i> No se pueden vincular más proyectos a este evento.
                    </p>
                </div>
                @endif
            </div>
            @if($activo)
            <div class="g_panel" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;"
                x-show="activeTab !== 'general'">
                <h4 class="g_panel_titulo" style="color: #166534;"><i class="fa-solid fa-circle-info"></i> Resumen</h4>
                <p class="leyenda">Editando evento: <b>{{ $nombre }}</b>.</p>
                <p class="leyenda">Toda la configuración de esta pestaña se guarda independientemente.</p>
            </div>
            @else
            <div class="g_panel" style="background-color: #fef2f2; border: 1px solid #fecaca;"
                x-show="activeTab !== 'general'">
                <h4 class="g_panel_titulo" style="color: #991b1b;"><i class="fa-solid fa-circle-info"></i> Resumen</h4>
                <p class="leyenda">Evento cancelado: <b>{{ $nombre }}</b>.</p>
                <p class="leyenda">No se pueden realizar cambios en las Pestañas Adjuntas.</p>
            @endif
        </div>
    </div>
</div>

@script
<script>
    window.confirmarEliminarEntregaFest = function () {
        Swal.fire({
            title: '¿Quieres eliminar este evento?',
            text: 'Esta acción eliminará el Entrega Fest y sus datos relacionados. No se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '¡Sí, eliminar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.eliminarEntregaFestOn();
            }
        });
    }

    window.confirmarCancelarEntregaFest = function () {
        Swal.fire({
            title: '¿Estás seguro de cancelar este evento?',
            text: "El evento quedará inactivo. Además, se desactivarán todos los prospectos vinculados, prohibiendo su edición.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.cancelarEvento();
            }
        });
    }
</script>
@endscript
