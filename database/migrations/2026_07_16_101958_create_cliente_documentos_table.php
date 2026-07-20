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
        Schema::create('cliente_documentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('tipo_cliente_documentos_id')->constrained('tipo_cliente_documentos')->onDelete('cascade');

            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('icono')->nullable();

            $table->bigInteger('clicks')->default(0);
            $table->boolean('solo_lectura')->default(true);
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_documentos');
    }
};
