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
        Schema::create('configuracion_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique(); // precio_torneo, mercadopago_public_key, etc.
            $table->text('valor'); // El valor de la configuración
            $table->string('tipo')->default('string'); // string, decimal, integer, boolean, json
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // Insertar configuración por defecto
        DB::table('configuracion_sistema')->insert([
            [
                'clave' => 'precio_torneo',
                'valor' => '25000',
                'tipo' => 'decimal',
                'descripcion' => 'Precio por torneo (en pesos argentinos) para torneos a partir del segundo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_sistema');
    }
};
