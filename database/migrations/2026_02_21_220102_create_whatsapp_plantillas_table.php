<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_plantillas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('contenido');
            $table->string('categoria', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_plantillas');
    }
};
