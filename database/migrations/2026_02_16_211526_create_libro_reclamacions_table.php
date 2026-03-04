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
        Schema::create('libro_reclamacions', function (Blueprint $table) {
            $table->bigIncrements('ticket');
            $table->foreignId('unidad_negocio_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('proyecto_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('gestor_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('serie')->default('TCK');
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('domicilio');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->enum('tipo_documento', ['DNI', 'RUC', 'CE'])->default('DNI');
            $table->string('numero_documento');

            $table->enum('tipo_bien_contratado', ['PRODUCTO', 'SERVICIO'])->default('PRODUCTO');
            $table->decimal('monto_reclamado', 10, 2)->nullable();
            $table->text('descripcion')->nullable();

            $table->enum('tipo_pedido', ['RECLAMO', 'QUEJA'])->default('RECLAMO');
            $table->text('detalle')->nullable();
            $table->text('pedido')->nullable();
            $table->boolean('conformidad')->default(false);

            $table->text('observaciones')->nullable();
            $table->dateTime('fecha_respuesta')->nullable()->comment('Fecha de respuesta al usuario.');

            $table->string('archivo_1')->nullable();
            $table->string('archivo_2')->nullable();
            $table->string('archivo_3')->nullable();
            $table->string('archivo_4')->nullable();

            $table->boolean('leido')->default(false);
            $table->enum('estado', ['NUEVO', 'REVISION', 'RESUELTO', 'CERRADO'])->default('NUEVO');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libro_reclamacions');
    }
};
