<div class="g_gap_pagina">
    <x-loading-overlay wire:loading
        wire:target="descargarAceptantes, descargarLetras, descargarGirador, descargarArchivo"
        message="Descargando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle de Envío CAVALI #{{ $envio->id }}</h2>

        <div class="cabecera_titulo_botones">
            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_panel">
        <h3 class="g_margin_bottom_20"><i class="fa-solid fa-circle-info"></i> Información del Envío</h3>

        <div class="formulario">
            <div class="g_fila">
                <div class="g_columna_3">
                    <label>Unidad de Negocio</label>
                    <p class="g_negrita g_resaltar">{{ $envio->unidadNegocio?->nombre ?? '—' }}</p>
                </div>

                <div class="g_columna_3">
                    <label>Fecha de Corte</label>
                    <p class="g_negrita">{{ $envio->fecha_corte->format('d/m/Y') }}</p>
                </div>

                <div class="g_columna_3">
                    <label>Estado</label>
                    <p>
                        @if ($envio->estado)
                            <span class="g_badge g_badge_soft" style="color: {{ $envio->estado->color ?? '#666' }}">
                                @if($envio->estado->icono) <i class="{{ $envio->estado->icono }}"></i> @endif
                                {{ $envio->estado->nombre }}
                            </span>
                        @else
                            <span class="g_badge g_badge_light">Pendiente</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="g_fila">
                <div class="g_columna_3">
                    <label>Total solicitudes</label>
                    <p class="g_negrita">{{ $envio->solicitudes->count() }}</p>
                </div>

                <div class="g_columna_3">
                    <label>Fecha Envío</label>
                    <p>{{ $envio->enviado_at?->format('d/m/Y H:i') ?? 'No enviado aún' }}</p>
                </div>

                <div class="g_columna_3">
                    <label>Archivo Generado</label>
                    @if ($envio->archivo_zip)
                        <button wire:click="descargarArchivo" class="g_boton g_boton_success g_boton_sm">
                            <i class="fa-solid fa-download"></i> {{ $envio->archivo_nombre }}
                        </button>
                    @else
                        <p class="g_texto_muted">No disponible</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="g_tabla_cabecera">
            <div class="g_tabla_cabecera_botones">
                <button wire:click="descargarAceptantes" class="g_boton g_boton_excel">
                    <i class="fa-solid fa-download"></i> Aceptantes
                </button>

                <button wire:click="descargarLetras" class="g_boton g_boton_excel">
                    <i class="fa-solid fa-download"></i> Letras
                </button>

                <button wire:click="descargarGirador" class="g_boton g_boton_excel">
                    <i class="fa-solid fa-download"></i> Girador
                </button>
            </div>
        </div>
    </div>

    <div class="g_panel">
        <h3 class="g_margin_bottom_20"><i class="fa-solid fa-list-check"></i> Solicitudes Vinculadas</h3>

        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th class="g_celda_centro">Nº</th>
                        <th>Proyecto</th>
                        <th>Etapa</th>
                        <th>Mz. / Lt.</th>
                        <th class="g_celda_centro">Cuota</th>
                        <th>Código Cuota</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th class="g_celda_centro">Estado Solicitud</th>
                        <th>Fecha Solicitud</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($envio->solicitudes as $index => $solicitud)
                        <tr wire:key="solicitud-{{ $solicitud->id }}">
                            <td class="g_celda_centro">{{ $index + 1 }}</td>
                            <td class="g_resumir">{{ $solicitud->proyecto?->nombre ?? '—' }}</td>
                            <td class="g_resumir">{{ $solicitud->etapa }}</td>
                            <td class="g_resumir">{{ $solicitud->manzana }} / {{ $solicitud->lote }}</td>
                            <td class="g_celda_centro">{{ $solicitud->numero_cuota }}</td>
                            <td class="g_negrita">{{ $solicitud->codigo_cuota }}</td>
                            <td class="g_resaltar g_resumir">{{ $solicitud->userCliente?->name ?? '—' }}</td>
                            <td>{{ $solicitud->userCliente?->perfilCliente?->dni ?? '—' }}</td>
                            <td class="g_celda_centro">
                                @if ($solicitud->estado)
                                    <span class="g_badge g_badge_soft" style="color: {{ $solicitud->estado->color ?? '#666' }}">
                                        @if($solicitud->estado->icono) <i class="{{ $solicitud->estado->icono }}"></i> @endif
                                        {{ $solicitud->estado->nombre }}
                                    </span>
                                @else
                                    <span class="g_badge g_badge_light">Pendiente</span>
                                @endif
                            </td>
                            <td class="g_inferior g_celda_centro">{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
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