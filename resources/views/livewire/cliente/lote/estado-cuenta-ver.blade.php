<div>
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

    <div class="cronograma_contenedor">
        <div class="g_contenedor_tabla">
            <table class="g_tabla tabla_info">

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

        <div class="g_contenedor_tabla">
            <table class="g_tabla tabla_detalle">
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
                        <tr wire:key="cuota-{{ $item['idCuota'] }}">
                            <td>{{ $item['NroCuota'] ?? '-' }}</td>
                            <td>{{ $item['FecVencimiento'] ?? '-' }}</td>
                            <td> S/ {{ $item['Cuota'] ?? 0 }}</td>
                            <td> S/ {{ $item['CuotaPagada'] ?? 0 }}</td>
                            <td>
                                @if ($item['EvidPago'])
                                    <span class="g_boton guardar" style="cursor: not-allowed; pointer-events: none;">
                                        <i class="fa-solid fa-circle-check"></i>
                                        Comprobado
                                    </span>
                                @else
                                    @if (($item['comprobantes_rechazados_count'] ?? 0) > 0)
                                        <x-tooltip
                                            text="Tienes {{ $item['comprobantes_rechazados_count'] }} evidencia(s) rechazada(s)" />
                                    @endif

                                    @if ($item['puede_subir'] ?? false)
                                        @if (($item['comprobantes_count'] ?? 0) == 0)
                                            <button wire:click="seleccionarCuota({{ json_encode($item) }})" class="g_boton cancelar"
                                                wire:loading.attr="disabled" wire:target="seleccionarCuota({{ json_encode($item) }})">
                                                <span wire:loading.remove wire:target="seleccionarCuota({{ json_encode($item) }})">
                                                    <i class="fas fa-upload"></i> Subir evidencia
                                                </span>
                                                <span wire:loading wire:target="seleccionarCuota({{ json_encode($item) }})">
                                                    <i class="fa-solid fa-spinner fa-spin"></i> Cargando...
                                                </span>
                                            </button>
                                        @else
                                            <button wire:click="seleccionarCuota({{ json_encode($item) }})" class="g_boton dark"
                                                wire:loading.attr="disabled" wire:target="seleccionarCuota({{ json_encode($item) }})">
                                                <span wire:loading.remove wire:target="seleccionarCuota({{ json_encode($item) }})">
                                                    <i class="fas fa-upload"></i> En validación ({{ $item['comprobantes_count'] }})
                                                </span>
                                                <span wire:loading wire:target="seleccionarCuota({{ json_encode($item) }})">
                                                    <i class="fa-solid fa-spinner fa-spin"></i> Cargando...
                                                </span>
                                            </button>
                                        @endif
                                    @else
                                        <span class="g_boton dark">
                                            <i class="fa-solid fa-image"></i> En validación ({{ $item['comprobantes_count'] ?? 0 }})
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if (!empty($item['Comprobante']))
                                    @if (substr_count($item['Comprobante'], '-') === 2 && ($item['SaldoPendiente'] ?? 0) == 0)
                                        <a href="{{ route('slin.comprobante.ver', ['empresa' => $lote['id_empresa'], 'comprobante' => $item['Comprobante']]) }}"
                                            target="_blank" class="g_boton guardar">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </a>
                                    @else
                                        <button class="g_boton cancelar" title="Tu boleta está siendo confirmada!" type="button"
                                            style="cursor: default;">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </button>
                                    @endif
                                @endif
                            </td>
                            <td>
                                {{--@if (!empty($item['NroCavali']) && ($item['SaldoPendiente'] ?? 0) == 0)--}}
                                @if (($item['Comprobante'] || $item['Ticket']) && ($item['SaldoPendiente'] ?? 0) == 0)
                                    @if ($item['tiene_constancia_cavali'] ?? false)
                                        <button wire:click="verConstanciaCavali({{ json_encode($item) }})" class="g_boton guardar"
                                            title="Ver letra digital firmada" wire:loading.attr="disabled"
                                            wire:target="verConstanciaCavali({{ json_encode($item) }})">
                                            <span wire:loading.remove wire:target="verConstanciaCavali({{ json_encode($item) }})">
                                                <i class="fas fa-file-shield"></i>
                                            </span>
                                            <span wire:loading wire:target="verConstanciaCavali({{ json_encode($item) }})">
                                                <i class="fa-solid fa-spinner fa-spin"></i>
                                            </span>
                                        </button>
                                    @elseif ($item['letra_digitalizada_local'] ?? false)
                                        <button wire:click="verConstanciaCavali({{ json_encode($item) }})" class="g_boton info"
                                            title="Ver letra digital local" wire:loading.attr="disabled"
                                            wire:target="verConstanciaCavali({{ json_encode($item) }})">
                                            <span wire:loading.remove wire:target="verConstanciaCavali({{ json_encode($item) }})">
                                                <i class="fas fa-file-shield"></i>
                                            </span>
                                            <span wire:loading wire:target="verConstanciaCavali({{ json_encode($item) }})">
                                                <i class="fa-solid fa-spinner fa-spin"></i>
                                            </span>
                                        </button>
                                    @elseif ($item['tiene_solicitud_digitalizacion'] ?? false)
                                        <button class="g_boton light" title="Tu letra esta siendo analizada" type="button"
                                            style="cursor: default;">
                                            <i class="fas fa-file-shield"></i>
                                        </button>
                                    @else
                                        <button wire:click="verConstanciaCavali({{ json_encode($item) }})" class="g_boton cancelar"
                                            title="Solicitar Digitalización" wire:loading.attr="disabled"
                                            wire:target="verConstanciaCavali({{ json_encode($item) }})">
                                            <span wire:loading.remove wire:target="verConstanciaCavali({{ json_encode($item) }})">
                                                <i class="fa-solid fa-file-circle-question"></i>
                                            </span>
                                            <span wire:loading wire:target="verConstanciaCavali({{ json_encode($item) }})">
                                                <i class="fa-solid fa-spinner fa-spin"></i>
                                            </span>
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
            <x-modal title="Subir Evidencia de Pago" wireClose="cerrarModalEvidenciaPago">
                @livewire('cliente.lote.adjuntar-voucher-pago', [
                    'cuota' => $cuota,
                    'lote' => $lote
                ], 'modal-adjuntar-' . ($cuota['idCuota'] ?? 'new'))
        </x-modal>
     @endif
@if ($cuotaCavali)
                    <x-modal title="Digitalización de Letra" wireClose="cerrarModalCavali">
        @livewire('cliente.lote.aceptar-digitalizar-letra', [
            'cuota' => $cuotaCavali,
            'lote' => $lote
        ], 'modal-cavali-' . ($cuotaCavali['idCuota'] ?? 'new'))
    </x-modal>
@endif
</div>