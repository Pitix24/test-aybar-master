<div style="padding: 20px;">
    @foreach(['pre-invitacion', 'confirmacion'] as $tipo)
    <div class="g_panel g_margin_bottom_20" style="border-left: 5px solid {{ $tipo == 'pre-invitacion' ? '#4f46e5' : '#10b981' }};">
        <h4 class="g_panel_titulo" style="display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fa-solid {{ $tipo == 'pre-invitacion' ? 'fa-envelope-open' : 'fa-check-double' }}"></i> ETAPA: {{ strtoupper(str_replace('-', ' ', $tipo)) }}</span>
        </h4>

        <div class="formulario">
            <div class="g_fila">
                <div class="g_columna_8">
                    <div class="g_margin_bottom_10">
                        <label>Título / Asunto</label>
                        <input type="text" wire:model="plantillas_data.{{ $tipo }}.titulo" placeholder="Ej: Invitación al Entrega Fest 2026">
                    </div>
                    <div class="g_margin_bottom_10">
                        <label>Subtítulo</label>
                        <input type="text" wire:model="plantillas_data.{{ $tipo }}.subtitulo" placeholder="Ej: Nos encantaría contar con su presencia...">
                    </div>
                </div>
                <div class="g_columna_4">
                    <label>Imagen del mensaje</label>
                    @if($plantillas_data[$tipo]['imagen_url'])
                        <img src="{{ $plantillas_data[$tipo]['imagen_url'] }}" style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px; margin-bottom: 5px; border: 1px solid #ddd;">
                    @else
                        <div style="width: 100%; height: 100px; background-color: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8; border: 1px dashed #cbd5e1; margin-bottom: 5px;">
                            <i class="fa-solid fa-image fa-2x"></i>
                        </div>
                    @endif
                    <input type="file" wire:model="{{ str_replace('-', '_', $tipo) }}_file" accept="image/*" style="font-size: 0.8em;">
                </div>
            </div>

            <div class="g_margin_bottom_10">
                <label>Cuerpo del Mensaje (Descripción)</label>
                <textarea wire:model="plantillas_data.{{ $tipo }}.descripcion" rows="6" placeholder="Escribe el cuerpo del mensaje aquí..."></textarea>
            </div>

            <div class="g_margin_bottom_10">
                <label>Link de Acción (Botón)</label>
                <input type="text" wire:model="plantillas_data.{{ $tipo }}.link_boton" placeholder="Ej: https://plataforma.aybarcorp.com/evento/...">
            </div>

            <div class="formulario_botones g_margin_top_10">
                <button type="button" wire:click="guardarPlantilla('{{ $tipo }}')" class="g_boton {{ $tipo == 'pre-invitacion' ? 'primary' : 'success' }}">
                    <i class="fa-solid fa-save"></i> Guardar Plantilla de {{ ucwords(str_replace('-', ' ', $tipo)) }}
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>
