<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_conversaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contacto_id')->constrained('whatsapp_contactos')->onDelete('cascade');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
            $table->foreignId('agente_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('estado', ['nuevo', 'en_menu', 'asignado', 'cerrado'])->default('nuevo');
            $table->enum('departamento_destino', ['atc', 'backoffice', 'letras'])->nullable();
            $table->integer('mensajes_sin_leer')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversaciones');
    }
};
