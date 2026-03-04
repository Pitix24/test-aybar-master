<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entrega_fest_itinerario_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerario_bloque_id')->constrained('entrega_fest_itinerario_bloques', indexName: 'ef_it_ch_bloque_fk')->cascadeOnDelete();
            $table->string('tarea');
            $table->boolean('esta_listo')->default(false);
            $table->timestamp('completado_at')->nullable();
            $table->foreignId('completado_por_user_id')->nullable()->constrained('users', indexName: 'ef_it_ch_user_fk')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_fest_itinerario_checklists');
    }
};
