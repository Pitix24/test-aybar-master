<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Support\Collection;

class Rol extends SpatieRole
{
    protected $table = 'roles';

        protected $fillable = [
            'name',
            'guard_name',
            'area_id',
            'upper_id',
        ];

    public function superior()
    {
        return $this->belongsTo(self::class, 'upper_id');
    }

    public function subordinados()
    {
        return $this->hasMany(self::class, 'upper_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }



    /**
     * Get the chain of superior roles ascending to the root (excluding this role).
     */
    public function cadenaSuperior(): Collection
    {
        $cadena = collect();
        $rol = $this->superior;

        while ($rol) {
            $cadena->push($rol);
            $rol = $rol->superior;
        }

        return $cadena;
    }

    /**
     * Check if this role is an ancestor of the given role.
     */
    public function esAntepasadoDe(Rol $rol): bool
    {
        $superior = $rol->superior;
        while ($superior) {
            if ($superior->id === $this->id) {
                return true;
            }
            $superior = $superior->superior;
        }
        return false;
    }

    /**
     * Check if this role is a descendant of the given role.
     */
    public function esDescendienteDe(Rol $rol): bool
    {
        return $rol->esAntepasadoDe($this);
    }
}
