<div class="g_gap_pagina">
    <x-loading-overlay message="Cargando..." />

    <div class="g_panel">
        @if (session()->has('error'))
            <div class="g_alerta_info">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="g_panel_titulo">
            <h2>Mis proyectos</h2>
        </div>

        <div class="formulario">
            <div class="g_fila">
                <div class="g_columna_4">
                    <select wire:model.live="razon_social_id" id="razon_social_id" name="razon_social_id">
                        <option value="" selected disabled>Seleccione Razón Social</option>
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

    @if ($lote_select && $vista === 'cronograma')
        <div class="g_panel">
            <div class="tabla_cabecera_botones">
                <button wire:click="cerrarVista" class="g_boton g_boton_darkt">
                    <i class="fa-solid fa-arrow-left"></i> REGRESAR
                </button>

                <button wire:click="descargarPDFcronograma" class="g_boton g_boton_empresa_primario">
                    PDF <i class="fa-solid fa-file-pdf"></i>
                </button>
            </div>

            <div class="g_panel_titulo centrar">
                <h2>CRONOGRAMA DE PAGOS</h2>
            </div>

            @livewire('cliente.cronograma.cronograma-ver-livewire', ['lote' => $lote_select, 'cronograma' => $cronograma], key('cronograma-' . $lote_select['id_recaudo']))
        </div>
    @endif

    @if ($lote_select && $vista === 'estado_cuenta')
        <div class="g_panel">
            <div class="tabla_cabecera_botones">
                <button wire:click="cerrarVista" class="g_boton g_boton_darkt">
                    <i class="fa-solid fa-arrow-left"></i> REGRESAR
                </button>

                <button wire:click="descargarPDFestadoCuenta" class="g_boton g_boton_empresa_primario">
                    PDF <i class="fa-solid fa-file-pdf"></i>
                </button>
            </div>

            <div class="g_panel_titulo centrar">
                <h2>ESTADO DE CUENTA</h2>
            </div>

            @livewire('cliente.estado-cuenta.estado-cuenta-ver-livewire', ['lote' => $lote_select, 'estado_cuenta' => $estado_cuenta], key('estado-' . $lote_select['id_recaudo']))
        </div>
    @endif

    @if ($lote_select && $vista === 'cronograma_estado_cuenta')
        <div class="g_panel">
            <div class="tabla_cabecera_botones">
                <button wire:click="cerrarVista" class="g_boton g_boton_darkt">
                    <i class="fa-solid fa-arrow-left"></i> REGRESAR
                </button>

                <button wire:click="descargarPDFestadoCuenta" class="g_boton g_boton_empresa_primario">
                    ESTADO CUENTA<i class="fa-solid fa-download"></i>
                </button>

                <button wire:click="descargarPDFcronograma" class="g_boton g_boton_empresa_secundario">
                    CRONOGRAMA<i class="fa-solid fa-download"></i>
                </button>
            </div>

            <div class="g_panel_titulo centrar">
                <h2>CRONOGRAMA DE PAGOS</h2>
            </div>

            @livewire('cliente.cronograma-estado-cuenta.cronograma-estado-cuenta-ver-livewire', ['lote' => $lote_select, 'estado_cuenta' => $cronograma_estado_cuenta], key('cronograma-estado-' . $lote_select['id_recaudo']))
        </div>
    @endif

    @if (!$lote_select)
        <div class="g_panel tabla_contenido">
            <div class="contenedor_tabla borde">
                <table class="tabla">
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
                                <td colspan="5" style="text-align: center; padding: 20px;">
                                    Selecciona una razón social para ver tus lotes.
                                </td>
                            </tr>
                        @elseif (empty($lotes) || count($lotes) === 0)
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 20px;">
                                    No se encontraron lotes para esta razón social.
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
                                        <button class="g_boton g_boton_empresa_primario"
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
                                    Cantidad de lotes:
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