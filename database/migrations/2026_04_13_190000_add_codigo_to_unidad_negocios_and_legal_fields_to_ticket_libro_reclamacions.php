<?php

use App\Models\UnidadNegocio;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('unidad_negocios', function (Blueprint $table): void {
            $table->string('codigo', 3)->nullable()->unique()->after('nombre');
        });

        Schema::table('ticket_libro_reclamacions', function (Blueprint $table): void {
            $table->string('cliente_tipo_documento', 10)->nullable()->after('cliente_id');
            $table->string('cliente_documento', 20)->nullable()->index()->after('cliente_tipo_documento');
            $table->string('cliente_nombre')->nullable()->after('cliente_documento');
            $table->string('cliente_email')->nullable()->after('cliente_nombre');
            $table->string('cliente_celular', 30)->nullable()->after('cliente_email');
            $table->text('cliente_direccion')->nullable()->after('cliente_celular');
            $table->string('asunto')->nullable()->after('cliente_direccion');
            $table->json('lotes')->nullable()->after('asunto');
            $table->string('nota_fuente_titulo')->nullable()->after('nota_fuente');
            $table->dateTime('nota_fuente_fecha')->nullable()->after('nota_fuente_titulo');
        });

        DB::transaction(function (): void {
            $unidades = UnidadNegocio::query()
                ->whereNull('codigo')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($unidades as $unidad) {
                $unidad->forceFill([
                    'codigo' => UnidadNegocio::generarCodigoSecuencial((int) $unidad->id),
                ])->saveQuietly();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_libro_reclamacions', function (Blueprint $table): void {
            $table->dropColumn([
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
            ]);
        });

        Schema::table('unidad_negocios', function (Blueprint $table): void {
            $table->dropUnique(['codigo']);
            $table->dropColumn('codigo');
        });
    }
};