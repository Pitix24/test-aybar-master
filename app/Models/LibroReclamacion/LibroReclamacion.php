<?php

namespace App\Models\LibroReclamacion;

use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\User;
use App\Services\LibroReclamacion\LibroReclamacionNumeroService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibroReclamacion extends Model
{
    use SoftDeletes;

    protected $table = 'libro_reclamacions';
    protected $primaryKey = 'ticket';

    protected $fillable = [
        'unidad_negocio_id',
        'proyecto_id',
        'manzana',
        'lote',
        'cliente_id',
        'gestor_id',
        'serie',
        'numero_reclamo',
        'codigo_ticket',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'domicilio',
        'telefono',
        'email',
        'tipo_documento',
        'numero_documento',
        'tipo_bien_contratado',
        'monto_reclamado',
        'descripcion',
        'tipo_pedido',
        'detalle',
        'pedido',
        'conformidad',
        'observaciones',
        'fecha_respuesta',
        'archivo_1',
        'archivo_2',
        'archivo_3',
        'archivo_4',
        'leido',
        'estado',
        'codigo',
        'estado_libro_reclamaciones_id',
        'clasificacion',
        'cliente_tipo_documento',
        'cliente_documento',
        'cliente_nombre',
        'cliente_email',
        'cliente_celular',
        'cliente_direccion',
        'asunto',
        'lotes',
        'nota_fuente_titulo',
        'nota_fuente_fecha',
        'assigned_at',
        'observaciones_internas',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'conformidad' => 'boolean',
        'leido' => 'boolean',
        'fecha_respuesta' => 'datetime',
        'monto_reclamado' => 'decimal:2',
        'lotes' => 'array',
        'assigned_at' => 'datetime',
        'nota_fuente_fecha' => 'datetime',
    ];

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

    public function estadoLibroReclamacion()
    {
        return $this->belongsTo(EstadoLibroReclamacion::class, 'estado_libro_reclamaciones_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function eliminador()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function esOrigenErp(): bool
    {
        return ! is_null($this->created_by);
    }

    public function tituloNotaFuenteResuelto(): string
    {
        if ($this->esOrigenErp()) {
            return 'ERP - Registro Interno';
        }

        return trim((string) $this->nota_fuente_titulo) ?: 'Formulario web';
    }

    public function contenidoNotaFuenteResuelto(): string
    {
        return $this->esOrigenErp() ? '' : $this->tituloNotaFuenteResuelto();
    }

    protected static function booted()
    {
        static::creating(function ($reclamacion) {
            // Keep backward compatibility with legacy non-null columns in libro_reclamacions.
            if (! $reclamacion->nombre) {
                $reclamacion->nombre = (string) ($reclamacion->cliente_nombre ?: 'NO DEFINIDO');
            }
            if (! $reclamacion->apellido_paterno) {
                $reclamacion->apellido_paterno = '-';
            }
            if (! $reclamacion->apellido_materno) {
                $reclamacion->apellido_materno = '-';
            }
            if (! $reclamacion->domicilio) {
                $reclamacion->domicilio = (string) ($reclamacion->cliente_direccion ?: 'NO DEFINIDO');
            }
            if (! $reclamacion->tipo_documento) {
                $reclamacion->tipo_documento = self::normalizarTipoDocumento($reclamacion->cliente_tipo_documento);
            }
            if (! $reclamacion->numero_documento) {
                $reclamacion->numero_documento = (string) ($reclamacion->cliente_documento ?: 'NO DEFINIDO');
            }

            if (!$reclamacion->estado_libro_reclamaciones_id) {
                $estadoNuevo = EstadoLibroReclamacion::where('nombre', 'NUEVO')->first();
                if ($estadoNuevo) {
                    $reclamacion->estado_libro_reclamaciones_id = $estadoNuevo->id;
                }
            }
            if (!$reclamacion->codigo && $reclamacion->unidad_negocio_id) {
                $ticket = self::generarTicket($reclamacion->unidad_negocio_id);
                $reclamacion->codigo = (string) ($ticket['codigo_ticket'] ?? '');
            }
            if (! $reclamacion->codigo_ticket) {
                $reclamacion->codigo_ticket = $reclamacion->codigo ?: null;
            }
            if (!$reclamacion->created_by) {
                $reclamacion->created_by = auth()?->user()?->id;
            }
        });
        static::updating(function ($reclamacion) {
            $reclamacion->updated_by = auth()?->user()?->id;
        });
    }

    public static function generarTicket(int $unidadNegocioId): array
    {
        return app(LibroReclamacionNumeroService::class)->generar($unidadNegocioId);
    }

    protected static function normalizarTipoDocumento(?string $tipo): string
    {
        $tipo = strtoupper(trim((string) $tipo));

        return in_array($tipo, ['DNI', 'RUC', 'CE', 'NO_DEFINIDO'], true)
            ? $tipo
            : 'NO_DEFINIDO';
    }
}
