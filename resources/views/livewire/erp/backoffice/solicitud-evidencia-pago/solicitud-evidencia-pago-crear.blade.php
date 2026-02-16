<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store" message="Guardando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Nueva Solicitud de Evidencia</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.solicitud-evidencia-pago.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_panel">
        <form wire:submit="store" class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Empresa</label>
                    <select wire:model.live="unidad_negocio_id"
                        class="@error('unidad_negocio_id') input-error @enderror">
                        <option value="">Seleccione...</option>
                        @foreach($unidades_negocios as $un)
                            <option value="{{ $un->id }}">{{ $un->nombre }}</option>
                        @endforeach
                    </select>
                    @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Proyecto</label>
                    <select wire:model.live="proyecto_id" class="@error('proyecto_id') input-error @enderror">
                        <option value="">Seleccione...</option>
                        @foreach($proyectos as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->nombre }}</option>
                        @endforeach
                    </select>
                    @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Gestor Asignado</label>
                    <select wire:model.live="gestor_id" class="@error('gestor_id') input-error @enderror">
                        <option value="">Seleccione...</option>
                        @foreach($gestores as $ge)
                            <option value="{{ $ge->id }}">{{ $ge->name }}</option>
                        @endforeach
                    </select>
                    @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_10 g_columna_3">
                    <label>Estado Inicial</label>
                    <select wire:model.live="estado_id" class="@error('estado_id') input-error @enderror">
                        <option value="">Seleccione...</option>
                        @foreach($estados as $es)
                            <option value="{{ $es->id }}">{{ $es->nombre }}</option>
                        @endforeach
                    </select>
                    @error('estado_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="g_margin_bottom_10">
                <label>Observaciones iniciales</label>
                <textarea wire:model.live="observacion" rows="4"
                    placeholder="Alguna nota o referencia inicial..."></textarea>
                @error('observacion') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

            <div class="formulario_botones">
                <button type="submit" class="g_boton g_boton_guardar">
                    Continuar <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>