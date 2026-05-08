<div class="g_gap_pagina">
    <div style="display: flex; justify-content: flex-end; margin-bottom: 0.5rem;">
        <a href="{{ route('erp.reporte.vista.cliente-powerbi') }}" wire:navigate
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
    <!-- Fila 1: KPIs -->
    <div class="g_fila">
        <div class="g_panel_dashboard_grid">
            <div class="g_panel">
                <div class="g_panel_dashboard" style="border-left-color: #0da8dcff;">
                    <div class="g_panel_dashboard_1">
                        <h2>Total Clientes</h2>
                        <p class="g_negrita">{{ number_format($totalClientes) }}</p>
                    </div>
                    <i class="fa-solid fa-users" style="color: #0da8dcff;"></i>
                </div>
                <div class="g_panel_dashboard_trend">
                    <span class="g_trend_valor {{ $crecimientoMensual >= 0 ? 'g_trend_up' : 'g_trend_down' }}">
                        <i class="fa-solid fa-arrow-{{ $crecimientoMensual >= 0 ? 'up' : 'down' }}"></i>
                        {{ round($crecimientoMensual, 1) }}%
                    </span>
                    vs mes anterior
                </div>
            </div>

            <div class="g_panel">
                <div class="g_panel_dashboard" style="border-left-color: #10B981;">
                    <div class="g_panel_dashboard_1">
                        <h2>Nuevos este Mes</h2>
                        <p class="g_negrita">{{ $clientesEsteMes }}</p>
                    </div>
                    <i class="fa-solid fa-user-plus" style="color: #10B981;"></i>
                </div>
                <div class="g_panel_dashboard_trend">
                    Meta mensual: 50 (ejemplo)
                </div>
            </div>

            <div class="g_panel">
                <div class="g_panel_dashboard" style="border-left-color: #3B82F6;">
                    <div class="g_panel_dashboard_1">
                        <h2>Activos / Inactivos</h2>
                        <p class="g_negrita">{{ $clientesActivos }} / {{ $clientesInactivos }}</p>
                    </div>
                    <i class="fa-solid fa-toggle-on" style="color: #3B82F6;"></i>
                </div>
                <div class="g_panel_dashboard_trend">
                    Tasa de actividad:
                    {{ $totalClientes > 0 ? round(($clientesActivos / $totalClientes) * 100, 1) : 0 }}%
                </div>
            </div>

            <div class="g_panel">
                <div class="g_panel_dashboard" style="border-left-color: #F59E0B;">
                    <div class="g_panel_dashboard_1">
                        <h2>Email Verificado</h2>
                        <p class="g_negrita">{{ $clientesEmailVerificado['data'][0] }}</p>
                    </div>
                    <i class="fa-solid fa-envelope-circle-check" style="color: #F59E0B;"></i>
                </div>
                <div class="g_panel_dashboard_trend">
                    Pendientes: {{ $clientesEmailVerificado['data'][1] }}
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: Tendencia Diaria Dinámica -->
    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <div>
                    <h2><i class="fa-solid fa-chart-line"></i> Tendencia de registros</h2>
                    <div>
                        <input type="month" wire:model.live="mesSeleccionado">
                    </div>
                </div>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartDiaMes"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Distribución y Perfil -->
    <div class="g_fila">
        <div class="g_columna_3">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1rem;"><i class="fa-solid fa-map-location-dot"></i> Top 5
                    Regiones</h2>
                <div style="height: 220px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartRegiones"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_3">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1rem;"><i class="fa-solid fa-shield-halved"></i> Políticas
                </h2>
                <div style="height: 220px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartPoliticas"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_3">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1rem;"><i class="fa-solid fa-at"></i> Proveedores Email</h2>
                <div style="height: 220px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartDominios"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_3">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1rem;"><i class="fa-solid fa-bullseye"></i> Salud Global (%)
                </h2>
                <div style="height: 220px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartRadar"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 4: Completitud y Horarios -->
    <div class="g_fila">
        <div class="g_columna_6">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-list-check"></i> Completitud
                    de Datos (% de la base)</h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartCompletitud"></canvas>
                </div>
            </div>
        </div>
        <div class="g_columna_6">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-clock"></i> Picos de Registro
                    (Horario 24h)</h2>
                <div style="height: 250px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartHoras"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 5: Historico y Tabla -->
    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel" style="height: 100%;">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-clock-rotate-left"></i>
                    Registros Recientes</h2>
                <div style="overflow-x: auto;">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Nombre / Email</th>
                                <th>DNI</th>
                                <th>Fecha / Hora</th>
                                <th>Seguridad</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosClientes as $cliente)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">{{ $cliente->user->name }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">{{ $cliente->user->email }}</div>
                                    </td>
                                    <td>{{ $cliente->dni }}</td>
                                    <td>
                                        <div>{{ $cliente->created_at->format('d/m/Y') }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">
                                            {{ $cliente->created_at->format('H:i') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <i class="fa-solid fa-camera {{ $cliente->user->profile_photo_path ? 'text-success' : 'text-muted' }}"
                                                title="Foto de perfil"></i>
                                            <i class="fa-solid fa-key {{ $cliente->user->password_changed_at ? 'text-success' : 'text-muted' }}"
                                                title="Password actualizada"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="g_badge {{ $cliente->user->activo ? 'success' : 'error' }}">
                                            {{ $cliente->user->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="g_columna_4">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-chart-column"></i> Crecimiento
                    12 meses</h2>
                <div style="height: 350px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartMeses"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    let chartDiaMes = null;
    const charts = {};

    document.addEventListener('livewire:init', () => {
        const colores = ['#4F46E5', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];

        Chart.defaults.font.family = "'Instrument Sans', sans-serif";
        Chart.defaults.color = '#64748b';

        const initChart = (id, config) => {
            const ctx = document.getElementById(id);
            if (ctx) {
                try {
                    const chart = new Chart(ctx, config);
                    if (id === 'chartDiaMes') chartDiaMes = chart;
                    else charts[id] = chart;
                } catch (e) {
                    console.error('Error init chart ' + id, e);
                }
            }
        };

        // Escuchar cambios dinámicos desde Livewire - Patrón del usuario
        Livewire.on('actualizarGraficoDiaMes', (payload) => {
            console.log(payload);
            const data = payload[0];
            if (chartDiaMes) {
                chartDiaMes.data.labels = data.labels;
                chartDiaMes.data.datasets[0].data = data.data;
                chartDiaMes.update();
            }
        });

        // Inicialización de todos los gráficos
        setTimeout(() => {
            // 1. Tendencia Diaria (Línea) - Basado en tu patrón
            initChart('chartDiaMes', {
                type: 'line',
                data: {
                    labels: @json($clientesPorDiaMesActual['labels']),
                    datasets: [{
                        label: 'Clientes nuevos',
                        data: @json($clientesPorDiaMesActual['data']),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. Regiones (Donut)
            initChart('chartRegiones', {
                type: 'doughnut',
                data: {
                    labels: @json(array_values($clientesPorRegion['labels'] ?? [])),
                    datasets: [{
                        data: @json(array_values($clientesPorRegion['data'] ?? [])),
                        backgroundColor: colores,
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

            // 3. Políticas (Pie)
            initChart('chartPoliticas', {
                type: 'pie',
                data: {
                    labels: @json(array_values($clientesPoliticas['labels'] ?? [])),
                    datasets: [{
                        data: @json(array_values($clientesPoliticas['data'] ?? [])),
                        backgroundColor: ['#3B82F6', '#10B981', '#EF4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } }
                    }
                }
            });

            // 3b. Dominios (Doughnut)
            initChart('chartDominios', {
                type: 'doughnut',
                data: {
                    labels: @json(array_values($clientesDominios['labels'] ?? [])),
                    datasets: [{
                        data: @json(array_values($clientesDominios['data'] ?? [])),
                        backgroundColor: colores,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } }
                    }
                }
            });

            // 4. Perfil Radar
            initChart('chartRadar', {
                type: 'radar',
                data: {
                    labels: @json(array_values($clientesRadar['labels'] ?? [])),
                    datasets: [{
                        label: 'Completado %',
                        data: @json(array_values($clientesRadar['data'] ?? [])),
                        backgroundColor: 'rgba(79, 70, 229, 0.2)',
                        borderColor: '#4F46E5',
                        pointBackgroundColor: '#4F46E5',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            angleLines: { display: true },
                            suggestedMin: 0,
                            suggestedMax: 100,
                            ticks: { display: false }
                        }
                    },
                    plugins: { legend: { display: false } }
                }
            });

            // 5. Completitud (Barras Horizontales)
            initChart('chartCompletitud', {
                type: 'bar',
                data: {
                    labels: @json(array_values($perfilCompletitud['labels'] ?? [])),
                    datasets: [{
                        label: 'Completitud %',
                        data: @json(array_values($perfilCompletitud['data'] ?? [])),
                        backgroundColor: '#3B82F6',
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, max: 100 },
                        y: { grid: { display: false } }
                    }
                }
            });

            // 6. Horarios (Barras)
            initChart('chartHoras', {
                type: 'bar',
                data: {
                    labels: @json(array_values($clientesPorHora['labels'] ?? [])),
                    datasets: [{
                        label: 'Registros',
                        data: @json(array_values($clientesPorHora['data'] ?? [])),
                        backgroundColor: '#10B981',
                        borderRadius: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 5 } },
                        x: { grid: { display: false }, ticks: { font: { size: 9 } } }
                    }
                }
            });

            // 7. Histórico 12 Meses (Barras)
            initChart('chartMeses', {
                type: 'bar',
                data: {
                    labels: @json(array_values($clientesPorMes['labels'] ?? [])),
                    datasets: [{
                        label: 'Clientes',
                        data: @json(array_values($clientesPorMes['data'] ?? [])),
                        backgroundColor: '#4F46E5',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true },
                        x: { grid: { display: false } }
                    }
                }
            });
        }, 300);
    });
</script>