php artisan make:model UnidadNegocio -mfsc
php artisan make:livewire erp.unidad-negocio.unidad-negocio-lista --class
php artisan make:livewire erp.unidad-negocio.unidad-negocio-crear --class
php artisan make:livewire erp.unidad-negocio.unidad-negocio-editar --class

php artisan make:model GrupoProyecto -mfsc
php artisan make:livewire erp.grupo-proyecto.grupo-proyecto-lista --class
php artisan make:livewire erp.grupo-proyecto.grupo-proyecto-crear --class
php artisan make:livewire erp.grupo-proyecto.grupo-proyecto-editar --class

php artisan make:model Proyecto -mfsc
php artisan make:livewire erp.proyecto.proyecto-lista --class
php artisan make:livewire erp.proyecto.proyecto-crear --class
php artisan make:livewire erp.proyecto.proyecto-editar --class

php artisan make:model Pais -mfs
php artisan make:model Region -mfs
php artisan make:model Provincia -mfs
php artisan make:model Distrito -mfs

php artisan make:model Cliente -mfsc
php artisan make:livewire erp.cliente.cliente-lista --class
php artisan make:livewire erp.cliente.cliente-crear --class
php artisan make:livewire erp.cliente.cliente-editar --class

php artisan make:livewire erp.admin.admin-lista --class
php artisan make:livewire erp.admin.admin-crear --class
php artisan make:livewire erp.admin.admin-editar --class

php artisan make:livewire erp.rol.rol-lista --class
php artisan make:livewire erp.rol.rol-crear --class
php artisan make:livewire erp.rol.rol-editar --class

php artisan make:livewire erp.permiso.permiso-lista --class
php artisan make:livewire erp.permiso.permiso-crear --class
php artisan make:livewire erp.permiso.permiso-editar --class

php artisan make:model Direccion -mfsc
php artisan make:livewire erp.direccion.direccion-lista --class
php artisan make:livewire erp.direccion.direccion-crear --class
php artisan make:livewire erp.direccion.direccion-editar --class

php artisan make:migration add_module_to_permissions_table --table=permissions

php artisan make:model Sede -mfsc
php artisan make:livewire erp.sede.sede-lista --class
php artisan make:livewire erp.sede.sede-crear --class
php artisan make:livewire erp.sede.sede-editar --class

php artisan make:model Area -mfsc
php artisan make:livewire erp.area.area-lista --class
php artisan make:livewire erp.area.area-crear --class
php artisan make:livewire erp.area.area-editar --class

php artisan make:migration create_area_sede_table --table=area_sede

php artisan make:migration create_area_user_table --table=area_user

php artisan make:model TipoSolicitud -mfsc
php artisan make:livewire erp.tipo-solicitud.tipo-solicitud-lista --class
php artisan make:livewire erp.tipo-solicitud.tipo-solicitud-crear --class
php artisan make:livewire erp.tipo-solicitud.tipo-solicitud-editar --class

php artisan make:model SubTipoSolicitud -mfsc
php artisan make:livewire erp.sub-tipo-solicitud.sub-tipo-solicitud-lista --class
php artisan make:livewire erp.sub-tipo-solicitud.sub-tipo-solicitud-crear --class
php artisan make:livewire erp.sub-tipo-solicitud.sub-tipo-solicitud-editar --class

php artisan make:migration create_area_tipo_solicitud_table --table=area_tipo_solicitud

php artisan make:model EstadoTicket -mfsc
php artisan make:livewire erp.estado-ticket.estado-ticket-lista --class
php artisan make:livewire erp.estado-ticket.estado-ticket-crear --class
php artisan make:livewire erp.estado-ticket.estado-ticket-editar --class

php artisan make:model PrioridadTicket -mfsc
php artisan make:livewire erp.prioridad-ticket.prioridad-ticket-lista --class
php artisan make:livewire erp.prioridad-ticket.prioridad-ticket-crear --class
php artisan make:livewire erp.prioridad-ticket.prioridad-ticket-editar --class

php artisan make:model Canal -mfsc
php artisan make:livewire erp.canal.canal-lista --class
php artisan make:livewire erp.canal.canal-crear --class
php artisan make:livewire erp.canal.canal-editar --class

php artisan make:model Ticket -mfsc
php artisan make:livewire erp.ticket.ticket-lista --class
php artisan make:livewire erp.ticket.ticket-crear --class
php artisan make:livewire erp.ticket.ticket-editar --class

php artisan make:model TicketParticipante -mfsc
php artisan make:livewire erp.ticket-participante.ticket-participante-lista --class
php artisan make:livewire erp.ticket-participante.ticket-participante-crear --class
php artisan make:livewire erp.ticket-participante.ticket-participante-editar --class

php artisan make:model TicketArchivo -mfsc
php artisan make:livewire erp.ticket-archivo.ticket-archivo-lista --class
php artisan make:livewire erp.ticket-archivo.ticket-archivo-crear --class
php artisan make:livewire erp.ticket-archivo.ticket-archivo-editar --class

php artisan make:model TicketHistorial -mfsc
php artisan make:livewire erp.ticket-historial.ticket-historial-lista --class
php artisan make:livewire erp.ticket-historial.ticket-historial-crear --class
php artisan make:livewire erp.ticket-historial.ticket-historial-editar --class

php artisan make:model TicketDerivado -mfsc
php artisan make:livewire erp.ticket-derivado.ticket-derivado-lista --class
php artisan make:livewire erp.ticket-derivado.ticket-derivado-crear --class
php artisan make:livewire erp.ticket-derivado.ticket-derivado-editar --class

php artisan make:model TicketMensaje -mfsc
php artisan make:livewire erp.ticket-mensaje.ticket-mensaje-lista --class
php artisan make:livewire erp.ticket-mensaje.ticket-mensaje-crear --class
php artisan make:livewire erp.ticket-mensaje.ticket-mensaje-editar --class

php artisan make:model TicketEmail -mfsc
php artisan make:livewire erp.ticket-email.ticket-email-lista --class
php artisan make:livewire erp.ticket-email.ticket-email-crear --class
php artisan make:livewire erp.ticket-email.ticket-email-editar --class