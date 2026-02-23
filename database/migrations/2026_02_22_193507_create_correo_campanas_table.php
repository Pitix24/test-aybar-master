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
        Schema::create('correo_campanas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantilla_id')->constrained('correo_plantillas')->onDelete('cascade');
            $table->foreignId('lista_id')->constrained('correo_listas')->onDelete('cascade');
            $table->string('nombre');
            $table->enum('estado', ['PENDIENTE', 'EN_PROCESO', 'COMPLETADO', 'FALLIDO'])->default('PENDIENTE');
            $table->integer('total_enviados')->default(0);
            $table->integer('total_errores')->default(0);
            $table->dateTime('scheduled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correo_campanas');
    }
};
