<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('libro_reclamacions', function (Blueprint $table) {
            // Agregar solo columnas que no existan - Consolidación de campos
            
            if (!Schema::hasColumn('libro_reclamacions', 'estado_libro_reclamaciones_id')) {
                $table->unsignedBigInteger('estado_libro_reclamaciones_id')->nullable();
            }
            
            if (!Schema::hasColumn('libro_reclamacions', 'clasificacion')) {
                $table->enum('clasificacion', ['PROCEDE', 'NO_PROCEDE', 'PENDIENTE_REVISION'])->default('PENDIENTE_REVISION');
            }

            if (!Schema::hasColumn('libro_reclamacions', 'cliente_tipo_documento')) {
                $table->string('cliente_tipo_documento', 50)->nullable();
            }
            if (!Schema::hasColumn('libro_reclamacions', 'cliente_documento')) {
                $table->string('cliente_documento', 20)->nullable();
            }
            if (!Schema::hasColumn('libro_reclamacions', 'cliente_nombre')) {
                $table->string('cliente_nombre', 255)->nullable();
            }
            if (!Schema::hasColumn('libro_reclamacions', 'cliente_email')) {
                $table->string('cliente_email', 255)->nullable();
            }
            if (!Schema::hasColumn('libro_reclamacions', 'cliente_celular')) {
                $table->string('cliente_celular', 20)->nullable();
            }
            if (!Schema::hasColumn('libro_reclamacions', 'cliente_direccion')) {
                $table->text('cliente_direccion')->nullable();
            }

            if (!Schema::hasColumn('libro_reclamacions', 'asunto')) {
                $table->text('asunto')->nullable();
            }
            if (!Schema::hasColumn('libro_reclamacions', 'lotes')) {
                $table->json('lotes')->nullable();
            }

            if (!Schema::hasColumn('libro_reclamacions', 'nota_fuente_titulo')) {
                $table->string('nota_fuente_titulo', 255)->nullable();
            }
            if (!Schema::hasColumn('libro_reclamacions', 'nota_fuente_fecha')) {
                $table->datetime('nota_fuente_fecha')->nullable();
            }

            if (!Schema::hasColumn('libro_reclamacions', 'assigned_at')) {
                $table->datetime('assigned_at')->nullable();
            }
            if (!Schema::hasColumn('libro_reclamacions', 'observaciones_internas')) {
                $table->text('observaciones_internas')->nullable();
            }

            if (!Schema::hasColumn('libro_reclamacions', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
            if (!Schema::hasColumn('libro_reclamacions', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
            }
            if (!Schema::hasColumn('libro_reclamacions', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('libro_reclamacions', function (Blueprint $table) {
            $cols = [
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
            
            foreach ($cols as $col) {
                if (Schema::hasColumn('libro_reclamacions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
