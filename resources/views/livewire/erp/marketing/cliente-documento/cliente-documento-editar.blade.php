<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Documento: {{ $documento->titulo }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.cliente-documento.vista.todo') }}" class="g_boton default">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </a>
            <button wire:click="guardar" class="g_boton primary">
                Guardar <i class="fa-solid fa-floppy-disk"></i>
            </button>
        </div>
    </div>

    <div class="g_panel">
        <div class="formulario">
            <div class="g_fila">
                <div class="g_margin_bottom_20 g_columna_6">
                    <label>Título <span class="g_obligatorio">*</span></label>
                    <input type="text" wire:model="titulo" placeholder="Ej: Plano de Lotización">
                    @error('titulo') <span class="g_error">{{ $message }}</span> @enderror
                </div>

                <div class="g_margin_bottom_20 g_columna_3">
                    <label>Proyecto <span class="g_obligatorio">*</span></label>
                    <select wire:model="proyecto_id">
                        <option value="">Seleccione...</option>
                        @foreach($proyectos as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                    @error('proyecto_id') <span class="g_error">{{ $message }}</span> @enderror
                </div>

                <div class="g_margin_bottom_20 g_columna_3">
                    <label>Tipo de Documento <span class="g_obligatorio">*</span></label>
                    <select wire:model="tipo_cliente_documentos_id">
                        <option value="">Seleccione...</option>
                        @foreach($tipos as $t)
                        <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                    @error('tipo_cliente_documentos_id') <span class="g_error">{{ $message }}</span> @enderror
                </div>

                <div class="g_margin_bottom_20 g_columna_12">
                    <label>Descripción</label>
                    <textarea wire:model="descripcion" rows="3" placeholder="Descripción breve..."></textarea>
                    @error('descripcion') <span class="g_error">{{ $message }}</span> @enderror
                </div>

                <div class="g_margin_bottom_20 g_columna_4">
                    <label>Icono (Opcional)</label>
                    <input type="text" wire:model="icono" placeholder="fa-solid fa-file-pdf">
                    @error('icono') <span class="g_error">{{ $message }}</span> @enderror
                </div>

                <div class="g_margin_bottom_20 g_columna_2">
                    <label>Orden</label>
                    <input type="number" wire:model="orden">
                    @error('orden') <span class="g_error">{{ $message }}</span> @enderror
                </div>

                <div class="g_margin_bottom_20 g_columna_3">
                    <label class="g_checkbox" style="margin-top: 1.8rem;">
                        <input type="checkbox" wire:model="solo_lectura">
                        <span>Solo Lectura (Bloquear descarga)</span>
                    </label>
                    @error('solo_lectura') <span class="g_error">{{ $message }}</span> @enderror
                </div>

                <div class="g_margin_bottom_20 g_columna_3">
                    <label class="g_checkbox" style="margin-top: 1.8rem;">
                        <input type="checkbox" wire:model="activo">
                        <span>Activo</span>
                    </label>
                    @error('activo') <span class="g_error">{{ $message }}</span> @enderror
                </div>

                <div class="g_margin_bottom_20 g_columna_12">
                    <label>Archivo PDF (Subir uno nuevo para reemplazar el actual)</label>
                    @if($documento->archivoPdf)
                    <div style="margin-bottom: 10px;">
                        <a href="{{ $documento->archivoPdf->url }}" target="_blank" class="g_badge outline primary"
                            style="display: inline-block;">
                            <i class="fa-solid fa-file-pdf"></i> Ver PDF Actual
                        </a>
                    </div>
                    @endif
                    <input type="file" wire:model="archivo_nuevo" accept="application/pdf">
                    @error('archivo_nuevo') <span class="g_error">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="archivo_nuevo" class="g_texto_secundario" style="margin-top: 5px;">
                        Cargando archivo... <i class="fa-solid fa-spinner fa-spin"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
