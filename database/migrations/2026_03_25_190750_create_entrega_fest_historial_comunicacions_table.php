<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entrega_fest_historial_comunicaciones', function (Blueprint $table) {
            $table->id();

            // Relación Polimórfica: Con el índice acortado para evitar el error de MySQL
            $table->morphs('persona', 'idx_persona_com'); 
            
            // Canal: whatsapp, correo, sms
            $table->string('canal')->comment('whatsapp, correo, sms');
            
            // Etapa: pre-invitacion, invitacion, recordatorio
            $table->string('etapa')->comment('pre-invitacion, invitacion, recordatorio');

            // Estado para saber si llegó o falló
            $table->string('estado')->default('enviado')->comment('borrador, enviado, fallido, leido');
            
            // Un campo extra por si n8n manda algún error o ID de mensaje
            $table->json('metadata')->nullable();

            $table->timestamp('fecha_envio')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrega_fest_historial_comunicacions');
    }
};
