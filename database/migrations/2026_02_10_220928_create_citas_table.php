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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unidad_negocio_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('proyecto_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('users')->nullOnDelete(); //quien es agendado
            $table->foreignId('gestor_id')->nullable()->constrained('users')->nullOnDelete(); //quien atiende al cliente //asignado
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();

            $table->foreignId('usuario_crea_id')->constrained('users')->onDelete('cascade'); //quien crea

            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();

            $table->foreignId('motivo_cita_id')->constrained('motivo_citas')->onDelete('restrict');

            $table->foreignId('estado_cita_id')->default(1)->constrained('estado_citas')->onDelete('restrict');

            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->dateTime('fecha_cierre')->nullable();

            $table->string('asunto_solicitud')->nullable();
            $table->text('descripcion_solicitud')->nullable();

            $table->string('asunto_respuesta')->nullable();
            $table->text('descripcion_respuesta')->nullable();

            //DB ANTIGUO
            $table->string('dni')->nullable();
            $table->string('nombres')->nullable();
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
        Schema::dropIfExists('citas');
    }
};
