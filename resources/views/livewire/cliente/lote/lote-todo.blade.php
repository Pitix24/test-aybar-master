<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="razon_social_id, verCronogramaEstadoCuenta, descargarPDFcronograma, descargarPDFestadoCuenta"
        message="Procesando información..." />

    <div class="g_panel">
        <div class="g_panel_titulo">
            <h2><i class="fa-solid fa-house-laptop"></i> Mis Proyectos</h2>
        </div>

        @if (session()->has('error'))
            <div class="g_alerta_error g_margin_bottom_20">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('info'))
            <div class="g_alerta_info g_margin_bottom_20">
                <i class="fa-solid fa-circle-info"></i>
                {{ session('info') }}
            </div>
        @endif

        <div class="formulario">
            <div class="g_fila">
                <div class="g_columna_6">
                    <label for="razon_social_id">Selecciona una Razón Social para ver tus lotes</label>
                    <select wire:model.live="razon_social_id" id="razon_social_id" class="g_input">
                        <option value="" selected>Seleccione una opción...</option>
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

    @if ($lote_select && $vista === 'cronograma_estado_cuenta')
        <div class="g_panel animate__animated animate__fadeIn">
            <div class="g_tabla_cabecera">
                <div class="g_tabla_cabecera_botones">
                    <button wire:click="cerrarVista" class="g_boton g_boton_dark">
                        <i class="fa-solid fa-arrow-left"></i> REGRESAR
                    </button>
                </div>
                <div class="g_tabla_cabecera_botones">
                    <button wire:click="descargarPDFestadoCuenta" class="g_boton g_boton_excel">
                        <i class="fa-solid fa-file-pdf"></i> ESTADO CUENTA
                    </button>

                    <button wire:click="descargarPDFcronograma" class="g_boton g_boton_excel" style="background-color: #6366f1;">
                        <i class="fa-solid fa-calendar-days"></i> CRONOGRAMA
                    </button>
                </div>
            </div>

            <div class="g_panel_titulo centrar g_margin_top_20">
                <h3>Cronograma de Pagos y Estado de Cuenta</h3>
            </div>

            @livewire('cliente.lote.estado-cuenta-ver', [
                'lote' => $lote_select,
                'estado_cuenta' => $cronograma_estado_cuenta
            ], key: 'cronograma-estado-' . ($lote_select['id_recaudo'] ?? $lote_select['id_cliente']))
        </div>
    @endif

    @if (!$lote_select)
        <div class="g_panel animate__animated animate__fadeIn">
            <div class="g_contenedor_tabla">
                <table class="g_tabla">
                    <thead>
                        <tr>
                            <th class="g_celda_centro">Nro. Cliente</th>
                            <th>Proyecto / Descripción</th>
                            <th class="g_celda_centro">Mz.</th>
                            <th class="g_celda_centro">Lt.</th>
                            <th class="g_celda_centro">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($razon_social_id))
                            <tr>
                                <td colspan="5">
                                    <div class="g_vacio">
                                        <p>Por favor, selecciona una razón social para visualizar tus lotes activos.</p>
                                        <i class="fa-solid fa-arrow-up"></i>
                                    </div>
                                </td>
                            </tr>
                        @elseif (empty($lotes))
                            <tr>
                                <td colspan="5">
                                    <div class="g_vacio">
                                        <p>No se encontraron lotes vinculados a esta razón social.</p>
                                        <i class="fa-regular fa-face-frown"></i>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($lotes as $lote)
                                <tr wire:key="lote-{{ $lote['id_recaudo'] }}">
                                    <td class="g_celda_centro g_negrita">{{ $lote['id_cliente'] ?? '-' }}</td>
                                    <td class="g_resumir g_resaltar">{{ $lote['descripcion'] ?? '-' }}</td>
                                    <td class="g_celda_centro">{{ $lote['id_manzana'] ?? '-' }}</td>
                                    <td class="g_celda_centro">{{ $lote['id_lote'] ?? '-' }}</td>
                                    <td class="g_celda_centro">
                                        <button class="g_boton g_boton_info g_boton_sm"
                                            wire:click="verCronogramaEstadoCuenta({{ json_encode($lote) }})">
                                            <i class="fas fa-calendar-alt"></i> Ver Detalle
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    @if (!empty($lotes))
                        <tfoot>
                            <tr>
                                <td colspan="5" class="g_inferior">
                                    Total de lotes encontrados: <strong>{{ count($lotes) }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @endif
</div>