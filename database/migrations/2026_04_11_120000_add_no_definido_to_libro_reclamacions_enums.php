<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE libro_reclamacions MODIFY COLUMN tipo_documento ENUM('DNI', 'RUC', 'CE', 'NO_DEFINIDO') NOT NULL DEFAULT 'DNI'");
        DB::statement("ALTER TABLE libro_reclamacions MODIFY COLUMN tipo_bien_contratado ENUM('PRODUCTO', 'SERVICIO', 'NO_DEFINIDO') NOT NULL DEFAULT 'PRODUCTO'");
        DB::statement("ALTER TABLE libro_reclamacions MODIFY COLUMN tipo_pedido ENUM('RECLAMO', 'QUEJA', 'NO_DEFINIDO') NOT NULL DEFAULT 'RECLAMO'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE libro_reclamacions SET tipo_documento = 'DNI' WHERE tipo_documento = 'NO_DEFINIDO'");
        DB::statement("UPDATE libro_reclamacions SET tipo_bien_contratado = 'PRODUCTO' WHERE tipo_bien_contratado = 'NO_DEFINIDO'");
        DB::statement("UPDATE libro_reclamacions SET tipo_pedido = 'RECLAMO' WHERE tipo_pedido = 'NO_DEFINIDO'");

        DB::statement("ALTER TABLE libro_reclamacions MODIFY COLUMN tipo_documento ENUM('DNI', 'RUC', 'CE') NOT NULL DEFAULT 'DNI'");
        DB::statement("ALTER TABLE libro_reclamacions MODIFY COLUMN tipo_bien_contratado ENUM('PRODUCTO', 'SERVICIO') NOT NULL DEFAULT 'PRODUCTO'");
        DB::statement("ALTER TABLE libro_reclamacions MODIFY COLUMN tipo_pedido ENUM('RECLAMO', 'QUEJA') NOT NULL DEFAULT 'RECLAMO'");
    }
};
