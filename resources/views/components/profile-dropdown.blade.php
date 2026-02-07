@props(['name', 'email', 'avatar' => null])

<div class="g_profile_container" x-data="{ open: false }" @click="open = !open" @click.outside="open = false"
    :class="{ 'active': open }">

    <div class="g_profile_info">
        <p class="g_profile_name">{{ $name }}</p>
        <span class="g_profile_email">{{ $email }}</span>
    </div>

    <img src="{{ $avatar ?? asset('assets/imagen/default.jpg') }}" class="g_profile_avatar" alt="{{ $name }}">

    <i class="fa-solid fa-sort-down g_profile_arrow"></i>

    <div class="g_dropdown_profile" x-show="open" x-transition x-cloak @click.stop="">

        <button type="button" class="g_dropdown_item" x-on:click.stop="toggleTheme()" title="Cambiar tema">
            <div class="g_dropdown_theme_toggle">
                <span>
                    <i x-show="theme === 'light'" class="fa-solid fa-moon"></i>
                    <i x-show="theme === 'dark'" class="fa-solid fa-sun"></i>
                    Modo <span x-text="theme === 'light' ? 'Oscuro' : 'Claro'"></span>
                </span>
            </div>
        </button>

        <div class="g_dropdown_divider"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="g_dropdown_item g_dropdown_item_danger">
                <i class="fa-solid fa-power-off"></i>
                Cerrar sesión
            </button>
        </form>
    </div>
</div>