MODULO
modulo-reporte.ver

PERMISO BASADO EN ROLES:
No tiene un rol base propio en el seeder. El acceso se controla por el permiso de modulo y permisos de vista por reporte.

REPORTE USUARIO
reporte-usuario.navegacion
reporte-usuario.admin.ver
reporte-usuario.cliente.ver
reporte-usuario.direccion.ver

REPORTE BACKOFFICE
reporte-backoffice.navegacion
reporte-backoffice.solicitud-evidencia-pago.ver
reporte-backoffice.evidencia-pago.ver
reporte-backoffice.evidencia-pago-antiguo.ver

REPORTE ATC
reporte-atc.navegacion
reporte-atc.ticket.ver

REPORTE CITA
reporte-cita.navegacion
reporte-cita.cita.ver

REPORTE LETRA
reporte-letra.navegacion
reporte-letra.letra.ver

USO EN CODIGO
routes/erp/reporte.php
app/Livewire/Erp/Reporte/Usuario/ReporteAdmin.php
app/Livewire/Erp/Reporte/Usuario/ReporteCliente.php
app/Livewire/Erp/Reporte/Usuario/ReporteDireccion.php
app/Livewire/Erp/Reporte/Backoffice/ReporteSolicitudEvidenciaPago.php
app/Livewire/Erp/Reporte/Backoffice/ReporteEvidenciaPago.php
app/Livewire/Erp/Reporte/Backoffice/ReporteEvidenciaPagoAntiguo.php
app/Livewire/Erp/Reporte/Atc/ReporteTicket.php
app/Livewire/Erp/Reporte/Cita/ReporteCita.php
app/Livewire/Erp/Reporte/Letra/ReporteLetra.php

NOTA
La proteccion principal vive en la ruta mediante middleware de permiso. Los componentes Livewire de este modulo no agregan authorize adicional.

PERMISOS NO USADOS / NO APLICADOS
Ninguno detectado en el codigo actual de Reporte.
