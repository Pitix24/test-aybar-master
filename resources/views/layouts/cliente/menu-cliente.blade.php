<div class="cliente_menu_pricipal">
    <a href="{{ route('cliente.home') }}" class="{{ request()->routeIs('cliente.home') ? 'active' : '' }}">
        <span>
            <i class="fa-solid fa-address-card"></i>
            Perfil
            @if (auth()->user()->necesitaActualizarDatosPersonales() || auth()->user()->necesitaActualizarDireccion())
                <span class="g_menu_badge warning">Actualiza</span>
            @endif
        </span>
        <i class="fa-solid fa-chevron-right"></i>
    </a>

    <a href="{{ route('cliente.lote') }}" class="{{ request()->routeIs('cliente.lote') ? 'active' : '' }}">
        <span><i class="fa-solid fa-border-all"></i> Mis Proyectos</span>
        <i class="fa-solid fa-chevron-right"></i>
    </a>

    <a href="{{ route('cliente.tutorial') }}" class="{{ request()->routeIs('cliente.tutorial') ? 'active' : '' }}">
        <span><i class="fa-solid fa-circle-play"></i> Tutoriales</span>
        <i class="fa-solid fa-chevron-right"></i>
    </a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">
            <span><i class="fa-solid fa-power-off"></i> Cerrar sesión</span>
        </button>
    </form>
</div>