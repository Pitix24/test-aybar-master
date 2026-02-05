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
        // ----------------------------------------
        // 1. ESTRUCTURA DE PERMISOS POR MÓDULO
        // ----------------------------------------
        $modulos = [
            'Atención al Cliente' => [
                'tipo-solicitud-ver',
                'tipo-solicitud-crear',
                'tipo-solicitud-editar',
                'tipo-solicitud-eliminar',
                'prioridad-ticket-ver',
                'prioridad-ticket-crear',
                'prioridad-ticket-editar',
                'prioridad-ticket-eliminar',
                'estado-ticket-ver',
                'estado-ticket-crear',
                'estado-ticket-editar',
                'estado-ticket-eliminar',
                'canal-ver',
                'canal-crear',
                'canal-editar',
                'canal-eliminar',
                'ticket-ver',
                'ticket-crear',
                'ticket-editar',
                'ticket-eliminar',
                'ticket-derivar',
                'ticket-reportar',
                'ticket-validar',
                'motivo-cita-ver',
                'motivo-cita-crear',
                'motivo-cita-editar',
                'motivo-cita-eliminar',
                'estado-cita-ver',
                'estado-cita-crear',
                'estado-cita-editar',
                'estado-cita-eliminar',
                'cita-ver',
                'cita-crear',
                'cita-editar',
                'cita-eliminar',
                'cita-reportar',
                'cita-validar',
                'calendario-ver',
            ],
            'Backoffice & Pagos' => [
                'estado-evidencia-pago-ver',
                'estado-evidencia-pago-crear',
                'estado-evidencia-pago-editar',
                'estado-evidencia-pago-eliminar',
                'evidencia-pago-ver',
                'evidencia-pago-crear',
                'evidencia-pago-editar',
                'evidencia-pago-eliminar',
                'evidencia-pago-validar',
                'evidencia-pago-reporte',
            ],
            'Configuración & Seguridad' => [
                'rol-ver',
                'rol-crear',
                'rol-editar',
                'rol-eliminar',
                'permiso-ver',
                'permiso-crear',
                'permiso-editar',
                'permiso-eliminar',
                'admin-ver',
                'admin-crear',
                'admin-editar',
                'admin-eliminar',
                'cliente-ver',
                'cliente-crear',
                'cliente-editar',
                'cliente-eliminar',
            ],
            'Proyectos' => [
                'unidad-negocio-ver',
                'unidad-negocio-crear',
                'unidad-negocio-editar',
                'unidad-negocio-eliminar',
                'grupo-proyecto-ver',
                'grupo-proyecto-crear',
                'grupo-proyecto-editar',
                'grupo-proyecto-eliminar',
                'proyecto-ver',
                'proyecto-crear',
                'proyecto-editar',
                'proyecto-eliminar',
            ]
        ];

        foreach ($modulos as $nombreModulo => $permisos) {
            foreach ($permisos as $nombrePermiso) {
                Permission::updateOrCreate(
                    ['name' => $nombrePermiso],
                    ['module' => $nombreModulo, 'guard_name' => 'web']
                );
            }
        }

        // ----------------------------------------
        // 2. CREAR ROLES
        // ----------------------------------------
        $roles = [
            'super-admin',
            'admin',
            'supervisor-atc',
            'asesor-atc',
            'supervisor-backoffice',
            'asesor-backoffice',
            'supervisor-legal',
            'asesor-legal',
            'supervisor-archivo',
            'asesor-archivo',
        ];

        foreach ($roles as $rolName) {
            Role::firstOrCreate(['name' => $rolName]);
        }

        // ----------------------------------------
        // 3. ASIGNACIÓN DE PERMISOS POR ROL
        // ----------------------------------------

        // Admin: Tiene todo
        $admin = Role::findByName('admin');
        $admin->syncPermissions(Permission::all());

        // Atención al Cliente (ATC)
        $supervisor_atc = Role::findByName('supervisor-atc');
        $supervisor_atc->syncPermissions(Permission::where('module', 'Atención al Cliente')->get());

        $asesor_atc = Role::findByName('asesor-atc');
        $asesor_atc->syncPermissions([
            'ticket-ver',
            'ticket-crear',
            'ticket-editar',
            'ticket-eliminar',
            'ticket-derivar',
            'cita-ver',
            'cita-crear',
            'cita-editar',
            'cita-eliminar',
            'calendario-ver',
        ]);

        // Backoffice
        $supervisor_backoffice = Role::findByName('supervisor-backoffice');
        $supervisor_backoffice->syncPermissions(Permission::where('module', 'Backoffice & Pagos')->get());

        $asesor_backoffice = Role::findByName('asesor-backoffice');
        $asesor_backoffice->syncPermissions([
            'evidencia-pago-ver',
            'evidencia-pago-crear',
            'evidencia-pago-editar',
        ]);
    }
}
