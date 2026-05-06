<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadoTicket extends Model
{
    /** @use HasFactory<\Database\Factories\EstadoTicketFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'color',
        'icono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public const NUEVO = 'NUEVO';
    public const EN_GESTION = 'EN GESTIÓN';
    public const DERIVADO = 'DERIVADO';
    public const EN_ESPERA_CLIENTE = 'EN ESPERA CLIENTE';
    public const ATENDIDO = 'ATENDIDO';
    public const CERRADO = 'CERRADO';

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public static function id(string $nombre): ?int
    {
        return static::where('nombre', $nombre)->value('id');
    }
}
