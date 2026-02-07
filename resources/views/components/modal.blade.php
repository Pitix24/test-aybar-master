@props(['name' => null, 'open' => false, 'maxWidth' => '600px'])

<div x-data="{ 
        show: @js($open),
        init() {
            if (this.show) {
                document.body.style.overflow = 'hidden';
            }
            $watch('show', value => {
                if (value) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = null;
                }
            });
        }
    }" x-show="show" x-on:close.stop="show = false" x-on:keydown.escape.window="show = false" x-cloak id="{{ $name }}"
    class="g_modal_overlay">
    <div class="g_modal_container" style="max-width: {{ $maxWidth }};" x-show="show"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        @click.outside="show = false">
        @if (isset($titulo))
            <div class="g_modal_header">
                <div class="g_modal_title">
                    {{ $titulo }}
                </div>
                <button type="button" @click="show = false" class="g_modal_close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        <div class="g_modal_body">
            {{ $cuerpo ?? $slot }}
        </div>

        @if (isset($pie))
            <div class="g_modal_footer">
                {{ $pie }}
            </div>
        @endif
    </div>
</div>