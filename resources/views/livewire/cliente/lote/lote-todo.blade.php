<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="razon_social_id, verCronogramaEstadoCuenta, descargarPDFcronograma, descargarPDFestadoCuenta"
        message="Procesando información..." />

    <div class="g_panel">
        @if (session()->has('success'))
            <div class="g_alerta success g_margin_bottom_10">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="g_alerta error g_margin_bottom_10">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('info'))
            <div class="g_alerta info g_margin_bottom_10">
                <i class="fa-solid fa-circle-info"></i>
                {{ session('info') }}
            </div>
        @endif

        <div class="g_panel_titulo">
            <h2>Mis proyectos</h2>
        </div>

        <div class="formulario">
            <div class="g_fila">
                <div class="g_columna_4">
                    <select wire:model.live="razon_social_id" id="razon_social_id" class="g_input">
                        <option value="" selected>Seleccione Razón Social</option>
                        @foreach ($razones_sociales as $empresa)
                            <option value="{{ $empresa['id_empresa'] }}">
                                {{ $empresa['razon_social'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if ($lote_select)
        <div class="g_panel">
            <div class="g_tabla_cabecera">
                <div class="g_tabla_cabecera_botones">
                    <button wire:click="cerrarVista" class="g_boton dark" wire:loading.attr="disabled" wire:target="cerrarVista">
                        <span wire:loading.remove wire:target="cerrarVista">
                            <i class="fa-solid fa-arrow-left"></i> REGRESAR
                        </span>
                        <span wire:loading wire:target="cerrarVista">
                            <i class="fa-solid fa-spinner fa-spin"></i> Cargando...
                        </span>
                    </button>
                </div>
                <div class="g_tabla_cabecera_botones">
                    <button wire:click="descargarPDFestadoCuenta" class="g_boton guardar" wire:loading.attr="disabled" wire:target="descargarPDFestadoCuenta">
                        <span wire:loading.remove wire:target="descargarPDFestadoCuenta">
                            <i class="fa-solid fa-file-pdf"></i> ESTADO CUENTA
                        </span>
                        <span wire:loading wire:target="descargarPDFestadoCuenta">
                            <i class="fa-solid fa-spinner fa-spin"></i> Generando...
                        </span>
                    </button>

                    <button wire:click="descargarPDFcronograma" class="g_boton guardar" wire:loading.attr="disabled" wire:target="descargarPDFcronograma">
                        <span wire:loading.remove wire:target="descargarPDFcronograma">
                            <i class="fa-solid fa-calendar-days"></i> CRONOGRAMA
                        </span>
                        <span wire:loading wire:target="descargarPDFcronograma">
                            <i class="fa-solid fa-spinner fa-spin"></i> Generando...
                        </span>
                    </button>

                    <button wire:click="descargarPDFletras" class="g_boton guardar" wire:loading.attr="disabled" wire:target="descargarPDFletras">
                        <span wire:loading.remove wire:target="descargarPDFletras">
                            <i class="fa-solid fa-calendar-days"></i> LETRAS
                        </span>
                        <span wire:loading wire:target="descargarPDFletras">
                            <i class="fa-solid fa-spinner fa-spin"></i> Generando...
                        </span>
                    </button>
                </div>
            </div>

            <div class="g_panel_titulo centrar g_margin_top_20">
                <h3>Cronograma de Pagos y Estado de Cuenta</h3>
            </div>

            @livewire('cliente.lote.estado-cuenta-ver', [
                'lote' => $lote_select,
                'estado_cuenta' => $cronograma_estado_cuenta
            ], 'cronograma-estado-' . ($lote_select['id_recaudo'] ?? $lote_select['id_cliente']))
        </div>
    @endif

    @if (!$lote_select)
     <div class="g_panel">
            <div class="g_contenedor_tabla">
                <table class="g_tabla">
                    <thead>
                        <tr>
                            <th>Codigo cliente</th>
                            <th>Proyecto</th>
                            <th>Manzana</th>
                            <th>Lote</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($razon_social_id))
                            <tr>
                                <td colspan="5">
                                    <div class="g_vacio">
                                        <i class="fa-regular fa-face-grin-wink"></i>
                                        <p>Selecciona una razón social para ver tus lotes.</p>
                                    </div>
                                </td>
                            </tr>
                        @elseif (empty($lotes) || count($lotes) === 0)
                            <tr>
                                <td colspan="5">
                                    <div class="g_vacio">
                                        <i class="fa-regular fa-face-grin-wink"></i>
                                        <p>No se encontraron lotes para esta razón social.</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($lotes ?? [] as $lote)
                                <tr>
                                    <td>{{ $lote['id_cliente'] ?? '-' }}</td>
                                    <td>{{ $lote['descripcion'] ?? '-' }}</td>
                                    <td>{{ $lote['id_manzana'] ?? '-' }}</td>
                                    <td>{{ $lote['id_lote'] ?? '-' }}</td>

                                    <td>
                                        <button class="g_boton guardar"
                                            wire:click="verCronogramaEstadoCuenta({{ json_encode($lote) }})">
                                            <i class="fas fa-calendar-alt"></i> Cronograma
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        @if ($razon_social_id)
                            <tr>
                                <td colspan="5">
                                    <span class="g_negrita">
                                        Cantidad de lotes:
                                    </span>
                                    {{ is_array($lotes) ? count($lotes) : 0 }}
                                </td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>