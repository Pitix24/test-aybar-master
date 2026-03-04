<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entrega_fest_recursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_fest_id')->constrained('entrega_fests')->cascadeOnDelete();
            $table->string('nombre_publico');
            $table->enum('tipo_recurso', ['MAPA', 'MANUAL', 'FOTO', 'OTRO']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_fest_recursos');
    }
};
