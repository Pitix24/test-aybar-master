<div class="cliente_menu_pricipal">
    <a href="{{ route('cliente.home') }}">
        <span>
            <i class="fa-solid fa-address-card"></i>
            Perfil
            @if (auth()->user()->necesitaActualizarDatosPersonales() || auth()->user()->necesitaActualizarDirecciones())
            <span class="g_menu_badge warning">Actualiza</span>
            @endif
        </span>
        <i class="fa-solid fa-chevron-right"></i>
    </a>

    <a href="{{ route('cliente.lote') }}" class="">
        <span><i class="fa-solid fa-border-all"></i> Mis Proyectos</span>
        <i class="fa-solid fa-chevron-right"></i>
    </a>

    <a href="{{ route('cliente.tutorial') }}" class="">
        <span><i class="fa-solid fa-circle-play"></i> Tutoriales</span>
        <i class="fa-solid fa-chevron-right"></i>
    </a>

    <form method="POST" action="{{ route('logout.cliente') }}">
        @csrf
        <button type="submit">
            <span><i class="fa-solid fa-power-off"></i> Cerrar</span>
        </button>
    </form>
</div>