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
            'Módulo Sistema' => [
                'modulo-sistema.ver',
                'rol.navegacion',
                'rol.lista',
                'rol.ver',
                'rol.crear',
                'rol.editar',
                'rol.eliminar',
                'rol.exportar-filtro',
                'rol.exportar-todo',
                'permiso.navegacion',
                'permiso.lista',
                'permiso.ver',
                'permiso.crear',
                'permiso.editar',
                'permiso.eliminar',
                'permiso.exportar-filtro',
                'permiso.exportar-todo',
                'menu.navegacion',
                'menu.lista',
                'menu.ver',
                'menu.crear',
                'menu.editar',
                'menu.eliminar',
                'menu.exportar-filtro',
                'menu.exportar-todo',
            ],
            'Módulo Usuario' => [
                'modulo-usuarios.ver',
                'admin.navegacion',
                'admin.lista',
                'admin.ver',
                'admin.crear',
                'admin.editar',
                'admin.eliminar',
                'admin.exportar-filtro',
                'admin.exportar-todo',
                'admin.cambiar-clave',
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
            ],
            'Módulo Negocio' => [
                'modulo-negocio.ver',
                'unidad-negocio.navegacion',
                'unidad-negocio.lista',
                'unidad-negocio.ver',
                'unidad-negocio.crear',
                'unidad-negocio.editar',
                'unidad-negocio.eliminar',
                'unidad-negocio.exportar-filtro',
                'unidad-negocio.exportar-todo',
                'grupo-proyecto.navegacion',
                'grupo-proyecto.lista',
                'grupo-proyecto.ver',
                'grupo-proyecto.crear',
                'grupo-proyecto.editar',
                'grupo-proyecto.eliminar',
                'grupo-proyecto.exportar-filtro',
                'grupo-proyecto.exportar-todo',
                'proyecto.navegacion',
                'proyecto.lista',
                'proyecto.ver',
                'proyecto.crear',
                'proyecto.editar',
                'proyecto.eliminar',
                'proyecto.exportar-filtro',
                'proyecto.exportar-todo',
                'sede.navegacion',
                'sede.lista',
                'sede.ver',
                'sede.crear',
                'sede.editar',
                'sede.eliminar',
                'sede.exportar-filtro',
                'sede.exportar-todo',
                'area.navegacion',
                'area.lista',
                'area.ver',
                'area.crear',
                'area.editar',
                'area.eliminar',
                'area.exportar-filtro',
                'area.exportar-todo',
                'area.ver-usuarios',
                'area.ver-solicitudes',
                'area.agregar-usuarios',
                'area.agregar-solicitudes',
                'area.eliminar-usuarios',
                'area.eliminar-solicitudes',
                'area.exportar-usuarios',
                'area.exportar-solicitudes',
            ],
            'Módulo ATC' => [
                'modulo-atc.ver',
                'tipo-solicitud.navegacion',
                'tipo-solicitud.lista',
                'tipo-solicitud.ver',
                'tipo-solicitud.crear',
                'tipo-solicitud.editar',
                'tipo-solicitud.eliminar',
                'tipo-solicitud.exportar-filtro',
                'tipo-solicitud.exportar-todo',
                'sub-tipo-solicitud.navegacion',
                'sub-tipo-solicitud.lista',
                'sub-tipo-solicitud.ver',
                'sub-tipo-solicitud.crear',
                'sub-tipo-solicitud.editar',
                'sub-tipo-solicitud.eliminar',
                'sub-tipo-solicitud.exportar-filtro',
                'sub-tipo-solicitud.exportar-todo',
                'prioridad-ticket.navegacion',
                'prioridad-ticket.lista',
                'prioridad-ticket.ver',
                'prioridad-ticket.crear',
                'prioridad-ticket.editar',
                'prioridad-ticket.eliminar',
                'prioridad-ticket.exportar-filtro',
                'prioridad-ticket.exportar-todo',
                'estado-ticket.navegacion',
                'estado-ticket.lista',
                'estado-ticket.ver',
                'estado-ticket.crear',
                'estado-ticket.editar',
                'estado-ticket.eliminar',
                'estado-ticket.exportar-filtro',
                'estado-ticket.exportar-todo',
                'canal.navegacion',
                'canal.lista',
                'canal.ver',
                'canal.crear',
                'canal.editar',
                'canal.eliminar',
                'canal.exportar-filtro',
                'canal.exportar-todo',
                'ticket.navegacion',
                'ticket.lista',
                'ticket.ver',
                'ticket.crear',
                'ticket.editar',
                'ticket.eliminar',
                'ticket.exportar-filtro',
                'ticket.exportar-todo',
                'ticket.derivar',
                'ticket.agregar-archivo',
                'ticket.eliminar-archivo',
                'ticket.ver-archivo',
                'ticket.enviar-correo',
                'ticket.chat',
            ],
            'Módulo Cita' => [
                'modulo-cita.ver',
                'estado-cita.navegacion',
                'estado-cita.lista',
                'estado-cita.ver',
                'estado-cita.crear',
                'estado-cita.editar',
                'estado-cita.eliminar',
                'estado-cita.exportar-filtro',
                'estado-cita.exportar-todo',
                'motivo-cita.navegacion',
                'motivo-cita.lista',
                'motivo-cita.ver',
                'motivo-cita.crear',
                'motivo-cita.editar',
                'motivo-cita.eliminar',
                'motivo-cita.exportar-filtro',
                'motivo-cita.exportar-todo',
                'cita.navegacion',
                'cita.lista',
                'cita.ver',
                'cita.crear',
                'cita.editar',
                'cita.eliminar',
                'cita.exportar-filtro',
                'cita.exportar-todo',
                'cita.enviar-correo',
            ],
            'Módulo Backoffice' => [
                'modulo-backoffice.ver',
                'estado-solicitud-evidencia-pago.navegacion',
                'estado-solicitud-evidencia-pago.lista',
                'estado-solicitud-evidencia-pago.ver',
                'estado-solicitud-evidencia-pago.crear',
                'estado-solicitud-evidencia-pago.editar',
                'estado-solicitud-evidencia-pago.eliminar',
                'estado-solicitud-evidencia-pago.exportar-filtro',
                'estado-solicitud-evidencia-pago.exportar-todo',
                'solicitud-evidencia-pago.navegacion',
                'solicitud-evidencia-pago.lista',
                'solicitud-evidencia-pago.ver',
                'solicitud-evidencia-pago.editar',
                'solicitud-evidencia-pago.exportar-filtro',
                'solicitud-evidencia-pago.exportar-todo',
                'solicitud-evidencia-pago.validar',
                'solicitud-evidencia-pago.enviar-correo',
                'solicitud-evidencia-pago.chat',
                'evidencia-pago-antiguo.navegacion',
                'evidencia-pago-antiguo.lista',
                'evidencia-pago-antiguo.ver',
                'evidencia-pago-antiguo.editar',
                'evidencia-pago-antiguo.exportar-filtro',
                'evidencia-pago-antiguo.exportar-todo',
                'evidencia-pago-antiguo.validar',
            ],
            'Módulo Letras' => [
                'modulo-letras.ver',
                'estado-solicitud-digitalizar-letra.navegacion',
                'estado-solicitud-digitalizar-letra.lista',
                'estado-solicitud-digitalizar-letra.ver',
                'estado-solicitud-digitalizar-letra.crear',
                'estado-solicitud-digitalizar-letra.editar',
                'estado-solicitud-digitalizar-letra.eliminar',
                'estado-solicitud-digitalizar-letra.exportar-filtro',
                'estado-solicitud-digitalizar-letra.exportar-todo',
                'solicitud-digitalizar-letra.navegacion',
                'solicitud-digitalizar-letra.lista',
                'solicitud-digitalizar-letra.ver',
                'solicitud-digitalizar-letra.exportar-filtro',
                'solicitud-digitalizar-letra.exportar-todo',
                'envio-cavali.navegacion',
                'envio-cavali.lista',
                'envio-cavali.detalle',
                'envio-cavali.exportar-filtro',
                'envio-cavali.exportar-todo',
            ],
            'Módulo Reportes' => [
                'modulo-reporte.ver',
                'reporte-usuario.navegacion',
                'reporte-usuario.cliente.ver',
                'reporte-backoffice.navegacion',
                'reporte-backoffice.solicitud-evidencia-pago.ver',
            ],
            'Módulo Marketing' => [
                'modulo-marketing.ver',
                'tutorial.navegacion',
                'tutorial.lista',
                'tutorial.ver',
                'tutorial.crear',
                'tutorial.editar',
                'tutorial.eliminar',
                'tutorial.exportar-filtro',
                'tutorial.exportar-todo',
            ],
            'Módulo Entrega Fest' => [
                'modulo-entrega-fest.ver',
                'entrega-fest.navegacion',
                'entrega-fest.lista',
                'entrega-fest.ver',
                'entrega-fest.crear',
                'entrega-fest.editar',
                'entrega-fest.eliminar',
                'entrega-fest.exportar-filtro',
                'entrega-fest.exportar-todo',
                'prospecto-entrega-fest.navegacion',
                'prospecto-entrega-fest.lista',
                'prospecto-entrega-fest.ver',
                'prospecto-entrega-fest.crear',
                'prospecto-entrega-fest.editar',
                'prospecto-entrega-fest.eliminar',
                'prospecto-entrega-fest.exportar-filtro',
                'prospecto-entrega-fest.exportar-todo',
                'invitado-entrega-fest.navegacion',
                'invitado-entrega-fest.lista',
                'invitado-entrega-fest.ver',
                'invitado-entrega-fest.crear',
                'invitado-entrega-fest.editar',
                'invitado-entrega-fest.eliminar',
                'invitado-entrega-fest.exportar-filtro',
                'invitado-entrega-fest.exportar-todo',
                'asistencia-entrega-fest.navegacion',
                'asistencia-entrega-fest.lista',
                'asistencia-entrega-fest.ver',
                'asistencia-entrega-fest.crear',
                'asistencia-entrega-fest.editar',
                'asistencia-entrega-fest.eliminar',
                'asistencia-entrega-fest.exportar-filtro',
                'asistencia-entrega-fest.exportar-todo',
            ]
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

        $this->command->newLine();
        $this->command->info("========================================");
        $this->command->info("✓ Roles y permisos creados exitosamente");
        $this->command->info("========================================");
    }
}
