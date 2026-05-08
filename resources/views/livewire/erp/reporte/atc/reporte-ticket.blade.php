<div class="g_gap_pagina">
    <div style="display: flex; justify-content: flex-end; margin-bottom: 0.5rem;">
        <a href="{{ route('erp.reporte.vista.ticket-powerbi') }}" wire:navigate
           style="background: linear-gradient(135deg, #F2C811, #E3BA0B); 
                  color: #000; text-decoration: none; 
                  display: inline-flex; align-items: center; gap: 0.5rem; 
                  padding: 0.5rem 1rem; border-radius: 6px; 
                  font-size: 0.85rem; font-weight: 600;
                  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                  border: 1px solid #D4AF00;">
            <i class="fa-solid fa-chart-column"></i> Ver en Power BI
        </a>
    </div>
    <!-- Fila 1: KPIs de ATC -->
    <div class="g_fila">
        <div class="g_panel_dashboard_grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; width: 100%;">
            <div class="g_panel" title="Total tickets históricos">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Total Tickets</h2>
                        <p class="g_negrita">{{ number_format($totalTickets) }}</p>
                    </div>
                    <i class="fa-solid fa-ticket" style="color: var(--color-primario);"></i>
                </div>
            </div>

            <div class="g_panel" title="Tickets con estado Cerrado">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Resueltos</h2>
                        <p class="g_negrita">{{ number_format($ticketsCerrados) }}</p>
                    </div>
                    <i class="fa-solid fa-check-double" style="color: #10B981;"></i>
                </div>
            </div>

            <div class="g_panel" title="Tickets que no están cerrados">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>En Proceso</h2>
                        <p class="g_negrita">{{ number_format($ticketsAbiertos) }}</p>
                    </div>
                    <i class="fa-solid fa-spinner" style="color: #3B82F6;"></i>
                </div>
            </div>

            <div class="g_panel" title="Tickets sin cierre con más de 3 días de antigüedad">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Vencidos (+3d)</h2>
                        <p class="g_negrita" style="color: #EF4444;">{{ number_format($ticketsVencidos) }}</p>
                    </div>
                    <i class="fa-solid fa-triangle-exclamation" style="color: #EF4444;"></i>
                </div>
            </div>

            <div class="g_panel" title="Tiempo promedio entre creación y cierre">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Tpo. Cierre</h2>
                        <p class="g_negrita">{{ $tiempoPromedioCierre }}h</p>
                    </div>
                    <i class="fa-solid fa-stopwatch" style="color: #8B5CF6;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: Tendencia de Productividad -->
    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel" style="height: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.1rem; margin: 0;"><i class="fa-solid fa-chart-line"></i> Productividad:
                        Creados vs Resueltos</h2>
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
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-user-tie"></i> Ranking
                    Resolutores (Mes)</h2>
                <div style="height: 300px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartGestores"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Distribución ATC -->
    <div class="g_fila">
        <div class="g_columna_3">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1rem;"><i class="fa-solid fa-building"></i> Por Área</h2>
                <div style="height: 220px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartArea"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_3">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1rem;"><i class="fa-solid fa-headset"></i> Por Canal</h2>
                <div style="height: 220px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartCanal"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_3">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1rem;"><i class="fa-solid fa-tag"></i> Por Tipo</h2>
                <div style="height: 220px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartTipo"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_3">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1rem;"><i class="fa-solid fa-circle-notch"></i> Por Estado
                </h2>
                <div style="height: 220px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartEstado"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 4: Tablas de ATC -->
    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-list-check"></i> Últimos 5
                    Tickets Ingresados</h2>
                <div style="overflow-x: auto;">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Cliente</th>
                                <th>Área</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosTickets as $t)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">#{{ $t->id }}</div>
                                        <div
                                            style="font-size: 0.75rem; color: #64748b; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $t->asunto_inicial }}</div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem;">
                                            {{ explode(' ', $t->cliente?->name ?: $t->nombres)[0] }}</div>
                                        <div style="font-size: 0.7rem; color: #64748b;">{{ $t->dni }}</div>
                                    </td>
                                    <td>{{ $t->area?->nombre ?: 'N/A' }}</td>
                                    <td>
                                        <span
                                            style="font-size: 0.75rem; color: {{ str_contains(strtolower($t->prioridad?->nombre), 'alta') ? '#EF4444' : '#64748b' }}; font-weight: 600;">
                                            {{ $t->prioridad?->nombre ?: 'Normal' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="g_badge"
                                            style="background: {{ str_contains(strtolower($t->estado?->slug), 'cerrado') ? '#dcfce7' : '#e0f2fe' }}; color: {{ str_contains(strtolower($t->estado?->slug), 'cerrado') ? '#166534' : '#0369a1' }};">
                                            {{ $t->estado?->nombre }}
                                        </span>
                                    </td>
                                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-share-nodes"></i> Flujo de
                    Derivaciones Recientes</h2>
                <div style="overflow-x: auto;">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Asignado Por</th>
                                <th>Asignado A</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasDerivaciones as $d)
                                <tr>
                                    <td><span style="font-weight: 600;">#{{ $d->ticket_id }}</span></td>
                                    <td>{{ $d->deArea?->nombre }}</td>
                                    <td><i class="fa-solid fa-arrow-right-long"
                                            style="margin-right: 0.5rem; color: #94a3b8;"></i> {{ $d->aArea?->nombre }}</td>
                                    <td>{{ explode(' ', $d->usuarioDeriva?->name ?: 'Sistema')[0] }}</td>
                                    <td><span
                                            style="color: #3B82F6; font-weight: 500;">{{ explode(' ', $d->usuarioRecibe?->name ?: 'Fila')[0] }}</span>
                                    </td>
                                    <td>{{ $d->created_at->diffForHumans() }}</td>
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
                chartTendencia.data.datasets[0].data = items.tendencia.creados;
                chartTendencia.data.datasets[1].data = items.tendencia.cerrados;
                chartTendencia.update();
            }
            if (chartGestores && items.gestores) {
                chartGestores.data.labels = items.gestores.labels;
                chartGestores.data.datasets[0].data = items.gestores.data;
                chartGestores.update();
            }
        });

        setTimeout(() => {
            // 1. Tendencia: Creados vs Cerrados
            initChart('chartTendencia', {
                type: 'line',
                data: {
                    labels: @json($ticketsPorDiaMes['labels']),
                    datasets: [
                        { label: 'Creados', data: @json($ticketsPorDiaMes['creados']), borderColor: '#3B82F6', backgroundColor: 'rgba(59, 130, 246, 0.05)', fill: true, tension: 0.4 },
                        { label: 'Resueltos', data: @json($ticketsPorDiaMes['cerrados']), borderColor: '#10B981', backgroundColor: 'rgba(16, 185, 129, 0.05)', fill: true, tension: 0.4 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { boxWidth: 10, usePointStyle: true } } },
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

            // 3. Por Área
            initChart('chartArea', {
                type: 'bar',
                data: {
                    labels: @json($ticketsPorArea['labels']),
                    datasets: [{ data: @json($ticketsPorArea['data']), backgroundColor: '#4F46E5', borderRadius: 4 }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true }, y: { ticks: { font: { size: 9 } } } }
                }
            });

            // 4. Por Canal
            initChart('chartCanal', {
                type: 'doughnut',
                data: {
                    labels: @json($ticketsPorCanal['labels']),
                    datasets: [{ data: @json($ticketsPorCanal['data']), backgroundColor: coloresArr, borderWidth: 0 }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 9 } } } },
                    cutout: '65%'
                }
            });

            // 5. Por Tipo
            initChart('chartTipo', {
                type: 'pie',
                data: {
                    labels: @json($ticketsPorTipo['labels']),
                    datasets: [{ data: @json($ticketsPorTipo['data']), backgroundColor: coloresArr, borderWidth: 0 }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 9 } } } }
                }
            });

            // 6. Por Estado
            initChart('chartEstado', {
                type: 'bar',
                data: {
                    labels: @json($ticketsPorEstado['labels']),
                    datasets: [{ data: @json($ticketsPorEstado['data']), backgroundColor: '#F59E0B', borderRadius: 4 }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

        }, 500);
    });
</script>