<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntregaFestEstadoCliente extends Model
{
    /** @use HasFactory<\Database\Factories\EntregaFestEstadoClienteFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'entrega_fest_estado_clientes';

    protected $fillable = [
        'nombre',
        'color',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function prospectos()
    {
        return $this->hasMany(ProspectoEntregaFest::class, 'estado_cliente_id');
    }

    public static function id(string $nombre): ?int
    {
        return static::where('nombre', $nombre)->value('id');
    }
}
