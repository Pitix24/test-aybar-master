# Plan: Correo Post Registro Libro Reclamaciones

Implementar envio desacoplado con Events + Listeners al finalizar el formulario: confirmacion al cliente y notificacion al equipo legal. El registro del reclamo y ticket debe mantenerse transaccional y el correo debe ejecutarse despues del commit para no perder el ticket si falla el transporte SMTP.

## Steps

1. Definir contrato del evento de dominio para registro exitoso: crear un evento dedicado que reciba el reclamo ya persistido con sus relaciones necesarias para construir ambos correos. Este evento se dispara solo cuando el alta en BD ya termino correctamente.
2. Crear mailables separados por destinatario: uno para cliente (confirmacion de ticket) y otro para legal (resumen operativo). Mantener vistas de email en carpeta de dominio para Libro Reclamacion.
3. Crear listener unico de envio que escuche el evento y resuelva destinatarios: email del cliente desde el reclamo y email legal desde variable de entorno/config. Manejar errores de envio con log sin romper la experiencia del formulario.
4. Registrar el listener en el proveedor de eventos y validar que quede alineado con el patron ya usado en TicketCreado para ATC.
5. Integrar el disparo en el Livewire web despues del commit: registrar reclamo y ticket, hacer commit, luego dispatch del evento. No enviar correos dentro de la transaccion.
6. Opcional recomendado: preparar listener para cola (ShouldQueue) y dejar fallback sync en local para simplificar pruebas.
7. Validar extremo a extremo: envio real a cliente y legal, revisar logs, y comprobar que un fallo SMTP no revierta el reclamo guardado.

## Relevant files

- app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php - punto de disparo del evento luego del commit.
- app/Events/TicketCreado.php - referencia de estilo para evento simple en el proyecto.
- app/Providers/EventServiceProvider.php - registro de listeners para el nuevo evento.
- app/Mail/TicketCreadoMail.php - referencia de mailable simple.
- app/Mail/LibroReclamacion/LibroReclamacionClienteMail.php - nuevo mailable para cliente.
- app/Mail/LibroReclamacion/LibroReclamacionLegalMail.php - nuevo mailable para equipo legal.
- app/Listeners/LibroReclamacion/EnviarCorreosLibroReclamacion.php - nuevo listener de envio doble.
- resources/views/emails/libro-reclamacion/cliente-confirmacion.blade.php - plantilla cliente.
- resources/views/emails/libro-reclamacion/legal-notificacion.blade.php - plantilla legal.
- config/libro_reclamacion.php - configuracion de destinatario legal y modo de envio.
- .env - variable de destino legal del modulo.

## Verification

1. Caso feliz: enviar formulario con email valido, verificar reclamo creado y dos correos enviados.
2. Caso cliente sin correo valido: reclamo se registra y listener loguea condicion sin romper flujo.
3. Caso fallo SMTP simulado: reclamo y ticket quedan persistidos, se registra error de correo en log.
4. Repetir con dos unidades de negocio para confirmar que correo usa el codigo_ticket correcto generado por unidad.
5. Revisar payload de mailable legal para confirmar datos clave: proyecto, unidad, codigo_ticket, fecha y datos del reclamo.

## Decisions

- El envio se desacopla del Livewire usando evento de dominio.
- El evento se emite despues del commit para evitar inconsistencias.
- Se evita n8n en este flujo; se usa solo Mail de Laravel y configuracion del proyecto.
- Un solo punto de configuracion para el destinatario legal del modulo.

## Further Considerations

1. Definir si el correo legal va a un solo destino o a lista separada por comas.
2. Evaluar tabla de auditoria de envios si se requiere trazabilidad formal como en TicketEmail.
3. Si aumenta volumen, activar cola para listener y reintentos controlados.