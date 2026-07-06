<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prospecto_entrega_fests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entrega_fest_id')->constrained();
            $table->foreignId('proyecto_id')->nullable()->constrained();
            $table->foreignId('user_id')->nullable()->constrained(); // quien lo registró

            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Relación con Histórico y Estado Lógico (Consolidados)
            $table->unsignedBigInteger('prospecto_historico_id')->nullable();
            $table->foreign('prospecto_historico_id', 'fk_pef_historico')
                  ->references('id')->on('prospecto_historicos')
                  ->onDelete('set null');
            $table->boolean('activo')->default(true);

            $table->string('dni', 15);
            $table->string('nombres');
            $table->string('email')->nullable();
            $table->string('celular')->nullable();

            $table->boolean('preinvitacion_confirmada')->nullable();
            $table->boolean('invitacion_confirmada')->nullable();

            $table->string('lote')->nullable()->collation('utf8mb4_bin');
            $table->string('manzana')->nullable()->collation('utf8mb4_bin');

            $table->foreignId('reubicado_proyecto_id')->nullable()->constrained('proyectos')->nullOnDelete();
            $table->string('reubicado_lote')->nullable()->collation('utf8mb4_bin');
            $table->string('reubicado_manzana')->nullable()->collation('utf8mb4_bin');

            $table->foreignId('estado_cliente_id')->nullable()->constrained('entrega_fest_estado_clientes')->nullOnDelete();

            // ============ BackOffice ============
            $table->enum('grupo', [
                'A',
                'B',
                'C',
                'D'
            ])->default('A');
            $table->foreignId('responsable_llamada_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('responsable_llamada_fecha_asignacion')->nullable();

            $table->foreignId('gestor_backoffice_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('gestor_fecha_asignacion')->nullable();

            $table->dateTime('fecha_culminacion_eecc')->nullable();
            $table->string('link_carpeta_eecc')->nullable();
            $table->string('link_eecc_firmado')->nullable();

            $table->enum('estado_gestor_backoffice', [
                'PENDIENTE',
                'BANCARIZAR',
                'PENALIDAD',
                'OBSERVADO',
                'CONFORME',
                'VIGENTE'
            ])->default('PENDIENTE');
            $table->text('observacion_gestor_backoffice')->nullable();

            $table->foreignId('validador_backoffice_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_validacion_eecc')->nullable();
            $table->enum('estado_backoffice', [
                'PENDIENTE',
                'BANCARIZAR',
                'PENALIDAD',
                'OBSERVADO',
                'CONFORME',
                'VIGENTE'
            ])->default('PENDIENTE');

            // ============ Legal ============
            $table->enum('estado_contrato_preeliminar_emitido', [
                'PENDIENTE',
                'GENERADO',
                'OBSERVADO',
                'CONFORME'
            ])->default('PENDIENTE');

            // ============ GESTOR LEGAL ============
            $table->foreignId('gestor_legal_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('legal_fecha_asignacion')->nullable();
            $table->text('observacion_gestor_legal')->nullable();

            // ============ VALIDADOR LEGAL (FIRMA) ============
            $table->foreignId('validador_legal_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_firma_presencial')->nullable();
            $table->dateTime('fecha_validacion_firma')->nullable();

            // ============ FIRMA CONTRATO ============
            $table->enum('estado_firma_contrato_firmado', [
                'PENDIENTE',
                'FIRMADO'
            ])->default('PENDIENTE');
            $table->dateTime('fecha_firma')->nullable();
            $table->dateTime('fecha_generacion_contrato')->nullable();

            $table->timestamps();

            $table->unique(
                ['entrega_fest_id', 'proyecto_id', 'lote', 'manzana'],
                'prospecto_entrega_fest_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospecto_entrega_fests');
    }
};
