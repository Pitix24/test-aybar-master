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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unidad_negocio_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('proyecto_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('gestor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('ticket_padre_id')->nullable()->constrained('tickets')->nullOnDelete();

            $table->foreignId('tipo_solicitud_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sub_tipo_solicitud_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('canal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('estado_ticket_id')->default(1)->constrained('estado_tickets')->cascadeOnDelete();
            $table->foreignId('prioridad_ticket_id')->default(3)->constrained('prioridad_tickets')->cascadeOnDelete();

            $table->string('asunto_inicial');
            $table->text('descripcion_inicial');
            $table->json('lotes')->nullable();

            $table->string('asunto_respuesta')->nullable();
            $table->text('descripcion_respuesta')->nullable();

            //DB ANTIGUO
            $table->string('dni')->nullable();
            $table->string('nombres')->nullable();
            $table->string('email')->nullable();
            $table->string('celular')->nullable();
            $table->string('direccion')->nullable();
            $table->string('origen')->nullable(); //antiguo:clientes_2 o slin

            //SUPERVISOR
            $table->foreignId('usuario_valida_id')->nullable()->constrained('users')->nullOnDelete(); //CERRADO POR
            $table->dateTime('fecha_validacion')->nullable(); //FECHA CIERRE

            //AUDITORIA
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); //CREADO POR
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps(); //FECHA CREADO
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
