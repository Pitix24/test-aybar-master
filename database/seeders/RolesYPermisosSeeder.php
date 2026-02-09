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
            'Atención al Cliente' => [
                'tipo-solicitud.ver',
                'tipo-solicitud.crear',
                'tipo-solicitud.editar',
                'tipo-solicitud.eliminar',
                'tipo-solicitud.exportar',
                'sub-tipo-solicitud.ver',
                'sub-tipo-solicitud.crear',
                'sub-tipo-solicitud.editar',
                'sub-tipo-solicitud.eliminar',
                'prioridad-ticket.ver',
                'prioridad-ticket.crear',
                'prioridad-ticket.editar',
                'prioridad-ticket.eliminar',
                'estado-ticket.ver',
                'estado-ticket.crear',
                'estado-ticket.editar',
                'estado-ticket.eliminar',
                'canal.ver',
                'canal.crear',
                'canal.editar',
                'canal.eliminar',
                'ticket.ver',
                'ticket.crear',
                'ticket.editar',
                'ticket.eliminar',
                'ticket.exportar',
                'ticket.derivar',
                'ticket.reportar',
                'ticket.validar',
                'motivo-cita.ver',
                'motivo-cita.crear',
                'motivo-cita.editar',
                'motivo-cita.eliminar',
                'estado-cita.ver',
                'estado-cita.crear',
                'estado-cita.editar',
                'estado-cita.eliminar',
                'cita.ver',
                'cita.crear',
                'cita.editar',
                'cita.eliminar',
                'cita.reportar',
                'cita.validar',
                'calendario.ver',
            ],
            'Backoffice & Pagos' => [
                'estado-evidencia-pago.ver',
                'estado-evidencia-pago.crear',
                'estado-evidencia-pago.editar',
                'estado-evidencia-pago.eliminar',
                'evidencia-pago.ver',
                'evidencia-pago.crear',
                'evidencia-pago.editar',
                'evidencia-pago.eliminar',
                'evidencia-pago.validar',
                'evidencia-pago.reporte',
            ],
            'Configuración & Seguridad' => [
                'rol.ver',
                'rol.crear',
                'rol.editar',
                'rol.eliminar',
                'permiso.ver',
                'permiso.crear',
                'permiso.editar',
                'permiso.eliminar',
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
                'menu.ver',
                'menu.crear',
                'menu.editar',
                'menu.eliminar',
            ],
            'Proyectos & Organización' => [
                'direccion.ver',
                'direccion.crear',
                'direccion.editar',
                'direccion.eliminar',
                'unidad-negocio.ver',
                'unidad-negocio.crear',
                'unidad-negocio.editar',
                'unidad-negocio.eliminar',
                'grupo-proyecto.ver',
                'grupo-proyecto.crear',
                'grupo-proyecto.editar',
                'grupo-proyecto.eliminar',
                'proyecto.ver',
                'proyecto.crear',
                'proyecto.editar',
                'proyecto.eliminar',
                'proyecto.exportar',
                'sede.ver',
                'sede.crear',
                'sede.editar',
                'sede.eliminar',
                'area.ver',
                'area.crear',
                'area.editar',
                'area.eliminar',
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
            'supervisor-legal' => 'Supervisor Legal',
            'asesor-legal' => 'Asesor Legal',
            'supervisor-archivo' => 'Supervisor Archivo',
            'asesor-archivo' => 'Asesor Archivo',
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
        $permisosATC = Permission::where('module', 'Atención al Cliente')->get();
        $supervisor_atc->syncPermissions($permisosATC);
        $this->command->info("✓ Supervisor ATC: {$permisosATC->count()} permisos");

        // Asesor ATC: Permisos limitados de tickets y citas
        $asesor_atc = Role::findByName('asesor-atc');
        $asesor_atc->syncPermissions([
            'ticket.ver',
            'ticket.crear',
            'ticket.editar',
            'ticket.derivar',
            'cita.ver',
            'cita.crear',
            'cita.editar',
            'calendario.ver',
        ]);
        $this->command->info("✓ Asesor ATC: 8 permisos");

        // Supervisor Backoffice: Todos los permisos de Backoffice
        $supervisor_backoffice = Role::findByName('supervisor-backoffice');
        $permisosBackoffice = Permission::where('module', 'Backoffice & Pagos')->get();
        $supervisor_backoffice->syncPermissions($permisosBackoffice);
        $this->command->info("✓ Supervisor Backoffice: {$permisosBackoffice->count()} permisos");

        // Asesor Backoffice: Permisos limitados
        $asesor_backoffice = Role::findByName('asesor-backoffice');
        $asesor_backoffice->syncPermissions([
            'evidencia-pago.ver',
            'evidencia-pago.crear',
            'evidencia-pago.editar',
        ]);
        $this->command->info("✓ Asesor Backoffice: 3 permisos");

        $this->command->newLine();
        $this->command->info("========================================");
        $this->command->info("✓ Roles y permisos creados exitosamente");
        $this->command->info("========================================");
    }
}
