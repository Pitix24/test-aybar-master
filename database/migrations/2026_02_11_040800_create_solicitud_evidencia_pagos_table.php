<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitud_evidencia_pagos', function (Blueprint $table) {
            $table->id();

            // Principales
            $table->foreignId('unidad_negocio_id')->constrained('unidad_negocios')->cascadeOnDelete();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('users')->nullOnDelete(); //user_id
            $table->foreignId('gestor_id')->nullable()->constrained('users')->nullOnDelete(); //asignado
            $table->foreignId('estado_solicitud_evidencia_pago_id')->default(1)->constrained('estado_solicitud_evidencia_pagos', indexName: 'sol_evid_pago_estado_fk')->onDelete('restrict');

            // Identidad de la cuota
            $table->string('lote_completo')->nullable();
            $table->string('codigo_cuota')->unique();

            // Slin
            $table->string('razon_social');
            $table->string('nombre_proyecto');
            $table->string('etapa');
            $table->string('manzana');
            $table->string('lote');
            $table->string('codigo_cliente')->nullable();
            $table->string('numero_cuota')->nullable();
            $table->string('transaccion_id')->nullable(); //idcobranzas
            $table->string('fecha_operacion')->nullable();
            $table->string('fecha_vencimiento')->nullable();
            $table->decimal('monto_operacion', 10, 2)->nullable();
            $table->decimal('slin_monto', 10, 2)->nullable();
            $table->decimal('slin_penalidad', 10, 2)->nullable();
            $table->string('slin_numero_operacion')->nullable();
            $table->string('comprobante')->nullable();
            $table->string('ticket')->nullable();
            $table->boolean('slin_asbanc')->default(false);
            $table->boolean('slin_evidencia')->default(false);
            $table->boolean('resuelto_manual')->default(false);

            $table->text('observacion')->nullable();

            //SUPERVISOR
            $table->foreignId('usuario_valida_id')->nullable()->constrained('users')->nullOnDelete(); //CERRADO POR
            $table->dateTime('fecha_validacion')->nullable(); //FECHA CIERRE

            //AUDITORIA
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); //CREADO POR
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps(); //FECHA CREADO
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_evidencia_pagos');
    }
};
