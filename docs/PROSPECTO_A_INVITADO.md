# Proceso de Conversión: Prospecto a Invitado (Auto-gestión)

Este documento describe el flujo para que un prospecto aprobado en Backoffice pueda confirmar su asistencia y convertirse automáticamente en invitado mediante un formulario público.

## 1. Requisitos Previos y Reglas
*   **Filtro:** Solo prospectos con `estado_backoffice = 'aprobado'`.
*   **Seguridad:** Enlaces únicos basados en UUID para evitar accesos no autorizados.
*   **MVP:** Una vez enviado el formulario, no se permiten ediciones posteriores.
*   **Automatización:** Si el prospecto acepta asistir, se crea automáticamente su registro de Invitado con su código QR/interno.

## 2. Cambios en la Base de Datos

### Tabla `prospecto_entrega_fests`
*   Añadir campo `uuid` (string, unique) para el acceso público.

### Tabla `invitado_entrega_fests`
*   Añadir `estado_confirmacion` (enum: 'pendiente', 'confirmado', 'no_asiste') default 'pendiente'.
*   Añadir `transporte` (enum: 'bus', 'propio', 'na') default 'na'.
*   Añadir `observaciones_asistencia` (text, nullable).

## 3. Componentes del Sistema

### A. Generación del Link
*   Al aprobar un prospecto en Backoffice, se debe asegurar que tenga un `uuid`.
*   El link será: `{APP_URL}/asistencia-evento/{prospecto_uuid}`.

### B. Formulario de Asistencia (Vista Pública)
Componente Livewire: `EntregaFestAsistenciaPublica`
*   **Datos fijos (informativos):**
    *   Nombre completo.
    *   DNI.
    *   Proyecto y Lote(s).
*   **Campos del formulario:**
    *   `¿Asistirá?`: Toggle/Radio (Sí / No).
    *   `Nº acompañantes`: Dropdown (0, 1, 2, 3).
    *   `Transporte`: Select (Bus Aybar / Movilidad Propia).
    *   `Observaciones`: Textarea opcional.
*   **Botón:** "Enviar Registro".

### C. Lógica de Conversión
Al hacer clic en "Enviar":
1.  **Validación:** Verificar que el prospecto no tenga ya un registro de invitado.
2.  **Si NO asiste:**
    *   Se crea el registro en `invitado_entrega_fests`.
    *   `confirmado = false`.
    *   `estado_confirmacion = 'no_asiste'`.
3.  **Si SÍ asiste:**
    *   Se crea el registro en `invitado_entrega_fests`.
    *   `confirmado = true`.
    *   `estado_confirmacion = 'confirmado'`.
    *   **Generación de código:** Se crea el `codigo_invitado` (ej. INV-001-XXXX).
    *   Se vinculan acompañantes y transporte.

## 4. Plan de Implementación
1.  **Migración:** Crear/Actualizar tablas para soportar los nuevos campos.
2.  **Modelos:** Actualizar fillables y relaciones.
3.  **Rutas:** Crear la ruta pública `/asistencia-evento/{uuid}`.
4.  **Livewire Component:** Desarrollar la lógica del formulario y la vista con estética premium (vibrant colors, glassmorphism).
5.  **Notificación (Opcional):** Preparar el texto para WhatsApp/Email.
