<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="consultar" message="Consultando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Consultar Constancia de Letra (Cavali)</h2>
    </div>

    <div class="g_fila">
        <div class="g_columna_4">
            <div class="g_panel">
                <div class="formulario">
                    <form wire:submit.prevent="consultar">
                        <div class="g_margin_bottom_10">
                            <label>Número de Letra</label>
                            <input type="text" wire:model.defer="numeroLetra">

                            @error('numeroLetra')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="formulario_botones">
                            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled"
                                wire:target="consultar">
                                <span wire:loading.remove wire:target="consultar">
                                    <i class="fa-solid fa-save"></i> Consultar
                                </span>
                                <span wire:loading wire:target="consultar">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Consultando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="g_columna_8">
            <div class="g_panel">
                @if($resultado && $resultado['codigo'] === '001')
                    <div>
                        <div class="g_tabla_cabecera">
                            <div class="g_tabla_cabecera_botones">
                                <a href="data:application/pdf;base64,{{ $resultado['base64'] }}"
                                    download="constancia_{{ $numeroLetra }}.pdf" class="g_boton success">
                                    Descargar PDF <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        </div>

                        <div style="height: 80vh; width: 100%;">
                            <iframe src="data:application/pdf;base64,{{ $resultado['base64'] }}"
                                style="width: 100%; height: 100%; border: none;"></iframe>
                        </div>
                    </div>
                @elseif($resultado && isset($resultado['error']))
                    <div class="g_alerta error">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <div>
                            <strong>No se pudo obtener la letra:</strong>
                            <p>{{ $resultado['error'] }}</p>
                        </div>
                    </div>
                @else
                    <div class="g_alerta info">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <div>
                            <strong>Ingresa un número de letra para visualizar la constancia de cancelación.</strong>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>