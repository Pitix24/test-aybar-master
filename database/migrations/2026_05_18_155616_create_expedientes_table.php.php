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
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_ticket')->unique()->nullable();
            $table->string('emisor');
            $table->string('emisor_nombre')->nullable();
            $table->json('cc')->nullable();
            $table->string('asunto');
            $table->longText('cuerpo');
            $table->longText('cuerpo_html')->nullable();
            $table->string('message_id')->unique(); // evita duplicados
            $table->timestamp('fecha_correo');
            $table->string('estado')->default('nuevo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
