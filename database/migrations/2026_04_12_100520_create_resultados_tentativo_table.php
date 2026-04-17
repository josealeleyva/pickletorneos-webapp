<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultados_tentativo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->unique()->constrained('partidos')->onDelete('cascade');
            $table->foreignId('propuesto_por_equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('propuesto_por_jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->json('juegos'); // [{"juego_equipo1": 6, "juego_equipo2": 3}, ...]
            $table->integer('sets_equipo1')->default(0);
            $table->integer('sets_equipo2')->default(0);
            $table->foreignId('equipo_ganador_id')->nullable()->constrained('equipos')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultados_tentativo');
    }
};
