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
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            // 1. ANCLAJES DE NEGOCIO (REQUERIMIENTOS DEL PO/PM)
            // Número oficial emitido por Indecopi (ej: 447-2025/CC2). Clave para el Upsert.
            $table->string('numero_expediente')->nullable()->index(); 
            // Razón Social afectada (1 al 6). Sacado del header To del reenvío.
            $table->foreignId('unidad_negocio_id')->constrained('unidad_negocios')->onDelete('cascade');
            // El Ticket Padre asignado en el ERP (permite gestionar chats, tareas e hijos)
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->onDelete('set null');
            // 2. CLASIFICACIONES DE PARSEO
            // TRASLADO_RECLAMO | CITACION_AUDIENCIA | NOTIFICACION_ADMINISTRATIVA | ACTA_AUDIENCIA
            $table->string('tipo_notificacion')->nullable();             
            // Nuevo | Notificación | Incompleto (Registro de emergencia ante fallas de lectura)
            $table->string('tipo_registro')->default('Nuevo'); 
            // 3. DATOS DEL CORREO CAPTURADO
            $table->string('emisor');
            $table->string('emisor_nombre')->nullable();
            $table->json('cc')->nullable();
            $table->string('asunto');
            $table->longText('cuerpo');
            $table->longText('cuerpo_html')->nullable();
            $table->string('message_id')->unique(); // El ID único de Gmail para evitar duplicados históricos
            // 4. DATOS LOGÍSTICOS Y ADICIONALES (Fase 1 Ideas)
            $table->timestamp('fecha_correo'); // Cuándo llegó realmente a los servidores
            $table->string('estado')->default('nuevo'); // Estado interno del flujo legal
            // Campos opcionales para almacenar enlaces de audiencias o advertencias si aplica
            $table->json('payload_metadata')->nullable(); 
            // 5. TIMESTAMPS DE CONTROL Y AUDITORÍA (TU REQUERIMIENTO)
            $table->timestamps();   // Genera created_at y updated_at
            $table->softDeletes();  // Genera deleted_at (Auditoría de eliminación)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};