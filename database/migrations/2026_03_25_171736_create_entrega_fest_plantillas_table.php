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
        Schema::create('entrega_fest_plantillas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entrega_fest_id')->constrained('entrega_fests')->cascadeOnDelete();

            $table->string('tipo')->comment('pre-invitacion, confirmacion, recordatorio');
            $table->string('titulo')->nullable(); //es como el titulo
            $table->string('subtitulo')->nullable(); //subtitulo
            $table->text('descripcion')->nullable(); //descripcion
            $table->string('link_boton')->nullable(); //boton
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
        Schema::dropIfExists('entrega_fest_plantillas');
    }
};
