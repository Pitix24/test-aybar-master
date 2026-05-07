<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add nullable FK columns
        Schema::table('soportes', function (Blueprint $table) {
            $table->foreignId('tipo_soporte_id')->nullable()->constrained('tipo_soportes');
            $table->foreignId('prioridad_soporte_id')->nullable()->constrained('prioridad_soportes');
            $table->foreignId('estado_soporte_id')->nullable()->constrained('estado_soportes');
        });

        // 2. Backfill data
        $soportes = DB::table('soportes')->get();

        foreach ($soportes as $soporte) {
            // Find or create tipo
            $tipoId = DB::table('tipo_soportes')->where('nombre', $soporte->tipo)->value('id');
            if (!$tipoId && $soporte->tipo) {
                $tipoId = DB::table('tipo_soportes')->insertGetId([
                    'nombre' => $soporte->tipo,
                    'color' => '#6c757d', // default color
                    'icono' => 'fa-solid fa-tag', // default icon
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Find or create prioridad
            $prioridadId = DB::table('prioridad_soportes')->where('nombre', $soporte->prioridad)->value('id');
            if (!$prioridadId && $soporte->prioridad) {
                $prioridadId = DB::table('prioridad_soportes')->insertGetId([
                    'nombre' => $soporte->prioridad,
                    'color' => '#ffc107',
                    'icono' => 'fa-solid fa-exclamation-triangle',
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Find or create estado
            $estadoId = DB::table('estado_soportes')->where('nombre', $soporte->estado)->value('id');
            if (!$estadoId && $soporte->estado) {
                $estadoId = DB::table('estado_soportes')->insertGetId([
                    'nombre' => $soporte->estado,
                    'color' => '#0d6efd',
                    'icono' => 'fa-solid fa-info-circle',
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update soporte
            DB::table('soportes')->where('id', $soporte->id)->update([
                'tipo_soporte_id' => $tipoId,
                'prioridad_soporte_id' => $prioridadId,
                'estado_soporte_id' => $estadoId,
            ]);
        }

        // 3. Drop old columns and make FKs required (optional, but good practice if all rows are migrated)
        // Note: SQLite doesn't support dropping columns easily or altering to non-nullable easily, 
        // but MySQL does. Assuming MySQL based on previous queries.
        Schema::table('soportes', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'prioridad', 'estado']);
        });
        
        // Wait, to make them non nullable we need to change them.
        // It's safer to leave them nullable for now or use another table modification
        // But since this is standard Laravel/MySQL, let's just make them non-nullable if we want
        /*
        Schema::table('soportes', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_soporte_id')->nullable(false)->change();
            $table->unsignedBigInteger('prioridad_soporte_id')->nullable(false)->change();
            $table->unsignedBigInteger('estado_soporte_id')->nullable(false)->change();
        });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soportes', function (Blueprint $table) {
            $table->enum('tipo', ['BUG', 'MEJORA', 'IMPLEMENTACION', 'CONSULTA'])->nullable();
            $table->enum('prioridad', ['BAJA', 'MEDIA', 'ALTA', 'CRITICA'])->nullable();
            $table->enum('estado', ['ABIERTO', 'EN_PROGRESO', 'EN_REVISION', 'RESUELTO', 'CERRADO'])->nullable();
        });

        // We can't really reverse the backfill perfectly, but we can try mapping back.
        $soportes = DB::table('soportes')->get();
        foreach ($soportes as $soporte) {
            $tipo = DB::table('tipo_soportes')->where('id', $soporte->tipo_soporte_id)->value('nombre');
            $prioridad = DB::table('prioridad_soportes')->where('id', $soporte->prioridad_soporte_id)->value('nombre');
            $estado = DB::table('estado_soportes')->where('id', $soporte->estado_soporte_id)->value('nombre');
            
            DB::table('soportes')->where('id', $soporte->id)->update([
                'tipo' => $tipo ?? 'CONSULTA',
                'prioridad' => $prioridad ?? 'MEDIA',
                'estado' => $estado ?? 'ABIERTO',
            ]);
        }

        Schema::table('soportes', function (Blueprint $table) {
            $table->dropForeign(['tipo_soporte_id']);
            $table->dropForeign(['prioridad_soporte_id']);
            $table->dropForeign(['estado_soporte_id']);
            
            $table->dropColumn(['tipo_soporte_id', 'prioridad_soporte_id', 'estado_soporte_id']);
        });
    }
};
