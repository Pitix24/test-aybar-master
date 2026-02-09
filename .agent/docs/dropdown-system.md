# Sistema de Dropdowns Reutilizables

Este documento describe el sistema de componentes dropdown escalables y reutilizables.

## 📦 Componentes Disponibles

### 1. `<x-theme-dropdown />`
Selector de tema (Claro/Oscuro) con menú desplegable.

**Uso básico:**
```blade
<x-theme-dropdown />
```

**Con posición personalizada:**
```blade
<x-theme-dropdown position="left" />
<x-theme-dropdown position="right" />
```

**Requisitos:**
- Debe estar dentro de un elemento con `x-data="themeSwitcher()" x-init="initTheme()"`

---

### 2. `<x-profile-dropdown />`
Menú de perfil de usuario con avatar, información y opciones.

**Uso:**
```blade
<x-profile-dropdown 
    :name="auth()->user()->name" 
    :email="auth()->user()->email" 
/>
```

**Con avatar personalizado:**
```blade
<x-profile-dropdown 
    :name="$user->name" 
    :email="$user->email"
    :avatar="$user->avatar_url"
/>
```

---

## 🎨 Clases CSS Disponibles

### Contenedores
- `.g_dropdown_wrapper` - Contenedor posicional para dropdowns
- `.g_dropdown_profile` - Menú dropdown base

### Variantes de Posición
- `.g_dropdown_profile.left` - Alineado a la izquierda
- `.g_dropdown_profile.right` - Alineado a la derecha (por defecto)

### Variantes de Ancho
- `.g_dropdown_profile.narrow` - Ancho reducido (200px)
- `.g_dropdown_profile` - Ancho estándar (240px)
- `.g_dropdown_profile.wide` - Ancho amplio (280px)

### Items del Dropdown
- `.g_dropdown_item` - Item estándar del menú
- `.g_dropdown_item.active` - Item activo/seleccionado
- `.g_dropdown_item_danger` - Item de acción peligrosa (ej: cerrar sesión)
- `.g_dropdown_item_check` - Icono de check para items activos
- `.g_dropdown_divider` - Línea divisoria

---

## 🔧 Ejemplo de Uso Completo

```blade
<header class="header_layout_pagina" x-data="themeSwitcher()" x-init="initTheme()">
    <div class="g_header_actions">
        <x-theme-dropdown />
        <x-profile-dropdown 
            :name="auth()->user()->name" 
            :email="auth()->user()->email" 
        />
    </div>
</header>
```

---

## 📝 Crear un Dropdown Personalizado

```blade
<div class="g_dropdown_wrapper" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open">
        Abrir Menú
    </button>

    <div class="g_dropdown_profile narrow left" x-show="open" x-cloak x-transition>
        <a href="#" class="g_dropdown_item">
            <i class="fa-solid fa-home"></i>
            Inicio
        </a>
        
        <div class="g_dropdown_divider"></div>
        
        <button class="g_dropdown_item active">
            <i class="fa-solid fa-star"></i>
            Favoritos
            <i class="fa-solid fa-check g_dropdown_item_check"></i>
        </button>
    </div>
</div>
```

---

## ✅ Ventajas del Sistema

1. **Sin estilos inline** - Todo está en CSS reutilizable
2. **Escalable** - Fácil agregar nuevas variantes
3. **Consistente** - Mismo diseño en toda la app
4. **Mantenible** - Un solo lugar para cambiar estilos
5. **Accesible** - Soporte para Alpine.js y transiciones
