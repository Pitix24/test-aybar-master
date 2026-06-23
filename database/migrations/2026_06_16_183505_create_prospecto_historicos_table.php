<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospecto_historicos', function (Blueprint $table) {
            $table->id();

            // Llaves foráneas principales
            $table->foreignId('proyecto_id')->constrained('proyectos');
            $table->foreignId('user_id')->nullable()->constrained('users');

            // Datos del Titular
            $table->string('dni', 15);
            $table->string('nombres');
            $table->string('email')->nullable();
            $table->string('celular')->nullable();

            // Ubicación en el proyecto (utf8mb4_bin para exactitud)
            $table->string('lote')->nullable()->collation('utf8mb4_bin');
            $table->string('manzana')->nullable()->collation('utf8mb4_bin');

            // Reubicación (Consolidado)
            $table->foreignId('reubicado_proyecto_id')->nullable()->constrained('proyectos');
            $table->string('reubicado_lote')->nullable();
            $table->string('reubicado_manzana')->nullable();

            // Estados y Asignaciones Generales
            $table->foreignId('estado_cliente_id')->nullable()->constrained('entrega_fest_estado_clientes');
            $table->enum('grupo', ['A', 'B', 'C', 'D'])->default('A');

            // ============ Llamadas y Backoffice (Consolidado) ============
            $table->foreignId('responsable_llamada_id')->nullable()->constrained('users');
            $table->dateTime('responsable_llamada_fecha_asignacion')->nullable();

            $table->foreignId('gestor_backoffice_id')->nullable()->constrained('users');
            $table->dateTime('gestor_fecha_asignacion')->nullable();
            $table->string('estado_gestor_backoffice')->nullable();
            $table->text('observacion_gestor_backoffice')->nullable();

            // Fechas y Links Backoffice
            $table->dateTime('fecha_culminacion_eecc')->nullable();
            $table->string('link_carpeta_eecc')->nullable();
            $table->string('link_eecc_firmado')->nullable();
            $table->foreignId('validador_backoffice_id')->nullable()->constrained('users');
            $table->dateTime('fecha_validacion_eecc')->nullable();

            // ============ Legal (Consolidado) ============
            $table->foreignId('gestor_legal_id')->nullable()->constrained('users');
            $table->dateTime('legal_fecha_asignacion')->nullable();
            $table->text('observacion_gestor_legal')->nullable();

            $table->foreignId('validador_legal_id')->nullable()->constrained('users');
            $table->dateTime('fecha_firma_presencial')->nullable();
            $table->dateTime('fecha_validacion_firma')->nullable();

            // Estados del proceso general
            $table->enum('estado_backoffice', ['PENDIENTE', 'BANCARIZAR', 'PENALIDAD', 'OBSERVADO', 'CONFORME', 'VIGENTE'])->default('PENDIENTE');
            $table->enum('estado_contrato_preeliminar_emitido', ['PENDIENTE', 'GENERADO', 'OBSERVADO', 'CONFORME'])->default('PENDIENTE');
            $table->enum('estado_firma_contrato_firmado', ['PENDIENTE', 'FIRMADO'])->default('PENDIENTE');

            // Fechas del Contrato
            $table->dateTime('fecha_firma')->nullable();
            $table->dateTime('fecha_generacion_contrato')->nullable();

            // FLAG CLAVE DE NEGOCIO
            $table->boolean('lote_entregado')->default(false);

            // Copropietario 2
            $table->string('dni_2', 15)->nullable();
            $table->string('nombres_2')->nullable();
            $table->string('email_2')->nullable();
            $table->string('celular_2')->nullable();

            // Copropietario 3
            $table->string('dni_3', 15)->nullable();
            $table->string('nombres_3')->nullable();
            $table->string('email_3')->nullable();
            $table->string('celular_3')->nullable();

            // Copropietario 4
            $table->string('dni_4', 15)->nullable();
            $table->string('nombres_4')->nullable();
            $table->string('email_4')->nullable();
            $table->string('celular_4')->nullable();

            // Auditoría
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Restricción única para evitar duplicados en el UPSERT
            $table->unique(['proyecto_id', 'dni', 'lote', 'manzana'], 'historico_unique_proy_dni_lote_mza');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospecto_historicos');
    }
};
