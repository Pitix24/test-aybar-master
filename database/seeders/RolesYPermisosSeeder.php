<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesYPermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Creando permisos y roles...');

        // ----------------------------------------
        // 1. ESTRUCTURA DE PERMISOS POR MÓDULO
        // ----------------------------------------
        $modulos = [
            'Módulo Sistema' => [//ok
                'modulo-sistema.ver',//ok
                /* ROLES */
                'rol.navegacion',//ok
                'rol.lista',//ok
                'rol.ver',//ok
                'rol.crear',//ok
                'rol.editar',//ok
                'rol.eliminar',//ok
                'rol.exportar-filtro',//ok
                'rol.exportar-todo',//ok
                /* PERMISOS */
                'permiso.navegacion',//ok
                'permiso.lista',//ok
                'permiso.ver',//ok
                'permiso.crear',//ok
                'permiso.editar',//ok
                'permiso.eliminar',//ok
                'permiso.exportar-filtro',//ok
                'permiso.exportar-todo',//ok
                /* MENUS */
                'menu.navegacion',//ok
                'menu.lista',//ok
                'menu.ver',//ok
                'menu.crear',//ok
                'menu.editar',//ok
                'menu.eliminar',//ok
                'menu.exportar-filtro',//ok
                'menu.exportar-todo',//ok
            ],
            'Módulo Usuario' => [
                'modulo-usuarios.ver',//ok
                'admin.navegacion',//ok
                'admin.lista',//ok
                'admin.ver',//ok
                'admin.crear',//ok
                'admin.editar',//ok
                'admin.eliminar',//ok
                'admin.exportar-filtro',//ok
                'admin.exportar-todo',//ok
                'admin.cambiar-clave',//ok
                'cliente.navegacion',
                'cliente.lista',
                'cliente.ver',
                'cliente.crear',
                'cliente.editar',
                'cliente.eliminar',
                'cliente.exportar-filtro',
                'cliente.exportar-todo',
                'cliente.consultar',
                'cliente.enviar-recuperar-clave',
                'cliente.movimientos',
                'cliente-antiguo.navegacion',
                'cliente-antiguo.lista',
                'cliente-antiguo.ver',
                'cliente-antiguo.crear',
                'cliente-antiguo.editar',
                'cliente-antiguo.eliminar',
                'cliente-antiguo.exportar-filtro',
                'cliente-antiguo.exportar-todo',
            ],
            'Módulo Negocio' => [//ok
                'modulo-negocio.ver',
                /* ROLES */
                'unidad-negocio.navegacion',//ok
                'unidad-negocio.lista',//ok
                'unidad-negocio.ver',//ok
                'unidad-negocio.crear',//ok
                'unidad-negocio.editar',//ok
                'unidad-negocio.eliminar',//ok
                'unidad-negocio.exportar-filtro',//ok
                'unidad-negocio.exportar-todo',//ok
                /* GRUPOS PROYECTOS */
                'grupo-proyecto.navegacion',//ok
                'grupo-proyecto.lista',//ok
                'grupo-proyecto.ver',//ok
                'grupo-proyecto.crear',//ok
                'grupo-proyecto.editar',//ok
                'grupo-proyecto.eliminar',//ok
                'grupo-proyecto.exportar-filtro',//ok
                'grupo-proyecto.exportar-todo',//ok
                /* PROYECTOS */
                'proyecto.navegacion',//ok
                'proyecto.lista',//ok
                'proyecto.ver',//ok
                'proyecto.crear',//ok
                'proyecto.editar',//ok
                'proyecto.eliminar',//ok
                'proyecto.exportar-filtro',//ok
                'proyecto.exportar-todo',//ok
                /* SEDES */
                'sede.navegacion',//ok
                'sede.lista',//ok
                'sede.ver',//ok
                'sede.crear',//ok
                'sede.editar',//ok
                'sede.eliminar',//ok
                'sede.exportar-filtro',//ok
                'sede.exportar-todo',//ok
                /* AREAS */
                'area.navegacion',//ok
                'area.lista',//ok
                'area.ver',//ok
                'area.crear',//ok
                'area.editar',//ok
                'area.eliminar',//ok
                'area.exportar-filtro',//ok
                'area.exportar-todo',//ok
                'area.ver-usuarios',//ok
                'area.ver-solicitudes',//ok
                'area.agregar-usuarios',//ok
                'area.marcar-principal-usuario',//ok
                'area.agregar-solicitudes',//ok
                'area.eliminar-usuarios',//ok
                'area.eliminar-solicitudes',//ok
                'area.exportar-usuarios',//ok
                'area.exportar-solicitudes',//ok
            ],
            'Módulo ATC' => [//ok
                'modulo-atc.ver',//ok
                'tipo-solicitud.navegacion',//ok
                'tipo-solicitud.lista',//ok
                'tipo-solicitud.ver',//ok
                'tipo-solicitud.crear',//ok
                'tipo-solicitud.editar',//ok
                'tipo-solicitud.eliminar',//ok
                'tipo-solicitud.exportar-filtro',//ok
                'tipo-solicitud.exportar-todo',//ok
                'sub-tipo-solicitud.navegacion',//ok
                'sub-tipo-solicitud.lista',//ok
                'sub-tipo-solicitud.ver',//ok
                'sub-tipo-solicitud.crear',//ok
                'sub-tipo-solicitud.editar',//ok
                'sub-tipo-solicitud.eliminar',//ok
                'sub-tipo-solicitud.exportar-filtro',//ok
                'sub-tipo-solicitud.exportar-todo',//ok
                'prioridad-ticket.navegacion',//ok
                'prioridad-ticket.lista',//ok
                'prioridad-ticket.ver',//ok
                'prioridad-ticket.crear',//ok
                'prioridad-ticket.editar',//ok
                'prioridad-ticket.eliminar',//ok
                'prioridad-ticket.exportar-filtro',//ok
                'prioridad-ticket.exportar-todo',//ok
                'estado-ticket.navegacion',//ok
                'estado-ticket.lista',//ok
                'estado-ticket.ver',//ok
                'estado-ticket.crear',//ok
                'estado-ticket.editar',//ok
                'estado-ticket.eliminar',//ok
                'estado-ticket.exportar-filtro',//ok
                'estado-ticket.exportar-todo',
                'canal.navegacion',//ok
                'canal.lista',//ok
                'canal.ver',//ok
                'canal.crear',//ok
                'canal.editar',//ok
                'canal.eliminar',//ok
                'canal.exportar-filtro',//ok
                'canal.exportar-todo',//ok
                'ticket.navegacion',//ok
                'ticket.lista',//ok
                'ticket.gestor',//ok
                'ticket.ver',//ok
                'ticket.crear',//ok
                'ticket.editar',//ok
                'ticket.eliminar',//ok
                'ticket.exportar-filtro',//ok
                'ticket.exportar-todo',//ok
                'ticket.derivar',//ok
                'ticket.agregar-archivo',//ok
                'ticket.eliminar-archivo',//ok
                'ticket.ver-archivo',//ok
                'ticket.enviar-correo',//ok
                'ticket.chat',//ok
            ],
            'Módulo Libro Reclamación Legal' => [
                'modulo-libro-reclamacion.ver',
                'ticket-libro-reclamacion.navegacion',
                'ticket-libro-reclamacion.lista',
                'ticket-libro-reclamacion.ver',
                'ticket-libro-reclamacion.crear',
                'ticket-libro-reclamacion.editar',
                'ticket-libro-reclamacion.eliminar',
                'ticket-libro-reclamacion.exportar-filtro',
                'ticket-libro-reclamacion.exportar-todo',
            ],
            'Módulo Cita' => [//ok
                'modulo-cita.ver',//ok
                'estado-cita.navegacion',//ok
                'estado-cita.lista',//ok
                'estado-cita.ver',//ok
                'estado-cita.crear',//ok
                'estado-cita.editar',//ok
                'estado-cita.eliminar',//ok
                'estado-cita.exportar-filtro',//ok
                'estado-cita.exportar-todo',//ok
                'motivo-cita.navegacion',//ok
                'motivo-cita.lista',//ok
                'motivo-cita.ver',//ok
                'motivo-cita.crear',//ok
                'motivo-cita.editar',//ok
                'motivo-cita.eliminar',//ok
                'motivo-cita.exportar-filtro',//ok
                'motivo-cita.exportar-todo',//ok
                'cita.navegacion',//ok
                'cita.lista',//ok
                'cita.gestor',//ok
                'cita.ver',//ok
                'cita.crear',//ok
                'cita.editar',//ok
                'cita.eliminar',//ok
                'cita.exportar-filtro',//ok
                'cita.exportar-todo',//ok
                'cita.enviar-correo',//ok
                'cita.calendario',//ok
            ],
            'Módulo Backoffice' => [
                'modulo-backoffice.ver',//ok
                'estado-solicitud-evidencia-pago.navegacion',//ok
                'estado-solicitud-evidencia-pago.lista',//ok
                'estado-solicitud-evidencia-pago.ver',//ok
                'estado-solicitud-evidencia-pago.crear',//ok
                'estado-solicitud-evidencia-pago.editar',//ok
                'estado-solicitud-evidencia-pago.eliminar',//ok
                'estado-solicitud-evidencia-pago.exportar-filtro',//ok
                'estado-solicitud-evidencia-pago.exportar-todo',//ok
                'solicitud-evidencia-pago.navegacion',//ok
                'solicitud-evidencia-pago.lista',//ok
                'solicitud-evidencia-pago.gestor',//ok
                'solicitud-evidencia-pago.ver',//ok
                'solicitud-evidencia-pago.editar',//ok
                'solicitud-evidencia-pago.exportar-filtro',//ok
                'solicitud-evidencia-pago.exportar-todo',//ok
                'solicitud-evidencia-pago.validar',//ok
                'solicitud-evidencia-pago.enviar-correo',//ok
                'solicitud-evidencia-pago.chat',//ok
                'evidencia-pago-antiguo.navegacion',
                'evidencia-pago-antiguo.lista',
                'evidencia-pago-antiguo.ver',
                'evidencia-pago-antiguo.editar',
                'evidencia-pago-antiguo.exportar-filtro',
                'evidencia-pago-antiguo.exportar-todo',
                'evidencia-pago-antiguo.validar',
            ],
            'Módulo Letras' => [//ok
                'modulo-letras.ver',//ok
                'estado-solicitud-digitalizar-letra.navegacion',//ok
                'estado-solicitud-digitalizar-letra.lista',//ok
                'estado-solicitud-digitalizar-letra.ver',//ok
                'estado-solicitud-digitalizar-letra.crear',//ok
                'estado-solicitud-digitalizar-letra.editar',//ok
                'estado-solicitud-digitalizar-letra.eliminar',//ok
                'estado-solicitud-digitalizar-letra.exportar-filtro',//ok
                'estado-solicitud-digitalizar-letra.exportar-todo',//ok
                'solicitud-digitalizar-letra.navegacion',//ok
                'solicitud-digitalizar-letra.lista',//ok
                'solicitud-digitalizar-letra.gestor',//ok
                'solicitud-digitalizar-letra.ver',//ok
                'solicitud-digitalizar-letra.exportar-filtro',//ok
                'solicitud-digitalizar-letra.exportar-todo',//ok
                'solicitud-digitalizar-letra.ejecutar-cron-letra',//ok
                'solicitud-digitalizar-letra.validar-cron-letra',//ok
                'envio-cavali.navegacion',//ok
                'envio-cavali.lista',//ok
                'envio-cavali.detalle',//ok
                'envio-cavali.exportar-filtro',//ok
                'envio-cavali.exportar-todo',//ok
                'envio-cavali.exportar-envios',//ok
            ],
            'Módulo Reportes' => [//ok
                'modulo-reporte.ver',//ok
                'reporte-usuario.navegacion',//ok
                'reporte-usuario.admin.ver',//ok
                'reporte-usuario.cliente.ver',//ok
                'reporte-usuario.direccion.ver',//ok
                'reporte-backoffice.navegacion',//ok
                'reporte-backoffice.solicitud-evidencia-pago.ver',//ok
                'reporte-backoffice.evidencia-pago.ver',//ok
                'reporte-backoffice.evidencia-pago-antiguo.ver',//ok
                'reporte-atc.navegacion',//ok
                'reporte-atc.ticket.ver',//ok
                'reporte-cita.navegacion',//ok
                'reporte-cita.cita.ver',//ok
                'reporte-letra.navegacion',//ok
                'reporte-letra.letra.ver',//ok
            ],
            'Módulo Marketing' => [
                'modulo-marketing.ver',//ok
                'tutorial.navegacion',//ok
                'tutorial.lista',//ok
                'tutorial.ver',//ok
                'tutorial.crear',//ok
                'tutorial.editar',//ok
                'tutorial.eliminar',//ok
                'tutorial.exportar-filtro',//ok
                'tutorial.exportar-todo',//ok
            ],
            'Módulo Entrega Fest' => [
                'modulo-entrega-fest.ver',
                /* ENTREGA FEST */
                'entrega-fest.navegacion',
                'entrega-fest.lista',
                'entrega-fest.gestor',//ok
                'entrega-fest.ver',
                'entrega-fest.crear',
                'entrega-fest.editar',
                'entrega-fest.eliminar',
                'entrega-fest.exportar-filtro',
                'entrega-fest.exportar-todo',
                'entrega-fest.ver-panel',
                'entrega-fest.ver-staff',
                /* PROSPECTO */
                'prospecto.navegacion',
                'prospecto.lista',
                'prospecto.ver',
                'prospecto.crear',
                'prospecto.editar',
                'prospecto.eliminar',
                'prospecto.exportar-filtro',
                'prospecto.exportar-todo',
                /* INVITADO */
                'invitado.navegacion',
                'invitado.lista',
                'invitado.ver',
                'invitado.exportar-filtro',
                'invitado.exportar-todo',
                /* ASISTENCIA */
                'asistencia.navegacion',
                'asistencia.lista',
                'asistencia.ver',
                'asistencia.marcar',
                'asistencia.exportar-filtro',
                'asistencia.exportar-todo',
                /* ITINERARIO */
                'itinerario.navegacion',
                'itinerario.lista',
                'itinerario.ver',
                'itinerario.crear',
                'itinerario.editar',
                'itinerario.eliminar',
                'itinerario.exportar-filtro',
                'itinerario.exportar-todo',
                'itinerario.crear-tarea',
                'itinerario.editar-tarea',
                'itinerario.eliminar-tarea',
                'itinerario.marcar-tarea',
                /* MOP */
                'mop.navegacion',
                'mop.lista',
                'mop.ver',
                'mop.crear',
                'mop.editar',
                'mop.eliminar',
                'mop.exportar-filtro',
                'mop.exportar-todo',
                'mop.crear-tarea',
                'mop.editar-tarea',
                'mop.eliminar-tarea',
                'mop.marcar-tarea',
                'mop.tareas',
                'mop.tareas.crear',
                'mop.tareas.editar',
                /* MOP PLANTILLA */
                'mop-plantilla.navegacion',
                'mop-plantilla.lista',
                'mop-plantilla.ver',
                'mop-plantilla.crear',
                'mop-plantilla.editar',
                'mop-plantilla.eliminar',
                'mop-plantilla.exportar-filtro',
                'mop-plantilla.exportar-todo',
                /* PROVEEDOR */
                'proveedor.navegacion',
                'proveedor.lista',
                'proveedor.ver',
                'proveedor.crear',
                'proveedor.editar',
                'proveedor.eliminar',
                'proveedor.exportar-filtro',
                'proveedor.exportar-todo',
                'proveedor.crear-requerimiento',
                'proveedor.editar-requerimiento',
                'proveedor.eliminar-requerimiento',
                'proveedor.marcar-requerimiento',
                /* INCIDENCIAS */
                'incidencia.navegacion',
                'incidencia.lista',
                'incidencia.ver',
                'incidencia.crear',
                'incidencia.editar',
                'incidencia.eliminar',
                'incidencia.exportar-filtro',
                'incidencia.exportar-todo',
                /* RECURSOS */
                'recurso.navegacion',
                'recurso.lista',
                'recurso.ver',
                'recurso.crear',
                'recurso.editar',
                'recurso.eliminar',
                'recurso.exportar-filtro',
                'recurso.exportar-todo',
                /* CONTINGENCIA */
                'contingencia.navegacion',
                'contingencia.lista',
                'contingencia.ver',
                'contingencia.crear',
                'contingencia.editar',
                'contingencia.eliminar',
                'contingencia.exportar-filtro',
                'contingencia.exportar-todo',
                /* PROTOCOLO */
                'protocolo.navegacion',
                'protocolo.lista',
                'protocolo.ver',
                'protocolo.crear',
                'protocolo.editar',
                'protocolo.eliminar',
                'protocolo.exportar-filtro',
                'protocolo.exportar-todo',
            ],
        ];

        $created = 0;
        $existing = 0;

        foreach ($modulos as $nombreModulo => $permisos) {
            foreach ($permisos as $nombrePermiso) {
                $permission = Permission::updateOrCreate(
                    ['name' => $nombrePermiso],
                    ['module' => $nombreModulo, 'guard_name' => 'web']
                );

                if ($permission->wasRecentlyCreated) {
                    $created++;
                    $this->command->info("✓ Creado: {$nombrePermiso}");
                } else {
                    $existing++;
                }
            }
        }

        $this->command->newLine();
        $this->command->info("Permisos creados: {$created} | Existentes: {$existing}");

        // ----------------------------------------
        // 2. CREAR ROLES
        // ----------------------------------------
        $this->command->info('Creando roles...');

        $roles = [
            'super-admin' => 'Super Administrador',
            'admin' => 'Administrador',
            'supervisor-atc' => 'Supervisor ATC',
            'asesor-atc' => 'Asesor ATC',
            'supervisor-backoffice' => 'Supervisor Backoffice',
            'asesor-backoffice' => 'Asesor Backoffice',
            'supervisor-cita' => 'Supervisor Cita',
            'asesor-cita' => 'Asesor Cita',
            'supervisor-letras' => 'Supervisor Letras',
            'asesor-letras' => 'Asesor Letras',
            'supervisor-marketing' => 'Supervisor Marketing',
            'asesor-marketing' => 'Asesor Marketing',
            'supervisor-entrega-fest' => 'Staff Operativo',
            'asesor-entrega-fest' => 'Staff de Lectura',
            'supervisor-legal' => 'Supervisor Legal',
            'asesor-legal' => 'Asesor Legal',
            'staff-asistencia' => 'Proveedor Externo',
            'staff-itinerario' => 'Proveedor Externo',
            'staff-mop' => 'Proveedor Externo',
            'staff-proveedores' => 'Proveedor Externo',
            'staff-incidencias' => 'Proveedor Externo',
            'staff-recursos' => 'Proveedor Externo',
            'staff-protocolo' => 'Proveedor Externo',
            'staff-contingencia' => 'Proveedor Externo',
        ];

        foreach ($roles as $rolName => $descripcion) {
            $rol = Role::firstOrCreate(['name' => $rolName]);
            if ($rol->wasRecentlyCreated) {
                $this->command->info("✓ Rol creado: {$descripcion}");
            }
        }

        // ----------------------------------------
        // 3. ASIGNACIÓN DE PERMISOS POR ROL
        // ----------------------------------------
        $this->command->newLine();
        $this->command->info('Asignando permisos a roles...');

        // Super Admin: Tiene absolutamente todo
        $superAdmin = Role::findByName('super-admin');
        $superAdmin->syncPermissions(Permission::all());
        $this->command->info("✓ Super Admin: " . Permission::count() . " permisos");

        // Admin: Tiene todo
        $admin = Role::findByName('admin');
        $admin->syncPermissions(Permission::all());
        $this->command->info("✓ Admin: " . Permission::count() . " permisos");

        // Supervisor ATC: Todos los permisos de Atención al Cliente
        $supervisor_atc = Role::findByName('supervisor-atc');
        $permisosATC = Permission::where('module', 'Módulo ATC')->get();
        $supervisor_atc->syncPermissions($permisosATC);
        $this->command->info("✓ Supervisor ATC: {$permisosATC->count()} permisos");

        // Asesor ATC: Permisos limitados de tickets
        $asesor_atc = Role::findByName('asesor-atc');
        $asesor_atc->syncPermissions([
            'ticket.ver',
            'ticket.crear',
            'ticket.editar',
            'ticket.derivar',
        ]);
        $this->command->info("✓ Asesor ATC: 4 permisos");

        // Supervisor Cita: Todos los permisos de Cita
        $supervisor_cita = Role::findByName('supervisor-cita');
        $permisosCita = Permission::where('module', 'Módulo Cita')->get();
        $supervisor_cita->syncPermissions($permisosCita);
        $this->command->info("✓ Supervisor Cita: {$permisosCita->count()} permisos");

        // Asesor Cita: Permisos limitados de citas
        $asesor_cita = Role::findByName('asesor-cita');
        $asesor_cita->syncPermissions([
            'cita.ver',
            'cita.crear',
            'cita.editar',
        ]);
        $this->command->info("✓ Asesor Cita: 3 permisos");

        // Supervisor Marketing: Todos los permisos de Marketing
        $supervisor_marketing = Role::findByName('supervisor-marketing');
        $permisosMarketing = Permission::where('module', 'Módulo Marketing')->get();
        $supervisor_marketing->syncPermissions($permisosMarketing);
        $this->command->info("✓ Supervisor Marketing: {$permisosMarketing->count()} permisos");

        // Asesor Marketing: Permisos limitados
        $asesor_marketing = Role::findByName('asesor-marketing');
        $asesor_marketing->syncPermissions([
            'modulo-marketing.ver',
            'tutorial.navegacion',
            'tutorial.lista',
            'tutorial.ver',
        ]);
        $this->command->info("✓ Asesor Marketing: 4 permisos");

        // Backoffice
        $supervisor_backoffice = Role::findByName('supervisor-backoffice');
        $permisosBackoffice = Permission::where('module', 'Módulo Backoffice')->get();
        $supervisor_backoffice->syncPermissions($permisosBackoffice);
        // Agregar permisos de prospecto según entrega-fest.php
        $supervisor_backoffice->givePermissionTo(Permission::where('name', 'like', 'prospecto.%')->get());
        $this->command->info("✓ Supervisor Backoffice: Configurado");

        $asesor_backoffice = Role::findByName('asesor-backoffice');
        $asesor_backoffice->syncPermissions([
            'solicitud-evidencia-pago.ver',
            'solicitud-evidencia-pago.editar',
            'solicitud-evidencia-pago.validar',
            'modulo-backoffice.ver',
        ]);
        // Agregar permisos de prospecto según entrega-fest.php
        $asesor_backoffice->givePermissionTo(Permission::where('name', 'like', 'prospecto.%')->get());
        $this->command->info("✓ Asesor Backoffice: Configurado");

        // Legal
        $supervisor_legal = Role::findByName('supervisor-legal');
        $supervisor_legal->syncPermissions(Permission::where('name', 'like', 'prospecto.%')->get());
        $this->command->info("✓ Supervisor Legal: Configurado");

        $asesor_legal = Role::findByName('asesor-legal');
        $asesor_legal->syncPermissions(Permission::where('name', 'like', 'prospecto.%')->get());
        $this->command->info("✓ Asesor Legal: Configurado");

        // Staff Operativo (supervisor-entrega-fest)
        $staff_operativo = Role::findByName('supervisor-entrega-fest');
        $staff_operativo->syncPermissions(Permission::where('module', 'Módulo Entrega Fest')->get());
        $this->command->info("✓ Staff Operativo: Configurado");

        // Staff Lectura (asesor-entrega-fest)
        $staff_lectura = Role::findByName('asesor-entrega-fest');
        $staff_lectura->syncPermissions([
            'modulo-entrega-fest.ver',
            'entrega-fest.navegacion',
            'entrega-fest.lista',
            'entrega-fest.ver',
            'prospecto.navegacion',
            'prospecto.lista',
            'prospecto.ver',
            'invitado.navegacion',
            'invitado.lista',
            'invitado.ver',
            'asistencia.navegacion',
            'asistencia.lista',
            'asistencia.ver',
            'itinerario.navegacion',
            'itinerario.lista',
            'itinerario.ver',
            'mop.navegacion',
            'mop.lista',
            'mop.ver',
            'proveedor.navegacion',
            'proveedor.lista',
            'proveedor.ver',
            'incidencia.navegacion',
            'incidencia.lista',
            'incidencia.ver',
            'recurso.navegacion',
            'recurso.lista',
            'recurso.ver',
        ]);
        $this->command->info("✓ Staff Lectura: Configurado");

        // Roles de Proveedores Externos
        $role_asistencia = Role::findByName('staff-asistencia');
        $role_asistencia->syncPermissions(Permission::where('name', 'like', 'asistencia.%')->get());
        $role_asistencia->givePermissionTo('modulo-entrega-fest.ver');

        $role_itinerario = Role::findByName('staff-itinerario');
        $role_itinerario->syncPermissions(Permission::where('name', 'like', 'itinerario.%')->get());
        $role_itinerario->givePermissionTo('modulo-entrega-fest.ver');

        $role_mop = Role::findByName('staff-mop');
        $role_mop->syncPermissions(Permission::where('name', 'like', 'mop.%')->get());
        $role_mop->givePermissionTo(Permission::where('name', 'like', 'mop-plantilla.%')->get());
        $role_mop->givePermissionTo('modulo-entrega-fest.ver');

        $role_proveedores = Role::findByName('staff-proveedores');
        $role_proveedores->syncPermissions(Permission::where('name', 'like', 'proveedor.%')->get());
        $role_proveedores->givePermissionTo('modulo-entrega-fest.ver');

        $role_incidencias = Role::findByName('staff-incidencias');
        $role_incidencias->syncPermissions(Permission::where('name', 'like', 'incidencia.%')->get());
        $role_incidencias->givePermissionTo('modulo-entrega-fest.ver');

        $role_recursos = Role::findByName('staff-recursos');
        $role_recursos->syncPermissions(Permission::where('name', 'like', 'recurso.%')->get());
        $role_recursos->givePermissionTo('modulo-entrega-fest.ver');

        $role_protocolo = Role::findByName('staff-protocolo');
        $role_protocolo->syncPermissions(Permission::where('name', 'like', 'protocolo.%')->get());
        $role_protocolo->givePermissionTo('modulo-entrega-fest.ver');

        $role_contingencia = Role::findByName('staff-contingencia');
        $role_contingencia->syncPermissions(Permission::where('name', 'like', 'contingencia.%')->get());
        $role_contingencia->givePermissionTo('modulo-entrega-fest.ver');

        $this->command->newLine();
        $this->command->info("========================================");
        $this->command->info("✓ Roles y permisos creados exitosamente");
        $this->command->info("========================================");
    }
}
