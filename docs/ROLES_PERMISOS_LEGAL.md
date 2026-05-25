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

USO EN CODIGO
routes/erp/libro-reclamacion.php
app/Livewire/Erp/LibroReclamacion/LibroReclamacionLista.php
app/Livewire/Erp/LibroReclamacion/LibroReclamacionVer.php
app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php
app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php
resources/views/livewire/erp/atc/ticket/ticket-ver.blade.php
resources/views/livewire/erp/atc/ticket/ticket-editar.blade.php

NOTA
El acceso a lista, ver, crear y editar se protege con libro-reclamacion.lista. Desde ATC se muestra el enlace al ticket de libro de reclamacion solo si existe libro-reclamacion.ver, y la edicion valida libro-reclamacion.editar / libro-reclamacion.eliminar.

PERMISOS NO USADOS / NO APLICADOS
Ninguno detectado en el codigo actual de Libro Reclamacion.
