<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="descargarAceptantes, descargarLetras, descargarGirador, descargarArchivo"
        message="Preparando descarga..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle de Envío CAVALI #{{ $envio->id }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.envio-cavali-solicitud.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_panel">
        <h4 class="g_panel_titulo">Información General</h4>
        <div class="formulario">
            <div class="g_fila">
                <div class="g_columna_3 g_margin_bottom_10">
                    <label>Unidad de Negocio</label>
                    <p class="g_resaltar">{{ $envio->unidadNegocio?->nombre ?? '—' }}</p>
                </div>

                <div class="g_columna_3 g_margin_bottom_10">
                    <label>Fecha de Corte</label>
                    <p class="g_negrita">{{ $envio->fecha_corte->format('d/m/Y') }}</p>
                </div>

                <div class="g_columna_3 g_margin_bottom_10">
                    <label>Estado</label>
                    <p>
                        @php
                            $badgeClass = match ($envio->estado) {
                                'pendiente' => 'g_badge_warning',
                                'enviado' => 'g_badge_info',
                                'observado' => 'g_badge_danger',
                                'aceptado' => 'g_badge_success',
                                default => 'g_badge_secondary'
                            };
                        @endphp
                        <span class="g_badge {{ $badgeClass }}">{{ ucfirst($envio->estado ?? 'pendiente') }}</span>
                    </p>
                </div>

                <div class="g_columna_3 g_margin_bottom_10">
                    <label>Archivo Zip</label>
                    @if($envio->archivo_zip)
                        <button wire:click="descargarArchivo" class="g_boton g_boton_success g_boton_sm">
                            <i class="fa-solid fa-download"></i> {{ $envio->archivo_nombre }}
                        </button>
                    @else
                        <span class="g_texto_secundario">No disponible</span>
                    @endif
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_3 g_margin_bottom_10">
                    <label>Solicitudes Vinculadas</label>
                    <p class="g_negrita">{{ $envio->solicitudes->count() }}</p>
                </div>

                <div class="g_columna_3 g_margin_bottom_10">
                    <label>Fecha de Envío</label>
                    <p>{{ $envio->enviado_at?->format('d/m/Y H:i') ?? 'No enviado' }}</p>
                </div>
            </div>
        </div>

        <div class="g_tabla_cabecera g_margin_top_20">
            <div class="g_tabla_cabecera_botones" style="display: flex; gap: 10px;">
                <button wire:click="descargarAceptantes" class="g_boton g_boton_primary">
                    <i class="fa-solid fa-file-excel"></i> ACEPTANTES
                </button>

                <button wire:click="descargarLetras" class="g_boton g_boton_primary">
                    <i class="fa-solid fa-file-excel"></i> LETRAS
                </button>

                <button wire:click="descargarGirador" class="g_boton g_boton_primary">
                    <i class="fa-solid fa-file-excel"></i> GIRADOR
                </button>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <h4 class="g_panel_titulo">Listado de Solicitudes</h4>
        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th class="g_celda_centro">Nº</th>
                        <th>ID</th>
                        <th>Código Venta</th>
                        <th>Proyecto</th>
                        <th>Etapa</th>
                        <th>Mz.</th>
                        <th>Lt.</th>
                        <th>N° Cuota</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th>Fecha Solicitud</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($envio->solicitudes as $index => $solicitud)
                        <tr>
                            <td class="g_celda_centro">{{ $index + 1 }}</td>
                            <td>{{ $solicitud->id }}</td>
                            <td class="g_negrita">{{ $solicitud->codigo_venta }}</td>
                            <td class="g_resumir">{{ $solicitud->proyecto->nombre ?? '—' }}</td>
                            <td>{{ $solicitud->etapa ?? '—' }}</td>
                            <td>{{ $solicitud->manzana ?? '—' }}</td>
                            <td>{{ $solicitud->lote ?? '—' }}</td>
                            <td class="g_negrita">{{ $solicitud->numero_cuota }}</td>
                            <td class="g_resumir">{{ $solicitud->userCliente->name ?? '—' }}</td>
                            <td>{{ $solicitud->userCliente?->perfilCliente?->dni ?? '—' }}</td>
                            <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($envio->solicitudes->isEmpty())
            <div class="g_vacio">
                <p>No hay solicitudes vinculadas a este envío.</p>
                <i class="fa-regular fa-face-grin-wink"></i>
            </div>
        @endif
    </div>
</div>