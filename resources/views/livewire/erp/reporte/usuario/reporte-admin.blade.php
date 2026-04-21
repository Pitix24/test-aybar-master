<div class="g_gap_pagina">
    <div style="display: flex; justify-content: flex-end; margin-bottom: 0.5rem;">
        <a href="{{ route('erp.reporte.vista.admin-powerbi') }}" wire:navigate
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
    <!-- Fila 1: KPIs de Administradores -->
    <div class="g_fila">
        <div class="g_panel_dashboard_grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; width: 100%;">
            <div class="g_panel" title="Total de usuarios con rol interno">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Total Staff</h2>
                        <p class="g_negrita">{{ number_format($totalAdmins) }}</p>
                    </div>
                    <i class="fa-solid fa-users-gear" style="color: var(--color-primario);"></i>
                </div>
            </div>

            <div class="g_panel" title="Administradores activos en el sistema">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Activos</h2>
                        <p class="g_negrita">{{ number_format($adminsActivos) }}</p>
                    </div>
                    <i class="fa-solid fa-user-check" style="color: #10B981;"></i>
                </div>
            </div>

            <div class="g_panel" title="Administradores eliminados o deshabilitados">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Inactivos</h2>
                        <p class="g_negrita">{{ number_format($adminsInactivos) }}</p>
                    </div>
                    <i class="fa-solid fa-user-slash" style="color: #EF4444;"></i>
                </div>
            </div>

            <div class="g_panel" title="Nuevas altas de staff este mes">
                <div class="g_panel_dashboard">
                    <div class="g_panel_dashboard_1">
                        <h2>Altas Mes</h2>
                        <p class="g_negrita">{{ number_format($nuevosEsteMes) }}</p>
                    </div>
                    <i class="fa-solid fa-user-plus" style="color: #3B82F6;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: Análisis de Staff -->
    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel" style="height: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.1rem; margin: 0;"><i class="fa-solid fa-chart-line"></i> Tendencia de
                        Incorporación (Día/Mes)</h2>
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
                <h2 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><i class="fa-solid fa-shield-halved"></i>
                    Distribución de Permisos (Spatie)</h2>
                <div style="height: 300px; width: 100%; position: relative;" wire:ignore>
                    <canvas id="chartRoles"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Tabla de Staff Reciente -->
    <div class="g_fila">
        <div class="g_columna_12">
            <div class="g_panel">
                <h2 style="margin-bottom: 1rem; font-size: 1.1rem;"><i class="fa-solid fa-address-book"></i> Últimos
                    Colaboradores Registrados</h2>
                <div style="overflow-x: auto;">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th>Nombre Completo</th>
                                <th>Correo Electrónico</th>
                                <th>Roles / Perfiles</th>
                                <th>Fecha Registro</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosAdmins as $admin)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">{{ $admin->name }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">ID: #{{ $admin->id }}</div>
                                    </td>
                                    <td>{{ $admin->email }}</td>
                                    <td>
                                        @foreach($admin->roles as $role)
                                            <span class="g_badge"
                                                style="background: #eef2ff; color: #4f46e5; font-size: 0.7rem; margin-right: 0.2rem;">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                        @if($admin->roles->isEmpty())
                                            <span style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">Sin roles
                                                asignados</span>
                                        @endif
                                    </td>
                                    <td>{{ $admin->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($admin->trashed())
                                            <span class="g_badge" style="background: #fee2e2; color: #991b1b;">Inactivo</span>
                                        @else
                                            <span class="g_badge" style="background: #dcfce7; color: #166534;">Activo</span>
                                        @endif
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
                        label: 'Nuevos Administradores',
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

            // 2. Distribución de Roles (DOUGHNUT)
            initChart('chartRoles', {
                type: 'doughnut',
                data: {
                    labels: @json($distribucionRoles['labels']),
                    datasets: [{
                        data: @json($distribucionRoles['data']),
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

        }, 500);
    });
</script>