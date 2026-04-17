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
        Schema::create('referidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referidor_id')->constrained('users');
            $table->foreignId('referido_id')->constrained('users');
            $table->timestamp('fecha_registro');
            $table->enum('estado', ['pendiente', 'activo', 'expirado'])->default('pendiente');
            $table->timestamp('fecha_activacion')->nullable();
            $table->timestamps();

            $table->index(['referidor_id', 'estado']);
            $table->unique(['referidor_id', 'referido_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referidos');
    }
};
