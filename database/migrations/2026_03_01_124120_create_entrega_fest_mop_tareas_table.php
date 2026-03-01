<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entrega_fest_mop_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('entrega_fest_id')->constrained('entrega_fests', indexName: 'ef_mop_fest_fk')->cascadeOnDelete();
            $table->string('titulo');
            $table->enum('fase', ['ANTES', 'DURANTE', 'CIERRE']);
            $table->text('instruccion');
            $table->boolean('esta_completado')->default(false);
            $table->timestamp('completado_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_fest_mop_tareas');
    }
};
