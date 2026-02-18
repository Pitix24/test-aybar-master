<div class="g_gap_pagina">
    <!-- Fila 1: KPIs de Citas -->
    <div class="g_fila">
        <div class="g_panel_dashboard_grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; width: 100%;">
            <div class="g_panel" title="Total citas programadas">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Total Citas</h2>
                        <p class="g_negrita">{{ number_format($totalCitas) }}</p>
                    </div>
                    <i class="fa-solid fa-calendar-check" style="color: var(--color-primario);"></i>
                </div>
            </div>

            <div class="g_panel" title="Citas marcadas como atendidas">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Atendidas</h2>
                        <p class="g_negrita">{{ number_format($citasAtendidas) }}</p>
                    </div>
                    <i class="fa-solid fa-clipboard-check" style="color: #10B981;"></i>
                </div>
            </div>

            <div class="g_panel" title="Citas aún no atendidas que no han sido canceladas">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Pendientes</h2>
                        <p class="g_negrita">{{ number_format($totalCitas - $citasAtendidas - $citasCanceladas) }}</p>
                    </div>
                    <i class="fa-solid fa-calendar-day" style="color: #3B82F6;"></i>
                </div>
            </div>

            <div class="g_panel" title="Citas canceladas por cliente o gestor">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Canceladas</h2>
                        <p class="g_negrita" style="color: #EF4444;">{{ number_format($citasCanceladas) }}</p>
                    </div>
                    <i class="fa-solid fa-calendar-xmark" style="color: #EF4444;"></i>
                </div>
            </div>

            <div class="g_panel" title="Porcentaje de citas atendidas vs programadas (sin contar canceladas)">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Efectividad</h2>
                        <p class="g_negrita">{{ $tasaCumplimiento }}%</p>
                    </div>
                    <i class="fa-solid fa-chart-line" style="color: #8B5CF6;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: Tendencia y Ranking -->
    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel" style="height: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.1rem; margin: 0;"><i class="fa-solid fa-chart-line"></i> Flujo de Citas
                        Programadas</h2>
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
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-crown"></i> Top Gestores
                    (Mes)</h2>
                <div style="height: 300px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartGestores"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Distribución Detallada -->
    <div class="g_fila">
        <div class="g_columna_4">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1rem;"><i class="fa-solid fa-map-location-dot"></i> Por
                    Sede</h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartSede"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_4">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1rem;"><i class="fa-solid fa-lightbulb"></i> Por Motivo
                </h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartMotivo"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_4">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1rem;"><i class="fa-solid fa-list-check"></i> Por Estado
                </h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartEstado"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 4: Tabla de Citas Recientes -->
    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-calendar-list"></i> Próximas
                    Citas y Recientes</h2>
                <div style="overflow-x: auto;">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Fecha / Hora</th>
                                <th>Cliente</th>
                                <th>Sede</th>
                                <th>Motivo</th>
                                <th>Gestor</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasCitas as $c)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">{{ $c->fecha_inicio->format('d/m/Y') }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">
                                            {{ $c->fecha_inicio->format('H:i') }} - {{ $c->fecha_fin->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem;">
                                            {{ explode(' ', $c->cliente?->name ?: $c->nombres)[0] }}</div>
                                        <div style="font-size: 0.7rem; color: #64748b;">{{ $c->dni }}</div>
                                    </td>
                                    <td>{{ $c->sede?->nombre ?: 'Sin sede' }}</td>
                                    <td
                                        style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $c->motivo?->nombre ?: 'Otros' }}
                                    </td>
                                    <td>{{ explode(' ', $c->gestor?->name ?: 'Fila')[0] }}</td>
                                    <td>
                                        <span class="g_badge"
                                            style="background: {{ str_contains(strtolower($c->estado?->slug), 'atendido') ? '#dcfce7' : (str_contains(strtolower($c->estado?->slug), 'cancelado') ? '#fee2e2' : '#e0f2fe') }}; color: {{ str_contains(strtolower($c->estado?->slug), 'atendido') ? '#166534' : (str_contains(strtolower($c->estado?->slug), 'cancelado') ? '#991b1b' : '#0369a1') }};">
                                            {{ $c->estado?->nombre }}
                                        </span>
                                    </td>
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
    let chartGestores = null;

    document.addEventListener('livewire:init', () => {
        const coloresArr = ['#4F46E5', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#6366F1'];
        Chart.defaults.font.family = "'Instrument Sans', sans-serif";
        Chart.defaults.color = '#64748b';

        const initChart = (id, config) => {
            const ctx = document.getElementById(id);
            if (ctx) {
                try {
                    const chart = new Chart(ctx, config);
                    if (id === 'chartTendencia') chartTendencia = chart;
                    else if (id === 'chartGestores') chartGestores = chart;
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
            if (chartGestores && items.gestores) {
                chartGestores.data.labels = items.gestores.labels;
                chartGestores.data.datasets[0].data = items.gestores.data;
                chartGestores.update();
            }
        });

        setTimeout(() => {
            // 1. Tendencia: Citas por Día
            initChart('chartTendencia', {
                type: 'line',
                data: {
                    labels: @json($tendenciaCitas['labels']),
                    datasets: [{
                        label: 'Citas Programadas',
                        data: @json($tendenciaCitas['data']),
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

            // 2. Ranking Gestores (HORIZ)
            initChart('chartGestores', {
                type: 'bar',
                data: {
                    labels: @json($rankingGestores['labels']),
                    datasets: [{ data: @json($rankingGestores['data']), backgroundColor: coloresArr, borderRadius: 5 }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true }, y: { grid: { display: false } } }
                }
            });

            // 3. Por Sede (DOUGHNUT)
            initChart('chartSede', {
                type: 'doughnut',
                data: {
                    labels: @json($porSede['labels']),
                    datasets: [{ data: @json($porSede['data']), backgroundColor: coloresArr, borderWidth: 0 }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } } },
                    cutout: '65%'
                }
            });

            // 4. Por Motivo (BAR HORIZ)
            initChart('chartMotivo', {
                type: 'bar',
                data: {
                    labels: @json($porMotivo['labels']),
                    datasets: [{ data: @json($porMotivo['data']), backgroundColor: '#10B981', borderRadius: 4 }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true }, y: { grid: { display: false }, ticks: { font: { size: 10 } } } }
                }
            });

            // 5. Por Estado (BAR)
            initChart('chartEstado', {
                type: 'bar',
                data: {
                    labels: @json($porEstado['labels']),
                    datasets: [{ data: @json($porEstado['data']), backgroundColor: '#3B82F6', borderRadius: 4 }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
                }
            });

        }, 500);
    });
</script>