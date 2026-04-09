<div style="padding: 20px;">
    <x-loading-overlay wire:loading wire:target="importarTickets" message="Procesando importación, por favor espere..." />

    {{-- Panel de subida --}}
    <div class="g_panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4 class="g_panel_titulo" style="margin: 0;"><i class="fa-solid fa-file-excel"></i> Importación Masiva de Tickets</h4>
            <button wire:click="descargarPlantilla" class="g_boton info">
                <i class="fa-solid fa-download"></i> Descargar Plantilla Excel
            </button>
        </div>

        <p class="leyenda">Sube el archivo Excel con los datos de los Tickets que deseas importar al sistema de Atención al Cliente (ATC).</p>

        <div class="g_margin_top_20 formulario">
            <div class="g_margin_bottom_20">
                <input type="file" wire:model="archivo_excel" accept=".xlsx, .xls">
                @error('archivo_excel') <p class="mensaje_error">{{ $message }}</p> @enderror

                <div wire:loading wire:target="archivo_excel" class="g_margin_top_10">
                    <p style="font-size: 0.8em; color: var(--color-primary);">
                        <i class="fa-solid fa-spinner fa-spin"></i> Cargando archivo...
                    </p>
                </div>
            </div>

            <div class="formulario_botones">
                <button wire:click="importarTickets" class="g_boton dark" style="width: 100%;"
                    wire:loading.attr="disabled" wire:target="importarTickets, archivo_excel">
                    <span wire:loading.remove wire:target="importarTickets">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Iniciar Importación
                    </span>
                    <span wire:loading wire:target="importarTickets">
                        <i class="fa-solid fa-spinner fa-spin"></i> Procesando...
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- Columnas detectadas (debug) --}}
    @if(count($columnasDetectadas) > 0)
        <div class="g_panel g_margin_top_20" style="border-left: 3px solid var(--color-info, #3b82f6);">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-table-columns"></i> Columnas detectadas en el Excel</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px;">
                @foreach($columnasDetectadas as $col)
                    <span class="g_badge light">{{ $col }}</span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Errores --}}
    @if(count($erroresImportacion) > 0)
        <div class="g_panel g_margin_top_20" style="border-left: 3px solid var(--color-danger, #ef4444);">
            <h4 class="g_panel_titulo" style="color: var(--color-danger, #ef4444);">
                <i class="fa-solid fa-triangle-exclamation"></i> Filas con Errores ({{ count($erroresImportacion) }})
            </h4>
            <div style="max-height: 250px; overflow-y: auto; margin-top: 10px;">
                @foreach($erroresImportacion as $error)
                    <p style="font-size: 0.82em; margin: 4px 0; padding: 4px 8px; background: rgba(239,68,68,0.07); border-radius: 4px;">
                        <i class="fa-solid fa-xmark" style="color: #ef4444;"></i> {{ $error }}
                    </p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Registros importados --}}
    @if(count($registrosImportados) > 0)
        <div class="g_panel g_margin_top_20">
            <h4 class="g_panel_titulo">
                <i class="fa-solid fa-list-check"></i> Registros Importados ({{ count($registrosImportados) }})
            </h4>
            <div class="g_contenedor_tabla">
                <table class="g_tabla">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Asunto</th>
                            <th>Cliente</th>
                            <th>DNI</th>
                            <th>Fecha Registro</th>
                            <th class="g_celda_centro">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrosImportados as $reg)
                            <tr>
                                <td><span class="g_badge light">#{{ $reg['id'] }}</span></td>
                                <td class="g_negrita">{{ $reg['asunto'] }}</td>
                                <td>{{ $reg['nombres'] }}</td>
                                <td>{{ $reg['dni'] }}</td>
                                <td>{{ $reg['fecha'] }}</td>
                                <td class="g_celda_acciones g_celda_centro">
                                    <a href="{{ route('erp.ticket.vista.ver', $reg['id']) }}" class="g_accion ver" target="_blank">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
