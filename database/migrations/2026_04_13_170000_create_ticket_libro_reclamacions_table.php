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
        Schema::create('ticket_libro_reclamacions', function (Blueprint $table) {
            $table->id();

            $table->string('codigo')->unique();
            $table->unsignedBigInteger('libro_reclamacion_ticket')->nullable();

            $table->foreignId('unidad_negocio_id')->nullable()->constrained('unidad_negocios')->nullOnDelete();
            $table->foreignId('proyecto_id')->nullable()->constrained('proyectos')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('gestor_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('estado_legal', ['NUEVO', 'EN_GESTION', 'OBSERVADO', 'RESUELTO', 'NO_PROCEDE', 'CERRADO'])
                ->default('NUEVO');
            $table->enum('clasificacion', ['PROCEDE', 'NO_PROCEDE', 'PENDIENTE_REVISION'])
                ->default('PENDIENTE_REVISION');

            $table->text('nota_fuente')->nullable();
            $table->text('observaciones_internas')->nullable();
            $table->dateTime('assigned_at')->nullable();

            $table->foreign('libro_reclamacion_ticket')
                ->references('ticket')
                ->on('libro_reclamacions')
                ->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('libro_reclamacion_ticket');
            $table->index('estado_legal');
            $table->index('clasificacion');
            $table->index('gestor_id');
            $table->index('proyecto_id');
            $table->index('unidad_negocio_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_libro_reclamacions');
    }
};
