<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Erp\Soporte\Soporte;

class SoportePolicy
{
    /**
     * Validar que el usuario pueda ver la lista de soportes.
     * No aplica bypass de super-admin: debe tener el permiso explícito.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('soporte.vista-lista');
    }

    /**
     * Validar que el usuario pueda ver un soporte específico.
     * No aplica bypass de super-admin: debe tener el permiso explícito.
     */
    public function view(User $user, Soporte $soporte): bool
    {
        return $user->hasPermissionTo('soporte.vista-ver');
    }

    /**
     * Validar que el usuario pueda crear un soporte.
     * No aplica bypass de super-admin: debe tener el permiso explícito.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('soporte.vista-crear');
    }

    /**
     * Validar que el usuario pueda editar un soporte.
     * No aplica bypass de super-admin: debe tener el permiso explícito.
     */
    public function update(User $user, Soporte $soporte): bool
    {
        return $user->hasPermissionTo('soporte.vista-editar');
    }

    /**
     * Validar que el usuario pueda eliminar un soporte.
     * No aplica bypass de super-admin: debe tener el permiso explícito.
     */
    public function delete(User $user, Soporte $soporte): bool
    {
        return $user->hasPermissionTo('soporte.accion-eliminar-soporte');
    }

    /**
     * Validar que el usuario pueda agregar archivos a un soporte.
     */
    public function attachFile(User $user, Soporte $soporte): bool
    {
        return $user->hasPermissionTo('soporte.accion-agregar-archivo');
    }

    /**
     * Validar que el usuario pueda ver archivos de un soporte.
     */
    public function viewFiles(User $user, Soporte $soporte): bool
    {
        return $user->hasPermissionTo('soporte.accion-ver-archivo');
    }

    /**
     * Validar que el usuario pueda eliminar archivos de un soporte.
     */
    public function deleteFile(User $user, Soporte $soporte): bool
    {
        return $user->hasPermissionTo('soporte.accion-eliminar-archivo');
    }

    /**
     * Validar que el usuario sea supervisor para gestionar catálogos.
     */
    public function manageCatalogues(User $user): bool
    {
        return $user->hasPermissionTo('soporte.supervisor');
    }
}
