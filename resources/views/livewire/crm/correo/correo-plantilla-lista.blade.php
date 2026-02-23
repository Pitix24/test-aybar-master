<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Plantillas de Correo</h2>
        <div class="cabecera_titulo_botones">
            <button class="g_boton primary">
                <i class="fa-solid fa-plus"></i> Nueva Plantilla
            </button>
            <a href="{{ route('erp.correo.vista.todo') }}" class="g_boton dark">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_buscador_input">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" wire:model.live="search" placeholder="Buscar por nombre o asunto...">
        </div>
    </div>

    <div class="g_fila">
        @forelse($plantillas as $plantilla)
            <div class="g_columna_4">
                <div class="g_panel" style="position: relative;">
                    <div class="g_margin_bottom_10">
                        <span class="g_badge g_badge_light">ID: #{{ $plantilla->id }}</span>
                    </div>
                    <h4 class="g_negrita">{{ $plantilla->nombre }}</h4>
                    <p class="g_texto_muted" style="font-size: 13px;">Asunto: {{ $plantilla->asunto }}</p>

                    <div class="g_margin_top_20" style="display: flex; gap: 10px;">
                        <button class="g_boton primary g_columna_6">
                            <i class="fa-solid fa-edit"></i> Editar
                        </button>
                        <button class="g_boton dark g_columna_6">
                            <i class="fa-solid fa-eye"></i> Previa
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="g_columna_12">
                <div class="g_panel g_celda_centro">No tienes plantillas creadas.</div>
            </div>
        @endforelse
    </div>

    <div class="g_margin_top_10">
        {{ $plantillas->links() }}
    </div>
</div>