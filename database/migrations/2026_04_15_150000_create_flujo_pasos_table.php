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
        Schema::create('flujo_pasos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_solicitud_id')->constrained('tipo_solicituds')->onDelete('cascade');
            $table->string('nombre_paso'); // Ej: "Derivar a tal", "Pedir esto"
            $table->integer('orden')->default(1); // Para saber qué paso va primero
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flujo_pasos');
    }
};
