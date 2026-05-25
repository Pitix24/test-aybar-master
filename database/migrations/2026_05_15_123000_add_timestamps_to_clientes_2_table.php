<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('clientes_2')) {
            if (!Schema::hasColumn('clientes_2', 'created_at')) {
                Schema::table('clientes_2', function (Blueprint $table) {
                    $table->timestamp('created_at')->nullable()->after('dni');
                });
            }

            if (!Schema::hasColumn('clientes_2', 'updated_at')) {
                Schema::table('clientes_2', function (Blueprint $table) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('clientes_2')) {
            if (Schema::hasColumn('clientes_2', 'updated_at')) {
                Schema::table('clientes_2', function (Blueprint $table) {
                    $table->dropColumn('updated_at');
                });
            }

            if (Schema::hasColumn('clientes_2', 'created_at')) {
                Schema::table('clientes_2', function (Blueprint $table) {
                    $table->dropColumn('created_at');
                });
            }
        }
    }
};
