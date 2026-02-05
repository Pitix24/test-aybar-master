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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nombre')->nullable();
            $table->string('email')->nullable();

            $table->string('dni')->nullable();
            $table->string('telefono_principal')->nullable();
            $table->string('telefono_alternativo')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['email', 'deleted_at']);
            $table->unique(['dni', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
