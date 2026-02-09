<div class="header_dropdown_cabecera normal right" x-show="open" x-cloak x-transition @click.stop>
    <div class="header_dropdown_cuerpo">
        <a href="{{ route('erp.home') }}" class="header_dropdown_item">
            <i class="fa-solid fa-id-card"></i>
            Perfil
        </a>

        <div class="header_dropdown_divider"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="header_dropdown_item header_dropdown_item_danger">
                <i class="fa-solid fa-power-off"></i>
                Cerrar sesión
            </button>
        </form>
    </div>
</div>