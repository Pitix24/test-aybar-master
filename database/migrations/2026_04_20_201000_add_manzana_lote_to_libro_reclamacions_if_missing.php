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

        Schema::table('libro_reclamacions', function (Blueprint $table): void {
            if (! Schema::hasColumn('libro_reclamacions', 'manzana')) {
                $table->string('manzana', 5)->nullable()->after('codigo');
            }

            if (! Schema::hasColumn('libro_reclamacions', 'lote')) {
                $table->string('lote', 5)->nullable()->after('manzana');
            }
        });
    }

    public function down(): void
    {
        // No-op. Safe alignment migration for restored backups.
    }
};
