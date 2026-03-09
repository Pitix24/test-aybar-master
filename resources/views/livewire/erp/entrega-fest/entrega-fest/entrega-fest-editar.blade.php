<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, agregarProyecto, quitarProyecto" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Entrega Fest</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i></a>

            <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Gestión
            </a>

            <button type="button" class="g_boton danger" onclick="confirmarEliminarCanal()">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>


    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit.prevent="update" class="formulario g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Información General</h4>

                <div class="g_margin_bottom_10">
                    <label for="estado_activo">
                        Estado <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                    </label>

                    <div class="g_switch-wrapper">
                        <label class="g_switch">
                            <input id="estado_activo" type="checkbox" wire:model.live="activo">
                            <span class="g_switch-slider"></span>
                        </label>

                        <span class="g_switch-label">
                            {{ $activo ? 'Activo' : 'Inactivo' }}
                        </span>

                        @error('activo')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Nombre del Evento <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" wire:model="nombre" class="@error('nombre') input-error @enderror">
                        @error('nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                        <p class="leyenda">Ej: Entrega Fest Verano 2026</p>
                    </div>

                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Código único <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" wire:model="codigo" class="@error('codigo') input-error @enderror">
                        @error('codigo') <p class="mensaje_error">{{ $message }}</p> @enderror
                        <p class="leyenda">Ej: EF-2026-001</p>
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Descripción del Evento</label>
                    <textarea wire:model="descripcion" rows="3"></textarea>
                    @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_fila">
                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Fecha de Entrega <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="date" wire:model="fecha_entrega"
                            class="@error('fecha_entrega') input-error @enderror">
                        @error('fecha_entrega') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_10 g_columna_6">
                        <label>Responsable <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <select wire:model="gestor_id" class="@error('gestor_id') select-error @enderror">
                            <option value="">Seleccione...</option>
                            @foreach ($gestores as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                        @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if (!empty($proyectos_agregados))
                    <div class="g_margin_bottom_10">
                        <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Proyectos vinculados</h4>

                        <div class="g_contenedor_tabla">
                            <table class="g_tabla">
                                <thead>
                                    <tr>
                                        <th>Empresa</th>
                                        <th>Proyecto</th>
                                        <th class="g_celda_centro">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proyectos_agregados as $p)
                                        <tr wire:key="p-agregado-{{ $p['id'] }}">
                                            <td class="g_negrita">{{ $p['unidad_negocio_nombre'] }}</td>
                                            <td>ID: {{ $p['id'] }} - {{ $p['nombre'] }} </td>
                                            <td class="g_celda_acciones g_celda_centro">
                                                <button type="button" wire:click="quitarProyecto({{ $p['id'] }})"
                                                    class="g_boton danger" title="Quitar">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update">
                            <i class="fa-solid fa-save"></i> Guardar Cambios
                        </span>
                        <span wire:loading wire:target="update">
                            <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                        </span>
                    </button>

                    <button type="button" class="g_boton cancelar" onclick="history.back()">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>

        <div class="g_columna_4 formulario">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-diagram-project"></i> Proyectos</h4>

                <div class="g_margin_bottom_10">
                    <label>Unidad de Negocio <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <select wire:model.live="unidad_negocio_id"
                        class="@error('unidad_negocio_id') select-error @enderror">
                        <option value="">Seleccione...</option>
                        @foreach ($unidades_negocios as $u)
                            <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                        @endforeach
                    </select>
                    @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_10">
                    <label>Proyecto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <div style="display: flex; gap: 10px;">
                        <select wire:model="proyecto_id" style="flex: 1;" {{ !$unidad_negocio_id ? 'disabled' : '' }}>
                            <option value="">Seleccione un proyecto...</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('proyectos_agregados')
                        <p class="mensaje_error" style="margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="formulario_botones">
                    <button wire:click="agregarProyecto" class="g_boton guardar" wire:loading.attr="disabled"
                        wire:target="agregarProyecto">
                        <span wire:loading.remove wire:target="agregarProyecto"><i class="fa-solid fa-plus"></i>
                            Agregar</span>
                        <span wire:loading wire:target="agregarProyecto">Agregando...</span>
                    </button>
                </div>
            </div>

            @if ($evento->prospectos()->doesntExist())
                <div class="g_panel g_margin_top_20">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 class="g_panel_titulo" style="margin: 0;"><i class="fa-solid fa-file-excel"></i> Importar
                            Prospectos</h4>
                        <button wire:click="descargarPlantilla" class="g_boton info small" title="Descargar formato Excel">
                            <i class="fa-solid fa-download"></i> Plantilla
                        </button>
                    </div>
                    <p class="leyenda" style="margin-bottom: 15px;">Arrastra o selecciona el archivo Excel con el formato de
                        titulares y copropietarios.</p>

                    <div class="g_margin_bottom_10">
                        <input type="file" wire:model="archivo_excel" id="archivo_excel"
                            class="@error('archivo_excel') input-error @enderror">
                        @error('archivo_excel') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="formulario_botones">
                        <button wire:click="importarProspectos" class="g_boton dark" wire:loading.attr="disabled"
                            wire:target="archivo_excel, importarProspectos">
                            <span wire:loading.remove wire:target="importarProspectos">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Procesar Excel
                            </span>
                            <span wire:loading wire:target="importarProspectos">
                                <i class="fa-solid fa-spinner fa-spin"></i> Importando...
                            </span>
                        </button>
                    </div>

                    <div wire:loading wire:target="archivo_excel" class="g_margin_top_10">
                        <p style="font-size: 0.8em; color: var(--color-primary);"><i
                                class="fa-solid fa-spinner fa-spin"></i> Cargando archivo...</p>
                    </div>
                </div>
            @endif

            @if ($evento->itinerarioBloques()->doesntExist())
                <div class="g_panel g_margin_top_20">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 class="g_panel_titulo" style="margin: 0;"><i class="fa-solid fa-clock"></i> Importar
                            Itinerario</h4>
                        <button wire:click="descargarPlantillaItinerario" class="g_boton info small"
                            title="Descargar formato Excel">
                            <i class="fa-solid fa-download"></i> Plantilla
                        </button>
                    </div>
                    <p class="leyenda" style="margin-bottom: 15px;">Carga el cronograma de actividades desde un archivo
                        Excel para este evento.</p>

                    <div class="g_margin_bottom_10">
                        <input type="file" wire:model="archivo_itinerario" id="archivo_itinerario"
                            class="@error('archivo_itinerario') input-error @enderror">
                        @error('archivo_itinerario') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="formulario_botones">
                        <button wire:click="importarItinerario" class="g_boton dark" wire:loading.attr="disabled"
                            wire:target="archivo_itinerario, importarItinerario">
                            <span wire:loading.remove wire:target="importarItinerario">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Procesar Excel
                            </span>
                            <span wire:loading wire:target="importarItinerario">
                                <i class="fa-solid fa-spinner fa-spin"></i> Importando...
                            </span>
                        </button>
                    </div>

                    <div wire:loading wire:target="archivo_itinerario" class="g_margin_top_10">
                        <p style="font-size: 0.8em; color: var(--color-primary);"><i
                                class="fa-solid fa-spinner fa-spin"></i> Cargando archivo...</p>
                    </div>
                </div>
            @endif

            <div class="g_panel g_margin_top_20">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h4 class="g_panel_titulo" style="margin: 0;"><i class="fa-solid fa-tasks"></i> Tareas MOP</h4>
                    <button wire:click="descargarPlantillaMopTareas" class="g_boton info small"
                        title="Descargar formato Excel">
                        <i class="fa-solid fa-download"></i> Plantilla
                    </button>
                </div>
                <p class="leyenda" style="margin-bottom: 15px;">Asigna tareas operativas específicas a miembros del
                    staff para este evento.</p>

                <div class="g_margin_bottom_10">
                    <input type="file" wire:model="archivo_mop_tareas" id="archivo_mop_tareas"
                        class="@error('archivo_mop_tareas') input-error @enderror">
                    @error('archivo_mop_tareas') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="formulario_botones">
                    <button wire:click="importarMopTareas" class="g_boton dark" wire:loading.attr="disabled"
                        wire:target="archivo_mop_tareas, importarMopTareas">
                        <span wire:loading.remove wire:target="importarMopTareas">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Procesar Excel
                        </span>
                        <span wire:loading wire:target="importarMopTareas">
                            <i class="fa-solid fa-spinner fa-spin"></i> Importando...
                        </span>
                    </button>
                </div>

                <div wire:loading wire:target="archivo_mop_tareas" class="g_margin_top_10">
                    <p style="font-size: 0.8em; color: var(--color-primary);"><i
                            class="fa-solid fa-spinner fa-spin"></i> Cargando archivo...</p>
                </div>
            </div>
        </div>
    </div>
</div>