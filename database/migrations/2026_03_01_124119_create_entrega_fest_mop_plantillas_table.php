<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entrega_fest_mop_plantillas', function (Blueprint $table) {
            $table->id();
            $table->string('rol_nombre');
            $table->enum('fase', ['ANTES', 'DURANTE', 'CIERRE']);
            $table->text('instruccion');
            $table->integer('prioridad')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_fest_mop_plantillas');
    }
};
