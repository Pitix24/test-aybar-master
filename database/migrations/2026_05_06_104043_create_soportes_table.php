<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Reemplazar el stub vacío con el schema completo:
     * id, codigo (auto-generado, ej: SP-0001)
     * tipo (BUG, MEJORA, IMPLEMENTACION, CONSULTA)
     * prioridad (BAJA, MEDIA, ALTA, CRITICA)
     * estado (ABIERTO, EN_PROGRESO, EN_REVISION, RESUELTO, CERRADO)
     * titulo (string 255, required)
     * descripcion (text, required)
     * solicitante_id → FK users (quien crea)
     * gestor_id → FK users (quien atiende), nullable
     * assigned_at (datetime), nullable
     * resuelto_at (datetime), nullable
     * created_by, updated_by, deleted_by
     * timestamps, softDeletes

     */
    public function up(): void
    {
        Schema::create('soportes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->enum('tipo', ['BUG', 'MEJORA', 'IMPLEMENTACION', 'CONSULTA']);
            $table->enum('prioridad', ['BAJA', 'MEDIA', 'ALTA', 'CRITICA']);
            $table->enum('estado', ['ABIERTO', 'EN_PROGRESO', 'EN_REVISION', 'RESUELTO', 'CERRADO']);
            $table->string('titulo');
            $table->text('descripcion');
            $table->foreignId('solicitante_id')->constrained('users');
            $table->foreignId('gestor_id')->nullable()->constrained('users');
            $table->dateTime('assigned_at')->nullable();
            $table->dateTime('resuelto_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soportes');
    }
};
