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
                'rol.ver',
                'rol.crear',
                'rol.editar',
                'rol.eliminar',
                'rol.exportar',
                'permiso.ver',
                'permiso.crear',
                'permiso.editar',
                'permiso.eliminar',
                'permiso.exportar',
                'menu.ver',
                'menu.crear',
                'menu.editar',
                'menu.eliminar',
            ],
            'Módulo Usuario' => [
                'admin.ver',
                'admin.crear',
                'admin.editar',
                'admin.eliminar',
                'admin.exportar',
                'cliente.ver',
                'cliente.crear',
                'cliente.editar',
                'cliente.eliminar',
                'cliente.exportar',
                'direccion.ver',
                'direccion.crear',
                'direccion.editar',
                'direccion.eliminar',
                'direccion.exportar',
            ],
            'Módulo Negocio' => [
                'unidad-negocio.ver',
                'unidad-negocio.crear',
                'unidad-negocio.editar',
                'unidad-negocio.eliminar',
                'unidad-negocio.exportar',
                'sede.ver',
                'sede.crear',
                'sede.editar',
                'sede.eliminar',
                'sede.exportar',
                'area.ver',
                'area.crear',
                'area.editar',
                'area.eliminar',
                'area.exportar',
                'grupo-proyecto.ver',
                'grupo-proyecto.crear',
                'grupo-proyecto.editar',
                'grupo-proyecto.eliminar',
                'grupo-proyecto.exportar',
                'proyecto.ver',
                'proyecto.crear',
                'proyecto.editar',
                'proyecto.eliminar',
                'proyecto.exportar',
            ],
            'Módulo ATC' => [
                'tipo-solicitud.ver',
                'tipo-solicitud.crear',
                'tipo-solicitud.editar',
                'tipo-solicitud.eliminar',
                'tipo-solicitud.exportar',
                'sub-tipo-solicitud.ver',
                'sub-tipo-solicitud.crear',
                'sub-tipo-solicitud.editar',
                'sub-tipo-solicitud.eliminar',
                'sub-tipo-solicitud.exportar',
                'prioridad-ticket.ver',
                'prioridad-ticket.crear',
                'prioridad-ticket.editar',
                'prioridad-ticket.eliminar',
                'prioridad-ticket.exportar',
                'estado-ticket.ver',
                'estado-ticket.crear',
                'estado-ticket.editar',
                'estado-ticket.eliminar',
                'estado-ticket.exportar',
                'canal.ver',
                'canal.crear',
                'canal.editar',
                'canal.eliminar',
                'canal.exportar',
                'ticket.ver',
                'ticket.crear',
                'ticket.editar',
                'ticket.eliminar',
                'ticket.exportar',
                'ticket.derivar',
                'ticket.reportar',
                'ticket.validar',
            ],
            'Módulo Cita' => [
                'estado-cita.ver',
                'estado-cita.crear',
                'estado-cita.editar',
                'estado-cita.eliminar',
                'estado-cita.exportar',
                'motivo-cita.ver',
                'motivo-cita.crear',
                'motivo-cita.editar',
                'motivo-cita.eliminar',
                'motivo-cita.exportar',
                'cita.ver',
                'cita.crear',
                'cita.editar',
                'cita.eliminar',
                'cita.exportar',
            ],
            'Módulo Backoffice' => [
                'backoffice.ver',
                'estado-solicitud-evidencia-pago.ver',
                'estado-solicitud-evidencia-pago.crear',
                'estado-solicitud-evidencia-pago.editar',
                'estado-solicitud-evidencia-pago.eliminar',
                'estado-solicitud-evidencia-pago.exportar',
                'solicitud-evidencia-pago.ver',
                'solicitud-evidencia-pago.crear',
                'solicitud-evidencia-pago.editar',
                'solicitud-evidencia-pago.eliminar',
                'solicitud-evidencia-pago.exportar',
                'evidencia-pago-antiguo.ver',
                'evidencia-pago-antiguo.editar',
                'evidencia-pago-antiguo.eliminar',
                'evidencia-pago-antiguo.exportar',
            ],
            'Módulo Letras' => [
                'solicitud-letra.ver',
                'solicitud-letra.crear',
                'solicitud-letra.editar',
                'solicitud-letra.eliminar',
                'solicitud-letra.exportar',
                'envio-letra.ver',
                'envio-letra.crear',
                'envio-letra.editar',
                'envio-letra.eliminar',
                'envio-letra.exportar',
                'estado-solicitud-digitalizar-letra.ver',
                'estado-solicitud-digitalizar-letra.crear',
                'estado-solicitud-digitalizar-letra.editar',
                'estado-solicitud-digitalizar-letra.eliminar',
                'estado-solicitud-digitalizar-letra.exportar',
                'envio-cavali-solicitud.ver',
            ],
            'Módulo Reportes' => [
                'reporte.ver',
                'reporte-cliente.ver',
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
            'supervisor-cita' => 'Supervisor Cita',
            'asesor-cita' => 'Asesor Cita',
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

        $this->command->newLine();
        $this->command->info("========================================");
        $this->command->info("✓ Roles y permisos creados exitosamente");
        $this->command->info("========================================");
    }
}
