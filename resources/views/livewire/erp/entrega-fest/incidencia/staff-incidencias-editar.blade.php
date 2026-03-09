<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Editar Incidencia
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.incidencia.todo', $evento->id) }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Staff
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    {{-- FORMULARIO DE REPORTE --}}
    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit.prevent="update" class="formulario g_panel g_gap_pagina">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-pencil"></i> Datos de la Incidencia</h4>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Tipo de Problema</label>
                        <select wire:model="tipo">
                            <option value="Logística">Logística</option>
                            <option value="Seguridad">Seguridad</option>
                            <option value="Técnico">Técnico</option>
                            <option value="Salud">Salud</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Prioridad</label>
                        <select wire:model="prioridad">
                            <option value="BAJA">Baja</option>
                            <option value="MEDIA">Media</option>
                            <option value="ALTA">Alta</option>
                        </select>
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Estado Actual</label>
                    <select wire:model="estado">
                        <option value="ABIERTO">ABIERTO</option>
                        <option value="PROCESO">EN PROCESO</option>
                        <option value="RESUELTO">RESUELTO</option>
                    </select>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Responsable Asignado</label>
                    <select wire:model="responsable_user_id">
                        <option value="">Sin asignar</option>
                        @foreach($staff_users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Descripción de los hechos <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <textarea wire:model="descripcion" rows="3"></textarea>
                    @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Ubicación exacta</label>
                        <input type="text" wire:model="ubicacion">
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Añadir más fotos</label>
                        <input type="file" wire:model="fotos" multiple>
                        <div wire:loading wire:target="fotos" class="g_inferior">Subiendo archivo...</div>
                        @error('fotos.*') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update"><i class="fa-solid fa-save"></i> Guardar
                            Cambios</span>
                        <span wire:loading wire:target="update"><i class="fa-solid fa-spinner fa-spin"></i>
                            Actualizando...</span>
                    </button>
                    <a href="{{ route('erp.entrega-fest.incidencia.todo', $evento->id) }}" class="g_boton cancelar"><i
                            class="fa-solid fa-times"></i> Cancelar</a>
                </div>
            </form>
        </div>

        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-images"></i> Galería de Evidencias</h4>
                <div class="g_grid" style="grid-template-columns: repeat(2, 1fr); gap:10px; margin-top:15px;">
                    @foreach($incidencia->getMedia('evidencias') as $media)
                        <div style="position:relative;">
                            <img src="{{ $media->getUrl() }}"
                                style="width:100%; height:80px; object-fit:cover; border-radius:6px; border:1px solid #ddd;">
                            <button type="button" wire:click="eliminarFoto({{ $media->id }})" class="g_boton danger small"
                                style="position:absolute; top:4px; right:4px; width:22px; height:22px; padding:0;">
                                <i class="fa-solid fa-trash" style="font-size:10px;"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
                @if($incidencia->media->count() === 0)
                    <p class="g_inferior" style="margin-top:10px;">No hay evidencias cargadas.</p>
                @endif
            </div>

            <div class="g_panel g_margin_top_20">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-user-clock"></i> Información Adicional</h4>
                <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Reportado por</p>
                <p class="g_negrita" style="margin:0 0 12px 0;">{{ $incidencia->informante->name }}</p>

                <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Fecha y Hora</p>
                <p class="g_negrita" style="margin:0;">{{ $incidencia->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>

</div>