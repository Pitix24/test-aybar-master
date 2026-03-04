<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entrega_fest_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_fest_id')->constrained('entrega_fests')->cascadeOnDelete();
            $table->string('nombre_comercial');
            $table->string('contacto_nombre')->nullable();
            $table->string('contacto_telefono')->nullable();
            $table->string('servicio_tipo')->nullable();
            $table->time('h_llegada')->nullable();
            $table->time('h_montaje')->nullable();
            $table->time('h_show')->nullable();
            $table->time('h_desmontaje')->nullable();
            $table->enum('estado', ['CONFIRMADO', 'EN_SITIO', 'COMPLETADO'])->default('CONFIRMADO');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_fest_proveedores');
    }
};
