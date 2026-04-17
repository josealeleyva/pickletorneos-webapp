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
        Schema::create('equipo_plantillas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre del equipo (ej: "Boca Juniors")
            $table->foreignId('organizador_id')->constrained('users')->onDelete('cascade'); // Quien creó este equipo
            $table->foreignId('deporte_id')->constrained('deportes')->onDelete('cascade'); // Deporte del equipo
            $table->json('ultima_formacion')->nullable(); // Array de IDs de jugadores de la última vez
            $table->integer('veces_usado')->default(0); // Contador de cuántas veces se usó
            $table->timestamp('ultimo_uso')->nullable(); // Última vez que se usó
            $table->timestamps();

            // Índices para búsqueda rápida
            $table->index(['organizador_id', 'deporte_id', 'nombre']);
            $table->index(['organizador_id', 'deporte_id', 'ultimo_uso']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipo_plantillas');
    }
};
