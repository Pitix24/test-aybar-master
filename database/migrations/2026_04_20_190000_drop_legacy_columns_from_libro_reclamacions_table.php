<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('libro_reclamacions')) {
            return;
        }

        $columns = [
            'nombre',
            'apellido_paterno',
            'apellido_materno',
            'domicilio',
            'telefono',
            'email',
            'tipo_documento',
            'numero_documento',
            'nota_fuente_titulo',
            'nota_fuente_fecha',
        ];

        $dropColumns = array_values(array_filter($columns, static fn (string $column): bool => Schema::hasColumn('libro_reclamacions', $column)));

        if (empty($dropColumns)) {
            return;
        }

        Schema::table('libro_reclamacions', function (Blueprint $table) use ($dropColumns): void {
            $table->dropColumn($dropColumns);
        });
    }

    public function down(): void
    {
        // Destructive schema migration. No rollback to avoid restoring deprecated fields.
    }
};
