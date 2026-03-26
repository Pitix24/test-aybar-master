<div style="padding: 20px;">
    <div class="g_panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4 class="g_panel_titulo" style="margin: 0;"><i class="fa-solid fa-tasks"></i> Gestión de Operaciones (MOP)</h4>
            <button wire:click="descargarPlantillaMopTareas" class="g_boton info">
                <i class="fa-solid fa-download"></i> Descargar Plantilla
            </button>
        </div>
        
        <p class="leyenda" style="margin-bottom: 20px;">Asigna tareas operativas masivas a los miembros del staff para la ejecución del evento <b>{{ $evento->nombre }}</b>.</p>
        
        <div class="formulario">
            <div class="g_margin_bottom_20">
                <input type="file" wire:model="archivo_mop_tareas" accept=".xlsx, .xls">
                @error('archivo_mop_tareas') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

            <div class="formulario_botones">
                <button wire:click="importarMopTareas" class="g_boton dark" style="width: 100%;" wire:loading.attr="disabled" wire:target="archivo_mop_tareas">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Cargar Plan de Operaciones
                </button>
            </div>
        </div>
    </div>
</div>
