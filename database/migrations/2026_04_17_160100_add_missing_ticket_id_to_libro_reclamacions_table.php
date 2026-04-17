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
        if (! Schema::hasColumn('libro_reclamacions', 'ticket_id')) {
            Schema::table('libro_reclamacions', function (Blueprint $table) {
                $table->foreignId('ticket_id')
                    ->nullable()
                    ->after('gestor_id')
                    ->constrained('tickets')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('libro_reclamacions', 'ticket_id')) {
            Schema::table('libro_reclamacions', function (Blueprint $table) {
                $table->dropForeign(['ticket_id']);
                $table->dropColumn('ticket_id');
            });
        }
    }
};
