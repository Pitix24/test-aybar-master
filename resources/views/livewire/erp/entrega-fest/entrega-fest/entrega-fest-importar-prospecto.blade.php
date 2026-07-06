<div style="padding: 20px;">
    @if(!$evento->activo)
    <div class="g_margin_bottom_20" style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; color: #991b1b; border-radius: 4px;">
        <i class="fa-solid fa-lock"></i> <b>Evento Cancelado:</b> No se permite la carga masiva ni autocarga de prospectos en eventos inactivos.
    </div>
    @else
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
    @can('prospecto-historico.cargar-desde-historico')
    <div class="g_panel" style="margin-top: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4 class="g_panel_titulo" style="margin: 0; color: var(--color-primary);">
                <i class="fa-solid fa-robot"></i> Autocarga Inteligente desde Histórico
            </h4>
        </div>

        <p class="leyenda" style="margin-bottom: 20px;">
            El sistema buscará automáticamente en la Base de Datos Maestra a todos los clientes que pertenecen a los proyectos asignados a este evento.
            <br><br>
            <span style="color: #e74c3c; font-weight: 600;"><i class="fa-solid fa-shield-halved"></i> Filtro Activo:</span>
            Se omitirán automáticamente los clientes que tengan la marca de <b>Lote Entregado</b> en el Histórico, así como aquellos que ya estén en la lista del evento actual.
        </p>

        <div class="formulario_botones">
            <button
                wire:click="cargarDesdeHistorico"
                wire:confirm="¿Estás seguro de iniciar la autocarga? Esta acción traerá a todos los clientes pendientes de los proyectos vinculados a este evento."
                class="g_boton primary"
                style="width: 100%; font-weight: bold;"
                wire:loading.attr="disabled"
                wire:target="cargarDesdeHistorico"
            >
                <span wire:loading.remove wire:target="cargarDesdeHistorico">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Iniciar Carga Inteligente
                </span>
                <span wire:loading wire:target="cargarDesdeHistorico">
                    <i class="fa-solid fa-spinner fa-spin"></i> Analizando e inyectando prospectos...
                </span>
            </button>
        </div>
    </div>
    @endcan
    @endif
</div>
