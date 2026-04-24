<div x-data="{
        show: false,
        videoId: null,

        open(id, tutorialId) {
            this.videoId = id;
            this.show = true;
            @this.registrarClick(tutorialId);
        },

        close() {
            this.show = false;
            this.videoId = null;
        }
    }" x-cloak>

    <div class="g_panel g_margin_bottom_20">
        <div class="g_panel_titulo">
            <h2>Avance del Proyecto</h2>
        </div>

        <div class="formulario">
            <div class="g_fila">
                <div class="g_columna_4">
                    <select wire:model.live="unidad_id" class="g_input">
                        <option value="">Empresas</option>
                        @foreach ($unidades as $unidad)
                            <option value="{{ $unidad->id }}">
                                {{ $unidad->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="g_columna_4">
                    <select wire:model.live="proyecto_id" class="g_input">
                        <option value="">Proyectos</option>
                        @foreach ($proyectos as $proyecto)
                            <option value="{{ $proyecto->id }}">
                                {{ $proyecto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- GRID -->
    <div class="tutorial_videos_grid">
        @forelse ($avanceProyectos as $avance)
            <div class="video_card" @click="open('{{ $avance->video_id }}', {{ $avance->id }})">
                <div class="video_thumb">
                    @if ($avance->miniatura)
                        <img src="{{ $avance->miniatura->url ?? asset($avance->miniatura->path) }}" alt="{{ $avance->titulo }}">
                    @else
                        <img src="{{ asset('assets/youtube/Descargar-Boletas-Miniatura-1280x720.png') }}"
                            alt="{{ $avance->titulo }}">
                    @endif
                    <span class="play_icon"></span>
                </div>
                <p class="video_title">{{ $avance->titulo }}</p>
            </div>
        @empty
            <div class="g_alerta info">
                <i class="fa-solid fa-circle-info"></i>
                No hay avances de proyectos disponibles en este momento.
            </div>
        @endforelse
    </div>

    <!-- MODAL -->
    <div class="video_modal" x-show="show" x-transition.opacity @click.self="close" @keydown.escape.window="close">
        <div class="video_modal_content">
            <span class="video_modal_close" @click="close">&times;</span>

            <!-- IFRAME LIMPIO -->
            <iframe x-show="show" :src="show && videoId ?
                    `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&modestbranding=1&controls=1&fs=1&iv_load_policy=3&disablekb=1` :
                    ''" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>
            </iframe>
        </div>
    </div>
</div>