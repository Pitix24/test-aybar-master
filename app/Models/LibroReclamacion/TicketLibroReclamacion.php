<?php

namespace App\Models\LibroReclamacion;

use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketLibroReclamacion extends Model
{
    use SoftDeletes;

    protected $table = 'ticket_libro_reclamacions';

    protected $fillable = [
        'codigo',
        'libro_reclamacion_ticket',
        'unidad_negocio_id',
        'proyecto_id',
        'cliente_id',
        'cliente_tipo_documento',
        'cliente_documento',
        'cliente_nombre',
        'cliente_email',
        'cliente_celular',
        'cliente_direccion',
        'asunto',
        'lotes',
        'gestor_id',
        'estado_legal',
        'clasificacion',
        'nota_fuente',
        'nota_fuente_titulo',
        'nota_fuente_fecha',
        'observaciones_internas',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'nota_fuente_fecha' => 'datetime',
        'lotes' => 'array',
    ];

    public function libroReclamacion()
    {
        return $this->belongsTo(LibroReclamacion::class, 'libro_reclamacion_ticket', 'ticket');
    }

    public function unidadNegocio()
    {
        return $this->belongsTo(UnidadNegocio::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $ticket): void {
            if (auth()->check()) {
                $ticket->created_by = auth()->id();
            }

            if (empty($ticket->codigo)) {
                $ticket->codigo = self::generarCodigo();
            }
        });

        static::updating(function (self $ticket): void {
            if (auth()->check()) {
                $ticket->updated_by = auth()->id();
            }
        });

        static::deleting(function (self $ticket): void {
            if (auth()->check()) {
                $ticket->deleted_by = auth()->id();
                $ticket->saveQuietly();
            }
        });
    }

    public static function generarCodigo(): string
    {
        $next = (int) static::withTrashed()->max('id') + 1;

        return 'TLR-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
