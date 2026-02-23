<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('correo_campana_envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campana_id')->constrained('correo_campanas')->onDelete('cascade');
            $table->foreignId('contacto_id')->constrained('correo_contactos')->onDelete('cascade');
            $table->enum('estado', ['ENVIADO', 'ERROR'])->default('ENVIADO');
            $table->text('error_mensaje')->nullable();
            $table->timestamp('enviado_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correo_campana_envios');
    }
};
