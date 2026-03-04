# Gestión de Mensajería y Eventos

Actualmente los siguientes Listeners son **SÍNCRONOS** (se envían al instante sin esperar al worker):

1. `EnviarInvitacionesAsistencia.php` (Invitación inicial)
2. `EnviarNotificacionesAsistenciaConfirmada.php` (Ticket e Instrucciones)
3. `EnviarRecordatorioFirma.php` (Recordatorio de cita)

**¿Qué significa esto?**
- No necesitas ejecutar `php artisan queue:work` para que estos mensajes salgan.
- Al dar clic en "Aceptar" o "Enviar", la página tardará un poquito más en cargar porque está enviando los correos y WhatsApp en ese momento.

---

### Mantenimiento
Si en algún momento el volumen de invitados es muy alto y la página se pone muy lenta, puedes volver a añadir `implements ShouldQueue` a los archivos mencionados arriba y usar:

`php artisan queue:work`