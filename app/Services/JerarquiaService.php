<?php

namespace App\Services;

use App\Models\Rol;
use Illuminate\Support\Collection;

class JerarquiaService
{
    /**
     * Obtener el árbol jerárquico de roles.
     * Los roles raíz son aquellos con upper_id nulo o cuyo superior no está en la colección filtrada.
     */
    public function obtenerArbol(?int $areaId = null): Collection
    {
        $query = Rol::query()
            ->with(['area']);           // Quitamos 'level' también

        if ($areaId) {
            $query->where('area_id', $areaId);
        }

        $roles = $query->orderBy('name')->get();

        // Agrupar por upper_id para construir el árbol en memoria
        $grouped = $roles->groupBy('upper_id');
        $roleIds = $roles->pluck('id')->all();

        $roots = $roles->filter(function ($role) use ($roleIds) {
            return is_null($role->upper_id) || !in_array($role->upper_id, $roleIds);
        });

        $this->vincularHijos($roots, $grouped);

        return $roots;
    }
    
    private function vincularHijos($parents, $grouped)
    {
        foreach ($parents as $parent) {
            $children = $grouped->get($parent->id) ?? collect();
            $parent->setRelation('subordinados', $children);
            if ($children->isNotEmpty()) {
                $this->vincularHijos($children, $grouped);
            }
        }
    }

    /**
     * Obtiene la cadena ascendente de superiores directos e indirectos.
     */
    public function obtenerCadenaAscendente(Rol $rol): Collection
    {
        return $rol->cadenaSuperior();
    }

    /**
     * Valida que no se generen ciclos al asignar un superior a un rol.
     * Retorna false si se detecta un ciclo, true si es una asignación válida.
     */
    public function validarSinCiclos(int $rolId, ?int $upperId): bool
    {
        if (is_null($upperId)) {
            return true;
        }

        if ($rolId === $upperId) {
            return false;
        }

        $superior = Rol::find($upperId);
        if (!$superior) {
            return true;
        }

        $current = $superior;
        while ($current) {
            if ($current->id === $rolId) {
                return false;
            }
            $current = $current->superior;
        }

        return true;
    }
}
