# Sistema ERP - Arquitectura Modular

Para organizar correctamente los módulos de **Web**, **Cliente** y **ERP**, separaremos la aplicación en tres contextos claros. Esto evita mezclar lógica pública con gestión interna.

## 1. Estructura de Rutas (`routes/`)
Usaremos archivos de rutas separados para mantener el orden y facilitar la aplicación de "middlewares" (permisos) específicos.

-   `routes/web.php`:
    -   **Propósito**: Landing page pública, "Somos", "Contacto".
    -   **Acceso**: Público (sin login).
-   `routes/client.php` (Nuevo):
    -   **Propósito**: Portal de autogestión para clientes (ver facturas, pedidos).
    -   **Acceso**: Autenticación de Cliente (`auth:client` o similar).
-   `routes/erp.php` (Nuevo):
    -   **Propósito**: Sistema de gestión interna (Ventas, RRHH, Inventario).
    -   **Acceso**: Autenticación de Staff/Admin (`auth:web` + permisos).

## 2. Estructura de Vistas (`resources/views/`)
Cada módulo tendrá su propio sub-directorio y su propio "Layout" base, ya que el diseño visual será diferente en cada uno.

```text
resources/views/
├── layouts/
│   ├── web.blade.php      # Diseño sitio público (Header/Footer marketing)
│   ├── client.blade.php   # Diseño portal cliente (Sidebar simplificado)
│   └── erp.blade.php      # Diseño sistema completo (Sidebar complejo, herramientas)
├── web/
│   ├── home.blade.php
│   └── ...
├── client/
│   ├── dashboard.blade.php
│   └── ...
└── erp/
    ├── dashboard.blade.php
    ├── users/
    └── ...
```

## 3. Componentes Livewire (`app/Livewire/`)
Usaremos "Namespaces" para agrupar la lógica.

```text
app/Livewire/
├── Web/           # Lógica pública
│   └── Home.php
├── Client/        # Lógica portal clientes
│   └── Dashboard.php
└── Erp/           # Lógica del sistema
    ├── Dashboard.php
    └── Users/
        ├── Index.php
        └── ...
```

## Estilos (CSS)
Dado que usaremos CSS Vanilla, podemos cargar un archivo CSS principal o archivos separados para cada módulo si los diseños son muy distintos:
-   `public/css/web.css`
-   `public/css/app.css` (Base compartida)
-   `public/css/erp.css`
