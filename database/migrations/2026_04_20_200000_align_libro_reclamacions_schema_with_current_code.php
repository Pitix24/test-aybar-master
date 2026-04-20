<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('libro_reclamacions')) {
            return;
        }

        Schema::table('libro_reclamacions', function (Blueprint $table): void {
            if (! Schema::hasColumn('libro_reclamacions', 'ticket_id')) {
                $table->unsignedBigInteger('ticket_id')->nullable()->after('gestor_id');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'numero_reclamo')) {
                $table->unsignedBigInteger('numero_reclamo')->nullable()->after('serie');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'codigo_ticket')) {
                $table->string('codigo_ticket', 20)->nullable()->after('numero_reclamo');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'codigo')) {
                $table->string('codigo', 20)->nullable()->after('codigo_ticket');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'clasificacion')) {
                $table->enum('clasificacion', ['PROCEDE', 'NO_PROCEDE', 'PENDIENTE_REVISION'])
                    ->default('PENDIENTE_REVISION')
                    ->after('fecha_respuesta');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'cliente_tipo_documento')) {
                $table->string('cliente_tipo_documento', 50)->nullable()->after('clasificacion');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'cliente_documento')) {
                $table->string('cliente_documento', 20)->nullable()->after('cliente_tipo_documento');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'cliente_nombre')) {
                $table->string('cliente_nombre', 255)->nullable()->after('cliente_documento');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'cliente_email')) {
                $table->string('cliente_email', 255)->nullable()->after('cliente_nombre');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'cliente_celular')) {
                $table->string('cliente_celular', 30)->nullable()->after('cliente_email');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'cliente_direccion')) {
                $table->text('cliente_direccion')->nullable()->after('cliente_celular');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'asunto')) {
                $table->text('asunto')->nullable()->after('cliente_direccion');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'lotes')) {
                $table->json('lotes')->nullable()->after('asunto');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'assigned_at')) {
                $table->dateTime('assigned_at')->nullable()->after('lotes');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'observaciones_internas')) {
                $table->text('observaciones_internas')->nullable()->after('assigned_at');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('estado');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
            if (! Schema::hasColumn('libro_reclamacions', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
            }
        });

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE libro_reclamacions MODIFY tipo_documento ENUM('DNI','RUC','CE','NO_DEFINIDO') NOT NULL DEFAULT 'DNI'");
            DB::statement("ALTER TABLE libro_reclamacions MODIFY tipo_bien_contratado ENUM('PRODUCTO','SERVICIO','NO_DEFINIDO') NOT NULL DEFAULT 'PRODUCTO'");
            DB::statement("ALTER TABLE libro_reclamacions MODIFY tipo_pedido ENUM('RECLAMO','QUEJA','NO_DEFINIDO') NOT NULL DEFAULT 'RECLAMO'");
        }

        if (! $this->hasIndex('libro_reclamacions', 'libro_reclamacions_codigo_ticket_index') && Schema::hasColumn('libro_reclamacions', 'codigo_ticket')) {
            Schema::table('libro_reclamacions', function (Blueprint $table): void {
                $table->index('codigo_ticket');
            });
        }

        if (! $this->hasIndex('libro_reclamacions', 'libro_reclamacions_unidad_numero_unique')
            && Schema::hasColumn('libro_reclamacions', 'unidad_negocio_id')
            && Schema::hasColumn('libro_reclamacions', 'numero_reclamo')) {
            Schema::table('libro_reclamacions', function (Blueprint $table): void {
                $table->unique(['unidad_negocio_id', 'numero_reclamo'], 'libro_reclamacions_unidad_numero_unique');
            });
        }

        if (! $this->hasIndex('libro_reclamacions', 'libro_reclamacions_codigo_unique') && Schema::hasColumn('libro_reclamacions', 'codigo')) {
            Schema::table('libro_reclamacions', function (Blueprint $table): void {
                $table->unique('codigo');
            });
        }
    }

    public function down(): void
    {
        // No-op: schema alignment for restored backups.
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $dbName = DB::getDatabaseName();

            return DB::table('information_schema.statistics')
                ->where('table_schema', $dbName)
                ->where('table_name', $table)
                ->where('index_name', $indexName)
                ->exists();
        }

        return false;
    }
};
