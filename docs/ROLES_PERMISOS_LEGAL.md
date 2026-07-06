MODULO
modulo-legal.ver

PERMISO BASADO EN ROLES:
legal.asesor
legal.supervisor

LIBRO RECLAMACION
libro-reclamacion.lista
libro-reclamacion.ver
libro-reclamacion.editar
libro-reclamacion.eliminar

CARTAS NOTARIALES
ticket-notarial.navegacion
ticket-notarial.lista
ticket-notarial.crear
ticket-notarial.accion-crear
ticket-notarial.ver
ticket-notarial.editar
ticket-notarial.accion-editar
ticket-notarial.accion-exportar-filtro
ticket-notarial.accion-exportar-todo

USO EN CODIGO
routes/erp/legal.php
app/Livewire/Erp/LibroReclamacion/LibroReclamacionLista.php
app/Livewire/Erp/LibroReclamacion/LibroReclamacionVer.php
app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php
app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php
app/Livewire/Erp/Legal/TicketNotarial/TicketNotarialLista.php
app/Livewire/Erp/Atc/Ticket/TicketCrear.php
resources/views/livewire/erp/atc/ticket/ticket-ver.blade.php
resources/views/livewire/erp/atc/ticket/ticket-editar.blade.php

NOTA
El acceso a lista, crear, ver, editar y exportar de cartas notariales se protege con ticket-notarial.lista / ticket-notarial.crear / ticket-notarial.ver / ticket-notarial.editar. Crear usa directamente TicketCrear de ATC con tipo de solicitud precargado; ver y editar usan directamente TicketVer y TicketEditar. Las acciones de guardado validan ticket-notarial.accion-crear y ticket-notarial.accion-editar. Las exportaciones usan ticket-notarial.accion-exportar-filtro y ticket-notarial.accion-exportar-todo.

PERMISOS NO USADOS / NO APLICADOS
Ninguno detectado en el codigo actual de Libro Reclamacion.
