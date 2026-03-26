<div style="padding: 20px;">
    <div class="g_panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4 class="g_panel_titulo" style="margin: 0;"><i class="fa-solid fa-file-excel"></i> Importación Masiva de Prospectos</h4>
            <button wire:click="descargarPlantilla" class="g_boton info">
                <i class="fa-solid fa-download"></i> Descargar Plantilla Excel
            </button>
        </div>
        
        <p class="leyenda">Sube el archivo Excel con los datos de los Titulares y Copropietarios seleccionados para participar en el evento <b>{{ $evento->nombre }}</b>.</p>
        
        <div class="g_margin_top_20 formulario">
            <div class="g_margin_bottom_20">
                <input type="file" wire:model="archivo_excel" accept=".xlsx, .xls">
                @error('archivo_excel') <p class="mensaje_error">{{ $message }}</p> @enderror
                
                <div wire:loading wire:target="archivo_excel" class="g_margin_top_10">
                    <p style="font-size: 0.8em; color: var(--color-primary);"><i class="fa-solid fa-spinner fa-spin"></i> Cargando archivo...</p>
                </div>
            </div>

            <div class="formulario_botones">
                <button wire:click="importarProspectos" class="g_boton dark" style="width: 100%;" wire:loading.attr="disabled" wire:target="archivo_excel">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Iniciar Importación 
                </button>
            </div>
        </div>
    </div>
</div>
