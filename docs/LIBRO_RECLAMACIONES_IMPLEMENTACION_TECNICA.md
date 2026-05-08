# Libro de Reclamaciones: Implementacion Tecnica

## Proposito

Este documento resume la creacion, integracion y estructura tecnica del modulo de Libro de Reclamaciones, incluyendo el flujo de registro, la generacion de tickets por unidad de negocio, el envio de correos y la configuracion requerida en `.env`.

## Alcance funcional

El modulo queda compuesto por estos bloques:

1. Formulario publico en Livewire para registrar el reclamo.
2. Resolucion interna de `unidad_negocio_id` a partir del `proyecto_id` seleccionado.
3. Generacion transaccional de ticket por unidad de negocio.
4. Persistencia del reclamo en `libro_reclamacions`.
5. Emision de evento de dominio luego del `commit`.
6. Envio de correos al cliente y al equipo legal mediante eventos/listeners.
7. Plantillas HTML dedicadas para cliente y legal.
8. Documentacion operativa y de flujo de uso.

## Arquitectura aplicada

### 1. Capa publica

- Componente Livewire: `app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php`
- Vista Blade: `resources/views/livewire/web/libro-reclamacion/libro-reclamacion-livewire.blade.php`

Responsabilidades:

- Mostrar el formulario.
- Validar formato de campos opcionales (sin bloqueo por faltantes).
- Resolver la unidad de negocio desde el proyecto.
- Resolver unidad por defecto cuando no hay proyecto.
- Crear el reclamo.
- Disparar el evento de notificacion al finalizar el registro.

### 2. Capa de dominio

- Modelo principal: `app/Models/LibroReclamacion/LibroReclamacion.php`
- Contador de numeracion: `app/Models/LibroReclamacion/LibroReclamacionContador.php`
- Servicio de numeracion: `app/Services/LibroReclamacion/LibroReclamacionNumeroService.php`
- Evento: `app/Events/LibroReclamacion/LibroReclamacionRegistrado.php`
- Listener: `app/Listeners/LibroReclamacion/EnviarCorreosLibroReclamacion.php`

Responsabilidades:

- Generar el numero correlativo por unidad de negocio.
- Construir el `codigo_ticket` con el nombre de la unidad.
- Mantener el flujo de correo desacoplado del alta del reclamo.

### 3. Capa de presentacion de correo

- Mailable cliente: `app/Mail/LibroReclamacion/LibroReclamacionClienteMail.php`
- Mailable legal: `app/Mail/LibroReclamacion/LibroReclamacionLegalMail.php`
- Vista cliente: `resources/views/emails/libro-reclamacion/cliente-confirmacion.blade.php`
- Vista legal: `resources/views/emails/libro-reclamacion/legal-notificacion.blade.php`

Responsabilidades:

- Confirmacion al cliente con resumen del ticket.
- Notificacion al equipo legal con los datos operativos del reclamo.

## Flujo tecnico de registro

1. El usuario puede seleccionar o no un `Proyecto`.
2. Si hay proyecto, el sistema resuelve la `Unidad de Negocio` asociada.
3. Si no hay proyecto, se usa la unidad por defecto configurada en `.env`.
4. El servicio de numeracion reserva el siguiente correlativo disponible por unidad.
5. Se guarda el reclamo con:
   - `unidad_negocio_id`
   - `proyecto_id`
   - `serie`
   - `numero_reclamo`
   - `codigo_ticket`
   - datos del consumidor
   - detalle del reclamo
   - estado inicial
6. Se hace `commit` de la transaccion.
7. Se dispara `LibroReclamacionRegistrado`.
8. El listener envia correo legal siempre y correo cliente solo si hay email valido.

## Politica de validacion del formulario

No hay campos obligatorios para enviar el formulario. El sistema aplica solo validacion de formato cuando el usuario decide completar un campo.

Ejemplos de validacion de formato:

- `email` solo se valida si se completa.
- `monto_reclamado` debe ser numerico si se completa.
- `tipo_documento`, `tipo_pedido` y `tipo_bien_contratado` se validan por catalogo cuando tengan valor.

Campos opcionales disponibles:

- Domicilio
- Telefono
- Tipo de bien contratado
- Monto reclamado
- Descripcion del producto o servicio
- Tipo de solicitud
- Detalle del reclamo o queja
- Pedido del consumidor

## Datos que se guardan en la base

Tabla principal: `libro_reclamacions`

Datos relevantes persistidos:

- `ticket` como identificador tecnico
- `unidad_negocio_id`
- `proyecto_id`
- `numero_reclamo`
- `codigo_ticket`
- `nombre`
- `apellido_paterno`
- `apellido_materno`
- `domicilio`
- `telefono`
- `email`
- `tipo_documento`
- `numero_documento`
- `tipo_bien_contratado`
- `monto_reclamado`
- `descripcion`
- `tipo_pedido`
- `detalle`
- `pedido`
- `conformidad`
- `estado`

## Integracion con correo

El envio de correo se resolvio con el patron Events + Listeners para evitar que un error SMTP rompa el alta del reclamo.

### Correo al cliente

- Destino: el correo ingresado en el formulario (solo si es valido y no vacio).
- Finalidad: confirmar el registro y mostrar el ticket emitido.

### Correo al equipo legal

- Destino principal: `LIBRO_RECLAMACION_EMAIL_LEGAL_TO`
- Fallback operativo: `LIBRO_RECLAMACION_EMAIL_PRUEBAS`
- Finalidad: notificacion interna con el resumen legal del reclamo.

## Configuracion en `.env`

Variables a tener en cuenta para este modulo:

- `LIBRO_RECLAMACION_EMAIL_PRUEBAS`
  - Correo de pruebas o respaldo para validaciones internas.
- `LIBRO_RECLAMACION_EMAIL_LEGAL_TO`
  - Destinatario principal del equipo legal.
- `LIBRO_RECLAMACION_SERIE`
  - Serie comercial del ticket.
- `LIBRO_RECLAMACION_UNIDAD_DEFAULT_ID`
  - ID de unidad de negocio por defecto para generar ticket cuando no se selecciona proyecto.
- `LIBRO_RECLAMACION_AYBAR_RAZON_SOCIAL`
  - Razon social usada por defecto para AYBAR.
- `LIBRO_RECLAMACION_AYBAR_NUMERO_INICIAL`
  - Correlativo inicial configurado para AYBAR cuando aplique.
- `MAIL_HOST_RECLAMACION`
- `MAIL_PORT_RECLAMACION`
- `MAIL_USERNAME_RECLAMACION`
- `MAIL_PASSWORD_RECLAMACION`
- `MAIL_ENCRYPTION_RECLAMACION`
- `MAIL_FROM_ADDRESS_RECLAMACION`

Nota: en este documento no se registran valores secretos. Solo se documenta el nombre de la variable y su proposito.

## Estructura de carpetas aplicada

El modulo se ordeno por dominio:

- `app/Models/LibroReclamacion/`
- `app/Services/LibroReclamacion/`
- `app/Events/LibroReclamacion/`
- `app/Listeners/LibroReclamacion/`
- `app/Mail/LibroReclamacion/`
- `resources/views/emails/libro-reclamacion/`

## Observaciones tecnicas

- El correo debe enviarse fuera de la transaccion.
- El `codigo_ticket` se construye con el nombre de la unidad de negocio y el correlativo.
- El formulario publico no debe exponer `ruc` ni `direccion` como campos editables.
- La unidad de negocio se deriva desde proyecto o desde la unidad por defecto configurada.
- La validacion visual debe coincidir con la politica no bloqueante del componente Livewire.

## Verificacion recomendada

1. Crear un reclamo sin proyecto y confirmar que se usa `LIBRO_RECLAMACION_UNIDAD_DEFAULT_ID`.
2. Confirmar que el ticket se genera y se guarda en BD.
3. Revisar que el correo al cliente llega solo cuando hay email valido.
4. Revisar que el correo legal llega al destinatario configurado.
5. Probar otro proyecto de la misma o de otra unidad y confirmar correlativo independiente por unidad.
6. Verificar logs ante un fallo SMTP sin perder el reclamo guardado.
