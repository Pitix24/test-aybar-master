# Proceso de Conversión: Prospecto a Invitado, QR y Check-in

Este documento describe el flujo completo desde que un prospecto es aprobado hasta que ingresa al evento mediante el escaneo de su código QR.

## 1. El Flujo de Invitación (ERP)
1.  **Aprobación:** Solo los prospectos con `estado_backoffice = 'aprobado'` son elegibles para recibir la invitación.
2.  **Envío Masivo:** Desde el listado de Prospectos en el ERP, el gestor utiliza los botones **"Enviar Correos"** o **"Enviar WhatsApp"**.
3.  **Contenido:** Se envía un link personalizado al cliente: `{APP_URL}/evento/{slug}/{id}`.

## 2. Confirmación del Cliente (Formulario Público)
1.  **Acceso:** El cliente abre el link desde su celular.
2.  **Formulario:** El cliente indica si asistirá, cuántos acompañantes lleva (máximo permitido) y su método de transporte.
3.  **Registro Automático:** Al confirmar, el sistema:
    *   Crea un registro en `invitado_entrega_fests`.
    *   Genera un **Código de Invitado único** (Ej: `INV-002-2839`).
    *   Marca al prospecto como "Invitado".

## 3. El Ticket y Código QR
1.  **Generación:** El código de invitado es la base para el QR.
2.  **Visualización:** 
    *   **En confirmación:** Tras enviar el formulario, el cliente ve su QR en pantalla (su "Ticket Digital").
    *   **Por Correo (Pendiente):** Se puede implementar un Observer que envíe automáticamente un segundo correo con el ticket adjunto o el QR visible cuando se crea el registro de invitado.
3.  **Uso:** El cliente debe presentar este QR (o captura de pantalla) el día del evento.

## 4. Control de Asistencia el día del Evento (Check-in)
1.  **Herramienta:** El personal de Aybar (Vigilancia/Recepción) utiliza la vista **"Asistencia"** en el ERP.
2.  **Escaneo:**
    *   Se utiliza un lector QR (pistola USB) o la cámara de una tablet/celular.
    *   Al escanear el QR, el código entra al sistema automáticamente.
3.  **Validación en Tiempo Real:**
    *   **Éxito:** Muestra mensaje de bienvenida y registra el ingreso.
    *   **Alerta:** Si el QR ya fue usado antes, muestra la hora del primer ingreso (evita fraude).
    *   **Error:** Si el QR es falso o de otro evento, lanza un error visual.

## 5. Resumen Técnico de Tablas
*   `prospecto_entrega_fests`: Origen de datos y link UUID.
*   `invitado_entrega_fests`: Registro de confirmación y `codigo_invitado`.
*   `asistencia_entrega_fests`: Historial de ingresos reales al evento (Check-in).
