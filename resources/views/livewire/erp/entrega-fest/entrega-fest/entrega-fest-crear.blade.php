<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store, agregarProyecto, quitarProyecto" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Entrega Fest</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>


    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit.prevent="store" class="formulario g_panel">
                <h4 class="g_panel_titulo">Información General</h4>

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
                                            <td>{{ $p['nombre'] }}</td>
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
                        <span wire:loading.remove wire:target="store">
                            <i class="fa-solid fa-save"></i> Crear
                        </span>
                        <span wire:loading wire:target="store">
                            <i class="fa-solid fa-spinner fa-spin"></i> Creando...
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
                <h4 class="g_panel_titulo"><i class="fa-solid fa-sliders"></i> Proyectos</h4>

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
        </div>
    </div>
</div>