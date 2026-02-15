<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Sub Tipo de Solicitud</h2>

        <div class="cabecera_titulo_botones">
            @can('sub-tipo-solicitud.lista')
                <a href="{{ route('erp.sub-tipo-solicitud.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="store" class="formulario">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel">
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

                    <div class="g_margin_bottom_10">
                        <label for="tipo_solicitud_id">
                            Tipo de Solicitud <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>
                        <select id="tipo_solicitud_id" wire:model.live="tipo_solicitud_id"
                            class="@error('tipo_solicitud_id') input-error @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach ($tipos as $item)
                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                        @error('tipo_solicitud_id')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="nombre">
                                Nombre Sub Tipo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="text" id="nombre" wire:model.blur="nombre"
                                class="@error('nombre') input-error @enderror" autocomplete="off">
                            @error('nombre')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="tiempo_solucion">
                                Tiempo Solución (Horas)
                            </label>
                            <input type="number" id="tiempo_solucion" wire:model.blur="tiempo_solucion"
                                class="@error('tiempo_solucion') input-error @enderror" autocomplete="off">
                            @error('tiempo_solucion')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                            <p class="leyenda">Vacío para heredar del tipo</p>
                        </div>
                    </div>

                    <div class="formulario_botones">
                        @can('sub-tipo-solicitud.crear')
                            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="store">
                                <span wire:loading.remove wire:target="store">
                                    <i class="fa-solid fa-save"></i> Crear
                                </span>
                                <span wire:loading wire:target="store">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Creando...
                                </span>
                            </button>
                        @endcan

                        @can('sub-tipo-solicitud.lista')
                            <button type="button" class="g_boton cancelar" onclick="history.back()">
                                <i class="fa-solid fa-times"></i> Cancelar
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>