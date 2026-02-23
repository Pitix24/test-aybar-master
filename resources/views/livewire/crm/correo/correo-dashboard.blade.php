<div class="g_gap_pagina">
    {{-- Cabecera --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Dashboard Email Marketing (Aybar Mail)</h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.correo.vista.contactos') }}" class="g_boton primary">
                <i class="fa-solid fa-users"></i> Contactos
            </a>
            <a href="{{ route('erp.correo.vista.listas') }}" class="g_boton light">
                <i class="fa-solid fa-list-ul"></i> Listas
            </a>
        </div>
    </div>

    {{-- Cards de Resumen --}}
    <div class="g_fila">
        <div class="g_columna_3">
            <div class="g_panel card_dashboard">
                <div class="card_dashboard_icono" style="background: var(--g_primary_alpha);">
                    <i class="fa-solid fa-user-check" style="color: var(--g_primary);"></i>
                </div>
                <div class="card_dashboard_info">
                    <span class="card_dashboard_label">Contactos Totales</span>
                    <h3 class="card_dashboard_valor">{{ number_format($totalContactos) }}</h3>
                </div>
            </div>
        </div>

        <div class="g_columna_3">
            <div class="g_panel card_dashboard">
                <div class="card_dashboard_icono" style="background: rgba(var(--g_exito_rgb), 0.1);">
                    <i class="fa-solid fa-tags" style="color: var(--g_exito);"></i>
                </div>
                <div class="card_dashboard_info">
                    <span class="card_dashboard_label">Listas / Grupos</span>
                    <h3 class="card_dashboard_valor">{{ number_format($totalListas) }}</h3>
                </div>
            </div>
        </div>

        <div class="g_columna_3">
            <div class="g_panel card_dashboard">
                <div class="card_dashboard_icono" style="background: rgba(var(--g_alerta_rgb), 0.1);">
                    <i class="fa-solid fa-file-code" style="color: var(--g_alerta);"></i>
                </div>
                <div class="card_dashboard_info">
                    <span class="card_dashboard_label">Plantillas</span>
                    <h3 class="card_dashboard_valor">{{ number_format($totalPlantillas) }}</h3>
                </div>
            </div>
        </div>

        <div class="g_columna_3">
            <div class="g_panel card_dashboard">
                <div class="card_dashboard_icono" style="background: rgba(var(--g_peligro_rgb), 0.1);">
                    <i class="fa-solid fa-paper-plane" style="color: var(--g_peligro);"></i>
                </div>
                <div class="card_dashboard_info">
                    <span class="card_dashboard_label">Campañas</span>
                    <h3 class="card_dashboard_valor">{{ number_format($totalCampanas) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Cuerpo Principal --}}
    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Últimas Campañas Ejecutadas</h4>
                <div class="tabla_contenido">
                    <div class="contenedor_tabla">
                        <table class="g_tabla">
                            <thead>
                                <tr>
                                    <th>Campaña</th>
                                    <th>Estado</th>
                                    <th>Lista</th>
                                    <th>Enviados</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campanasRecientes as $campana)
                                    <tr>
                                        <td class="g_negrita">{{ $campana->nombre }}</td>
                                        <td>
                                            <span
                                                class="g_badge @if($campana->estado == 'COMPLETADO') g_badge_success @elseif($campana->estado == 'FALLIDO') g_badge_danger @else g_badge_warning @endif">
                                                {{ $campana->estado }}
                                            </span>
                                        </td>
                                        <td>{{ $campana->lista->nombre ?? '-' }}</td>
                                        <td class="g_negrita">{{ $campana->total_enviados }}</td>
                                        <td>{{ $campana->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="g_celda_centro">No hay campañas registradas aún.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Accesos Rápidos</h4>
                <div class="g_gap_pagina">
                    <a href="{{ route('erp.correo.vista.plantillas') }}" class="g_item_menu_opcion">
                        <div class="g_item_menu_opcion_icono">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <div class="g_item_menu_opcion_texto">
                            <strong>Crear Plantilla</strong>
                            <p>Diseña el contenido de tus correos.</p>
                        </div>
                    </a>

                    <a href="{{ route('erp.correo.vista.contactos') }}" class="g_item_menu_opcion">
                        <div class="g_item_menu_opcion_icono">
                            <i class="fa-solid fa-file-import"></i>
                        </div>
                        <div class="g_item_menu_opcion_texto">
                            <strong>Importar Contactos</strong>
                            <p>Sube tu lista de Excel rápidamente.</p>
                        </div>
                    </a>

                    <div class="g_alerta g_alerta_info">
                        <i class="fa-solid fa-info-circle"></i>
                        <p>Recuerda que Office 365 tiene un límite de 30 correos por minuto.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card_dashboard {
            display: flex;
            align-items: center;
            padding: 20px;
            gap: 15px;
            height: 100px;
        }

        .card_dashboard_icono {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .card_dashboard_info {
            display: flex;
            flex-direction: column;
        }

        .card_dashboard_label {
            font-size: 13px;
            color: var(--g_texto_muted);
            font-weight: 500;
        }

        .card_dashboard_valor {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .g_item_menu_opcion {
            display: flex;
            padding: 15px;
            border-radius: 12px;
            background: var(--g_white);
            border: 1px solid var(--g_borde);
            text-decoration: none;
            transition: all 0.3s;
            gap: 15px;
            align-items: center;
        }

        .g_item_menu_opcion:hover {
            border-color: var(--g_primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background: var(--g_primary_background);
        }

        .g_item_menu_opcion_icono {
            width: 40px;
            height: 40px;
            background: var(--g_light);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: var(--g_primary);
            font-size: 18px;
        }

        .g_item_menu_opcion_texto strong {
            display: block;
            color: var(--g_dark);
            margin-bottom: 2px;
        }

        .g_item_menu_opcion_texto p {
            margin: 0;
            font-size: 12px;
            color: var(--g_texto_muted);
        }
    </style>
</div>