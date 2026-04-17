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
        Schema::create('creditos_referidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('referido_id')->constrained('users');
            $table->decimal('monto', 10, 2)->default(25000);
            $table->enum('estado', ['disponible', 'usado', 'expirado'])->default('disponible');
            $table->timestamp('fecha_acreditacion');
            $table->timestamp('fecha_vencimiento');
            $table->foreignId('torneo_usado_id')->nullable()->constrained('torneos');
            $table->timestamp('fecha_uso')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditos_referidos');
    }
};
