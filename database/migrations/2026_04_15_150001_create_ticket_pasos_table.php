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
        Schema::create('ticket_pasos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('flujo_paso_id')->constrained('flujo_pasos');
            $table->boolean('completado')->default(false); // El check que mencionas
            $table->dateTime('fecha_completado')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users'); // Quien marcó el check
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_pasos');
    }
};
