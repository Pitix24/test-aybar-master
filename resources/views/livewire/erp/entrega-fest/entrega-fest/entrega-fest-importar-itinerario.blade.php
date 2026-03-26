<div style="padding: 20px;">
    <div class="g_panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4 class="g_panel_titulo" style="margin: 0;"><i class="fa-solid fa-calendar-alt"></i> Planificación del Evento</h4>
            <button wire:click="descargarPlantillaItinerario" class="g_boton info">
                <i class="fa-solid fa-download"></i> Descargar Formato
            </button>
        </div>
        
        <p class="leyenda" style="margin-bottom: 20px;">Carga el cronograma de actividades desde un archivo Excel para el evento <b>{{ $evento->nombre }}</b>.</p>
        
        <div class="formulario">
            <div class="g_margin_bottom_20">
                <input type="file" wire:model="archivo_itinerario" accept=".xlsx, .xls">
                @error('archivo_itinerario') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

            <div class="formulario_botones">
                <button wire:click="importarItinerario" class="g_boton dark" style="width: 100%;" wire:loading.attr="disabled" wire:target="archivo_itinerario">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Cargar Itinerario del Evento
                </button>
            </div>
        </div>
    </div>
</div>
