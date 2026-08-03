<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="guardar" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Tipo de Documento</h2>

        <div class="cabecera_titulo_botones">
            @can('tipo_cliente_documento.lista')
            <a href="{{ route('erp.tipo-cliente-documento.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <form wire:submit.prevent="guardar" class="formulario g_panel g_gap_pagina">
        <div class="g_fila">
            {{-- COLUMNA IZQUIERDA: INFORMACIÓN GENERAL --}}
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Información General</h4>

                    <div class="g_margin_bottom_10">
                        <label>Nombre del Tipo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" wire:model="nombre" class="@error('nombre') input-error @enderror" placeholder="Ej: Manuales de Usuario">
                        @error('nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Descripción</label>
                        <textarea wire:model="descripcion" rows="4" class="@error('descripcion') input-error @enderror" placeholder="Descripción breve del tipo de documento..."></textarea>
                        @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Icono Principal</label>
                            <input type="text" wire:model="icono" class="@error('icono') input-error @enderror" placeholder="fa-solid fa-file">
                            <p class="leyenda">Ej: fa-solid fa-file-pdf</p>
                            @error('icono') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Icono Documentos</label>
                            <input type="text" wire:model="icono_documentos" class="@error('icono_documentos') input-error @enderror" placeholder="fa-solid fa-file">
                            <p class="leyenda">Opcional para vistas específicas.</p>
                            @error('icono_documentos') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: CONFIGURACIONES --}}
            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Configuraciones</h4>

                    <div class="g_margin_bottom_20">
                        <label>Color Identificador</label>
                        <input type="color" wire:model="color" style="height: 40px; padding: 0; width: 100%; border-radius: 8px; cursor: pointer; border: 1.5px solid #e0e0e0;">
                        @error('color') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_20">
                        <label>Orden de visualización</label>
                        <input type="number" wire:model="orden" class="@error('orden') input-error @enderror" placeholder="0">
                        @error('orden') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_margin_bottom_10">
                        <label for="estado_activo" style="margin-bottom: 8px; display: block;">Estado de Visibilidad</label>
                        <div class="g_switch-wrapper">
                            <label class="g_switch">
                                <input id="estado_activo" type="checkbox" wire:model.live="activo">
                                <span class="g_switch-slider"></span>
                            </label>
                            <span class="g_switch-label">
                                {{ $activo ? 'Activo (Visible)' : 'Inactivo (Oculto)' }}
                            </span>
                        </div>
                        @error('activo') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="formulario_botones">
            @can('tipo_cliente_documento.editar')
            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="guardar">
                <span wire:loading.remove wire:target="guardar">
                    <i class="fa-solid fa-save"></i> Guardar
                </span>
                <span wire:loading wire:target="guardar">
                    <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                </span>
            </button>
            @endcan

            @can('tipo_cliente_documento.lista')
            <button type="button" class="g_boton cancelar" onclick="history.back()">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
            @endcan
        </div>
    </form>
</div>
