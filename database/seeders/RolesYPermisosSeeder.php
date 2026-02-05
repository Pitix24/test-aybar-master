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
        // 1. LISTA DE PERMISOS (recurso.accion)
        // ----------------------------------------
        $permisos = [
            // Tickets
            'tipo-solicitud.ver',
            'tipo-solicitud.crear',
            'tipo-solicitud.editar',
            'tipo-solicitud.eliminar',

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
            'ticket.derivar',
            'ticket.reporte',
            'ticket.validar',

            // Citas
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
            'cita.reporte',
            'cita.validar',

            'calendario.ver',

            // Pagos / Evidencias
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
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
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

        // El 'super-admin' NO necesita permisos asignados explícitamente 
        // si usamos Gate::before en AuthServiceProvider/AppServiceProvider.

        // Admin: Tiene todo excepto quizás acciones destructivas críticas si se desea (aquí le damos todo)
        $admin = Role::findByName('admin');
        $admin->syncPermissions(Permission::all());

        // Atención al Cliente (ATC)
        $supervisor_atc = Role::findByName('supervisor-atc');
        $supervisor_atc->syncPermissions([
            'tipo-solicitud.ver',
            'tipo-solicitud.crear',
            'tipo-solicitud.editar',
            'tipo-solicitud.eliminar',
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
            'ticket.reporte',
            'ticket.validar',
            'motivo-cita.ver',
            'motivo-cita.crear',
            'motivo-cita.editar',
            'motivo-cita.eliminar',
            'estado-cita.ver',
            'estado-cita.crear',
            'estado-cita.editar',
            'estado-cita.eliminar',
            'cita.reporte',
            'cita.validar',
            'evidencia-pago.reporte',
            'evidencia-pago.validar',
        ]);

        $asesor_atc = Role::findByName('asesor-atc');
        $asesor_atc->syncPermissions([
            'ticket.ver',
            'ticket.crear',
            'ticket.editar',
            'ticket.eliminar',
            'ticket.derivar',
            'cita.ver',
            'cita.crear',
            'cita.editar',
            'cita.eliminar',
            'calendario.ver',
        ]);

        // Backoffice
        $supervisor_backoffice = Role::findByName('supervisor-backoffice');
        $supervisor_backoffice->syncPermissions([
            'estado-evidencia-pago.ver',
            'estado-evidencia-pago.crear',
            'estado-evidencia-pago.editar',
            'estado-evidencia-pago.eliminar',
            'evidencia-pago.reporte',
            'evidencia-pago.eliminar',
            'evidencia-pago.validar',
        ]);

        $asesor_backoffice = Role::findByName('asesor-backoffice');
        $asesor_backoffice->syncPermissions([
            'evidencia-pago.ver',
            'evidencia-pago.crear',
            'evidencia-pago.editar',
        ]);
    }
}
