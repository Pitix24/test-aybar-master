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
        Schema::create('evidencia_pago_antiguos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unidad_negocio_id')->nullable()->constrained('unidad_negocios')->nullOnDelete();
            $table->foreignId('proyecto_id')->nullable()->constrained('proyectos')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('gestor_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('imagen_url')->nullable();

            $table->string('operacion_numero')->nullable();
            $table->string('operacion_hora')->nullable();
            $table->string('union')->nullable();
            $table->string('cuota_fija')->nullable();
            $table->string('monto')->nullable();
            $table->string('pago_de')->nullable();
            $table->string('codigo_cuenta')->nullable();
            $table->string('nombre_archivo')->nullable();
            $table->string('moneda')->nullable();
            $table->string('medio_pago')->nullable();
            $table->date('fecha_deposito')->nullable();

            $table->text('observacion')->nullable();
            $table->foreignId('estado_solicitud_evidencia_pago_id')->default(1)->constrained('estado_solicitud_evidencia_pagos', indexName: 'evid_antiguo_pago_estado_fk')->onDelete('restrict');
            $table->string('estado_registro')->default('PENDIENTE');

            $table->string('dni_cliente')->nullable();
            $table->string('codigo_cliente')->nullable();
            $table->string('nombres_cliente')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('proyecto_nombre')->nullable();
            $table->string('etapa')->nullable();
            $table->string('lote')->nullable();
            $table->string('numero_cuota')->nullable();

            $table->string('gestor')->nullable();
            $table->date('fecha_registro')->nullable();

            //SUPERVISOR
            $table->foreignId('usuario_valida_id')->nullable()->constrained('users')->nullOnDelete(); //CERRADO POR
            $table->string('validador')->nullable();
            $table->date('fecha_validacion')->nullable(); //FECHA CIERRE

            //AUDITORIA
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); //CREADO POR
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps(); //FECHA CREADO
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidencia_pago_antiguos');
    }
};
