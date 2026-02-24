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
        Schema::create('entrega_fests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gestor_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('codigo')->unique();
            $table->date('fecha_entrega');
            $table->boolean('activo')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrega_fests');
    }
};
