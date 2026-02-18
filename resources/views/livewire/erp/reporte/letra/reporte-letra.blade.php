<div class="g_gap_pagina">
    <!-- Fila 1: KPIs de Letras Digitalizadas -->
    <div class="g_fila">
        <div class="g_panel_dashboard_grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; width: 100%;">
            <div class="g_panel" title="Total de solicitudes de digitalización">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Total Letras</h2>
                        <p class="g_negrita">{{ number_format($totalSolicitudes) }}</p>
                    </div>
                    <i class="fa-solid fa-file-signature" style="color: var(--color-primario);"></i>
                </div>
            </div>

            <div class="g_panel" title="Suma total de importes de las letras">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Importe Total</h2>
                        <p class="g_negrita">S/ {{ number_format($totalImporte, 2) }}</p>
                    </div>
                    <i class="fa-solid fa-sack-dollar" style="color: #10B981;"></i>
                </div>
            </div>

            <div class="g_panel" title="Letras aprobadas exitosamente">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Aprobadas</h2>
                        <p class="g_negrita">{{ number_format($solicitudesAprobadas) }}</p>
                    </div>
                    <i class="fa-solid fa-circle-check" style="color: #3B82F6;"></i>
                </div>
            </div>

            <div class="g_panel" title="Letras pendientes de revisión">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Pendientes</h2>
                        <p class="g_negrita">{{ number_format($solicitudesPendientes) }}</p>
                    </div>
                    <i class="fa-solid fa-clock-rotate-left" style="color: #F59E0B;"></i>
                </div>
            </div>

            <div class="g_panel" title="Tasa de aprobación mensual">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Eficacia</h2>
                        <p class="g_negrita">{{ $tasaAprobacion }}%</p>
                    </div>
                    <i class="fa-solid fa-percent" style="color: #8B5CF6;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: Tendencia y Distribución Principal -->
    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel" style="height: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.1rem; margin: 0;"><i class="fa-solid fa-chart-line"></i> Solicitudes
                        Recibidas (Día/Mes)</h2>
                    <input type="month" wire:model.live="mesSeleccionado" class="g_input"
                        style="padding: 0.2rem 0.5rem; width: 150px; font-size: 0.85rem;">
                </div>
                <div style="height: 300px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartTendencia"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_4">
            <div class="g_panel" style="height: 100%;">
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-filter"></i> Por Estado</h2>
                <div style="height: 300px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartEstado"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Análisis de Negocio -->
    <div class="g_fila">
        <div class="g_columna_6">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-building"></i> Top Unidades
                    de Negocio</h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartUN"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_6">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-diagram-project"></i> Top
                    Proyectos</h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartProyecto"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 4: Detalle Operativo Reciente -->
    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-list-ul"></i> Últimas 8 Letras
                    Ingresadas</h2>
                <div style="overflow-x: auto;">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Lote / Cuota</th>
                                <th>Cliente</th>
                                <th>Razón Social</th>
                                <th>Proyecto</th>
                                <th>Importe</th>
                                <th>Estado</th>
                                <th>Vencimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasSolicitudes as $sol)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">{{ $sol->lote_completo ?: 'N/A' }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">COD: {{ $sol->codigo_cuota }}</div>
                                    </td>
                                    <td>{{ explode(' ', $sol->cliente?->name ?: 'N/A')[0] }}</td>
                                    <td style="font-size: 0.85rem;">{{ $sol->razon_social }}</td>
                                    <td style="font-size: 0.85rem;">{{ $sol->nombre_proyecto }}</td>
                                    <td style="font-weight: 600; color: var(--color-primario);">S/
                                        {{ number_format((float) $sol->importe_cuota, 2) }}</td>
                                    <td>
                                        <span class="g_badge"
                                            style="background: {{ str_contains(strtolower($sol->estado?->nombre), 'aprobado') ? '#dcfce7' : (str_contains(strtolower($sol->estado?->nombre), 'pendiente') ? '#e0f2fe' : '#fee2e2') }}; color: {{ str_contains(strtolower($sol->estado?->nombre), 'aprobado') ? '#166534' : (str_contains(strtolower($sol->estado?->nombre), 'pendiente') ? '#0369a1' : '#991b1b') }};">
                                            {{ $sol->estado?->nombre }}
                                        </span>
                                    </td>
                                    <td>{{ $sol->fecha_vencimiento ?: 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let chartTendencia = null;

    document.addEventListener('livewire:init', () => {
        const coloresArr = ['#4F46E5', '#10B981', '#F59E0B', '#3B82F6', '#EF4444', '#8B5CF6', '#06B6D4', '#6366F1'];
        Chart.defaults.font.family = "'Instrument Sans', sans-serif";
        Chart.defaults.color = '#64748b';

        const initChart = (id, config) => {
            const ctx = document.getElementById(id);
            if (ctx) {
                try {
                    const chart = new Chart(ctx, config);
                    if (id === 'chartTendencia') chartTendencia = chart;
                    return chart;
                } catch (e) { console.error('Error init chart ' + id, e); }
            }
        };

        Livewire.on('actualizarGraficosDinamicos', (payload) => {
            const items = payload[0];
            if (chartTendencia && items.tendencia) {
                chartTendencia.data.labels = items.tendencia.labels;
                chartTendencia.data.datasets[0].data = items.tendencia.data;
                chartTendencia.update();
            }
        });

        setTimeout(() => {
            // 1. Tendencia de Registro
            initChart('chartTendencia', {
                type: 'line',
                data: {
                    labels: @json($tendenciaRegistro['labels']),
                    datasets: [{
                        label: 'Nuevas Solicitudes',
                        data: @json($tendenciaRegistro['data']),
                        borderColor: '#4F46E5',
                        backgroundColor: 'rgba(79, 70, 229, 0.05)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
                }
            });

            // 2. Por Estado (DOUGHNUT)
            initChart('chartEstado', {
                type: 'doughnut',
                data: {
                    labels: @json($porEstado['labels']),
                    datasets: [{
                        data: @json($porEstado['data']),
                        backgroundColor: coloresArr,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } }
                    },
                    cutout: '70%'
                }
            });

            // 3. Por UN (BAR HORIZ)
            initChart('chartUN', {
                type: 'bar',
                data: {
                    labels: @json($porUnidadNegocio['labels']),
                    datasets: [{
                        data: @json($porUnidadNegocio['data']),
                        backgroundColor: '#3B82F6',
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });

            // 4. Por Proyecto (BAR HORIZ)
            initChart('chartProyecto', {
                type: 'bar',
                data: {
                    labels: @json($porProyecto['labels']),
                    datasets: [{
                        data: @json($porProyecto['data']),
                        backgroundColor: '#10B981',
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });

        }, 500);
    });
</script>