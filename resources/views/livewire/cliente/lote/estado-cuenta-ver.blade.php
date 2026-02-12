<div>
    <div class="g_panel animate__animated animate__fadeIn">
        <h3 class="g_margin_bottom_20"><i class="fa-solid fa-circle-info"></i> Información del Lote</h3>

        <div class="formulario">
            <div class="g_fila">
                <div class="g_columna_8">
                    <label>Proyecto</label>
                    <p class="g_negrita g_resaltar">{{ $estado_cuenta['Proyecto'] ?? '-' }}</p>
                </div>
                <div class="g_columna_4">
                    <label>Etapa</label>
                    <p class="g_negrita">{{ $estado_cuenta['Etapa'] ?? '-' }}</p>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_4">
                    <label>Manzana - Lote</label>
                    <p class="g_negrita">{{ $estado_cuenta['Manzana'] ?? '-' }} - {{ $estado_cuenta['Lote'] ?? '-' }}</p>
                </div>
                <div class="g_columna_8">
                    <label>Cliente</label>
                    <p>{{ $estado_cuenta['Cliente'] ?? '-' }} ({{ $estado_cuenta['DNI'] ?? '-' }})</p>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_3">
                    <label>Precio Venta</label>
                    <p class="g_negrita">S/ {{ number_format((float) str_replace(',', '', $estado_cuenta['Venta'] ?? 0), 2) }}</p>
                </div>
                <div class="g_columna_3">
                    <label>Inicial</label>
                    <p>S/ {{ number_format((float) str_replace(',', '', $estado_cuenta['Inicial'] ?? 0), 2) }}</p>
                </div>
                <div class="g_columna_3">
                    <label>Financiado</label>
                    <p class="g_texto_info">S/ {{ number_format((float) str_replace(',', '', $estado_cuenta['ImporteFinanciado'] ?? 0), 2) }}</p>
                </div>
                <div class="g_columna_3">
                    <label>Amortizado</label>
                    <p class="g_texto_success">S/ {{ number_format((float) str_replace(',', '', $estado_cuenta['importe_amortizado'] ?? 0), 2) }}</p>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_6">
                    <label>Código Recaudo</label>
                    <p class="g_badge g_badge_soft g_badge_dark">{{ $estado_cuenta['IdRecaudo'] ?? '-' }}</p>
                </div>
                <div class="g_columna_6">
                    <label>Fecha Emisión</label>
                    <p>{{ $estado_cuenta['FecEmision'] ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLA DETALLE DEL CRONOGRAMA -->
    <div class="g_panel animate__animated animate__fadeIn">
        <h3 class="g_margin_bottom_20"><i class="fa-solid fa-list-ol"></i> Detalle de Cuotas</h3>

        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th class="g_celda_centro">Nro</th>
                        <th>Vencimiento</th>
                        <th class="g_celda_derecha">Mto. Cuota</th>
                        <th class="g_celda_derecha">Amortizado</th>
                        <th class="g_celda_centro">Evidencia Pago</th>
                        <th class="g_celda_centro">Boleta</th>
                        <th class="g_celda_centro">Letra Digital</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($detalle ?? [] as $item)
                        <tr wire:key="cuota-row-{{ $item['idCuota'] }}">
                            <td class="g_celda_centro g_negrita">{{ $item['NroCuota'] ?? '-' }}</td>
                            <td class="g_inferior">{{ $item['FecVencimiento'] ?? '-' }}</td>
                            <td class="g_celda_derecha g_negrita">S/ {{ number_format((float) str_replace(',', '', $item['Cuota'] ?? 0), 2) }}</td>
                            <td class="g_celda_derecha g_texto_success">S/ {{ number_format((float) str_replace(',', '', $item['CuotaPagada'] ?? 0), 2) }}</td>
                            <td class="g_celda_centro">
                                @if ($item['EvidPago'])
                                    <span class="g_badge g_badge_soft g_badge_success">
                                        <i class="fa-solid fa-circle-check"></i> Verificado
                                    </span>
                                @else
                                    @if ($item['comprobantes_rechazados_count'] > 0)
                                        <x-tooltip text="Tienes {{ $item['comprobantes_rechazados_count'] }} evidencia(s) rechazada(s)" />
                                    @endif

                                    @if ($item['puede_subir'])
                                        <button wire:click="seleccionarCuota({{ json_encode($item) }})"
                                            class="g_boton {{ $item['comprobantes_count'] == 0 ? 'g_boton_primary' : 'g_boton_dark' }} g_boton_xs">
                                            <i class="fas fa-upload"></i>
                                            {{ $item['comprobantes_count'] == 0 ? 'Subir Evidencia' : 'En Validación (' . $item['comprobantes_count'] . ')' }}
                                        </button>
                                    @else
                                        <span class="g_badge g_badge_soft g_badge_info">
                                            <i class="fa-solid fa-clock-rotate-left"></i> En Validación ({{ $item['comprobantes_count'] }})
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="g_celda_centro">
                                @if (!empty($item['Comprobante']))
                                    @if (substr_count($item['Comprobante'], '-') === 2 && ($item['SaldoPendiente'] ?? 0) <= 0)
                                        <a href="{{ route('slin.comprobante.ver', ['empresa' => $lote['id_empresa'], 'comprobante' => $item['Comprobante']]) }}"
                                            target="_blank" class="g_boton g_boton_excel g_boton_xs" title="Ver Boleta">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </a>
                                    @else
                                        <button class="g_boton g_boton_dark g_boton_xs"
                                            title="Tu boleta está siendo confirmada" type="button" style="cursor: default; opacity: 0.6;">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </button>
                                    @endif
                                @endif
                            </td>
                            <td class="g_celda_centro">
                                @if (!empty($item['NroCavali']) && ($item['SaldoPendiente'] ?? 0) <= 0)
                                    @if ($item['tiene_solicitud_digitalizacion'])
                                        <button class="g_boton g_boton_warning g_boton_xs"
                                            title="Tu letra está siendo analizada" type="button" style="cursor: default;">
                                            <i class="fas fa-file-shield"></i>
                                        </button>
                                    @else
                                        <button wire:click="verConstanciaCavali({{ json_encode($item) }})"
                                            class="g_boton g_boton_info g_boton_xs" title="Ver letra digital firmada">
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

    @if ($cuota)
        <div class="g_modal">
            <div class="modal_contenedor">
                <div class="modal_cerrar">
                    <button wire:click="cerrarModalEvidenciaPago"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <div class="modal_titulo g_panel_titulo">
                    <h2>Subir Evidencia de Pago</h2>
                </div>

                <div class="modal_cuerpo">
                    @livewire('cliente.lote.adjuntar-voucher-pago', [
                        'cuota' => $cuota,
                        'lote' => $lote
                    ], 'modal-adjuntar-' . ($cuota['idCuota'] ?? 'new'))
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
                    <h2>Digitalización de Letra</h2>
                </div>

                <div class="modal_cuerpo">
                    @livewire('cliente.lote.aceptar-digitalizar-letra', [
                        'cuota' => $cuotaCavali,
                        'lote' => $lote
                    ], 'modal-cavali-' . ($cuotaCavali['idCuota'] ?? 'new'))
                </div>
            </div>
        </div>
    @endif
</div>