<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Reemplazar el stub vacío con el schema completo:
     * id, codigo (auto-generado, ej: SP-0001)
     * tipo_soporte_id (FK tipo_soportes)
     * prioridad_soporte_id (FK prioridad_soportes)
     * estado_soporte_id (FK estado_soportes)
     * area_id (FK areas)
     * titulo (string 255, required)
     * descripcion (text, required)
     * observaciones (text, nullable)
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
            $table->foreignId('tipo_soporte_id')->nullable()->constrained('tipo_soportes');
            $table->foreignId('prioridad_soporte_id')->nullable()->constrained('prioridad_soportes');
            $table->foreignId('estado_soporte_id')->nullable()->constrained('estado_soportes');
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->string('titulo');
            $table->text('descripcion');
            $table->text('observaciones')->nullable();
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
