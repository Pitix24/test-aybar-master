<div class="g_panel g_panel_dashboard_grid" style="margin-bottom: 12px;">
    <button wire:click="filterTotal" class="g_panel" style="background:none; border:none; padding:0; text-align:left;">
        <div class="g_panel_dashboard">
            <div class="g_panel_dashboard_1">
                <h4>Reclamos Totales</h4>
                <p class="g_negrita">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <i class="fa-solid fa-list" style="font-size:28px; color: #2b2b2b;"></i>
        </div>
    </button>

    <button wire:click="filterResueltos" class="g_panel"
        style="background:none; border:none; padding:0; text-align:left;">
        <div class="g_panel_dashboard">
            <div class="g_panel_dashboard_1">
                <h4>Reclamos Resueltos</h4>
                <p class="g_negrita">{{ $stats['resueltos'] ?? 0 }}</p>
            </div>
            <i class="fa-solid fa-check" style="font-size:28px; color: #16a34a;"></i>
        </div>
    </button>

    <button wire:click="filterPendientes" class="g_panel"
        style="background:none; border:none; padding:0; text-align:left;">
        <div class="g_panel_dashboard">
            <div class="g_panel_dashboard_1">
                <h4>Reclamos Pendientes</h4>
                <p class="g_negrita">{{ $stats['pendientes'] ?? 0 }}</p>
            </div>
            <i class="fa-solid fa-clock" style="font-size:28px; color: #f59e0b;"></i>
        </div>
    </button>

    <button wire:click="filterNoProcede" class="g_panel"
        style="background:none; border:none; padding:0; text-align:left;">
        <div class="g_panel_dashboard">
            <div class="g_panel_dashboard_1">
                <h4>Reclamos No Procedentes</h4>
                <p class="g_negrita">{{ $stats['no_procede'] ?? 0 }}</p>
            </div>
            <i class="fa-solid fa-xmark" style="font-size:28px; color: #dc2626;"></i>
        </div>
    </button>

    <div class="g_panel">
        <div class="g_panel_dashboard">
            <div class="g_panel_dashboard_1">
                <h4>Promedio por día</h4>
                <p class="g_negrita">{{ $stats['promedio_por_dia'] ?? 0 }}</p>
                <small class="g_text_muted">Desde hace {{ $stats['dias'] ?? 0 }} días</small>
            </div>
            <i class="fa-solid fa-chart-line" style="font-size:28px; color: #2563eb;"></i>
        </div>
    </div>
</div>
