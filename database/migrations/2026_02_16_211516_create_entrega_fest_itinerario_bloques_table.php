<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entrega_fest_itinerario_bloques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_fest_id')->constrained('entrega_fests')->cascadeOnDelete();
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('ubicacion')->nullable();
            $table->enum('estado', ['PENDIENTE', 'CURSO', 'COMPLETADO'])->default('PENDIENTE');
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_fest_itinerario_bloques');
    }
};
