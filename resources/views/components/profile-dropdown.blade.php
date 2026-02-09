<div class="g_dropdown_cabecera normal right" x-show="open" x-cloak x-transition @click.stop>
    <div class="g_dropdown_cuerpo">
        <a href="{{ route('erp.home') }}" class="g_dropdown_item">
            <i class="fa-solid fa-id-card"></i>
            Perfil
        </a>

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