<div class="header_dropdown_cabecera narrow right" x-show="open" x-cloak x-transition @click.stop>
    <div class="header_dropdown_cuerpo">
        <button class="header_dropdown_item" :class="{ 'active': theme === 'light' }"
            @click="setTheme('light'); open = false">
            <i class="fa-solid fa-sun"></i>
            Modo Claro
            <i x-show="theme === 'light'" class="fa-solid fa-check header_dropdown_item_check" x-cloak></i>
        </button>

        <button class="header_dropdown_item" :class="{ 'active': theme === 'dark' }"
            @click="setTheme('dark'); open = false">
            <i class="fa-solid fa-moon"></i>
            Modo Oscuro
            <i x-show="theme === 'dark'" class="fa-solid fa-check header_dropdown_item_check" x-cloak></i>
        </button>
    </div>
</div>