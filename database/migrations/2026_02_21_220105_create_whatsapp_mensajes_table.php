<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversacion_id')->constrained('whatsapp_conversaciones')->onDelete('cascade');
            $table->enum('direccion', ['entrante', 'saliente']);
            $table->enum('tipo', ['texto', 'imagen', 'documento', 'audio', 'plantilla', 'reaccion'])->default('texto');
            $table->text('contenido');
            $table->string('wa_message_id')->unique();
            $table->enum('estado', ['enviado', 'entregado', 'leido', 'fallido'])->default('enviado');
            $table->string('reaccion', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_mensajes');
    }
};
