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
        Schema::create('solicitud_evidencia_mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_evidencia_pago_id')->constrained('solicitud_evidencia_pagos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->text('mensaje');
            $table->boolean('es_interno')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_evidencia_mensajes');
    }
};
