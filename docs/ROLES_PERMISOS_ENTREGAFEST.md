MODULO
modulo-entrega-fest.ver

PERMISO BASADO EN ROLES:
entrega-fest.gestor
entrega-fest.supervisor

PERMISOS APLICADOS

ENTREGA FEST
entrega-fest.navegacion ->OK
entrega-fest.vista-lista
entrega-fest.vista-ver
entrega-fest.vista-crear
entrega-fest.accion-crear
entrega-fest.vista-editar
entrega-fest.accion-editar
entrega-fest.accion-eliminar
entrega-fest.accion-exportar-filtro
entrega-fest.accion-exportar-todo
entrega-fest.vista-ver-panel-gestion
entrega-fest.vista-ver-panel-staff
entrega-fest.accion-importar-prospectos
entrega-fest.accion-importar-itinerario
entrega-fest.accion-importar-tareas-mop
entrega-fest.accion-guardar-plantilla-mensaje

PROSPECTO
prospecto.navegacion
prospecto.vista-lista
prospecto.vista-ver
prospecto.vista-crear
prospecto.accion-crear
prospecto.vista-editar
prospecto.accion-editar
prospecto.accion-eliminar
prospecto.accion-exportar-filtro
prospecto.accion-exportar-todo
prospecto.vista-panel-gestion
prospecto.vista-bancarizacion
prospecto.accion-enviar-preinvitacion
prospecto.accion-guardar-avance-bo
prospecto.accion-guardar-seguimiento-legal
prospecto.accion-confirmar-legal
prospecto.accion-agregar-copropietario
prospecto.accion-eliminar-copropietario
prospecto.accion-editar-copropietario
prospecto.accion-agregar-bancarizacion
prospecto.accion-eliminar-bancarizacion
prospecto.accion-editar-bancarizacion
prospecto.accion-enviar-recordatorio-cita

INVITADO
invitado.navegacion
invitado.vista-lista
invitado.vista-ver
invitado.vista-crear
invitado.accion-crear
invitado.vista-editar
invitado.accion-editar
invitado.accion-eliminar
invitado.accion-exportar-filtro
invitado.accion-exportar-todo
invitado.vista-panel-gestion
invitado.vista-asistencia
invitado.accion-agregar-acompanante
invitado.accion-eliminar-acompanante
invitado.accion-editar-acompanante

ASISTENCIA
asistencia.navegacion
asistencia.vista-lista
asistencia.vista-ver
asistencia.accion-marcar
asistencia.exportar-todo
invitado.vista-panel-gestion
invitado.vista-invitados

ITINERARIO
itinerario.navegacion
itinerario.vista-lista
itinerario.vista-ver
itinerario.vista-crear
itinerario.accion-crear
itinerario.vista-editar
itinerario.accion-editar
itinerario.accion-eliminar
itinerario.accion-exportar-filtro
itinerario.accion-exportar-todo
itinerario.vista-panel-gestion

PERMISOS NO APLICADOS
prospecto.accion-crear
prospecto.accion-editar
prospecto.accion-eliminar
prospecto.accion-enviar-preinvitacion
prospecto.accion-guardar-avance-bo
prospecto.accion-guardar-seguimiento-legal
prospecto.accion-confirmar-legal
prospecto.accion-agregar-copropietario
prospecto.accion-eliminar-copropietario
prospecto.accion-editar-copropietario
prospecto.accion-agregar-bancarizacion
prospecto.accion-eliminar-bancarizacion
prospecto.accion-editar-bancarizacion
prospecto.accion-enviar-recordatorio-cita
invitado.accion-crear
invitado.accion-editar
invitado.accion-eliminar
invitado.accion-agregar-acompanante
invitado.accion-eliminar-acompanante
invitado.accion-editar-acompanante
asistencia.accion-marcar

PERMISOS DETECTADOS SOLO EN CODIGO
entrega-fest.staff
invitado-entrega-fest.crear

USO EN CODIGO
app/Livewire/Erp/EntregaFest/Recurso/StaffRecursos.php
app/Livewire/Erp/EntregaFest/Invitado/EntregaFestInvitadoCrear.php

NOTA
Estos permisos controlan la gestion de recursos y la generacion de invitados desde el flujo de Entrega Fest.
