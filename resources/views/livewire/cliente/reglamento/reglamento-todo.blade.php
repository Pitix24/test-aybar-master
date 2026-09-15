<div x-data="{
        abrirPdf(url, reglamentoId) {
            if (!url || url === '#') return;
            window.open(url, '_blank');
            @this.registrarClick(reglamentoId);
        }
    }" x-cloak>

    {{-- CABECERA CON FILTRO --}}
    <div class="reglamentos_header">
        <div class="reglamentos_header_info">
            <h2 class="reglamentos_titulo">
                <i class="fa-solid fa-scale-balanced"></i>
                Reglamentos
            </h2>
            <p class="reglamentos_subtitulo">Documentos reglamentarios asignados a tus proyectos</p>
        </div>

        @if (count($proyectos) > 1)
        <div class="reglamentos_filtro">
            <label for="filtro_proyecto" class="filtro_label">
                <i class="fa-solid fa-filter"></i> Filtrar por proyecto
            </label>
            <select id="filtro_proyecto"
                    wire:model.live="proyectoFiltro"
                    class="filtro_select">
                <option value="">Todos los proyectos</option>
                @foreach ($proyectos as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
        </div>
        @endif
    </div>

    {{-- ALERTAS DE SESIÓN --}}
    @if (session()->has('info'))
        <div class="g_alerta info">
            <i class="fa-solid fa-circle-info"></i>
            <p>{{ session('info') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="g_alerta danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- LOADER LIVEWIRE --}}
    <div wire:loading.block class="reglamentos_loading">
        <i class="fa-solid fa-spinner fa-spin"></i> Cargando reglamentos...
    </div>

    {{-- GRID DE REGLAMENTOS --}}
    <div class="reglamentos_grid" wire:loading.class="reglamentos_grid--loading">
        @forelse ($reglamentos as $reglamento)
            <div class="reglamento_card"
                 @click="abrirPdf('{{ $reglamento->archivoPdf->url ?? '#' }}', {{ $reglamento->id }})"
                 title="{{ $reglamento->titulo }}">

                <div class="reglamento_icon">
                    <i class="fa-solid fa-file-pdf"></i>
                </div>

                <div class="reglamento_content">
                    <h3 class="reglamento_title">{{ $reglamento->titulo }}</h3>
                    <span class="reglamento_proyecto_badge">
                        <i class="fa-solid fa-building"></i>
                        {{ $reglamento->proyecto->nombre ?? 'Proyecto' }}
                    </span>
                    @if ($reglamento->descripcion)
                        <p class="reglamento_description">
                            {{ Str::limit($reglamento->descripcion, 120) }}
                        </p>
                    @endif
                </div>

                <div class="reglamento_action">
                    @if ($reglamento->archivoPdf)
                        <span class="reglamento_link">
                            Ver PDF <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </span>
                    @else
                        <span class="reglamento_link reglamento_link--disabled">
                            Sin archivo disponible
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="g_alerta info reglamentos_empty">
                <i class="fa-solid fa-circle-info"></i>
                <p>No hay reglamentos disponibles
                    @if ($proyectoFiltro)
                        para el proyecto seleccionado.
                    @else
                        para tus proyectos en este momento.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    @if ($reglamentos->count() > 0)
        <p class="reglamentos_contador">
            {{ $reglamentos->count() }} {{ $reglamentos->count() === 1 ? 'reglamento' : 'reglamentos' }} encontrado{{ $reglamentos->count() === 1 ? '' : 's' }}
        </p>
    @endif

    <style>
        /* ─── CABECERA ──────────────────────────────────── */
        .reglamentos_header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .reglamentos_titulo {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0 0 4px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .reglamentos_titulo i {
            color: #ffb001;
        }

        .reglamentos_subtitulo {
            font-size: 13px;
            color: #9e9e9e;
            margin: 0;
        }

        /* ─── FILTRO ────────────────────────────────────── */
        .reglamentos_filtro {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 240px;
        }

        .filtro_label {
            font-size: 12px;
            font-weight: 600;
            color: #616161;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filtro_select {
            width: 100%;
            padding: 9px 14px;
            border: 1.5px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            color: #212121;
            background: white;
            cursor: pointer;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            appearance: auto;
        }

        .filtro_select:focus {
            outline: none;
            border-color: #ffb001;
            box-shadow: 0 0 0 3px rgba(255, 176, 1, 0.12);
        }

        /* ─── LOADING ───────────────────────────────────── */
        .reglamentos_loading {
            text-align: center;
            padding: 40px;
            color: #9e9e9e;
            font-size: 15px;
        }

        .reglamentos_grid--loading {
            opacity: 0.5;
            pointer-events: none;
        }

        /* ─── GRID ──────────────────────────────────────── */
        .reglamentos_grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            transition: opacity 0.2s ease;
        }

        .reglamentos_empty {
            grid-column: 1 / -1;
        }

        /* ─── CARD ──────────────────────────────────────── */
        .reglamento_card {
            background: white;
            border: 1.5px solid #e8e8e8;
            border-radius: 12px;
            padding: 22px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
            gap: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        .reglamento_card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #e8e8e8;
            transition: background 0.25s ease;
        }

        .reglamento_card:hover {
            border-color: #ffb001;
            box-shadow: 0 6px 20px rgba(255, 176, 1, 0.12);
            transform: translateY(-3px);
        }

        .reglamento_card:hover::before {
            background: #ffb001;
        }

        .reglamento_icon {
            font-size: 40px;
            color: #ffb001;
            line-height: 1;
        }

        .reglamento_content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .reglamento_title {
            font-size: 16px;
            font-weight: 700;
            color: #212121;
            margin: 0;
            line-height: 1.4;
        }

        .reglamento_proyecto_badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            color: #163f49;
            background: #dceef1;
            padding: 3px 10px;
            border-radius: 20px;
            width: fit-content;
        }

        .reglamento_description {
            font-size: 13px;
            color: #9e9e9e;
            margin: 0;
            line-height: 1.5;
        }

        .reglamento_action {
            padding-top: 14px;
            border-top: 1px solid #f5f5f5;
        }

        .reglamento_link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #ffb001;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            transition: gap 0.2s ease;
        }

        .reglamento_card:hover .reglamento_link {
            gap: 10px;
        }

        .reglamento_link--disabled {
            color: #bdbdbd;
            cursor: default;
        }

        /* ─── CONTADOR ──────────────────────────────────── */
        .reglamentos_contador {
            margin-top: 16px;
            font-size: 12px;
            color: #bdbdbd;
            text-align: right;
        }

        /* ─── RESPONSIVE ────────────────────────────────── */
        @media (max-width: 768px) {
            .reglamentos_header {
                flex-direction: column;
            }

            .reglamentos_filtro {
                width: 100%;
                min-width: unset;
            }

            .reglamentos_grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 14px;
            }
        }

        @media (max-width: 480px) {
            .reglamentos_grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</div>