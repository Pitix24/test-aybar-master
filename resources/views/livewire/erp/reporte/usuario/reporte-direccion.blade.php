<div class="g_gap_pagina">
    <div style="display: flex; justify-content: flex-end; margin-bottom: 0.5rem;">
        <a href="{{ route('erp.reporte.vista.direccion-powerbi') }}" wire:navigate
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
    <!-- Fila 1: KPIs de Distribución -->
    <div class="g_fila">
        <div class="g_panel_dashboard_grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; width: 100%;">
            <div class="g_panel" title="Total de direcciones registradas por clientes">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Total Direcciones</h2>
                        <p class="g_negrita">{{ number_format($totalDirecciones) }}</p>
                    </div>
                    <i class="fa-solid fa-map-location-dot" style="color: var(--color-primario);"></i>
                </div>
            </div>

            <div class="g_panel" title="Número de regiones captadas">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Regiones</h2>
                        <p class="g_negrita">{{ number_format($regionesCubiertas) }}</p>
                    </div>
                    <i class="fa-solid fa-earth-americas" style="color: #10B981;"></i>
                </div>
            </div>

            <div class="g_panel" title="Número de distritos captados">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Distritos</h2>
                        <p class="g_negrita">{{ number_format($distritosCubiertos) }}</p>
                    </div>
                    <i class="fa-solid fa-city" style="color: #3B82F6;"></i>
                </div>
            </div>

            <div class="g_panel" title="Nuevas direcciones registradas este mes">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Nuevas (Mes)</h2>
                        <p class="g_negrita">{{ number_format($nuevasDireccionesMes) }}</p>
                    </div>
                    <i class="fa-solid fa-location-arrow" style="color: #F59E0B;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: Tendencia y Distribución Regional -->
    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel" style="height: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.1rem; margin: 0;"><i class="fa-solid fa-chart-line"></i> Tendencia de Nuevas
                        Ubicaciones</h2>
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
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-map"></i> Distribución por
                    Región</h2>
                <div style="height: 300px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartRegion"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Análisis Detallado Geográfico -->
    <div class="g_fila">
        <div class="g_columna_6">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1rem;"><i class="fa-solid fa-building-user"></i> Top 10
                    Provincias</h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartProvincia"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_6">
            <div class="g_panel">
                <h2 style="margin-bottom: 1.5rem; font-size: 1rem;"><i class="fa-solid fa-store"></i> Top 10 Distritos
                </h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartDistrito"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 4: Listado Reciente de Ubicaciones -->
    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-map-pin"></i> Últimas
                    Direcciones Registradas</h2>
                <div style="overflow-x: auto;">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Ubicación</th>
                                <th>Dirección Detallada</th>
                                <th>Cód. Postal</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasDirecciones as $dir)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">{{ $dir->user?->name }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">DNI:
                                            {{ $dir->user?->dni ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem;">{{ $dir->region?->nombre }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">{{ $dir->provincia?->nombre }} -
                                            {{ $dir->distrito?->nombre }}</div>
                                    </td>
                                    <td>
                                        <div
                                            style="font-size: 0.85rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $dir->direccion }} {{ $dir->direccion_numero }}</div>
                                        @if($dir->referencia)
                                            <div style="font-size: 0.7rem; color: #94a3b8;"><i
                                                    class="fa-solid fa-location-crosshairs"></i> {{ $dir->referencia }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $dir->codigo_postal }}</td>
                                    <td>{{ $dir->created_at->format('d/m/Y H:i') }}</td>
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
                        label: 'Nuevas Direcciones',
                        data: @json($tendenciaRegistro['data']),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
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

            // 2. Por Región (DOUGHNUT)
            initChart('chartRegion', {
                type: 'doughnut',
                data: {
                    labels: @json($porRegion['labels']),
                    datasets: [{
                        data: @json($porRegion['data']),
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

            // 3. Por Provincia (BAR HORIZ)
            initChart('chartProvincia', {
                type: 'bar',
                data: {
                    labels: @json($porProvincia['labels']),
                    datasets: [{
                        data: @json($porProvincia['data']),
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

            // 4. Por Distrito (BAR HORIZ)
            initChart('chartDistrito', {
                type: 'bar',
                data: {
                    labels: @json($porDistrito['labels']),
                    datasets: [{
                        data: @json($porDistrito['data']),
                        backgroundColor: '#F59E0B',
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