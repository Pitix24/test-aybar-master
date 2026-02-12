@props(['text'])

<span x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" class="g_tooltip">
    <i class="fa-solid fa-circle-info"></i>

    <span x-show="show" x-cloak x-transition class="g_tooltip_content">
        {{ $text }}
    </span>
</span>
