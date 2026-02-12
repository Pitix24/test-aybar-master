<div>
    <div class="cronograma_contenedor">
        <div class="tabla_contenido">
            <div class="contenedor_tabla">
                <table class="tabla_info">

                    <tr>
                        <td class="label">Proyecto</td>
                        <td class="valor grande" colspan="3">
                            {{ $estado_cuenta['Proyecto'] ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Etapa</td>
                        <td class="valor">
                            {{ $estado_cuenta['Etapa'] ?? '-' }}
                        </td>

                        <td class="label">Manzana - Lote</td>
                        <td class="valor">
                            {{ $estado_cuenta['Manzana'] ?? '-' }}
                            -
                            {{ $estado_cuenta['Lote'] ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Nombre Cliente</td>
                        <td class="valor" colspan="3">
                            {{ $estado_cuenta['Cliente'] ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <td class="label">DNI</td>
                        <td class="valor"> {{ $estado_cuenta['DNI'] ?? '-' }} </td>

                        <td class="label">Fecha emisión</td>
                        <td class="valor"> {{ $estado_cuenta['FecEmision'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="label">Precio Venta</td>
                        <td class="valor">S/ {{ $estado_cuenta['Venta'] ?? '-' }}</td>

                        <td class="label">Impor. Financiado</td>
                        <td class="valor">S/ {{ $estado_cuenta['ImporteFinanciado'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="label">Inicial</td>
                        <td class="valor">S/ {{ $estado_cuenta['Inicial'] ?? '-' }}</td>

                        <td class="label">Impor. Amortizado</td>
                        <td class="valor">S/ {{ $estado_cuenta['importe_amortizado'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="label">Recaudo</td>
                        <td class="valor" colspan="3">
                            {{ $estado_cuenta['IdRecaudo'] ?? '-' }}
                        </td>
                    </tr>
                </table>

            </div>
        </div>

        <!-- TABLA DETALLE DsEL CRONOGRAMA -->
        <div class="tabla_contenido">
            <div class="contenedor_tabla">
                <table class="tabla_detalle">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Fecha Venc.</th>
                            <th>Cuota</th>
                            <th>Mto. Amortizado</th>
                            <th>Evidencia pago</th>
                            <th>Boleta</th>
                            <th>Letra digital</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($detalle ?? [] as $item)
                            <tr>
                                <td>{{ $item['NroCuota'] ?? '-' }}</td>
                                <td>{{ $item['FecVencimiento'] ?? '-' }}</td>
                                <td> S/ {{ $item['Cuota'] ?? 0 }}</td>
                                <td> S/ {{ $item['CuotaPagada'] ?? 0 }}</td>
                                <td>
                                    @if ($item['EvidPago'])
                                        <span class="g_boton g_boton_empresa_primario"
                                            style="cursor: not-allowed; pointer-events: none;">
                                            <i class="fa-solid fa-circle-check"></i>
                                            Comprobado
                                        </span>
                                    @else
                                        @if ($item['comprobantes_rechazados_count'] > 0)
                                            <x-tooltip
                                                text="Tienes {{ $item['comprobantes_rechazados_count'] }} evidencia(s) rechazada(s)" />
                                        @endif

                                        @if ($item['puede_subir'])
                                            @if ($item['comprobantes_count'] == 0)
                                                <button wire:click="seleccionarCuota({{ json_encode($item) }})"
                                                    class="g_boton g_boton_empresa_secundario">
                                                    <i class="fas fa-upload"></i>
                                                    Subir evidencia
                                                    @if ($item['comprobantes_count'] > 0)
                                                        ({{ $item['comprobantes_count'] }})
                                                    @endif
                                                </button>
                                            @else
                                                <button wire:click="seleccionarCuota({{ json_encode($item) }})"
                                                    class="g_boton g_boton_darkt">
                                                    <i class="fas fa-upload"></i> En validación
                                                    ({{ $item['comprobantes_count'] }})
                                                </button>
                                            @endif
                                        @else
                                            <span class="g_boton g_boton_darkt">
                                                <i class="fa-solid fa-image"></i>
                                                En validación ({{ $item['comprobantes_count'] }})
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($item['Comprobante']))
                                        @if (substr_count($item['Comprobante'], '-') === 2 && $item['SaldoPendiente'] == 0)
                                            <a href="{{ route('slin.comprobante.ver', ['empresa' => $lote['id_empresa'], 'comprobante' => $item['Comprobante']]) }}"
                                                target="_blank" class="g_boton g_boton_empresa_primario">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </a>
                                        @else
                                            <button class="g_boton g_boton_empresa_secundario"
                                                title="Tu boleta está siendo confirmada!" type="button" style="cursor: default;">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </button>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($item['NroCavali']) && $item['SaldoPendiente'] == 0)
                                        @if ($item['tiene_solicitud_digitalizacion'])
                                            <button class="g_boton g_boton_empresa_secundario"
                                                title="Tu letra esta siendo analizada" type="button" style="cursor: default;">
                                                <i class="fas fa-file-shield"></i>
                                            </button>
                                        @else
                                            <button wire:click="verConstanciaCavali({{ json_encode($item) }})"
                                                class="g_boton g_boton_empresa_primario" title="Ver letra digital firmada">
                                                <i class="fas fa-file-shield"></i>
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($cuota)
        <div class="g_modal">
            <div class="modal_contenedor">
                <div class="modal_cerrar">
                    <button wire:click="cerrarModalEvidenciaPago"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <div class="modal_titulo g_panel_titulo">
                    <h2>Subir evidencia de pago</h2>
                </div>

                <div class="modal_cuerpo">
                    @livewire('cliente.lote.adjuntar-voucher-pago', ['cuota' => $cuota, 'lote' => $lote], key('cuota_' . $cuota['idCuota']))
                </div>
            </div>
        </div>
    @endif

    @if ($cuotaCavali)
        <div class="g_modal">
            <div class="modal_contenedor">
                <div class="modal_cerrar">
                    <button wire:click="cerrarModalCavali"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <div class="modal_titulo g_panel_titulo">
                    <h2>Aceptar que tu letra se digitalice</h2>
                </div>

                <div class="modal_cuerpo">
                    @livewire('cliente.lote.aceptar-digitalizar-letra', ['cuota' => $cuotaCavali, 'lote' => $lote], key('cuota_cavali_' . $cuotaCavali['idCuota']))
                </div>
            </div>
        </div>
    @endif
</div>