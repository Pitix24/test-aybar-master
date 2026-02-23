# Plan de Trabajo: CRM Email Marketing (Aybar Mail)

Este documento establece la hoja de ruta detallada para la construcción del sistema de envío masivo de correos, garantizando un servicio profesional, seguro y escalable.

---

## 📋 Checklist de Implementación

### 🏗️ Fase 1: Cimientos y Estructura (Backend)
- [x] **Diseño de Base de Datos**: 6 tablas (Contactos, Plantillas, Listas, Campañas, Vínculos y Logs).
- [x] **Modelos Eloquent**: Configuración de relaciones y fillables.
- [ ] **Migración de DB**: Ejecución de `php artisan migrate`.
- [ ] **Configuración de Colas**: Configurar el driver de colas para envíos en segundo plano.
- [ ] **Mailable Maestro**: Crear la estructura Blade base con el branding de Aybar (Header, Footer, Estilos Responsive).

### 👥 Fase 2: Gestión de Audiencia (Contactos y Listas)
- [ ] **CRUD de Contactos**: Vista para agregar, editar y eliminar destinatarios.
- [ ] **Módulo de Listas**: Crear grupos de segmentación.
- [ ] **Importador Excel Profesional**:
    - Mapeo de columnas (Nombre, Apellido, Email).
    - Lógica de "Upsert" (Crear si no existe, actualizar si existe).
    - Asignación masiva a listas durante la importación.
- [ ] **Exportador Excel**: Descarga de bases de datos por filtros o listas.

### 🎨 Fase 3: Estudio Creativo (Plantillas)
- [ ] **Editor de Contenido**: Integración de un editor visual (o soporte HTML) para diseñar el cuerpo del correo.
- [ ] **Sistema de Variables Dinámicas**: Implementar el motor que reemplaza `{nombres}`, `{apellidos}`, `{dni}`, etc.
- [ ] **Vista Previa**: Botón para visualizar cómo se verá el correo antes de enviarlo.

### 🚀 Fase 4: Motor de Campañas y Despacho
- [ ] **Creador de Campañas**: interfaz para unir (Plantilla + Lista).
- [ ] **Lógica de Envío Inteligente (Throttling)**:
    - Integración de `SendBulkEmailJob`.
    - Implementación de pausa de 2-3 segundos entre correos.
    - Manejo de límites de Office 365 (30 msg/min).
- [ ] **Monitor de Progreso**: Barra de progreso en tiempo real usando Livewire.

### 📊 Fase 5: Auditoría y Reportes (Post-Envío)
- [ ] **Historial de Envío**: Ver quién recibió el correo y quién falló.
- [ ] **Manejo de Errores**: Captura de mensajes de error de Outlook para limpiar la base de datos de correos inválidos.
- [ ] **Dashboard de Estadísticas**: Resumen de efectividad por campaña.

---

## 🔄 Flujo de Trabajo del Usuario

1.  **Preparar Audiencia**: Importas un Excel con 1,000 contactos y los etiquetas como "Clientes Julio".
2.  **Diseñar Mensaje**: Creas una plantilla de "Promoción Invierno" con banners y variables personalizadas.
3.  **Lanzar Campaña**: Creas la campaña "Lanzamiento Julio", eliges la lista y la plantilla.
4.  **Relajarte**: El sistema empieza a enviar por colas. Tú puedes cerrar el navegador o seguir trabajando.
5.  **Revisar**: Al terminar, el sistema te notifica el éxito y te muestra el reporte de enviados/fallidos.

---

## 🛡️ Protocolo de Seguridad (Anti-Spam)
- **Validación Estricta**: No se intentará enviar correos con formato inválido.
- **Queue Worker**: El proceso de envío corre independiente al servidor web para evitar caídas.
- **Respeto de Límites**: El sistema se auto-regula para nunca exceder los 10,000 correos diarios permitidos por Office 365.

---

## 📍 Estado Actual
- **Fase 1**: En progreso (Tablas definidas, Modelos creados).
- **Próximo Paso**: Ejecutar migraciones y crear el Mailable Maestro.
