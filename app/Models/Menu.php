<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'parent_id',
        'nombre',
        'ruta',
        'url',
        'icono',
        'nivel',
        'orden',
        'roles',
        'permisos',
        'activo',
    ];

    protected $casts = [
        'roles' => 'array',
        'permisos' => 'array',
        'activo' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('orden');
    }

    public function submenus()
    {
        return $this->children();
    }
}
