<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_campanas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->foreignId('plantilla_id')->constrained('whatsapp_plantillas')->onDelete('cascade');
            $table->json('segmento_filtro')->nullable();
            $table->enum('estado', ['borrador', 'programado', 'enviando', 'finalizado'])->default('borrador');
            $table->integer('total_enviados')->default(0);
            $table->integer('total_leidos')->default(0);
            $table->timestamp('programado_para')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_campanas');
    }
};
