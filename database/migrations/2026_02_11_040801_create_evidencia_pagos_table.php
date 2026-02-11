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
        Schema::create('evidencia_pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('solicitud_evidencia_pago_id')
                ->constrained('solicitud_evidencia_pagos')
                ->cascadeOnDelete();

            $table->foreignId('estado_solicitud_evidencia_pago_id')->default(1)->constrained('estado_solicitud_evidencia_pagos', indexName: 'evid_pago_estado_fk')->onDelete('restrict');

            // Archivo
            $table->string('path');
            $table->string('url');
            $table->string('extension');

            // OpenAI
            $table->string('numero_operacion')->nullable();
            $table->string('banco')->nullable();
            $table->decimal('monto', 10, 2)->nullable();
            $table->date('fecha')->nullable();

            $table->boolean('es_reenvio')->default(false);
            $table->text('slin_respuesta')->nullable();
            $table->text('observacion')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidencia_pagos');
    }
};
