<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entrega_fest_proveedor_requerimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('entrega_fest_proveedores', indexName: 'ef_pr_prov_fk')->cascadeOnDelete();
            $table->string('requerimiento');
            $table->boolean('esta_cubierto')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_fest_proveedor_requerimientos');
    }
};
