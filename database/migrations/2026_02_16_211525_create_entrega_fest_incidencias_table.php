<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entrega_fest_incidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_fest_id')->constrained('entrega_fests', indexName: 'ef_inc_fest_fk')->cascadeOnDelete();
            $table->string('tipo');
            $table->enum('prioridad', ['BAJA', 'MEDIA', 'ALTA'])->default('MEDIA');
            $table->text('descripcion');
            $table->text('solucion')->nullable();
            $table->string('ubicacion')->nullable();
            $table->foreignId('informante_user_id')->constrained('users', indexName: 'ef_inc_inf_fk')->cascadeOnDelete();
            $table->foreignId('responsable_user_id')->nullable()->constrained('users', indexName: 'ef_inc_resp_fk')->nullOnDelete();
            $table->enum('estado', ['ABIERTO', 'PROCESO', 'RESUELTO'])->default('ABIERTO');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_fest_incidencias');
    }
};
