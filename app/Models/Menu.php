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
        'permiso',
        'activo',
    ];

    protected $casts = [
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

    protected static function booted()
    {
        static::saving(function ($menu) {
            if ($menu->ruta && $menu->url) {
                throw new \LogicException('Un menú no puede tener ruta y url.');
            }
        });
    }
}
