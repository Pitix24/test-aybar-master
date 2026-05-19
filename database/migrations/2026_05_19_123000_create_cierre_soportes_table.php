<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cierre_soportes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('color')->nullable(); // HEX color
            $table->string('icono')->nullable(); // Font Awesome class
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('cierre_soportes');
    }
};
