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
        Schema::create('correo_lista_contacto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lista_id')->constrained('correo_listas')->onDelete('cascade');
            $table->foreignId('contacto_id')->constrained('correo_contactos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correo_lista_contacto');
    }
};
