# Plan de Mejora: Autenticacion y Sesiones Activas

## Resumen Ejecutivo

Este documento describe la mejora aplicada al sistema de acceso del ERP para manejar correctamente a los colaboradores que ya no forman parte de la empresa.

El objetivo es mantener el historial operativo del usuario en el sistema, pero impedir que una cuenta desactivada pueda seguir entrando al ERP o solicitar la recuperacion de su contrasena.

## Contexto Actual

En el sistema existen usuarios que pueden estar relacionados con tickets, cambios, registros y otras trazas historicas importantes. Por ese motivo, no conviene eliminarlos.

Sin embargo, cuando un colaborador termina su relacion laboral, su cuenta debe pasar a un estado inactivo para evitar accesos no autorizados.

## Problema

Antes de esta mejora, la cuenta podia seguir apareciendo como valida para ciertas acciones de autenticacion.

Eso generaba dos riesgos:

- El usuario podia intentar iniciar sesion aunque ya no debia tener acceso.
- El usuario podia pedir recuperacion de contrasena si aun tenia acceso a su correo corporativo.

## Solucion Implementada

Se aplicaron dos cambios principales:

### 1. Autenticacion con cuentas activas

El sistema ahora valida que la cuenta este activa antes de permitir el acceso.

Si la cuenta fue desactivada:

- No puede iniciar sesion.
- No puede volver a entrar por sesiones recordadas.
- Si ya tenia una sesion abierta, esta se invalida al volver a validar el acceso, regresando al login

### 2. Recuperacion de contrasena restringida

La solicitud de cambio de contrasena tambien fue ajustada para respetar el estado de la cuenta.

Si el correo pertenece a un usuario desactivado:

- No se envia el enlace de recuperacion.
- Se muestra un mensaje de error solo en el intento fallido.

## Mejora en la Experiencia del Usuario

Ademas del bloqueo tecnico, se mejoro la comunicacion visual del sistema.

Ahora el mensaje:

> Las cuentas desactivadas no pueden iniciar sesion ni solicitar recuperacion de contrasena.

solo aparece cuando el usuario intenta realmente acceder o recuperar la contrasena con una cuenta inactiva.

Ya no se muestra de forma permanente en pantalla, para evitar confusion.

## Beneficios Obtenidos

- Se protege el acceso al ERP de usuarios que ya no deben ingresar.
- Se conserva el historial de tickets, cambios y registros asociados al usuario.
- Se evita eliminar informacion necesaria para auditoria o trazabilidad.
- La regla de negocio queda clara para el usuario final y para el area administrativa.

## Alcance de la Mejora

Incluye:

- Bloqueo de inicio de sesion para cuentas desactivadas.
- Bloqueo de solicitud de recuperacion de contrasena para cuentas desactivadas.
- Mensaje de error visible solo cuando ocurre el intento.
- Conservacion del usuario en base de datos sin eliminar su historial.

No incluye:

- Eliminacion fisica de usuarios (Se busca preservar su Trabajo e Historial de Tickets).

## Resultado Esperado

Con esta mejora, el ERP mantiene la integridad de la informacion historica y, al mismo tiempo, asegura que solo las cuentas activas puedan autenticarse o solicitar recuperacion de acceso.

## Estado

- Autenticacion activa: implementada.
- Recuperacion restringida por estado de cuenta: implementada.
- Mensaje visual condicional: implementado.
