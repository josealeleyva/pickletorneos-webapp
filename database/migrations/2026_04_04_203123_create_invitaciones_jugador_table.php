<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitaciones_jugador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_equipo_id')->constrained('inscripciones_equipo')->cascadeOnDelete();
            $table->foreignId('jugador_id')->constrained('jugadores');
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada'])->default('pendiente');
            $table->boolean('auto_aceptada')->default(false);
            $table->string('token')->unique();
            $table->dateTime('respondido_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitaciones_jugador');
    }
};
