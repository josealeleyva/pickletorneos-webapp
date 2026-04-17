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
        Schema::table('juegos', function (Blueprint $table) {
            // Hacer nullable las columnas antiguas que ya no se usan
            $table->integer('juego_equipo1')->nullable()->change();
            $table->integer('juego_equipo2')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('juegos', function (Blueprint $table) {
            $table->integer('juego_equipo1')->nullable(false)->change();
            $table->integer('juego_equipo2')->nullable(false)->change();
        });
    }
};
