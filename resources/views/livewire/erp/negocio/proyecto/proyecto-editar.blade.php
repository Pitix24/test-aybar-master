<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, eliminarProyectoOn" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Proyecto</h2>

        <div class="cabecera_titulo_botones">
            @can('proyecto.lista')
                <a href="{{ route('erp.proyecto.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('proyecto.crear')
                <a href="{{ route('erp.proyecto.vista.crear') }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i></a>
            @endcan

            @can('proyecto.eliminar')
                <button type="button" class="g_boton danger" onclick="confirmarEliminarProyecto()">
                    Eliminar <i class="fa-solid fa-trash-can"></i>
                </button>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="update" class="formulario">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel" x-data="{ activeTab: 'general' }">

                    <div class="g_tab_navegacion">
                        <div class="g_tab_botones">
                            <button type="button" @click="activeTab = 'general'"
                                :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'"
                                class="g_tab_boton">
                                <i class="fa-solid fa-building"></i> Información General
                            </button>

                            <button type="button" @click="activeTab = 'slin'"
                                :class="activeTab === 'slin' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                                <i class="fa-solid fa-user-tie"></i> SLIN
                            </button>
                        </div>
                    </div>

                    <div x-show="activeTab === 'general'" class="g_tab_content">
                        <div class="g_margin_bottom_20">
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
                            </div>
                            @error('activo')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_20">
                                <label for="unidad_negocio_id">
                                    Unidad de Negocio <span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span>
                                </label>
                                <select id="unidad_negocio_id" wire:model.live="unidad_negocio_id"
                                    class="@error('unidad_negocio_id') input-error @enderror">
                                    <option value="">Seleccionar...</option>
                                    @foreach ($unidades as $un)
                                        <option value="{{ $un->id }}">{{ $un->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('unidad_negocio_id')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="g_columna_6 g_margin_bottom_20">
                                <label for="grupo_proyecto_id">
                                    Grupo de Proyecto <span class="obligatorio"><i
                                            class="fa-solid fa-asterisk"></i></span>
                                </label>
                                <select id="grupo_proyecto_id" wire:model.live="grupo_proyecto_id"
                                    class="@error('grupo_proyecto_id') input-error @enderror">
                                    <option value="">Seleccionar...</option>
                                    @foreach ($grupos as $gp)
                                        <option value="{{ $gp->id }}">{{ $gp->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('grupo_proyecto_id')
                                    <p class="mensaje_error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="g_margin_bottom_20">
                            <label for="nombre">
                                Nombre del Proyecto <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="text" id="nombre" wire:model.blur="nombre"
                                class="@error('nombre') input-error @enderror" autocomplete="off">
                            @error('nombre')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div x-show="activeTab === 'slin'" class="g_tab_content">
                        <div class="g_margin_bottom_20">
                            <label for="slin_id">SLIN ID</label>
                            <input type="text" id="slin_id" wire:model.blur="slin_id"
                                class="@error('slin_id') input-error @enderror" autocomplete="off">
                            @error('slin_id')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                            <p class="leyenda">Este ID es necesario para la integración con CAVALI</p>
                        </div>
                    </div>

                    <div class="formulario_botones">
                        @can('proyecto.editar')
                            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="update">
                                <span wire:loading.remove wire:target="update">
                                    <i class="fa-solid fa-save"></i> Actualizar
                                </span>
                                <span wire:loading wire:target="update">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                                </span>
                            </button>
                        @endcan

                        @can('proyecto.lista')
                            <button type="button" class="g_boton cancelar" onclick="history.back()">
                                <i class="fa-solid fa-times"></i> Cancelar
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </form>

    @script
    <script>
        window.confirmarEliminarProyecto = function () {
            Swal.fire({
                title: '¿Quieres eliminar este proyecto?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '¡Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.eliminarProyectoOn();
                }
            });
        }
    </script>
    @endscript
</div>