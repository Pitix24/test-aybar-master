<div x-data="{
        show: false,
        videoId: null,

        open(id) {
            this.videoId = id;
            this.show = true;
        },

        close() {
            this.show = false;
            this.videoId = null;
        }
    }" x-cloak>

    <!-- GRID -->
    <div class="tutorial_videos_grid">

        <div class="video_card" @click="open('dumEsf9xjLI')">
            <div class="video_thumb">
                <img src="{{ asset('assets/youtube/Descargar-Boletas-Miniatura-1280x720.png') }}"
                    alt="Descargar Boletas">
                <span class="play_icon"></span>
            </div>
            <p class="video_title">Cómo descargar Boletas</p>
        </div>

        <div class="video_card" @click="open('i3iTZZt84Zk')">
            <div class="video_thumb">
                <img src="{{ asset('assets/youtube/Voucher-de-Pago-Miniatura-1280x720.png') }}" alt="Voucher de Pago">
                <span class="play_icon"></span>
            </div>
            <p class="video_title">Cómo subir Voucher de Pago</p>
        </div>

        <div class="video_card" @click="open('Tcq8L3J8h7s')">
            <div class="video_thumb">
                <img src="{{ asset('assets/youtube/Estado-de-Cuenta-Miniatura-1280x720.png') }}" alt="Voucher de Pago">
                <span class="play_icon"></span>
            </div>
            <p class="video_title">Cómo descargar Estado de Cuenta</p>
        </div>


        <div class="video_card" @click="open('I76FbY5L8PM')">
            <div class="video_thumb">
                <img src="{{ asset('assets/youtube/Cronograma-de-Pagos-Miniatura-1280x720.png') }}"
                    alt="Voucher de Pago">
                <span class="play_icon"></span>
            </div>
            <p class="video_title">Cómo descargar Cronograma de Pago</p>
        </div>
    </div>

    <!-- MODAL -->
    <div class="video_modal" x-show="show" x-transition.opacity @click.self="close" @keydown.escape.window="close">
        <div class="video_modal_content">
            <span class="video_modal_close" @click="close">&times;</span>

            <!-- IFRAME LIMPIO -->
            <iframe x-show="show" :src="show && videoId
        ? `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&modestbranding=1&controls=1&fs=1&iv_load_policy=3&disablekb=1`
        : ''" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>
            </iframe>
        </div>
    </div>
</div>