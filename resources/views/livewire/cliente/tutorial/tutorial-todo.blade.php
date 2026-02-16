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

    <!-- GRID -->
    <div class="tutorial_videos_grid">
        @forelse ($tutoriales as $tutorial)
            <div class="video_card" @click="open('{{ $tutorial->video_id }}', {{ $tutorial->id }})">
                <div class="video_thumb">
                    @if ($tutorial->miniatura)
                        <img src="{{ $tutorial->miniatura->url ?? asset($tutorial->miniatura->path) }}"
                            alt="{{ $tutorial->titulo }}">
                    @else
                        <img src="{{ asset('assets/youtube/Descargar-Boletas-Miniatura-1280x720.png') }}"
                            alt="{{ $tutorial->titulo }}">
                    @endif
                    <span class="play_icon"></span>
                </div>
                <p class="video_title">{{ $tutorial->titulo }}</p>
            </div>
        @empty
            <div class="g_alerta info">
                <i class="fa-solid fa-circle-info"></i>
                No hay tutoriales disponibles en este momento.
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